<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__user_group}}`.
 */
class m191111_223219_create_amo__user_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__user_group
(
    user_id    bigint not null
        constraint amo__user_group_amo__user_id_fk
            references amo__user
            on update cascade,
    account_id bigint not null
        constraint amo__user_group_amo__account_id_fk
            references amo__account
            on update cascade,
    group_id   bigint not null,
    constraint amo__user_group_pk
        primary key (user_id, group_id)
);
--
alter table amo__user_group
	add constraint amo__user_group_amo__group_account_id_id_fk
		foreign key (account_id, group_id) references amo__group (account_id, id)
			on update cascade;
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
        $this->dropTable('amo__user_group');
    }
}