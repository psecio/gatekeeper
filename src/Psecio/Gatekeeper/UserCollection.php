<?php

namespace Psecio\Gatekeeper;

class UserCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
    /**
     * Find the users belonging to the given group
     *
     * @param integer $groupId Group ID
     */
    public function findByGroupId($groupId)
    {
        $data = array('groupId' => $groupId);
        $sql = 'select u.* from users u, user_group ug'
            .' where ug.group_id = :groupId'
            .' and ug.user_id = u.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $user = new UserModel($this->getDb(), $result);
            $this->add($user);
        }
    }
}