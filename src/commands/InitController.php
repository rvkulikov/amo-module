<?php

namespace rvkulikov\amo\module\commands;

use rvkulikov\amo\module\services\init\ModuleInitializer_Cfg;
use rvkulikov\amo\module\services\init\ModuleInitializer_Interface;
use yii\helpers\Console;

/**
 *
 */
class InitController extends BaseCliController
{
    /**
     * @var string Integration secret key from "keys" tab
     */
    public $secretKey;
    /**
     * @var string Integration redirect uri
     */
    public $redirectUri;
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

    public function actionIndex()
    {
        $cfg = new ModuleInitializer_Cfg([
            'username'               => $this->prompt('Enter admin username', ['default' => 'admin']),
            'userEmail'              => $this->prompt('Enter admin email', ['default' => 'admin@amo.module.loc']),
            'integrationRedirectUri' => $this->prompt('Enter integration redirect uri', ['default' => $this->redirectUri]),
            'integrationSecretKey'   => $this->prompt('Enter integration secret key', ['default' => $this->secretKey]),
            'integrationId'          => $this->prompt('Enter integration id', ['default' => $this->integrationId]),
        ]);

        $res = $this->moduleInitializer->initialize($cfg);

        Console::output($res->oauthGrantUrl);
    }
}