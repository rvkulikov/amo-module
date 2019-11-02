<?php
namespace rvkulikov\amo\module\commands;

use hipanel\rbac\RbacIniterInterface;
use rvkulikov\amo\module\Module;

/**
 * @property-read Module $module
 */
class RbacController extends \yii\console\Controller
{
    public $defaultAction = 'show';
    /**
     * @var RbacIniterInterface
     */
    private $initer;

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, RbacIniterInterface $initer, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionInit()
    {
        $auth = $this->module->authManager;
        $this->initer->init($auth);
    }

    public function actionReinit()
    {
        $auth = $this->module->authManager;
        $this->initer->reinit($auth);
    }

    public function actionShow()
    {
        $auth = $this->module->authManager;

        echo "Permissions:\n";
        $permissions = $auth->getPermissions();
        ksort($permissions);
        foreach ($permissions as $name => $perm) {
            echo "   $perm->name $perm->description\n";
        }

        echo "Roles:\n";
        foreach ($auth->getRoles() as $name => $role) {
            $children = implode(',', array_keys($auth->getChildren($name)));
            printf("   %-12s %s\n", "$role->name:", $children);
        }
    }
}
