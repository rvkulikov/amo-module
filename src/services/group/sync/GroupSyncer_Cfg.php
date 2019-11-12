<?php

namespace rvkulikov\amo\module\services\group\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class GroupSyncer_Cfg extends Model
{
    public $accountId;
    public $groups;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['groups', 'safe'],
            ['groups', 'required'],
        ];
    }
}