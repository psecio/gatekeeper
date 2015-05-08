<?php

class CreatePermissionParentXref extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'permission_parent';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $permissionXref = $this->table($this->getTableName());
        $permissionXref->addColumn('permission_id', 'integer')
              ->addColumn('parent_id', 'integer')
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