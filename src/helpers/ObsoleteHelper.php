<?php

namespace rvkulikov\amo\module\helpers;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 *
 */
class ObsoleteHelper
{
    /**
     * @param string $class
     * @param mixed[] $rows
     * @param mixed $where
     *
     * @return int amount of obsolete rows
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function markObsolete(string $class, $rows, $where = null)
    {
        /** @var ActiveRecord $class */
        if (!is_a($class, ActiveRecord::class, true)) {
            throw new InvalidConfigException("class parameter must be an instance of ActiveRecord");
        }

        $pk = $class::primaryKey();
        $entityPks = ArrayHelper::getColumn($rows, function ($row) use ($pk) {
            return array_intersect_key($row, array_flip($pk));
        });

        // searching for pks which are not present in current $rows
        $query = $class::find();
        $query->select($pk);
        $query->andFilterWhere($where);
        $query->andWhere([
            'and',
            ['not in', $pk, $entityPks],
            ['is', 'deleted_at', null]
        ]);

        $command = $query->createCommand();
        $obsoletePks = $command->queryAll();
        YII_DEBUG && $sql = $command->rawSql;

        if (!empty($obsoletePks)) {
            return $class::updateAll(
                ['deleted_at' => new Expression('NOW()')],
                ['in', $pk, $obsoletePks]
            );
        } else {
            return 0;
        }
    }
}