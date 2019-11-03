<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%app__oauth_state}}`.
 */
class m191102_230838_create_app__oauth_state_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table app__oauth_state
(
    id             bigserial   not null
        constraint app__oauth_state_pk
            primary key,
    user_id        bigint      not null
        constraint app__oauth_state_app__user_id_fk
            references app__user
            on update cascade on delete cascade,
    integration_id uuid        not null
        constraint app__oauth_state_amo__integration_id_fk
            references amo__integration
            on update cascade on delete cascade,
    token          varchar(64) not null,
    expires_at     timestamp   not null
);
--
create unique index app__oauth_state_token_uindex
    on app__oauth_state (token);
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
        $this->dropTable('app__oauth_state');
    }
}