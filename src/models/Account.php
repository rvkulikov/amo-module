<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int    $id                 [bigint]
 * @property string $subdomain          [varchar(255)]
 * @property string $integration_id     [varchar(255)]
 * @property string $secret_key         [varchar(255)]
 * @property string $redirect_uri       [varchar(255)]
 * @property string $access_token       [varchar(255)]
 * @property string $refresh_token      [varchar(255)]
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
            ['subdomain', 'safe'],
            ['integration_id', 'safe'],
            ['secret_key', 'safe'],
            ['authorization_code', 'safe'],
            ['redirect_uri', 'safe'],
            ['access_token', 'safe'],
            ['refresh_token', 'safe'],
        ];
    }
}