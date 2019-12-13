<?php

namespace rvkulikov\amo\module\services\account\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\services\customField\sync\CustomFieldSyncer_Interface;
use rvkulikov\amo\module\services\noteType\sync\NoteTypeSyncer_Interface;
use rvkulikov\amo\module\services\pipeline\sync\PipelineSyncer_Interface;
use rvkulikov\amo\module\services\taskType\sync\TaskTypeSyncer_Interface;
use rvkulikov\amo\module\services\user\sync\UserSyncer_Interface;
use Throwable;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;

/**
 *
 */
class AccountSyncer_Impl extends Component implements AccountSyncer_Interface
{
    const EVENT_BEFORE_SYNC = 'beforeSync';
    const EVENT_AFTER_SYNC = 'afterSync';

    /** @var AccountSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;

    /** @var UserSyncer_Interface */
    private $userSyncer;
    /** @var CustomFieldSyncer_Interface */
    private $customFieldSyncer;
    /** @var PipelineSyncer_Interface */
    private $pipelineSyncer;
    /** @var NoteTypeSyncer_Interface */
    private $noteTypeSyncer;
    /** @var TaskTypeSyncer_Interface */
    private $taskTypeSyncer;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        UserSyncer_Interface $userSyncer,
        CustomFieldSyncer_Interface $customFieldSyncer,
        PipelineSyncer_Interface $pipelineSyncer,
        NoteTypeSyncer_Interface $noteTypeSyncer,
        TaskTypeSyncer_Interface $taskTypeSyncer,
        $config = []
    ) {
        parent::__construct($config);
        $this->userSyncer = $userSyncer;
        $this->customFieldSyncer = $customFieldSyncer;
        $this->pipelineSyncer = $pipelineSyncer;
        $this->noteTypeSyncer = $noteTypeSyncer;
        $this->taskTypeSyncer = $taskTypeSyncer;
    }

    /**
     * @param AccountSyncer_Cfg|array $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     * @throws Throwable
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, AccountSyncer_Cfg::class);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        $client = $this->account->client;
        $request = $client->get(['account', 'free_users' => 'Y', 'with' => implode(',', $this->buildWith())]);
        $response = $request->send();
        $data = $response->data;

        $this->trigger(self::EVENT_BEFORE_SYNC, new Event(['data' => ['account' => $data]]));

        $this->syncAccount($data);
        $this->syncPipelines($data);
        $this->syncUsers($data);
        $this->syncCustomFields($data);
        $this->syncNoteTypes($data);
        $this->syncTaskTypes($data);

        $this->trigger(self::EVENT_AFTER_SYNC, new Event(['data' => ['account' => $data]]));
    }

    /**
     * @return string[]
     */
    private function buildWith()
    {
        $with = $this->cfg->withAllowed;
        !empty($this->cfg->withOnly) && $with = array_intersect($with, $this->cfg->withOnly);
        !empty($this->cfg->withExcept) && $with = array_diff($with, $this->cfg->withExcept);

        return $with;
    }

    /**
     * @param mixed $data
     * @throws InvalidModelException
     */
    private function syncAccount($data)
    {
        $this->account->load($data, '');
        if (!$this->account->save()) {
            throw new InvalidModelException($this->account);
        }
    }

    /**
     * @param $data
     * @throws Throwable
     */
    private function syncPipelines($data)
    {
        $this->pipelineSyncer->sync([
            'accountId' => $this->account->id,
            'pipelines' => ArrayHelper::getValue($data, '_embedded.pipelines', []),
        ]);
    }

    /**
     * @param $data
     * @throws InvalidConfigException
     * @throws InvalidModelException
     * @throws Throwable
     */
    private function syncUsers($data)
    {
        $this->userSyncer->sync([
            'accountId' => $this->account->id,
            'groups'    => ArrayHelper::getValue($data, '_embedded.groups', []),
            'users'     => ArrayHelper::getValue($data, '_embedded.users', []),
        ]);
    }

    /**
     * @param $data
     * @throws InvalidConfigException
     * @throws InvalidModelException
     */
    private function syncCustomFields($data)
    {
        $this->customFieldSyncer->sync([
            'accountId'    => $this->account->id,
            'customFields' => ArrayHelper::getValue($data, '_embedded.custom_fields')
        ]);
    }

    /**
     * @param $data
     * @throws InvalidConfigException
     * @throws InvalidModelException
     * @throws \yii\db\Exception
     */
    private function syncNoteTypes($data)
    {
        $this->noteTypeSyncer->sync([
            'accountId' => $this->account->id,
            'noteTypes' => ArrayHelper::getValue($data, '_embedded.note_types')
        ]);
    }

    /**
     * @param $data
     * @throws InvalidConfigException
     * @throws InvalidModelException
     * @throws \yii\db\Exception
     */
    private function syncTaskTypes($data)
    {
        $this->taskTypeSyncer->sync([
            'accountId' => $this->account->id,
            'taskTypes' => ArrayHelper::getValue($data, '_embedded.task_types')
        ]);
    }
}