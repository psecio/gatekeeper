<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Illuminate\Contracts\Auth\User as UserContract;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable;

use Psecio\Gatekeeper\Gatekeeper;

class UserProvider implements UserProviderInterface
{
    /**
     * Get the user iformation, fetched by provided identifier
     *
     * @param string $identifier Unique user identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = (is_int($identifier))
            ? Gatekeeper::findUserById($identifier)
            : Gatekeeper::findUserByUsername($identifier);
    	if ($user === false) {
    		return null;
    	}
		return new UserAuthenticatable($user);
    }

    /**
     * Fetch the user by the value of the "remember" me token
     *
     * @param string $identifier User identifier
     * @param string $token Token value
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
    	error_log(get_class().' :: '.__FUNCTION__);
        error_log(print_r($identifier, true).' - '.$token);
    }

    /**
     * Update the user's "remember me" token value
     *
     * @param Authenticatable $user User instance
     * @param string $token Token value
     * @return ?
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
    	error_log(get_class().' :: '.__FUNCTION__.' token: '.$token);
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
    	} elseif (isset($credentials['username'])) {
    		$user = Gatekeeper::findUserByUsername($credentials['username']);
    	}
    	if ($user === false) {
			return null;
		}
		$userAuth = new UserAuthenticatable($user);
		return $userAuth;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
error_log(print_r($credentials, true));

    	$username = $user->getAuthIdentifier();
    	$credentials = [
    		'username' => $username,
    		'password' => $credentials['password']
    	];
    	return Gatekeeper::authenticate($credentials);
    }

}