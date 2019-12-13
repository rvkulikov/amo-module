<?php

namespace rvkulikov\amo\module\services\taskType\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\TaskType;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
class TaskTypeSyncer_Impl extends Component implements TaskTypeSyncer_Interface
{
    /** @var TaskTypeSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;
    /** @var SafeDeleter_Interface */
    private $safeDeleter;

    /**
     * {@inheritDoc}
     */
    public function __construct(SafeDeleter_Interface $safeDeleter, $config = [])
    {
        parent::__construct($config);
        $this->safeDeleter = $safeDeleter;
    }

    /**
     * @param array|TaskTypeSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, TaskTypeSyncer_Cfg::class);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        $rows = array_values(array_map(function ($taskType) {
            return [
                'account_id' => $this->account->id,
                'id'         => $taskType['id'],
                'name'       => $taskType['name'],
                'color'      => $taskType['color'],
                'icon_id'    => $taskType['icon_id'],
            ];
        }, $this->cfg->taskTypes));

        if (!empty($rows)) {
            $cols = array_keys($rows[0]);
            $cmd = TaskType::getDb()->createCommand()->batchInsert(TaskType::tableName(), $cols, $rows);
            $sql = <<<SQL
{$cmd->rawSql} ON CONFLICT (account_id, id) DO UPDATE SET
    [[name]] = EXCLUDED.name,
    [[color]] = EXCLUDED.color,
    [[icon_id]] = EXCLUDED.icon_id
SQL;

            $cmd = TaskType::getDb()->createCommand($sql);
            $cmd->execute();
        }

        $this->safeDeleter->delete([
            'definition' => TaskType::class,
            'strategy'   => 'soft',
            'rows'       => $rows,
            'where'      => ['account_id' => $this->account->id]
        ]);
    }
}