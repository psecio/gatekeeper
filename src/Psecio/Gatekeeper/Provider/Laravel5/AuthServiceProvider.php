<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Validator;
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
    	// Add Gatekeeper to the Auth provider list
		Auth::extend('gatekeeper', function($app) {
			return new UserProvider();
		});

		// Create a new "unique" (gkunique)  validator for unique user checking
		Validator::extend('gk_unique', function($attribute, $value, $parameters) {
			$type = (isset($parameters[0])) ? $parameters[0] : 'user';

			// strip a training "s" if there is one
			if (substr($type, -1) === 's') {
				$type = substr($type, 0, strlen($type)-1);
			}
			$method = 'find'.ucwords($type).'By'.ucwords($attribute);
			try {
				$user = Gatekeeper::$method($value);
				return ($user === false);
			} catch (\Exception $e) {
				return false;
			}
		});

        parent::boot($router);
    }
}