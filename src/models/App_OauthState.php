<?php

namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id [bigint]
 * @property int $user_id [bigint]
 * @property string $integration_id [uuid]
 * @property string $token [varchar(64)]
 * @property int $expires_at [timestamp]
 *
 * @property-read App_User $user
 * @property-read Integration $integration
 * @property-read IdentityInterface $identity
 */
class App_OauthState extends ActiveRecord
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app__oauth_state';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['integration_id', 'exist', 'targetRelation' => 'integration'],
            ['integration_id', 'required'],

            ['user_id', 'exist', 'targetRelation' => 'identity'],
            ['user_id', 'required'],

            ['token', 'string', 'min' => 64],
            ['token', 'required'],
            ['token', 'unique'],

            ['expires_at', 'datetime', 'format' => 'php:' . DATE_RFC3339],
            ['expires_at', 'required'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getIdentity()
    {
        return $this->getUser()->andOnCondition(['status' => App_User::STATUS_ACTIVE]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(App_User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getIntegration()
    {
        return $this->hasOne(Integration::class, ['id' => 'integration_id']);
    }
}