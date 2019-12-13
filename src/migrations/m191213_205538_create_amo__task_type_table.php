<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__task_type}}`.
 */
class m191213_205538_create_amo__task_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__task_type
(
    account_id  bigint       not null
        constraint amo__task_type_amo__account_id_fk
            references amo__account
            on update cascade,
    id          bigint       not null,
    name        varchar(255) not null,
    color       varchar(255),
    icon_id     bigint,
    deleted_at  timestamp,
    constraint amo__task_type_pk
        primary key (account_id, id)
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
        $this->dropTable('amo__task_type');
    }
}
