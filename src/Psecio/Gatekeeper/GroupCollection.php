<?php

namespace Psecio\Gatekeeper;

class GroupCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
    /**
     * Find the groups that the given user belongs to
     *
     * @param integer $userId User ID
     */
    public function findByUserId($userId)
    {
        $data = array('userId' => $userId);
        $sql = 'select g.* from groups g, user_group ug'
            .' where ug.user_id = :userId'
            .' and ug.group_id = g.id';

        $results = $this->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new GroupModel($this->getDb(), $result);
            $this->add($group);
        }
    }
}