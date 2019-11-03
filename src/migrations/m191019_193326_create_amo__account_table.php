<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__account}}`.
 */
class m191019_193326_create_amo__account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__account
(
	id bigint not null
		constraint amo__account___pk
			primary key,
	subdomain varchar(255) not null
        constraint amo__account___uk_1 unique
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
        $this->dropTable('amo__account');
    }
}
