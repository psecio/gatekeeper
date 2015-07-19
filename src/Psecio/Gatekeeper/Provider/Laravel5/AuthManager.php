<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Illuminate\Support\Manager;

class AuthManager extends Manager
{
	/**
	 * Get the default driver for your manager
	 *
	 * @return string Driver type
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['auth.driver'];
	}
}