<?php

use rvkulikov\amo\module\rbac\Migration;

/**
 *
 */
class m191102_215553_add_oauth_redirect_permission extends Migration
{
    public function safeUp()
    {
        $this->createPermission('perm:amo:oauth_redirect');
        $this->createPermission('deny:perm:amo:oauth_redirect');
        $this->addChild(
            $this->authManager->getRole('role:amo:admin'),
            $this->authManager->getPermission('perm:amo:oauth_redirect')
        );
    }

    public function safeDown()
    {
        $this->removePermission('perm:amo:oauth_redirect');
        $this->removePermission('deny:perm:amo:oauth_redirect');
    }
}