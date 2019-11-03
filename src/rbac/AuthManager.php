<?php

namespace rvkulikov\amo\module\rbac;

use yii\rbac\DbManager;

/**
 *
 */
class AuthManager extends DbManager
{
    /**
     * {@inheritDoc}
     */
    public function checkAccess($userId, $permission, $params = [])
    {
        $allow = $permission;
        $deny = "deny:{$permission}";

        return parent::checkAccess($userId, $allow, $params)
            && !parent::checkAccess($userId, $deny, $params);
    }
}
