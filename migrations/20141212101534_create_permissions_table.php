<?php

class CreatePermissionsTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'permissions';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $groups = $this->table($this->getTableName());
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
        $this->dropTable($this->getTableName());
    }
}