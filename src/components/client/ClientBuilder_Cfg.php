<?php
namespace rvkulikov\amo\module\components\client;

use yii\base\Model;

/**
 *
 */
class ClientBuilder_Cfg extends Model
{
    public $subdomain;
    public $access_token;
    public $refresh_token;
}