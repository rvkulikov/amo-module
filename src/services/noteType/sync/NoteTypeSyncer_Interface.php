<?php
namespace rvkulikov\amo\module\services\noteType\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
interface NoteTypeSyncer_Interface
{
    /**
     * @param array|NoteTypeSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg);
}