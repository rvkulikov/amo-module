<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int              $id        [bigint]
 * @property string           $subdomain [varchar(255)]
 *
 * @property-read Credentials $credentials
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCredentials()
    {
        return $this->hasOne(Credentials::class, ['account_id' => 'id'])->inverseOf('account');
    }
}