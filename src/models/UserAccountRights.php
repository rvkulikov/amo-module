<?php

namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $user_id [bigint]
 * @property int $account_id [bigint]
 * @property string $incoming_leads [char]
 * @property string $catalogs [char]
 * @property string $lead_add [char]
 * @property string $lead_view [char]
 * @property string $lead_edit [char]
 * @property string $lead_export [char]
 * @property string $contact_add [char]
 * @property string $contact_view [char]
 * @property string $contact_edit [char]
 * @property string $contact_delete [char]
 * @property string $contact_export [char]
 * @property string $company_add [char]
 * @property string $company_view [char]
 * @property string $company_edit [char]
 * @property string $company_delete [char]
 * @property string $company_export [char]
 * @property string $task_edit [char]
 * @property string $task_delete [char]
 *
 * @property-read User $user
 * @property-read Account $account
 */
class UserAccountRights extends ActiveRecord
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
        return '{{%amo__user_account_rights}}';
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
            ['user_id', 'required'],

            ['account_id', 'exist', 'targetRelation' => 'account'],
            ['account_id', 'required'],

            ['incoming_leads', 'string'],
            ['incoming_leads', 'required'],

            ['catalogs', 'string'],
            ['catalogs', 'required'],

            ['lead_add', 'string'],
            ['lead_add', 'required'],

            ['lead_view', 'string'],
            ['lead_view', 'required'],

            ['lead_edit', 'string'],
            ['lead_edit', 'required'],

            ['lead_export', 'string'],
            ['lead_export', 'required'],

            ['contact_add', 'string'],
            ['contact_add', 'required'],

            ['contact_view', 'string'],
            ['contact_view', 'required'],

            ['contact_edit', 'string'],
            ['contact_edit', 'required'],

            ['contact_delete', 'string'],
            ['contact_delete', 'required'],

            ['contact_export', 'string'],
            ['contact_export', 'required'],

            ['company_add', 'string'],
            ['company_add', 'required'],

            ['company_view', 'string'],
            ['company_view', 'required'],

            ['company_edit', 'string'],
            ['company_edit', 'required'],

            ['company_delete', 'string'],
            ['company_delete', 'required'],

            ['company_export', 'string'],
            ['company_export', 'required'],

            ['task_edit', 'string'],
            ['task_edit', 'required'],

            ['task_delete', 'string'],
            ['task_delete', 'required'],
        ];

    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userAccountRights');
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('userAccountRights');
    }
}