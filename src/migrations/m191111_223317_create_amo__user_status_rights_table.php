<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__user_status_rights}}`.
 */
class m191111_223317_create_amo__user_status_rights_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__user_status_rights
(
    user_id     bigint not null
        constraint amo__user_status_rights_amo__user_id_fk
            references amo__user
            on update cascade,
    account_id  bigint not null
        constraint amo__user_status_rights_amo__account_id_fk
            references amo__account
            on update cascade,
    pipeline_id bigint not null
        constraint amo__user_status_rights_amo__pipeline_id_fk
            references amo__pipeline
            on update cascade,
    status_id   bigint not null,
    view        char,
    edit        char,
    delete      char,
    export      char,
    constraint amo__user_status_rights_pk
        primary key (user_id, account_id, pipeline_id, status_id)
);
--
alter table amo__user_status_rights
	add constraint amo__user_status_rights_amo__status_pipeline_id_id_fk
		foreign key (pipeline_id, status_id) references amo__status (pipeline_id, id)
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
        $this->dropTable('amo__user_status_rights');
    }
}