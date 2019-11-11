<?php

namespace rvkulikov\amo\module\services\pipeline\sync;

use rvkulikov\amo\module\models\Account;
use yii\base\Model;

/**
 *
 */
class PipelineSyncer_Cfg extends Model
{
    /** @var int */
    public $accountId;
    /** @var mixed[] */
    public $pipelines;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['accountId', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['accountId', 'required'],

            ['pipelines', 'safe'],
            ['pipelines', 'required'],
        ];
    }
}