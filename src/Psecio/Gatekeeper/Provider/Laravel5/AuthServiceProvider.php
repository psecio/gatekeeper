<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;
// namespace App;

use \Illuminate\Support\Facades\Auth;
use \Psecio\Gatekeeper\Gatekeeper;
use \Psecio\Gatekeeper\Provider\Laravel5\UserProvider;
use \Psecio\Gatekeeper\Provider\Laravel5\AuthManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AuthServiceProvider extends ServiceProvider
{
	public function register()
	{
		error_log(get_class().' :: '.__FUNCTION__);

		$config = array(
			'username' => 'gk42',
			'password' => 'gk42',
			'host' => '127.0.0.1',
			'name' => 'gatekeeper'
		);
		Gatekeeper::init(null, $config);
	}

	public function boot(Router $router)
    {
		error_log(get_class().' :: '.__FUNCTION__);

		Auth::extend('gatekeeper', function($app) {
			return new UserProvider();
		});

        parent::boot($router);
    }
}