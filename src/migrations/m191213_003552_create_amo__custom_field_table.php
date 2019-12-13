<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__custom_field}}`.
 */
class m191213_003552_create_amo__custom_field_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__custom_field
(
    account_id    bigint not null
        constraint amo__custom_field_amo__account_id_fk
            references amo__account
            on update cascade,
    entity        varchar(16) not null,
    id            bigint not null,
    catalog_id    bigint,
    name          varchar(255),
    field_type    bigint not null,
    sort          bigint not null,
    code          varchar(255) not null,
    is_multiple   boolean not null default false,
    is_system     boolean not null default false,
    is_editable   boolean not null default false,
    is_required   boolean not null default false,
    is_deletable  boolean not null default false,
    is_visible    boolean not null default false,
    params        jsonb,
    enums         jsonb,
    values_tree   jsonb,
    deleted_at    timestamp,
    constraint amo__custom_field_pk
        primary key (entity, id)
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
        $this->dropTable('amo__custom_field');
    }
}
