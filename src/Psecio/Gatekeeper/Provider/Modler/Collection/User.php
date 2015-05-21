<?php

namespace Psecio\Gatekeeper\Provider\Modler\Collection;

class User extends \Psecio\Gatekeeper\Collection\Mysql implements \Psecio\Gatekeeper\User\Collection\ProviderInterface
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
}