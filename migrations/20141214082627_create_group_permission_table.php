<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupPermissionTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $permissions = $this->table('group_permission');
        $permissions->addColumn('permission_id', 'integer')
              ->addColumn('group_id', 'integer')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('group_permission');
    }
}