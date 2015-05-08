<?php

class CreateAuthTokenTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'auth_tokens';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $tokens = $this->table($this->getTableName());
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
        $this->dropTable($this->getTableName());
    }
}