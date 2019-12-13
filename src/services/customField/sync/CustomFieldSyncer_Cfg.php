<?php

namespace rvkulikov\amo\module\services\customField\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class CustomFieldSyncer_Cfg extends Model
{
    public $accountId;
    public $customFields;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['customFields', 'safe'],
            ['customFields', 'required'],
        ];
    }
}