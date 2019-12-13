<?php
namespace rvkulikov\amo\module\services\taskType\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
interface TaskTypeSyncer_Interface
{
    /**
     * @param array|TaskTypeSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg);
}