<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Illuminate\Support\Manager;

class AuthManager extends Manager
{
	public function getDefaultDriver()
	{
		return $this->app['config']['auth.driver'];
	}
}