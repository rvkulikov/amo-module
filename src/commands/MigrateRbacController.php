<?php

namespace rvkulikov\amo\module\commands;

use yii2mod\rbac\commands\MigrateController;

/**
 * Class MigrateController
 *
 * Below are some common usages of this command:
 *
 * ```
 * # creates a new migration named 'create_rule'
 * yii rbac/migrate/create create_rule
 *
 * # applies ALL new migrations
 * yii rbac/migrate
 *
 * # reverts the last applied migration
 * yii rbac/migrate/down
 * ```
 */
class MigrateRbacController extends MigrateController
{
    public $migrationTable = 'rbac__migration';
    public $migrationPath = __DIR__ . '/../../src/migrations/rbac';
    public $templateFile = __DIR__ . '/../../src/rbac/views/migration.php';

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