<?php

namespace rvkulikov\amo\module\services\util\safeDelete;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
interface SafeDeleter_Interface
{
    /**
     * @param array|SafeDeleter_Cfg $cfg
     * @return int amount of deleted rows
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function delete($cfg);
}