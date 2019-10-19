<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int    $id                 [bigint]
 * @property string $integration_id     [varchar(255)]
 * @property string $secret_key         [varchar(255)]
 * @property string $authorization_code [varchar(255)]
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
        return '{{%account}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['integration_id', 'safe'],
            ['secret_key', 'safe'],
            ['authorization_code', 'safe'],
            ['access_token', 'safe'],
            ['refresh_token', 'safe'],
        ];
    }
}