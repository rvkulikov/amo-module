<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__integration}}`.
 */
class m191027_035549_create_amo__integration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->quoteTableName("amo__integration");
        $sql = <<<SQL
create table amo__integration
(
	id uuid not null
		constraint amo__integration___pk
			primary key,
	secret_key varchar(255) not null,
	redirect_uri varchar(255) not null
);
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
        $this->dropTable('amo__integration');
    }
}
