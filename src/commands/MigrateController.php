<?php
namespace rvkulikov\amo\module\commands;

class MigrateController extends \yii\console\controllers\MigrateController
{

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