<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__pipeline}}`.
 */
class m191111_221823_create_amo__pipeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__pipeline
(
    account_id bigint not null
        constraint amo__pipeline_amo__account_id_fk
            references amo__account
            on update cascade,
    id         bigint not null
        constraint amo__pipeline_pk
            primary key,
    name       varchar(255),
    sort       integer,
    is_main    boolean not null,
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
        $this->dropTable('amo__pipeline');
    }
}