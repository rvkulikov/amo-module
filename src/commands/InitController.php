<?php
namespace rvkulikov\amo\module\commands;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\Account;
use yii\console\Controller;
use yii\helpers\Console;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;

/**
 *
 */
class InitController extends Controller
{
    /**
     * @var string Account id
     */
    public $accountId;
    /**
     * @var string Account subdomain
     */
    public $accountSubdomain;
    /**
     * @var string Integration redirect uri
     */
    public $redirectUri;
    /**
     * @var string Integration secret key from "keys" tab
     */
    public $secretKey;
    /**
     * @var string Integration id from "keys" tab
     */
    public $integrationId;
    /**
     * @var string authorization code from "keys" tab (valid for 20 minutes only)
     */
    public $authorizationCode;

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return array_merge(parent::options($actionID), [
            'accountId',
            'accountSubdomain',
            'redirectUri',
            'secretKey',
            'integrationId',
            'authorizationCode',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'ai' => 'accountId',
            'as' => 'accountSubdomain',
            'ru' => 'redirectUri',
            'sc' => 'secretKey',
            'ii' => 'integrationId',
            'ac' => 'authorizationCode',
        ]);
    }

    /**
     * @throws InvalidModelException
     * @throws Exception
     */
    public function actionIndex()
    {
        $id            = $this->accountId ?? $this->prompt('Enter account id', ['required' => true]);
        $subdomain     = $this->accountSubdomain ?? $this->prompt('Enter account subdomain', ['required' => true]);
        $redirectUri   = $this->redirectUri ?? $this->prompt('Enter redirect uri', ['required' => true]);
        $secretKey     = $this->secretKey ?? $this->prompt('Enter secret key', ['required' => true]);
        $integrationId = $this->integrationId ?? $this->prompt('Enter integration id', ['required' => true]);
        $authCode      = $this->authorizationCode ?? $this->prompt('Enter authorization code', ['required' => true]);

        $client = new Client([
            'baseUrl'   => "https://{$subdomain}.amocrm.ru",
            'transport' => CurlTransport::class,
        ]);

        $request = $client->post(['oauth2/access_token'], [
            'client_id'     => $integrationId,
            'client_secret' => $secretKey,
            'grant_type'    => 'authorization_code',
            'code'          => $authCode,
            'redirect_uri'  => $redirectUri,
        ]);

        $response = $request->send();
        if (!$response->isOk) {
            Console::error("Error during grant flow");
            Console::error($response->content);
            return -1;
        }

        $account = Account::findOne(['id' => $id]) ?? new Account(['id' => $id]);
        if (!$account->isNewRecord && !$this->confirm("Override account settings?")) {
            return 0;
        }

        $account->load([
            'subdomain'      => $subdomain,
            'integration_id' => $integrationId,
            'secret_key'     => $secretKey,
            'redirect_uri'   => $redirectUri,
            'access_token'   => $response->data['access_token'],
            'refresh_token'  => $response->data['refresh_token'],
        ], '');

        if (!$account->save()) {
            throw new InvalidModelException($account);
        }

        Console::output("Account [id={$account->id}] was saved");
        return 0;
    }
}