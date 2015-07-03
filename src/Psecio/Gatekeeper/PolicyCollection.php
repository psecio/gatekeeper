<?php

namespace Psecio\Gatekeeper;

class PolicyCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
	/**
	 * Get the current list of policies
	 *
	 * @param integer $limit Limit the number of records
	 */
	public function getList($limit = null)
	{
		$sql = 'select * from policies';
		if ($limit !== null) {
			$sql.= ' limit '.$limit;
		}
		$results = $this->getDb()->fetch($sql);

        foreach ($results as $result) {
            $policy = new PolicyModel($this->getDb(), $result);
            $this->add($policy);
        }
	}
}