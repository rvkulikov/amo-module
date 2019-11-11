<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%amo__user_account_rights}}`.
 */
class m191111_223306_create_amo__user_account_rights_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
create table amo__user_account_rights
(
    user_id        bigint not null
        constraint amo__user_account_rights_amo__user_id_fk
            references amo__user
            on update cascade,
    account_id     bigint not null
        constraint amo__user_account_rights_amo__account_id_fk
            references amo__account
            on update cascade,
    incoming_leads char,
    catalogs       char,
    lead_add       char,
    lead_view      char,
    lead_edit      char,
    lead_export    char,
    contact_add    char,
    contact_view   char,
    contact_edit   char,
    contact_delete char,
    contact_export char,
    company_add    char,
    company_view   char,
    company_edit   char,
    company_delete char,
    company_export char,
    task_exit      char,
    task_delete    char,
    constraint amo__user_rights_pk
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
        $this->dropTable('amo__user_account_rights');
    }
}