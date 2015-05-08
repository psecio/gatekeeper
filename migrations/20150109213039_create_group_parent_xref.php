<?php

class CreateGroupParentXref extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'group_parent';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $groupXref = $this->table($this->getTableName());
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
        $this->dropTable($this->getTableName());
    }
}