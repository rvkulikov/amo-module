<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__user_account}}`.
 */
class m191111_223211_create_amo__user_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__user_account
(
    user_id    bigint  not null
        constraint amo__user_account_amo__user_id_fk
            references amo__user
            on update cascade,
    account_id bigint  not null
        constraint amo__user_account_amo__account_id_fk
            references amo__account
            on update cascade,
    is_active  boolean not null,
    is_free    boolean not null,
    is_admin   boolean not null,
    deleted_at timestamp,
    constraint amo__user_account_pk
        primary key (user_id, account_id)
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
        $this->dropTable('amo__user_account');
    }
}