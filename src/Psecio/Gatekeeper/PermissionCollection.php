<?php

namespace Psecio\Gatekeeper;

class PermissionCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
	/**
	 * Find the permissions for a given user ID
	 *
	 * @param integer $userId User ID
	 */
	public function findByUserId($userId)
	{
		$data = array('userId' => $userId);
		$sql = 'select p.* from permissions p, user_permission up'
			.' where p.id = up.permission_id'
			.' and up.user_id = :userId';

		$results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $perm = new PermissionModel($this->getDb(), $result);
            $this->add($perm);
        }
	}

	/**
	 * Find the permissions for the given group ID
	 *
	 * @param integer $groupId Group ID
	 */
	public function findByGroupId($groupId)
	{
		$data = array('groupId' => $groupId);
		$sql = 'select p.* from permissions p, group_permission gp'
			.' where p.id = gp.permision_id'
			.' and gp.group_id = :groupId';

		$results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $perm = new PermissionModel($this->getDb(), $result);
            $this->add($perm);
        }
	}

	/**
     * Find child permission by the parent permission ID
     *
     * @param integer $permId Permission ID
     */
    public function findChildrenByPermissionId($permId)
    {
        $data = array('permId' => $permId);
        $sql = 'select p.* from permissions p, permission_parent pp'
			.' where p.id = pp.permission_id'
			.' and p.parent_id = :permId';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new PermissionModel($this->getDb(), $result);
            $this->add($group);
        }
    }
}