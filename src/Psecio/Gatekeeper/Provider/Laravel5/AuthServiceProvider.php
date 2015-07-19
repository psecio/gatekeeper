<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Psecio\Gatekeeper\Gatekeeper;
use Psecio\Gatekeeper\Provider\Laravel5\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * Register (start) the service provider
	 * 	Sets up the Gatekeeper instance with init() call
	 */
	public function register()
	{
		$config = array(
			'username' => env('GATEKEEPER_USER'),
			'password' => env('GATEKEEPER_PASS'),
			'host' => env('GATEKEEPER_HOST'),
			'name' => env('GATEKEEPER_DATABASE'),
		);
		Gatekeeper::init(null, $config);
	}

	/**
	 * Boot the provider, adding the "gatekeeper" type to the Auth handling
	 *
	 * @param Router $router Laravel router instance
	 */
	public function boot(Router $router)
    {
		Auth::extend('gatekeeper', function($app) {
			return new UserProvider();
		});

        parent::boot($router);
    }
}