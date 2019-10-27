<?php
namespace rvkulikov\amo\module\components\client;

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
     */
    public function send()
    {
        $this->addHeaders(['Authorization' => "Bearer {$this->client->accessToken}"]);
        return parent::send();
    }
}