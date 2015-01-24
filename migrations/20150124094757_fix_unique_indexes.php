<?php

use Phinx\Migration\AbstractMigration;

class FixUniqueIndexes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('create unique index permissionid_userid on user_permission(permission_id, user_id)');
        $this->execute('create unique index groupid_userid on user_group(user_id, group_id)');
        $this->execute('create unique index permissionid_parentid on permission_parent(permission_id, parent_id)');
        $this->execute('create unique index permissionid_groupid on group_permission(permission_id, group_id)');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop index permissionid_userid on user_permission');
        $this->execute('drop index groupid_userid on user_group');
        $this->execute('drop index permissionid_parentid on permission_parent');
        $this->execute('drop index permissionid_groupid on group_permission');
    }
}