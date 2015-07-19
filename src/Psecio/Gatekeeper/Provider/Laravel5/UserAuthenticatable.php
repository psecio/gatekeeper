<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;

use Psecio\Gatekeeper\UserModel;
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
error_log(get_class().' :: '.__FUNCTION__);
    	$token = $this->model->authTokens[0]->token;
    }

    /**
     * Set the "remember me" token value
     *
     * @param string $value Token value
     */
    public function setRememberToken($value)
    {
error_log(get_class().' :: '.__FUNCTION__);
    	$token = $this->model->authTokens[0];
        $token->token($value);
        $token->save();
    }

    /**
     * Get the name for the current "remember me" token
     *
     * @return string Token name
     */
    public function getRememberTokenName()
    {
error_log(get_class().' :: '.__FUNCTION__);
    	return $this->tokenName;
    }
}