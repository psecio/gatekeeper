<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table('groups');
        $groups->addColumn('description', 'string', array('limit' => 20))
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->addcolumn('name', 'string', array('limit' => 100))
              ->addIndex(array('name'), array('unique' => true))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('groups');
    }
}