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

		$results = $this->fetch($sql, $data);

        foreach ($results as $result) {
            $perm = new PermissionModel($this->getDb(), $result);
            $this->add($perm);
        }
	}
}