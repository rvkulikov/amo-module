<?php

use rvkulikov\amo\module\rbac\Migration;

/**
 *
 */
class m191102_215442_add_client_role extends Migration
{
    public function safeUp()
    {
        $this->createRole('role:amo:client');
        $this->addChild(
            $this->authManager->getRole('role:amo:admin'),
            $this->authManager->getRole('role:amo:client')
        );
    }

    public function safeDown()
    {
        $this->removeRole('role:amo:client');
    }
}