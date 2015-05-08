<?php

class CreateGroupUserTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'user_group';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table($this->getTableName());
        $groups->addColumn('group_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', array('default' => null))
              ->addIndex(array('group_id', 'user_id'), array('unique' => true))
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