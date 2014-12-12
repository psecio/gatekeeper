<?php

use Phinx\Migration\AbstractMigration;

class CreatePermissionsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table('permissions');
        $groups->addColumn('name', 'string', array('limit' => 100))
              ->addColumn('description', 'text')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->addIndex(array('name'), array('unique' => true))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('permissions');
    }
}