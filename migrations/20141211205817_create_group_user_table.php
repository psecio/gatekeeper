<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupUserTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table('user_group');
        $groups->addColumn('group_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
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