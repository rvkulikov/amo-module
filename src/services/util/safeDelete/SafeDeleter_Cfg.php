<?php

namespace rvkulikov\amo\module\services\util\safeDelete;

use yii\base\Model;

/**
 *
 */
class SafeDeleter_Cfg extends Model
{
    const STRATEGY_HARD = 'hard';
    const STRATEGY_SOFT = 'soft';

    public $definition;
    public $rows;
    public $where;
    public $strategy;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['definition', 'safe'],
            ['rows', 'safe'],
            ['where', 'safe'],
            ['strategy', 'in', 'range' => [self::STRATEGY_HARD, self::STRATEGY_SOFT]],
        ];
    }
}