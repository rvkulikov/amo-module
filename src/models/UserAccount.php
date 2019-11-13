<?php

namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $user_id [bigint]
 * @property int $account_id [bigint]
 * @property bool $is_active [boolean]
 * @property bool $is_free [boolean]
 * @property bool $is_admin [boolean]
 *
 * @property-read  Account $account
 * @property-read  User $user
 */
class UserAccount extends ActiveRecord
{
    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->params['rvkulikov.amo.db.name']);
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%amo__user_account}}';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey()
    {
        return ['user_id', 'account_id'];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'exist', 'targetRelation' => 'user'],
            ['account_id', 'exist', 'targetRelation' => 'account'],

            ['is_active', 'boolean'],
            ['is_active', 'required'],

            ['is_admin', 'boolean'],
            ['is_admin', 'required'],

            ['is_free', 'boolean'],
            ['is_free', 'required'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('userAccounts');
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userAccounts');
    }
}