<?php
namespace rvkulikov\amo\module\services\account;

use yii\base\Model;

/**
 *
 */
class AccountCreator_Cfg extends Model
{
    public $account_id;
    public $integration_id;
    public $secret_key;
    public $authorization_code;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['account_id', 'safe'],
            ['account_id', 'required'],

            ['integration_id', 'safe'],
            ['integration_id', 'required'],

            ['secret_key', 'safe'],
            ['secret_key', 'required'],

            ['authorization_code', 'safe'],
            ['authorization_code', 'required'],
        ];
    }
}