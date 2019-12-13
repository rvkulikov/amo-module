<?php

namespace rvkulikov\amo\module\services\noteType\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\NoteType;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 *
 */
class NoteTypeSyncer_Impl extends Component implements NoteTypeSyncer_Interface
{
    /** @var NoteTypeSyncer_Cfg */
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
     * @param array|NoteTypeSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, NoteTypeSyncer_Cfg::class);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        $rows = array_values(array_map(function ($noteType) {
            return [
                'account_id'  => $this->account->id,
                'id'          => $noteType['id'],
                'code'        => $noteType['code'],
                'is_editable' => $noteType['is_editable'],
            ];
        }, $this->cfg->noteTypes));

        if (!empty($rows)) {
            $cols = array_keys($rows[0]);
            $cmd = NoteType::getDb()->createCommand()->batchInsert(NoteType::tableName(), $cols, $rows);
            $sql = <<<SQL
{$cmd->rawSql} ON CONFLICT (account_id, id) DO UPDATE SET
    [[code]] = EXCLUDED.code,
    [[is_editable]] = EXCLUDED.is_editable
SQL;

            $cmd = NoteType::getDb()->createCommand($sql);
            $cmd->execute();
        }

        $this->safeDeleter->delete([
            'definition' => NoteType::class,
            'strategy'   => 'soft',
            'rows'       => $rows,
            'where'      => ['account_id' => $this->account->id]
        ]);
    }
}