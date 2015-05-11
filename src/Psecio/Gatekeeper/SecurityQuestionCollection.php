<?php

namespace Psecio\Gatekeeper;

class SecurityQuestionCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
	/**
	 * Find the security questions for the given user ID
	 *
	 * @param integer $userId User ID
	 */
	public function findByUserId($userId)
	{
		$data = array('userId' => $userId);
		$sql = 'select * from '.$this->getPrefix().'security_questions where user_id = :userId';

		$results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $question = new SecurityQuestionModel($this->getDb(), $result);
            $this->add($question);
        }
	}
}