<?php

namespace rvkulikov\amo\module\rbac;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\rbac\DbManager;

/**
 *
 */
class Migration extends \yii2mod\rbac\migrations\Migration
{
    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->authManager = Instance::ensure(Yii::$app->params['rvkulikov.amo.auth_manager.name'], DbManager::class);
    }

    public function init()
    {

    }
}