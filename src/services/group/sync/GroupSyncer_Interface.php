<?php

namespace rvkulikov\amo\module\services\group\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;

/**
 *
 */
interface GroupSyncer_Interface
{
    /**
     * @param array|GroupSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     */
    public function sync($cfg);
}