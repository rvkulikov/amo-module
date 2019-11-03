<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int    $id           [bigint]
 * @property string $secret_key   [varchar(255)]
 * @property string $redirect_uri [varchar(255)]
 */
class Integration extends ActiveRecord
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
        return '{{%amo__integration}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['id', 'string'],
            ['id', 'required'],

            ['secret_key', 'string'],
            ['secret_key', 'required'],

            ['redirect_uri', 'url'],
            ['redirect_uri', 'required'],
        ];
    }
}