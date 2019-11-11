<?php

namespace rvkulikov\amo\module\services\user\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class UserSyncer_Cfg extends Model
{
    /** @var int */
    public $accountId;
    /** @var mixed[] */
    public $users;
    /** @var mixed[] */
    public $groups;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['users', 'safe'],
            ['users', 'required'],

            ['groups', 'safe'],
            ['groups', 'required'],
        ];
    }
}