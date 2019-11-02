<?php
namespace rvkulikov\amo\module\commands;

use rvkulikov\amo\module\models\App_User;
use rvkulikov\amo\module\services\init\ModuleInitializer_Cfg;
use rvkulikov\amo\module\services\init\ModuleInitializer_Interface;

/**
 *
 */
class InitController extends BaseCliController
{
    /**
     * @var string Integration redirect uri
     */
    public $redirectUri;
    /**
     * @var string Integration secret key from "keys" tab
     */
    public $secretKey;
    /**
     * @var string Integration id from "keys" tab
     */
    public $integrationId;

    /** @var ModuleInitializer_Interface */
    private $moduleInitializer;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, ModuleInitializer_Interface $moduleInitializer, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->moduleInitializer = $moduleInitializer;
    }

    /**
     * {@inheritDoc}
     */
    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return array_merge(parent::options($actionID), [
            'redirectUri',
            'secretKey',
            'integrationId',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'ru' => 'redirectUri',
            'sc' => 'secretKey',
            'ii' => 'integrationId',
            '',
        ]);
    }

    public function actionIndex()
    {
        $cfg = new ModuleInitializer_Cfg([
            'username'               => $this->prompt('Enter admin username', ['default' => 'admin']),
            'userEmail'              => $this->prompt('Enter admin email', ['required' => true]),
            'userStatus'             => $this->select("Enter admin status", ['active' => App_User::STATUS_ACTIVE, 'inactive' => App_User::STATUS_INACTIVE]),
            'integrationId'          => $this->prompt('Enter integration id', ['required' => true]),
            'integrationSecretKey'   => $this->prompt('Enter integration secret key', ['required' => true]),
            'integrationRedirectUri' => $this->prompt('Enter integration redirect uri', ['required' => true]),
        ]);

        $res = $this->moduleInitializer->initialize($cfg);
    }
}