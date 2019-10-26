<?php
namespace rvkulikov\amo\module\components\client;

use Closure;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use yii\httpclient\CurlTransport;

/**
 *
 */
class ClientBuilder
{
    /**
     * @param ClientBuilder_Cfg|Account|array $cfg
     *
     * @return Client
     */
    public static function build($cfg)
    {
        return self::lazy($cfg)();
    }

    /**
     * @param ClientBuilder_Cfg|Account|array $cfg
     *
     * @return Closure
     */
    public static function lazy($cfg)
    {
        return function () use ($cfg) {
            if ($cfg instanceof Account) {
                $cfg = new ClientBuilder_Cfg([
                    'subdomain'     => $cfg->subdomain,
                    'access_token'  => $cfg->access_token,
                    'refresh_token' => $cfg->refresh_token,
                ]);
            }

            $cfg = ModelHelper::ensure($cfg, ClientBuilder_Cfg::class);
            if (!$cfg->validate()) {
                throw new InvalidModelException($cfg);
            }

            return new Client([
                'baseUrl'       => "https://{$cfg->subdomain}.amocrm.ru/api/v2",
                'accessToken'   => $cfg->access_token,
                'refreshToken'  => $cfg->refresh_token,
                'transport'     => CurlTransport::class,
                'requestConfig' => ['class' => Request::class],
                // todo on before- afterRequest, log behavior
            ]);
        };
    }
}