<?php

namespace rvkulikov\amo\module\commands;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\services\account\sync\AccountSyncer_Interface;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\httpclient\Exception;

/**
 *
 */
class AccountsController extends Controller
{
    /** @var AccountSyncer_Interface */
    private $accountSyncer;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, AccountSyncer_Interface $accountSyncer, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->accountSyncer = $accountSyncer;
    }

    /**
     * @param int $id
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSync(int $id)
    {
        $this->accountSyncer->sync([
            'accountId' => $id
        ]);
    }
}