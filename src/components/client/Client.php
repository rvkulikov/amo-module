<?php

namespace rvkulikov\amo\module\components\client;

/**
 *
 */
class Client extends \yii\httpclient\Client
{
    /** @var string */
    public $subdomain;
    /** @var string */
    public $accessToken;
    /** @var string */
    public $refreshToken;

    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseUrl = "https://{$this->subdomain}.amocrm.ru/api/v2";
    }
}