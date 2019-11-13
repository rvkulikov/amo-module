<?php
namespace rvkulikov\amo\module\services\pipeline\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Pipeline;
use rvkulikov\amo\module\models\Status;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;
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
    /** @var SafeDeleter_Interface */
    private $safeDeleter;

    /**
     * {@inheritDoc}
     */
    public function __construct(SafeDeleter_Interface $safeDeleter, $config = [])
    {
        parent::__construct($config);
        $this->safeDeleter = $safeDeleter;
    }

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
     * @throws Exception
     * @throws InvalidConfigException
     * @throws InvalidModelException
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
                'deleted_at' => null,
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => Pipeline::class,
            'strategy' => 'soft',
            'rows' => $pipelines,
            'where' => ['account_id' => $this->account->id],
        ]);
    }

    /**
     * @param mixed[] $statuses
     * @throws Exception
     * @throws InvalidConfigException
     * @throws InvalidModelException
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
                'deleted_at' => null,
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => Status::class,
            'strategy' => 'soft',
            'rows' => $statuses,
            'where' => ['account_id' => $this->account->id],
        ]);
    }
}