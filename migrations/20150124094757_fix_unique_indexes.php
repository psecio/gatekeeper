<?php

class FixUniqueIndexes extends \Psecio\Gatekeeper\PhinxMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('create unique index permissionid_userid on '.$this->getPrefix().'user_permission(permission_id, user_id)');
        $this->execute('create unique index groupid_userid on '.$this->getPrefix().'user_group(user_id, group_id)');
        $this->execute('create unique index permissionid_parentid on '.$this->getPrefix().'permission_parent(permission_id, parent_id)');
        $this->execute('create unique index permissionid_groupid on '.$this->getPrefix().'group_permission(permission_id, group_id)');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop index permissionid_userid on '.$this->getPrefix().'user_permission');
        $this->execute('drop index groupid_userid on '.$this->getPrefix().'user_group');
        $this->execute('drop index permissionid_parentid on '.$this->getPrefix().'permission_parent');
        $this->execute('drop index permissionid_groupid on '.$this->getPrefix().'group_permission');
    }
}