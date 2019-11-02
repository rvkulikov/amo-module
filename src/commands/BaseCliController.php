<?php
namespace rvkulikov\amo\module\commands;

use yii\console\Controller;

/**
 *
 */
abstract class BaseCliController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'i' => 'interactive',
        ]);
    }
}