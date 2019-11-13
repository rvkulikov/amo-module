<?php

namespace rvkulikov\amo\module\services\group\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Group;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
class GroupSyncer_Impl extends Component implements GroupSyncer_Interface
{
    /** @var GroupSyncer_Cfg */
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
     * @param array|GroupSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, GroupSyncer_Cfg::class, true);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        $groups = array_map(function ($group) {
            $group['account_id'] = $this->cfg->accountId;
            return $group;
        }, $this->cfg->groups);
        $this->syncGroups($groups);
    }

    /**
     * @param mixed[] $groups
     * @throws InvalidConfigException
     * @throws Exception
     * @throws InvalidModelException
     */
    private function syncGroups($groups)
    {
        array_walk($groups, function ($group) {
            $model = null;
            $model = $model ?? Group::findOne(['account_id' => $this->account->id, 'id' => $group['id']]);
            $model = $model ?? new Group(['account_id' => $this->account->id, 'id' => $group['id']]);

            $model->load([
                'name' => $group['name'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => Group::class,
            'strategy' => 'soft',
            'rows' => $groups,
            'where' => ['account_id' => $this->account->id],
        ]);
    }
}