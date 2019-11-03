<?php

use rvkulikov\amo\module\rbac\Migration;

/**
 *
 */
class m191102_215241_add_admin_role extends Migration
{
    public function safeUp()
    {
        $this->createRole('role:amo:admin');
    }

    public function safeDown()
    {
        $this->removeRole('role:amo:admin');
    }
}