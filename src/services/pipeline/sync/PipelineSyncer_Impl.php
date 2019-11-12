<?php

namespace rvkulikov\amo\module\services\pipeline\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Pipeline;
use rvkulikov\amo\module\models\Status;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 *
 */
class PipelineSyncer_Impl extends Component implements PipelineSyncer_Interface
{
    /** @var PipelineSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;

    /**
     * @param array|PipelineSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, PipelineSyncer_Cfg::class, true);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        Account::getDb()->transaction(function () {
            $pipelines = $this->cfg->pipelines;
            $statuses = array_merge(...ArrayHelper::getColumn($pipelines, function ($pipeline) {
                return array_map(function ($status) use ($pipeline) {
                    $status['pipeline_id'] = $pipeline['id'];
                    return $status;
                }, $pipeline['statuses']);
            }));

            $this->savePipelines($pipelines);
            $this->saveStatuses($statuses);
        });
    }

    /**
     * @param mixed[] $pipelines
     */
    private function savePipelines($pipelines)
    {
        array_walk($pipelines, function ($pipeline) {
            $model = null;
            $model = $model ?? Pipeline::findOne(['id' => $pipeline['id']]);
            $model = $model ?? new Pipeline(['id' => $pipeline['id']]);

            $model->load([
                'account_id' => $this->account->id,
                'name' => $pipeline['name'],
                'sort' => $pipeline['sort'],
                'is_main' => $pipeline['is_main'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $pipelinePks = ArrayHelper::getColumn($pipelines, 'id');
        $obsoletePks = Pipeline::find()->where([
            'and',
            ['account_id' => $this->account->id],
            ['not in', 'id', $pipelinePks],
            ['is not', 'deleted_at', null]
        ])->select(['id'])->column();

        // mark all pipelines which was gone since last sync
        !empty($obsoletePks) && Pipeline::updateAll(
            ['deleted_at' => new Expression('NOW()')],
            ['id' => $obsoletePks]
        );
    }

    /**
     * @param mixed[] $statuses
     * @throws Exception
     */
    private function saveStatuses($statuses)
    {
        array_walk($statuses, function ($status) {
            $model = null;
            $model = $model ?? Status::findOne(['pipeline_id' => $status['pipeline_id'], 'id' => $status['id']]);
            $model = $model ?? new Status(['pipeline_id' => $status['pipeline_id'], 'id' => $status['id']]);

            $model->load([
                'account_id' => $this->account->id,
                'name' => $status['name'],
                'type' => $status['type'],
                'color' => $status['color'],
                'ord' => $status['ord'],
                'is_editable' => $status['is_editable'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $statusPks = ArrayHelper::getColumn($statuses, function ($status) {
            return [
                'pipeline_id' => $status['pipeline_id'],
                'id' => $status['id']
            ];
        });
        $obsoletePks = Status::find()->where([
            'and',
            ['account_id' => $this->account->id],
            ['not in', ['pipeline_id', 'id'], $statusPks],
            ['is not', 'deleted_at', null]
        ])->select(['pipeline_id', 'id'])->createCommand()->queryAll();

        // mark all statuses which was gone since last sync
        !empty($obsoletePks) && Status::updateAll(
            ['deleted_at' => new Expression('NOW()')],
            ['in', ['pipeline_id', 'id'], $obsoletePks]
        );
    }
}