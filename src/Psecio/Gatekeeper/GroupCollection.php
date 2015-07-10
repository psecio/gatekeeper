<?php

namespace Psecio\Gatekeeper;

class GroupCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
    /**
     * Find child groups by the parent group ID
     *
     * @param integer $groupId Group ID
     */
    public function findChildrenByGroupId($groupId)
    {
        $prefix = $this->getPrefix();
        $data = array('groupId' => $groupId);
        $sql = 'select g.* from '.$prefix.'groups g, '.$prefix.'group_parent gp'
            .' where g.id = gp.group_id'
            .' and gp.parent_id = :groupId';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new GroupModel($this->getDb(), $result);
            $this->add($group);
        }
    }

    /**
     * Find the groups that a permission belongs to
     *
     * @param integer $permId Permission ID
     */
    public function findGroupsByPermissionId($permId)
    {
        $prefix = $this->getPrefix();
        $data = array('permId' => $permId);
        $sql = 'select g.* from '.$prefix.'groups g, '.$prefix.'group_permission gp'
            .' where gp.permission_id = :permId'
            .' and gp.group_id = g.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new GroupModel($this->getDb(), $result);
            $this->add($group);
        }
    }
}