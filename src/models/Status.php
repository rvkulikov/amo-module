<?php
namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $account_id [bigint]
 * @property int $pipeline_id [bigint]
 * @property int $id [bigint]
 * @property string $name [varchar(255)]
 * @property string $color [varchar(255)]
 * @property string $sort [integer]
 * @property bool $is_editable [boolean]
 * @property int $deleted_at [timestamp]
 *
 * @property-read Account $account
 * @property-read Pipeline $pipeline
 */
class Status extends ActiveRecord
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
        return '{{%amo__status}}';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey()
    {
        return ['pipeline_id', 'id'];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['account_id', 'exist', 'targetRelation' => 'account'],
            ['account_id', 'required'],

            ['pipeline_id', 'exist', 'targetRelation' => 'pipeline'],
            ['pipeline_id', 'required'],

            ['id', 'integer'],
            ['id', 'required'],

            ['name', 'string'],
            ['color', 'string'],
            ['sort', 'integer'],
            ['is_editable', 'boolean'],
            ['deleted_at', 'safe']
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('statuses');
    }

    /**
     * @return ActiveQuery
     */
    public function getPipeline()
    {
        return $this->hasOne(Pipeline::class, ['id' => 'pipeline_id'])->inverseOf('statuses');
    }
}