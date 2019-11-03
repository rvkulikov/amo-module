<?php /** @noinspection PhpUndefinedVariableInspection */

use rvkulikov\amo\module\models\App_User as UserModel;
use yii\rbac\DbManager;
use yii\web\User;

return [
    'id' => 'amo-module-tests',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'language' => 'ru-RU',
    'aliases' => [
        '@app' => dirname(__DIR__) . '/tests',
    ],
    'components' => [
        'user' => [
            'class' => User::class,
            'identityClass' => UserModel::class,
        ],
        'authManager' => [
            'class' => DbManager::class,
            'db' => $params['rvkulikov.amo.db.name'],
            'cacheKey' => 'rvkulikov-amo-rbac',
            'itemTable' => 'rbac__auth_item',
            'ruleTable' => 'rbac__auth_rule',
            'itemChildTable' => 'rbac__auth_item_child',
            'assignmentTable' => 'rbac__auth_assignment',
        ],
        $params['rvkulikov.amo.db.name'] => [
            'class' => '\yii\db\Connection',
            'dsn' => $params['rvkulikov.amo.tests.db.dsn'],
            'username' => $params['rvkulikov.amo.tests.db.username'],
            'password' => $params['rvkulikov.amo.tests.db.password'],
        ],
    ],
    'params' => $params,
];