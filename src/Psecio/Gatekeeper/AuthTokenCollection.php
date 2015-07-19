<?php

namespace Psecio\Gatekeeper;

class AuthTokenCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
	/**
	 * Find the current token records for the provided user ID
	 *
	 * @param integer $userId User ID
	 */
	public function findTokensByUserId($userId)
	{
		$sql = 'select * from auth_tokens where user_id = :userId';
		$data = [ 'userId' => $userId];

		$results = $this->getDb()->fetch($sql, $data);
        if ($results !== false) {
            foreach ($results as $result) {
                $token = new AuthTokenModel($this->getDb(), $result);
                $this->add($token);
            }
        }

	}
}