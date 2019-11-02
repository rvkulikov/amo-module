<?php
namespace rvkulikov\amo\module\controllers;

use RuntimeException;
use rvkulikov\amo\module\components\client\ClientBuilder;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Credentials;
use rvkulikov\amo\module\models\Integration;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\rest\Controller;

/**
 *
 */
class OauthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'authMethods' => [
                    QueryParamAuth::class,
                    HttpBearerAuth::class,
                ],
            ],
            'access'        => [
                'class' => AccessControl::class,
                'only'  => ['redirect'],
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['integrate-account'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param string $integration_id
     * @param string $code
     * @param string $referer
     *
     * @return Account
     * @throws Exception
     * @throws InvalidModelException
     */
    public function actionRedirect($integration_id, $code, $referer)
    {
        preg_match('/(?<subdomain>[^\.])\.amocrm\.ru/', $referer, $matches);
        $subdomain = $matches['subdomain'];

        $integration = Integration::findOne(['id' => $integration_id]);
        $credentials = $this->fetchCredentials($code, $subdomain, $integration);

        $client = ClientBuilder::build($credentials);
        $data   = $client->get(['account'])->send()->data;

        $account     = Account::findOne(['subdomain' => $subdomain]) ?? new Account(['subdomain' => $subdomain]);
        $account->id = $data['id'];

        $credentials->account_id        = $account->id;
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
            'baseUrl'   => "https://{$subdomain}.amocrm.ru",
            'transport' => CurlTransport::class,
        ]);

        $request = $client->post(['oauth2/access_token'], [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $integration->redirect_uri,
            'client_secret' => $integration->secret_key,
            'redirect_uri'  => $integration->redirect_uri,
        ]);

        $response = $request->send();
        if (!$response->isOk) {
            throw new RuntimeException("Error during grant flow: {$response->content}");
        }

        $credentials = new Credentials([
            'integration_id' => $integration->id,
            'secret_key'     => $integration->secret_key,
            'redirect_uri'   => $integration->redirect_uri,
            'token_type'     => $response->data['token_type'],
            'access_token'   => $response->data['access_token'],
            'refresh_token'  => $response->data['refresh_token'],
            'expiresIn'      => $response->data['expires_in'],
        ]);

        return $credentials;
    }
}