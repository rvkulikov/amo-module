<?php

namespace rvkulikov\amo\module\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 *
 */
class SyncAccount_Job extends BaseObject implements JobInterface
{

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        // TODO: Implement execute() method.
    }
}