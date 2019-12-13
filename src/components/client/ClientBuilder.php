<?php

namespace rvkulikov\amo\module\components\client;

use Closure;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Credentials;
use yii\httpclient\CurlTransport;

/**
 *
 */
class ClientBuilder
{
    /**
     * @param ClientBuilder_Cfg|Credentials|Account|array $cfg
     *
     * @return Client
     */
    public static function build($cfg)
    {
        return self::lazy($cfg)();
    }

    /**
     * @param ClientBuilder_Cfg|Credentials|Account|array $cfg
     *
     * @return Closure
     */
    public static function lazy($cfg)
    {
        return function () use ($cfg) {
            if ($cfg instanceof Credentials) {
                $cfg = new ClientBuilder_Cfg([
                    'subdomain' => $cfg->account_subdomain,
                    'access_token' => $cfg->access_token,
                    'refresh_token' => $cfg->refresh_token,
                ]);
            }

            if ($cfg instanceof Account) {
                $cfg = new ClientBuilder_Cfg([
                    'subdomain' => $cfg->credentials->account_subdomain,
                    'access_token' => $cfg->credentials->access_token,
                    'refresh_token' => $cfg->credentials->refresh_token,
                ]);
            }

            /** @var ClientBuilder_Cfg $cfg */
            $cfg = ModelHelper::ensure($cfg, ClientBuilder_Cfg::class);

            return new Client([
                'subdomain' => $cfg->subdomain,
                'accessToken' => $cfg->access_token,
                'refreshToken' => $cfg->refresh_token,
                'transport' => CurlTransport::class,
                'requestConfig' => ['class' => Request::class],
                // todo on before- afterRequest, log behavior
            ]);
        };
    }
}