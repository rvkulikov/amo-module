<?php /** @noinspection PhpUndefinedVariableInspection, PhpUnusedAliasInspection */

return [
    'aliases'    => [
        '@rvkulikov/amo/module'       => dirname(__DIR__) . '/src',
        '@rvkulikov/amo/module/tests' => dirname(__DIR__) . '/tests',
    ],
    'container'  => [
        'definitions' => [
            'rvkulikov\amo\module\services\init\ModuleInitializer_Interface' => [
                'class'       => 'rvkulikov\amo\module\services\init\ModuleInitializer_Impl',
                'authManager' => $params['rvkulikov.amo.auth_manager.name'],
                'security'    => 'security',
            ],
        ],
    ],
    'bootstrap'  => [
        $params['rvkulikov.amo.module.name'],
    ],
    'modules'    => [
        $params['rvkulikov.amo.module.name'] => [
            'class' => 'rvkulikov\amo\module\Module',
        ],
    ],
    'components' => [
        $params['rvkulikov.amo.db.name']           => [
            'class'    => '\yii\db\Connection',
            'dsn'      => $params['rvkulikov.amo.db.dsn'],
            'username' => $params['rvkulikov.amo.db.username'],
            'password' => $params['rvkulikov.amo.db.password'],
        ],
        $params['rvkulikov.amo.auth_manager.name'] => [
            'class'           => 'yii\rbac\DbManager',
            'db'              => $params['rvkulikov.amo.db.name'],
            'itemTable'       => 'app__rbac__item',
            'itemChildTable'  => 'app__rbac__item_child',
            'assignmentTable' => 'app__rbac__assignment',
            'ruleTable'       => 'app__rbac__rule',
        ],
    ],
    'params'     => $params,
];