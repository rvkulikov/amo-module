<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%account}}`.
 */
class m191019_193326_create_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->quoteTableName("account");
        $sql = <<<SQL
create table account
(
	id bigint not null
		constraint account___pk
			primary key,
	integration_id varchar(255) not null,
	secret_key varchar(255) not null,
	authorization_code varchar(255) not null,
	access_token varchar(255),
	refresh_token varchar(255) not null
);
--
create unique index account_integration_id_uindex
	on account (integration_id);
--
create unique index account_refresh_token_uindex
	on account (refresh_token);
--
create unique index account_secret_key_uindex
	on account (secret_key);
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
        $this->dropTable('account');
    }
}
