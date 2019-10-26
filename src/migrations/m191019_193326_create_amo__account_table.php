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
        $this->db->quoteTableName("amo__account");
        $sql = <<<SQL
create table amo__account
(
	id bigint not null
		constraint amo__account___pk
			primary key,
	subdomain varchar(255) not null,
	integration_id varchar(255) not null,
	secret_key varchar(255) not null,
	redirect_uri varchar(255) not null,
	access_token text,
	refresh_token text not null
);
--
create unique index amo__account_subdomain_uindex
	on amo__account (subdomain);
--
create unique index amo__account_integration_id_uindex
	on amo__account (integration_id);
--
create unique index amo__account_refresh_token_uindex
	on amo__account (refresh_token);
--
create unique index amo__account_secret_key_uindex
	on amo__account (secret_key);
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
