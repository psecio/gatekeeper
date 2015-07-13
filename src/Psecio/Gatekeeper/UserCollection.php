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
        $prefix = $this->getPrefix();
        $data = array('groupId' => $groupId);
        $sql = 'select u.* from '.$prefix.'users u, '.$prefix.'user_group ug'
            .' where ug.group_id = :groupId'
            .' and ug.user_id = u.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $user = new UserModel($this->getDb(), $result);
            $this->add($user);
        }
    }

    /**
     * Find the users that have a permission defined by the
     *     given ID
     *
     * @param integer $permId Permission ID
     */
    public function findUsersByPermissionId($permId)
    {
        $prefix = $this->getPrefix();
        $data = array('permId' => $permId);
        $sql = 'select u.* from '.$prefix.'users u, '.$prefix.'user_permission up'
            .' where up.permission_id = :permId'
            .' and up.user_id = u.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $user = new UserModel($this->getDb(), $result);
            $this->add($user);
        }
    }
}