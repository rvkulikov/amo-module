<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__credentials}}`.
 */
class m191027_073530_create_amo__credentials_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->quoteTableName("amo__credentials");
        $sql = <<<SQL
create table amo__credentials
(
	id bigserial not null
		constraint amo__credentials___pk
			primary key,
	account_id bigint not null
		constraint amo__credentials___fk_1
			references amo__account (id)
				on update cascade on delete cascade,
    account_subdomain varchar(255) not null 
        constraint amo__credentials___fk_2
		    references amo__account (subdomain)
			    on update cascade on delete cascade,
	integration_id uuid not null,
	token_type varchar(255) not null,
	secret_key varchar(255) not null,
	redirect_uri varchar(255) not null,
	expires_in integer,
	expires_at timestamp,
	access_token text,
	refresh_token varchar(255) not null
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
        $this->dropTable('amo__credentials');
    }
}
