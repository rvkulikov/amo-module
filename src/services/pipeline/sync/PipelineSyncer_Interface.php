<?php

namespace rvkulikov\amo\module\services\pipeline\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;

/**
 *
 */
interface PipelineSyncer_Interface
{
    /**
     * @param array|PipelineSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     */
    public function sync($cfg);
}