<?php

namespace rvkulikov\amo\module\services\user\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use Throwable;
use yii\base\InvalidConfigException;

/**
 *
 */
interface UserSyncer_Interface
{
    /**
     * @param array|UserSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function sync($cfg);
}