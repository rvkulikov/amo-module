<?php

use yii\db\Migration;

/**
 * Class m191027_170036_init_extensions
 */
class m191018_170036_init_extensions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->quoteTableName("amo__account");
        $sql = <<<SQL
create extension if not exists "uuid-ossp";
SQL;

        foreach (explode("--", $sql) as $statement) {
            $this->execute($statement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('amo__account');
    }
}
