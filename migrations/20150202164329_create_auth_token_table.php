<?php

use Phinx\Migration\AbstractMigration;

class CreateAuthTokenTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $tokens = $this->table('auth_tokens');
        $tokens->addColumn('token', 'string', array('limit' => 100))
              ->addColumn('user_id', 'integer')
              ->addColumn('expires', 'datetime')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('auth_tokens');
    }
}