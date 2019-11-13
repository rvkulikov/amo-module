<?php /** @noinspection PhpUndefinedVariableInspection, PhpUnusedAliasInspection */

use rvkulikov\amo\module\services\account\sync\AccountSyncer_Impl;
use rvkulikov\amo\module\services\account\sync\AccountSyncer_Interface;
use rvkulikov\amo\module\services\customField\sync\CustomFieldsSyncer_Impl;
use rvkulikov\amo\module\services\customField\sync\CustomFieldSyncer_Interface;
use rvkulikov\amo\module\services\group\sync\GroupSyncer_Impl;
use rvkulikov\amo\module\services\group\sync\GroupSyncer_Interface;
use rvkulikov\amo\module\services\init\ModuleInitializer_Impl;
use rvkulikov\amo\module\services\init\ModuleInitializer_Interface;
use rvkulikov\amo\module\services\noteType\sync\NoteTypeSyncer_Impl;
use rvkulikov\amo\module\services\noteType\sync\NoteTypeSyncer_Interface;
use rvkulikov\amo\module\services\pipeline\sync\PipelineSyncer_Impl;
use rvkulikov\amo\module\services\pipeline\sync\PipelineSyncer_Interface;
use rvkulikov\amo\module\services\taskType\sync\TaskTypeSyncer_Impl;
use rvkulikov\amo\module\services\taskType\sync\TaskTypeSyncer_Interface;
use rvkulikov\amo\module\services\user\sync\UserSyncer_Impl;
use rvkulikov\amo\module\services\user\sync\UserSyncer_Interface;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Impl;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;

return [
    'aliases'    => [
        '@rvkulikov/amo/module'       => dirname(__DIR__) . '/src',
        '@rvkulikov/amo/module/tests' => dirname(__DIR__) . '/tests',
    ],
    'container'  => [
        'definitions' => [
            ModuleInitializer_Interface::class => [
                'class' => ModuleInitializer_Impl::class,
                'authManager' => $params['rvkulikov.amo.auth_manager.name'],
                'security'    => 'security',
            ],
            AccountSyncer_Interface::class => AccountSyncer_Impl::class,
            UserSyncer_Interface::class => UserSyncer_Impl::class,
            GroupSyncer_Interface::class => GroupSyncer_Impl::class,
            CustomFieldSyncer_Interface::class => CustomFieldsSyncer_Impl::class,
            PipelineSyncer_Interface::class => PipelineSyncer_Impl::class,
            NoteTypeSyncer_Interface::class => NoteTypeSyncer_Impl::class,
            TaskTypeSyncer_Interface::class => TaskTypeSyncer_Impl::class,
            SafeDeleter_Interface::class => SafeDeleter_Impl::class,
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