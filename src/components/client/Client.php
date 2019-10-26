<?php
namespace rvkulikov\amo\module\components\client;

class Client extends \yii\httpclient\Client
{
    /** @var string */
    public $accessToken;
    /** @var string */
    public $refreshToken;
}