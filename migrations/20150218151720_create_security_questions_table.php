<?php

use Phinx\Migration\AbstractMigration;

class CreateSecurityQuestionsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $tokens = $this->table('security_questions');
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
        $this->dropTable('security_questions');
    }
}