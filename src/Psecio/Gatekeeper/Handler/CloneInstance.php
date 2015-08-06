<?php

namespace Psecio\Gatekeeper\Handler;
use Psecio\Gatekeeper\Gatekeeper;

class CloneInstance extends \Psecio\Gatekeeper\Handler
{
	/**
	 * Execute the object/record clone handling
	 *
	 * @return boolean Success/fail of user cloning
	 */
	public function execute()
	{
		$args = $this->getArguments();
		$name = $this->getName();
		$method = ucwords($name);

		if (method_exists($this, $method) === true) {
			return $this->$method($args[0], $args[1]);
		}
		return false;
	}

	public function CloneUser($user, $data)
	{
		$ds = Gatekeeper::getDatasource();
		$newUser = new \Psecio\Gatekeeper\UserModel($ds, $data);
		$result = $newUser->save();

		if ($result == false) {
			return false;
		}

		// Get the user's groups and add
		foreach ($user->groups as $group) {
			$newUser->addGroup($group);
		}

		// Get the user's permissions and add
		foreach ($user->permissions as $permission) {
			$newUser->addPermission($permission);
		}

		return true;
	}
}