<?php

use Phinx\Migration\AbstractMigration;

class AddUserPermissionGroupExpire extends \Psecio\Gatekeeper\PhinxMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table user_permission add expire INT');
        $this->execute('alter table group_permission add expire INT');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table user_permission drop column expire');
        $this->execute('alter table group_permission drop column expire');
    }
}