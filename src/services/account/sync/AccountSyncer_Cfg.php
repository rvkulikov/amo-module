<?php

namespace rvkulikov\amo\module\services\account\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class AccountSyncer_Cfg extends Model
{
    /** @var int */
    public $accountId;

    /** @var string[] */
    public $withAllowed = ['users', 'groups', 'custom_fields', 'pipelines', 'note_types', 'task_types'];
    /** @var string[] */
    public $withOnly;
    /** @var string[] */
    public $withExcept;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['withOnly', 'in', 'range' => $this->withAllowed, 'allowArray' => true],
            ['withExcept', 'in', 'range' => $this->withAllowed, 'allowArray' => true],
        ];
    }
}