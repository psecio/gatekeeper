<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Psecio\Gatekeeper\Gatekeeper;
use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\AuthTokenModel;
use Illuminate\Contracts\Auth\Authenticatable;

class UserAuthenticatable implements Authenticatable
{
    /**
     * Current User model instance
     * @var \Psecio\Gatekeeper\UserModel
     */
	private $model;

    /**
     * The current Gatekeeper "remember me" token name
     * @var string
     */
    private $tokenName = 'gktoken';

    /**
     * Init the object and set the current User model instance
     *
     * @param \Psecio\Gatekeeper\UserModel $model User instance
     */
	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

    /**
     * Allow the fetching of properties directly from the model
     *
     * @param string $name Name of the property to fetch
     * @return mixed Property value
     */
    public function __get($name)
    {
        return $this->model->$name;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Allow for the direct calling of methods on the object
     *
     * @param string $name Function name
     * @param array $args Function arguments
     * @return mixed Function call return value
     */
    public function __call($name, array $args)
    {
        return call_user_func_array([$this->model, $name], $args);
    }

    /**
     * Get the primary identifier for the curent user
     *
     * @return string Username
     */
	public function getAuthIdentifier()
	{
		return $this->model->username;
	}

    /**
     * Get the current user's password (hashed value)
     *
     * @return string Hashed password string
     */
    public function getAuthPassword()
    {
    	return $this->model->password;
    }

    /**
     * Get the current token value for the "remember me" handling
     *
     * @return string Token value (hash)
     */
    public function getRememberToken()
    {
        $tokens = $this->model->authTokens;
        if (isset($tokens[0])) {
            return $tokens[0]->token;
        }
    }

    /**
     * Set the "remember me" token value
     *
     * @param string $value Token value
     */
    public function setRememberToken($value)
    {
        $tokens = $this->model->authTokens;
        if (isset($tokens[0])) {
            $token = $tokens[0];
            $token->token($value);
            $token->save();
        } else {
            // No token found, make one
            $token = new AuthTokenModel(Gatekeeper::getDatasource(), [
                'token' => $value,
                'user_id' => $this->model->id,
                'expires' => strtotime('+14 days')
            ]);
            $token->save();
        }
    }

    /**
     * Get the name for the current "remember me" token
     *
     * @return string Token name
     */
    public function getRememberTokenName()
    {
    	return $this->tokenName;
    }
}