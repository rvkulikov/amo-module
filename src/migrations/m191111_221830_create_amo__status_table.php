<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__status}}`.
 */
class m191111_221830_create_amo__status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__status
(
    id         bigint not null
        constraint amo__status_pk
            primary key,
    account_id  bigint  not null
        constraint amo__status_amo__account_id_fk
            references amo__account
            on update cascade,
    pipeline_id bigint  not null
        constraint amo__status_amo__pipeline_id_fk
            references amo__pipeline
            on update cascade,
    name        varchar(255),
    color       varchar(255),
    sort        integer,
    is_editable boolean not null,
    deleted_at  timestamp
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
        $this->dropTable('amo__status');
    }
}