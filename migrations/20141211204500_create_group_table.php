<?php

class CreateGroupTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'groups';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table($this->getTableName());
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
        $this->dropTable($this->getTableName());
    }
}