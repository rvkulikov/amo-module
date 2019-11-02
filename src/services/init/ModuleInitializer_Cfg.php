<?php
namespace rvkulikov\amo\module\services\init;

use yii\base\Model;

/**
 *
 */
class ModuleInitializer_Cfg extends Model
{
    public $username;
    public $userEmail;
    public $userStatus;

    public $integrationId;
    public $integrationSecretKey;
    public $integrationRedirectUri;
}