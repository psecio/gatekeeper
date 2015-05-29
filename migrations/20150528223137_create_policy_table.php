<?php

use Phinx\Migration\AbstractMigration;

class CreatePolicyTable extends \Psecio\Gatekeeper\PhinxMigration
{
    protected $tableName = 'policies';

    /**
     * Migrate Up.
     */
    public function up()
    {
        $tokens = $this->table($this->getTableName());
        $tokens->addColumn('expression', 'string')
            ->addColumn('name', 'string')
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