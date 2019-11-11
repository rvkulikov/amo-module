<?php

namespace rvkulikov\amo\module\controllers;

use RuntimeException;
use rvkulikov\amo\module\components\auth\OauthStateAccess;
use rvkulikov\amo\module\components\client\ClientBuilder;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\App_OauthState;
use rvkulikov\amo\module\models\Credentials;
use rvkulikov\amo\module\models\Integration;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

/**
 *
 */
class Oauth2Controller extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    OauthStateAccess::class,
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['redirect'],
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['perm:amo:oauth_redirect'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param $state
     * @param string $code
     * @param $referer
     * @return Account
     * @throws Exception
     * @throws InvalidModelException
     * @throws BadRequestHttpException
     */
    public function actionRedirect($state, $code, $referer)
    {
        $state = $this->findState($state);

        preg_match('/(?<subdomain>[^.]+)\.amocrm\.ru/', $referer, $matches);
        $subdomain = $matches['subdomain'];

        $integration = $state->integration;
        $credentials = $this->fetchCredentials($code, $subdomain, $integration);
        $credentials->account_subdomain = $subdomain;

        $client = ClientBuilder::build($credentials);
        $data = $client->get(['account'])->send()->data;

        $account = Account::findOne(['subdomain' => $subdomain]);
        $account = $account ?? new Account(['subdomain' => $subdomain]);
        $account->id = $data['id'];

        $credentials->account_id = $account->id;
        $credentials->account_subdomain = $account->subdomain;

        if (!$account->save()) {
            throw new InvalidModelException($account);
        }

        if (!$credentials->save()) {
            throw new InvalidModelException($credentials);
        }

        return $account;
    }

    /**
     * @param $code
     * @param $subdomain
     * @param $integration
     *
     * @return Credentials
     * @throws Exception
     */
    private function fetchCredentials($code, $subdomain, Integration $integration)
    {
        $client = new Client([
            'baseUrl' => "https://{$subdomain}.amocrm.ru",
            'transport' => CurlTransport::class,
        ]);

        $request = $client->post(['oauth2/access_token'], [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $integration->id,
            'client_secret' => $integration->secret_key,
            'redirect_uri' => $integration->redirect_uri,
        ]);

        $response = $request->send();
        if (!$response->isOk) {
            throw new RuntimeException("Error during grant flow: {$response->content}");
        }

        $credentials = new Credentials([
            'integration_id' => $integration->id,
            'secret_key' => $integration->secret_key,
            'redirect_uri' => $integration->redirect_uri,
            'token_type' => $response->data['token_type'],
            'access_token' => $response->data['access_token'],
            'refresh_token' => $response->data['refresh_token'],
            'expiresIn' => $response->data['expires_in'],
        ]);

        return $credentials;
    }

    /**
     * @param $token
     * @return App_OauthState|null
     * @throws BadRequestHttpException
     */
    private function findState($token)
    {
        if (!empty($state = App_OauthState::findOne(['token' => $token]))) {
            return $state;
        } else {
            throw new BadRequestHttpException("Invalid state");
        }
    }
}