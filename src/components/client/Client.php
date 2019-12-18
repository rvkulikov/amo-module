<?php

namespace rvkulikov\amo\module\components\client;

use Yii;
use yii\helpers\ArrayHelper;

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
        $this->baseUrl = Yii::$app->params['rvkulikov.amo.httpclient.proxy_url'];
        $this->requestConfig = ArrayHelper::merge($this->requestConfig, [
            'headers' => [
                'subdomain' => $this->subdomain
            ]
        ]);
    }
}