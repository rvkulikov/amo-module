<?php

use yii\db\Migration;

/**
 *
 */
class m130524_201442_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table app__user
(
  id                   bigserial           not null
    constraint app__user_pkey
      primary key,
  username             varchar(255)        not null
    constraint app__user_username_key
      unique,
  email                varchar(255)        not null
    constraint app__user_email_key
      unique,
  auth_key             varchar(64)         not null,
  password_hash        varchar(255)        not null,
  password_reset_token varchar(255)
    constraint app__user_password_reset_token_key
      unique,
  verification_token   varchar(255),
  status               smallint default 10 not null,
  created_at           timestamp(0)        not null,
  updated_at           timestamp(0)        not null
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
        $this->dropTable('app__user');
    }
}
