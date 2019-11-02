<?php
namespace rvkulikov\amo\module\services\init;

use rvkulikov\amo\module\models\App_User;
use rvkulikov\amo\module\models\Integration;
use yii\base\BaseObject;
use yii\rbac\Role;

/**
 *
 */
class ModuleInitializer_Res extends BaseObject
{
    /** @var App_User */
    public $user;
    /** @var string */
    public $password;
    /** @var string */
    public $authKey;
    /** @var Role */
    public $role;

    /** @var Integration */
    public $integration;

    /** @var string */
    public $oauthGrantUrl;
}