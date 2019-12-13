<?php

namespace rvkulikov\amo\module\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int $account_id [bigint]
 * @property int $id [bigint]
 * @property string $entity [varchar(255)]
 * @property int $catalog_id [bigint]
 * @property string $name [varchar(255)]
 * @property int $field_type [bigint]
 * @property int $sort [bigint]
 * @property string $code [varchar(255)]
 * @property bool $is_multiple [boolean]
 * @property bool $is_system [boolean]
 * @property bool $is_editable [boolean]
 * @property bool $is_required [boolean]
 * @property bool $is_deletable [boolean]
 * @property bool $is_visible [boolean]
 * @property string $params [jsonb]
 * @property string $enums [jsonb]
 * @property string $values_tree [jsonb]
 * @property int $deleted_at [timestamp]
 */
class CustomField extends ActiveRecord
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
        return '{{%amo__custom_field}}';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey()
    {
        return ['entity', 'id'];
    }
}