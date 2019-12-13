<?php

namespace rvkulikov\amo\module\services\noteType\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class NoteTypeSyncer_Cfg extends Model
{
    /** @var int */
    public $accountId;
    /** @var mixed[] */
    public $noteTypes;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['noteTypes', 'safe'],
        ];
    }
}