<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__user}}`.
 */
class m191111_222507_create_amo__user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__user
(
    id           bigint       not null
        constraint amo__user_pk
            primary key,
    login        varchar(255) not null,
    name         varchar(255),
    last_name    varchar(255),
    language     varchar(255),
    phone_number varchar(255)
);
--
create unique index amo__user_login_uindex
    on amo__user (login);
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
        $this->dropTable('amo__user');
    }
}