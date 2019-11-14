<?php
namespace rvkulikov\amo\module\jobs;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\services\account\sync\AccountSyncer_Cfg;
use rvkulikov\amo\module\services\account\sync\AccountSyncer_Interface;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 *
 */
class SyncAccount_Job extends BaseObject implements JobInterface
{
    /** @var array|AccountSyncer_Cfg */
    public $cfg;
    /** @var AccountSyncer_Interface */
    private $accountSyncer;

    /**
     * {@inheritDoc}
     */
    public function __construct(AccountSyncer_Interface $accountSyncer, $config = [])
    {
        parent::__construct($config);
        $this->accountSyncer = $accountSyncer;
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function execute($queue)
    {
        $this->accountSyncer->sync($this->cfg);
    }
}