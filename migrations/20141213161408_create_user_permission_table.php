<?php

class CreateUserPermissionTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'user_permission';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $permissions = $this->table($this->getTableName());
        $permissions->addColumn('permission_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->addIndex(array('permission_id', 'user_id'), array('unique' => true))
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