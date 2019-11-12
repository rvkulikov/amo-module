<?php
namespace rvkulikov\amo\module\models;

use rvkulikov\amo\module\components\client\Client;
use rvkulikov\amo\module\components\client\ClientBuilder;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [bigint]
 * @property string $name [varchar(255)]
 * @property string $subdomain [varchar(255)]
 * @property string $currency [varchar(255)]
 * @property string $timezone [varchar(255)]
 * @property string $timezone_offset [varchar(255)]
 * @property string $language [varchar(255)]
 * @property string $date_pattern [jsonb]
 * @property int $current_user [bigint]
 *
 * @property-read Client $client
 * @property-read Credentials $credentials
 * @property-read Pipeline[] $pipelines
 * @property-read Status[] $statuses
 * @property-read Group[] $groups
 */
class Account extends ActiveRecord
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
        return '{{%amo__account}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['id', 'unique'],
            ['id', 'required'],

            ['subdomain', 'string'],
            ['subdomain', 'unique'],
            ['subdomain', 'required'],

            ['name', 'string'],

            ['currency', 'string'],

            ['timezone', 'string'],

            ['timezone_offset', 'string'],

            ['language', 'string'],

            ['date_pattern', 'safe'],

            ['current_user', 'integer']
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCredentials()
    {
        return $this->hasOne(Credentials::class, ['account_id' => 'id'])->inverseOf('account');
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return ClientBuilder::build($this);
    }

    /**
     * @return ActiveQuery
     */
    public function getPipelines()
    {
        return $this->hasMany(Pipeline::class, ['account_id' => 'id'])->inverseOf('account');
    }

    /**
     * @return ActiveQuery
     */
    public function getStatuses()
    {
        return $this->hasMany(Status::class, ['account_id' => 'id'])->inverseOf('account');
    }

    /**
     * @return ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::class, ['account_id' => 'id'])->inverseOf('account');
    }
}