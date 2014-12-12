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
        $groups->addColumn('name', 'string', array('limit' => 20))
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
        $this->dropTable('groups');
    }
}