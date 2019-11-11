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
    id         bigint not null
        constraint amo__group_pk
            primary key,
    account_id bigint not null
        constraint amo__group_amo__account_id_fk
            references amo__account
            on update cascade,
    name       varchar(255),
    deleted_at timestamp
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
        $this->dropTable('amo__group');
    }
}