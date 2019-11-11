<?php

namespace rvkulikov\amo\module\services\account\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;

/**
 *
 */
interface AccountSyncer_Interface
{
    /**
     * @param AccountSyncer_Cfg|array $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg);
}