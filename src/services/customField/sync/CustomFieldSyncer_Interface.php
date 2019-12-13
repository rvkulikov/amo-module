<?php

namespace rvkulikov\amo\module\services\customField\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;

/**
 *
 */
interface CustomFieldSyncer_Interface
{
    /**
     * @param array|CustomFieldSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     */
    public function sync($cfg);
}