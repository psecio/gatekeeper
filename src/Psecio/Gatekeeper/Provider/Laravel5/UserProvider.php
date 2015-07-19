<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Illuminate\Contracts\Auth\User as UserContract;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable;

use Psecio\Gatekeeper\Gatekeeper;

class UserProvider implements UserProviderInterface
{
    public function retrieveById($identifier)
    {
    	error_log(get_class().' :: '.__FUNCTION__);

    	$user = Gatekeeper::findUserById($identifier);
    	if ($user === null) {
    		return null;
    	}
		return new UserAuthenticatable($user);
    }

    public function retrieveByToken($identifier, $token)
    {
    	error_log(get_class().' :: '.__FUNCTION__);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
    	error_log(get_class().' :: '.__FUNCTION__);
    }

    /**
     * Return \Illuminate\Contracts\Auth\Authenticatable
     *
     * @param array $credentials Credentials to use in locating the user
     * @return \Illuminate\Contracts\Auth\Authenticatable instance|null
     */
    public function retrieveByCredentials(array $credentials)
    {
    	if (isset($credentials['email'])) {
    		$user = Gatekeeper::findUserByEmail($credentials['email']);
    	} elseif (isset($credentials['username']))
    		$user = Gatekeeper::findUserByUsername($credentials['email']);
    	}
    	if ($user === null) {
			return null;
		}
		$userAuth = new UserAuthenticatable($user);
		return $userAuth;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
    	$username = $user->getAuthIdentifier();
    	$credentials = [
    		'username' => $username,
    		'password' => $credentials['password']
    	];
    	return Gatekeeper::authenticate($credentials);
    }

}