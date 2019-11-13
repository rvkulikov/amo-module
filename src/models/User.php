<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [bigint]
 * @property string $name [varchar(255)]
 * @property string $last_name [varchar(255)]
 * @property string $login [varchar(255)]
 * @property string $language [varchar(255)]
 * @property string $phone_number [varchar(255)]
 *
 * @property-read UserAccount[] $userAccounts
 */
class User extends ActiveRecord
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
        return '{{%amo__user}}';
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

            ['login', 'string'],
            ['login', 'unique'],
            ['login', 'required'],

            ['name', 'string'],
            ['last_name', 'string'],
            ['language', 'string'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserAccounts()
    {
        return $this->hasMany(UserAccount::class, ['user_id' => 'id'])->inverseOf('user');
    }
}