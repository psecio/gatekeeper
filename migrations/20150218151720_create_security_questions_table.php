<?php

class CreateSecurityQuestionsTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'security_questions';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $tokens = $this->table($this->getTableName());
        $tokens->addColumn('question', 'string')
            ->addColumn('answer', 'string', array('limit' => 100))
            ->addColumn('user_id', 'integer')
            ->addColumn('created', 'datetime')
            ->addColumn('updated', 'datetime', array('default' => null))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable($this->getTableName());
    }
}