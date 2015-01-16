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
        $data = array('groupId' => $groupId);
        $sql = 'select g.* from groups g, group_parent gp'
            .' where g.id = gp.group_id'
            .' and gp.parent_id = :groupId';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new GroupModel($this->getDb(), $result);
            $this->add($group);
        }
    }
}