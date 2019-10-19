<?php
namespace rvkulikov\amo\module\services\account;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 *
 */
class AccountCreator extends Component
{
    /**
     * @param $cfg
     *
     * @throws InvalidConfigException
     * @throws InvalidModelException
     * @throws \Throwable
     */
    public function create($cfg)
    {
        /** @var AccountCreator_Cfg $cfg */
        $cfg = ModelHelper::ensure($cfg, AccountCreator_Cfg::class);
        if (!$cfg->validate()) {
            throw new InvalidModelException($cfg);
        }

        Account::getDb()->transaction(function () use ($cfg) {
            $account = new Account([
                'integration_id'     => $cfg->integration_id,
                'secret_key'         => $cfg->secret_key,
                'authorization_code' => $cfg->authorization_code,
            ]);

            if (!$account->save()) {
                throw new InvalidModelException($account);
            }
        });
    }
}