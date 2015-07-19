<?php

namespace Psecio\Gatekeeper\Provider\Laravel5;
use Psecio\Gatekeeper\UserModel;
use Illuminate\Contracts\Auth\Authenticatable;

class UserAuthenticatable implements Authenticatable
{
	private $model;

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

	public function getAuthIdentifier()
	{
		return $this->model->username;
	}
    public function getAuthPassword()
    {
    	return $this->model->password;
    }
    public function getRememberToken()
    {
    	return null;
    }
    public function setRememberToken($value)
    {
    	return null;
    }
    public function getRememberTokenName()
    {
    	return null;
    }
}