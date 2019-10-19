<?php
namespace rvkulikov\amo\module\components\client;

use rvkulikov\amo\module\models\Account;

class ClientBuilder
{
    public static function build($cfg)
    {
        return self::lazy($cfg)();
    }

    public static function lazy($cfg)
    {
        return function () use ($cfg) {
            if ($cfg instanceof Account) {

            }

            return new Client();
        };
    }
}