<?php

use Phinx\Migration\AbstractMigration;

class CreateUserPermissionTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $permissions = $this->table('user_permission');
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
        $this->dropTable('user_group');
    }
}