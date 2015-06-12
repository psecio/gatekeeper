<?php

namespace Psecio\Gatekeeper\Handler;

class Save extends \Psecio\Gatekeeper\Handler
{
	/**
	 * Execute the save handling
	 *
	 * @return boolean Success/fail result of save
	 */
	public function execute()
	{
		$args = $this->getArguments();
        return $this-getDb()->save($args[0]);
	}
}