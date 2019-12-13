<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__note_type}}`.
 */
class m191213_204138_create_amo__note_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__note_type
(
    account_id  bigint       not null
        constraint amo__note_type_amo__account_id_fk
            references amo__account
            on update cascade,
    id          bigint       not null,
    code        varchar(255) not null,
    is_editable boolean      not null default false,
    deleted_at  timestamp,
    constraint amo__note_type_pk
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
        $this->dropTable('amo__note_type');
    }
}
