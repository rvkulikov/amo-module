<?php /** @noinspection PhpUndefinedVariableInspection */

use rvkulikov\amo\module\models\User as UserModel;
use yii\web\User;

return [
    'id'         => 'amo-module-tests',
    'basePath'   => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'   => 'ru-RU',
    'aliases'    => [
        '@app' => dirname(__DIR__) . '/tests',
    ],
    'container'  => [
        'definitions' => [
            'yii\test\InitDbFixture' => [
                'class' => 'yii\test\InitDbFixture',
                'db'    => $params['rvkulikov.amo.db.name'],
            ],
        ],
    ],
    'components' => [
        'user'                           => [
            'class'         => User::class,
            'identityClass' => UserModel::class,
        ],
        $params['rvkulikov.amo.db.name'] => [
            'class'    => '\yii\db\Connection',
            'dsn'      => $params['rvkulikov.amo.tests.db.dsn'],
            'username' => $params['rvkulikov.amo.tests.db.username'],
            'password' => $params['rvkulikov.amo.tests.db.password'],
        ],
    ],
    'params'     => $params,
];