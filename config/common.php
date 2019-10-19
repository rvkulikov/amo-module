<?php /** @noinspection PhpUndefinedVariableInspection, PhpUnusedAliasInspection */

return [
    'aliases'    => [
        '@rvkulikov/amo/module'       => dirname(__DIR__) . '/src',
        '@rvkulikov/amo/module/tests' => dirname(__DIR__) . '/tests',
    ],
    'container'  => [
        'definitions' => [

        ],
    ],
    'bootstrap'  => [$params['rvkulikov.amo.module.name']],
    'modules'    => [
        $params['rvkulikov.amo.module.name'] => 'rvkulikov\amo\module\Module',
    ],
    'components' => [
        $params['rvkulikov.amo.db.name'] => [
            'class'    => '\yii\db\Connection',
            'dsn'      => $params['rvkulikov.amo.db.dsn'],
            'username' => $params['rvkulikov.amo.db.username'],
            'password' => $params['rvkulikov.amo.db.password'],
        ],
    ],
    'params'     => $params,
];