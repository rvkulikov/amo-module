<?php

namespace rvkulikov\amo\module\services\taskType\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class TaskTypeSyncer_Cfg extends Model
{
    /** @var int */
    public $accountId;
    /** @var mixed[] */
    public $taskTypes;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['taskTypes', 'safe'],
        ];
    }
}