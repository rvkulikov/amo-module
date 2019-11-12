<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [bigint]
 * @property int $account_id [bigint]
 * @property string $name [varchar(255)]
 * @property string $sort [integer]
 * @property bool $is_main [boolean]
 * @property int $deleted_at [timestamp]
 *
 * @property-read Account $account
 * @property-read Status[] $statuses
 */
class Pipeline extends ActiveRecord
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
        return '{{%amo__pipeline}}';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey()
    {
        // todo composite [account_id, id]?
        return parent::primaryKey();
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['account_id', 'exist', 'targetRelation' => 'account'],
            ['account_id', 'required'],

            ['id', 'integer'],
            ['id', 'unique'],
            ['id', 'required'],

            ['name', 'string'],
            ['sort', 'integer'],
            ['is_main', 'boolean'],
            ['deleted_at', 'safe'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('pipelines');
    }

    /**
     * @return ActiveQuery
     */
    public function getStatuses()
    {
        return $this->hasMany(Status::class, ['pipeline_id' => 'id'])->inverseOf('pipeline');
    }
}