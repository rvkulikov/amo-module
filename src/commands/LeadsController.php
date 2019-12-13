<?php

namespace rvkulikov\amo\module\commands;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\services\account\sync\AccountSyncer_Interface;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;

class LeadsController extends Controller
{

    /** @var AccountSyncer_Interface */
    private $accountSyncer;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, AccountSyncer_Interface $accountSyncer, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->accountSyncer = $accountSyncer;
    }

    /**
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSyncAll()
    {
        $ids = Account::find()->select(['id'])->column();
        foreach ($ids as $id) {
            $this->actionSync($id);
        }
    }

    /**
     * @param int $id
     *
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionSync(int $id)
    {
        if (!$this->confirm("Are you sure you want to sync amo account [{$id}]")) {
            return;
        }

        $account = Account::findOne(['id' => $id]);

        $client = $account->client;
        $since = (new \DateTime('now', new \DateTimeZone('UTC')))->modify('-1 month')->format('D, d M Y H:i:s T');
        $request = $client->get(['leads']);
        $response = $request->send();
        $data = $response->data['_embedded']['items'];
        $index = ArrayHelper::index($data, 'id');

        // todo
        // 1) ensure order by id asc
        // 2)

    }
}