<?php

namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int $account_id [bigint]
 * @property int $id [bigint]
 * @property string $code [varchar(255)]
 * @property bool $is_editable [boolean]
 * @property int $deleted_at [timestamp]
 */
class NoteType extends ActiveRecord
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
        return '{{%amo__note_type}}';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey()
    {
        return ['account_id', 'id'];
    }
}