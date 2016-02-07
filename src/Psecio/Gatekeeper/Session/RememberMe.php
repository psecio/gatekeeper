<?php

namespace Psecio\Gatekeeper\Session;

class RememberMe
{
    /**
     * Token name
     * @var string
     */
    private $tokenName = 'gktoken';

    /**
     * Default expiration time
     * @var string
     */
    private $expireInterval = '+14 days';

    /**
     * Data (cookie) for use in token evaluation
     * @var array
     */
    private $data = array();

    /**
     * User instance to check against
     * @var \Psecio\Gatekeeper\UserModel
     */
    private $user;

    /**
     * Datasource for use in making find//save requests
     * @var \Psecio\Gatekeeper\DataSource
     */
    private $datasource;

    /**
     * Init the object and set up the datasource, data and possibly a user
     *
     * @param \Psecio\Gatekeeper\DataSource $datasource Data source to use for operations
     * @param array $data Data to use in evaluation
     * @param \Psecio\Gatekeeper\UserModel|null $user User model instance [optional]
     */
    public function __construct(\Psecio\Gatekeeper\DataSource $datasource, array $data, \Psecio\Gatekeeper\UserModel $user = null)
    {
        $this->datasource = $datasource;

        if (!empty($data)) {
            $this->data = $data;
        }
        if ($user !== null) {
            $this->user = $user;
        }
        if (isset($this->data['interval'])) {
            $this->expireInterval = $this->data['interval'];
        }
    }

    /**
     * Get the current data for the evaluation
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the current user for evaluation
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the current expiration interval
     */
    public function getExpireInterval()
    {
        return $this->expireInterval;
    }

    /**
     * Setup the "remember me" session and cookies
     *
     * @param \Psecio\Gatekeeper\UserModel|null $user User model instance [optional]
     * @return boolean Success/fail of setting up the session/cookies
     */
    public function setup(\Psecio\Gatekeeper\UserModel $user = null)
    {
        $user = ($user === null) ? $this->user : $user;
        $userToken = $this->getUserToken($user);

        if ($userToken->id !== null || $this->isExpired($userToken)) {
            return false;
        }
        $token = $this->generateToken();
        $tokenModel = $this->saveToken($token, $user);
        if ($tokenModel === false) {
            return false;
        }
        $this->setCookies($tokenModel, $token);

        return true;
    }

    /**
     * Verify the token if it exists
     *     Removes the old token and sets up a new one if valid
     *
     * @param \Psecio\Gatekeeper\AuthTokenModel $token Token model instance
     * @return boolean Pass/fail result of the validation
     */
    public function verify(\Psecio\Gatekeeper\AuthTokenModel $token = null)
    {
        if (!isset($this->data[$this->tokenName])) {
            return false;
        }

        if ($token === null) {
            $tokenParts = explode(':', $this->data[$this->tokenName]);
            $token = $this->getById($tokenParts[0]);
        }

        if ($token === false) {
            return false;
        }

        $user = $token->user;
        $userToken = $token->token;

        // Remove the token (a new one will be made later)
        $this->datasource->delete($token);

        if (\Psecio\Gatekeeper\Gatekeeper::hash_equals($this->data[$this->tokenName], $token->id.':'.hash('sha256', $userToken)) === false) {
            return false;
        }

        $this->setup($user);
        return $user;
    }

    /**
     * Get the token information searching on given token string
     *
     * @param string $tokenValue Token string for search
     * @return boolean|\Psecio\Gatekeeper\AuthTokenModel Instance if no query errors
     */
    public function getByToken($tokenValue)
    {
        $token = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource);
        $result = $this->datasource->find($token, array('token' => $tokenValue));
        return $result;
    }

    /**
     * Get a token by its unique ID
     *
     * @param integer $tokenId Token ID
     * @return boolean|\Psecio\Gatekeeper\AuthTokenModel instance
     */
    public function getById($tokenId)
    {
        $token = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource);
        $result = $this->datasource->find($token, array('id' => $tokenId));
        return $result;
    }

    /**
     * Get the token by user ID
     *     Also performs evaluation to check if token is expired, returns false if so
     *
     * @param \Psecio\Gatekeeper\UserModel $user User model instance
     * @return boolean|\Psecio\Gatekeeper\AuthTokenModel instance
     */
    public function getUserToken(\Psecio\Gatekeeper\UserModel $user)
    {
        $tokenModel = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource);
        return $this->datasource->find($tokenModel, array('userId' => $user->id));
    }

    /**
     * Check to see if the token has expired
     *
     * @param \Psecio\Gatekeeper\AuthTokenModel $token Token model instance
     * @param boolean $delete Delete/don't delete the token if expired [optional]
     * @return boolean Token expired/not expired
     */
    public function isExpired(\Psecio\Gatekeeper\AuthTokenModel $token, $delete = true)
    {
        if ($token->expires !== null && new \Datetime() > new \DateTime($token->expires)) {
            if ($delete === true) {
                $this->deleteToken($token->token);
            }
            return true;
        }
        return false;
    }

    /**
     * Save the new token to the data source
     *
     * @param string $token Token string
     * @param \Psecio\Gatekeeper\UserModel $user User model instance
     * @return boolean|\Psecio\Gatekeeper\AuthTokenModel Success/fail of token creation or AuthTokenModel instance
     */
    public function saveToken($token, \Psecio\Gatekeeper\UserModel $user)
    {
        $expires = new \DateTime($this->expireInterval);
        $tokenModel = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource, array(
            'token' => $token,
            'userId' => $user->id,
            'expires' => $expires->format('Y-m-d H:i:s')
        ));
        $result = $this->datasource->save($tokenModel);
        return ($result === false) ? false : $tokenModel;
    }

    /**
     * Delete the token by token string
     *
     * @param string $token Token hash string
     * @return boolean Success/fail of token record deletion
     */
    public function deleteToken($token)
    {
        $tokenModel = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource);
        $token = $this->datasource->find($tokenModel, array('token' => $token));
        if ($token !== false) {
            return $this->datasource->delete($token);
        }
        return false;
    }

    /**
     * Generate the token value
     *
     * @return string Token hash
     */
    public function generateToken()
    {
        $factory = new \RandomLib\Factory;
        $generator = $factory->getMediumStrengthGenerator();

        return base64_encode($generator->generate(24));
    }

    /**
     * Set the cookies with the main and auth tokens
     *
     * @param \Psecio\Gatekeeper\AuthTokenModel $tokenModel Auth token model instance
     * @param string $token Token hash
     * @param boolean $https Enable/disable HTTPS setting on cookies [optional]
     * @param string $domain Domain value to set cookies on
     */
    public function setCookies(\Psecio\Gatekeeper\AuthTokenModel $tokenModel, $token, $https = false, $domain = null)
    {
        if ($domain === null && isset($_SERVER['HTTP_HOST'])) {
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        }

        $tokenValue = $tokenModel->id.':'.hash('sha256', $token);
        $expires = new \DateTime($this->expireInterval);
        return setcookie($this->tokenName, $tokenValue, $expires->format('U'), '/', $domain, $https, true);
    }
}
