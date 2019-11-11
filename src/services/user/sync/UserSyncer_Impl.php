<?php

namespace rvkulikov\amo\module\services\user\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\User;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 *
 */
class UserSyncer_Impl extends Component implements UserSyncer_Interface
{
    /** @var UserSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;

    /**
     * @param array|UserSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, UserSyncer_Cfg::class, true);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        User::getDb()->transaction(function () use ($cfg) {
            foreach ($cfg->users as $item) {
                $this->syncUser($item);
            }
        });
    }

    /**
     * @param $item
     */
    private function syncUser($item)
    {

    }
}