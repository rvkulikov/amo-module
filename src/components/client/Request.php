<?php
namespace rvkulikov\amo\module\components\client;

use RuntimeException;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\Credentials;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Response;

/**
 *
 */
class Request extends \yii\httpclient\Request
{
    /**
     * @var Client owner client instance.
     */
    public $client;

    /**
     * @return Response
     * @throws Exception
     * @throws InvalidModelException
     */
    public function send()
    {
        $this->addHeaders(['Authorization' => "Bearer {$this->client->accessToken}"]);
        $response = parent::send();

        if (!$response->isOk && $response->statusCode == 401) {
            $credentials = $this->refreshCredentials();
            $this->client->accessToken = $credentials->access_token;

            $this->headers->remove('Authorization');
            $this->addHeaders(['Authorization' => "Bearer {$this->client->accessToken}"]);
            $response = parent::send();
        }

        // todo refresh token expires in 3 months
        return $response;
    }

    /**
     * @return Credentials|null
     * @throws Exception
     * @throws InvalidModelException
     */
    private function refreshCredentials()
    {
        if (empty($credentials = Credentials::findOne(['refresh_token' => $this->client->refreshToken]))) {
            throw new RuntimeException("Unable to find credentials for given refresh_token");
        }

        $client = new \yii\httpclient\Client([
            'baseUrl' => "https://{$this->client->subdomain}.amocrm.ru",
            'transport' => CurlTransport::class
        ]);

        $request = $client->post(['oauth2/access_token'], [
            'client_id' => $credentials->integration_id,
            'client_secret' => $credentials->secret_key,
            'grant_type' => 'refresh_token',
            'refresh_token' => $credentials->refresh_token,
            'redirect_uri' => $credentials->redirect_uri,
        ]);

        $response = $request->send();
        if (!$response->isOk) {
            // todo proper exception
            throw new RuntimeException("Error during access_token refreshing: {$response->content}");
        }

        $credentials->token_type = $response->data['token_type'];
        $credentials->access_token = $response->data['access_token'];
        $credentials->expiresIn = $response->data['expires_in'];

        if (!$credentials->save()) {
            throw new InvalidModelException($credentials);
        }

        return $credentials;
    }
}