<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupParentXref extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $groupXref = $this->table('group_parent');
        $groupXref->addColumn('group_id', 'integer')
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
        $this->dropTable('group_parent');
    }
}