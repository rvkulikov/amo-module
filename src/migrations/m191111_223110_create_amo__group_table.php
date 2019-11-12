<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__group}}`.
 */
class m191111_223110_create_amo__group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__group
(
    account_id bigint not null
        constraint amo__group_amo__account_id_fk
            references amo__account
            on update cascade,
    id         bigint not null,
    name       varchar(255),
    deleted_at timestamp
);
--
alter table amo__status
	add constraint amo__status_pk
		primary key (account_id, id);
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
        $this->dropTable('amo__group');
    }
}