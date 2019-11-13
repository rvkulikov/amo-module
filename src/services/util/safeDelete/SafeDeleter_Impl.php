<?php

namespace rvkulikov\amo\module\services\util\safeDelete;

use LogicException;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 *
 */
class SafeDeleter_Impl extends Component implements SafeDeleter_Interface
{
    /**
     * @param array|SafeDeleter_Cfg $cfg
     * @return int amount of deleted rows
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function delete($cfg)
    {
        $cfg = ModelHelper::ensure($cfg, SafeDeleter_Cfg::class, true);

        /** @var ActiveRecord $class */
        $class = $cfg->definition;
        $pk = $class::primaryKey();
        $entityPks = ArrayHelper::getColumn($cfg->rows, function ($row) use ($pk) {
            return array_intersect_key($row, array_flip($pk));
        });

        // searching for pks which are not present in current $rows
        $query = $class::find();
        $query->select($pk);
        $query->andWhere(['not in', $pk, $entityPks]);
        $query->andFilterWhere($cfg->where);

        if ($cfg->strategy == SafeDeleter_Cfg::STRATEGY_SOFT) {
            $query->andWhere(['is', 'deleted_at', null]);
        }

        $command = $query->createCommand();
        $obsoletePks = $command->queryAll();
        YII_DEBUG && $sql = $command->rawSql;

        if (empty($obsoletePks)) {
            return 0;
        }

        if ($cfg->strategy == SafeDeleter_Cfg::STRATEGY_SOFT) {
            return $class::updateAll(
                ['deleted_at' => new Expression('NOW()')],
                ['in', $pk, $obsoletePks]
            );
        }

        if ($cfg->strategy == SafeDeleter_Cfg::STRATEGY_HARD) {
            return $class::deleteAll(['in', $pk, $obsoletePks]);
        }

        throw new LogicException("Unknown strategy {$cfg->strategy}");
    }
}