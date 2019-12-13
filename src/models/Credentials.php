<?php

namespace rvkulikov\amo\module\models;

use DateTime;
use Exception;
use rvkulikov\amo\module\components\client\Client;
use rvkulikov\amo\module\components\client\ClientBuilder;
use rvkulikov\amo\module\models\query\CredentialsQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id                [bigint]
 * @property int $account_id        [bigint]
 * @property int $account_subdomain [varchar(255)]
 * @property string $integration_id    [varchar(255)]
 * @property string $secret_key        [varchar(255)]
 * @property string $redirect_uri      [varchar(255)]
 * @property string $token_type        [varchar(255)]
 * @property string $expires_in        [integer]
 * @property int $expires_at        [timestamp]
 * @property int $deleted_at        [timestamp]
 * @property string $access_token
 * @property string $refresh_token     [varchar(255)]
 *
 * @property-write int $expiresIn
 * @property-read Client $client
 * @property-read Account $account
 */
class Credentials extends ActiveRecord
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
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return '{{%amo__credentials}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['account_id', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['account_id', 'required'],

            ['account_subdomain', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'subdomain'],
            ['account_subdomain', 'required'],

            ['integration_id', 'string'],
            ['integration_id', 'required'],

            ['secret_key', 'string'],
            ['secret_key', 'required'],

            ['redirect_uri', 'url'],
            ['redirect_uri', 'required'],

            ['token_type', 'string'],
            ['token_type', 'required'],

            ['expires_in', 'integer'],

            ['expires_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['deleted_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['access_token', 'string'],

            ['refresh_token', 'string'],
            ['refresh_token', 'required'],
        ];
    }

    /**
     * @param int $value seconds
     *
     * @return $this
     * @throws Exception
     */
    public function setExpiresIn(int $value)
    {
        $this->expires_in = $value;
        $this->expires_at = (new DateTime('now'))->modify("+{$value} seconds")->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('credentials');
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return ClientBuilder::build($this);
    }


    /**
     * {@inheritdoc}
     * @return CredentialsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CredentialsQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     * @return null|Credentials|ActiveRecord
     */
    public static function findOne($condition)
    {
        return parent::findOne($condition);
    }

    /**
     * {@inheritdoc}
     * @return Credentials[]|ActiveRecord[]
     */
    public static function findAll($condition)
    {
        return parent::findAll($condition);
    }
}