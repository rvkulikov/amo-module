<?php

namespace rvkulikov\amo\module\services\pipeline\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 *
 */
class PipelineSyncer_Impl extends Component implements PipelineSyncer_Interface
{
    /** @var PipelineSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;

    /**
     * @param array|PipelineSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, PipelineSyncer_Cfg::class, true);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);
    }
}