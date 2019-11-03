<?php
namespace rvkulikov\amo\module\commands;

use yii\console\controllers\MigrateController;

/**
 *
 */
class MigrateAppController extends MigrateController
{
    public $migrationTable = 'app__migration';
    public $migrationPath = [
        __DIR__ . '/../migrations',
        '@vendor/yiisoft/yii2/rbac/migrations'
    ];

    /**
     * {@inheritdoc}
     * @since 2.0.8
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'i' => 'interactive',
        ]);
    }
}