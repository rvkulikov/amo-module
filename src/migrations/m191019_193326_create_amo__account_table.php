<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__account}}`.
 */
class m191019_193326_create_amo__account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__account
(
    id              bigint       not null
        constraint amo__account___pk
            primary key,
    name            varchar(255),
    subdomain       varchar(255) not null
        constraint amo__account___uk_1 unique,
    currency        varchar(255),
    timezone        varchar(255),
    timezone_offset varchar(255),
    language        varchar(255),
    date_pattern    jsonb,
    "current_user"  bigint
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
        $this->dropTable('amo__account');
    }
}
