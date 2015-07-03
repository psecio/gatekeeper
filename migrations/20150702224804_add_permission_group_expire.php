<?php

use Phinx\Migration\AbstractMigration;

class AddPermissionGroupExpire extends \Psecio\Gatekeeper\PhinxMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table permissions add expire INT');
        $this->execute('alter table groups add expire INT');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table permissions drop column expire');
        $this->execute('alter table groups drop column expire');
    }
}