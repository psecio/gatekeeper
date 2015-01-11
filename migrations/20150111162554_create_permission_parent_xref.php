<?php

use Phinx\Migration\AbstractMigration;

class CreatePermissionParentXref extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $permissionXref = $this->table('permission_parent');
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
        $this->dropTable('permission_parent');
    }
}