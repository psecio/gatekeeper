<?php

use Phinx\Migration\AbstractMigration;

class AddLastLoginColumn extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table users add last_login DATETIME');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table users drop column last_login');
    }
}