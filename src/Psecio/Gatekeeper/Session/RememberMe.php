<?php

namespace Psecio\Gatekeeper\Session;

class RememberMe
{
    /**
     * Auth token name
     * @var string
     */
    private $authName = 'gkauth';

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
        list($token, $auth) = $this->generateTokens();
        $this->saveToken($token, $auth, $user);
        $this->setCookies($token, $auth);

        return true;
    }

    /**
     * Verify the tokens if they exist
     *
     * @param string $token Token value
     * @param string $auth Auth value
     * @return boolean Pass/fail result of the validation
     */
    public function verify($token = null, $auth = null)
    {
        // See if we have our cookies
        $domain = $_SERVER['HTTP_HOST'];
        $https = (isset($_SERVER['HTTPS'])) ? true : false;

        if (!isset($this->data[$this->tokenName]) || !isset($this->data[$this->authName])) {
            return false;
        }

        $token = $this->getByToken($this->data[$this->tokenName]);
        if ($token === false) {
            return false;
        }
        $user = $token->user;

        // Remove the token (a new one will be made later)
        $this->datasource->delete($token);

        // Verify the "auth"
        if (password_verify($this->data[$this->authName], $token->verifier) === false) {
            return false;
        }

        $this->setup($user);
        return $user;
    }

    /**
     * Get the token information searching on given token string
     *
     * @param string $token Token string for search
     * @return boolean|\Psecio\Gatekeeper\AuthTokenModel Instance if no query errors
     */
    public function getByToken($token)
    {
        $token = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource);
        $result = $this->datasource->find($token, array('token' => $this->data['gktoken']));
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
        if (new \Datetime() > new \DateTime($token->expires)) {
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
     * @param string $auth Auth token string
     * @param \Psecio\Gatekeeper\UserModel $user User model instance
     * @return boolean Success/fail of token creation
     */
    public function saveToken($token, $auth, \Psecio\Gatekeeper\UserModel $user)
    {
        $expires = new \DateTime($this->expireInterval);
        $tokenModel = new \Psecio\Gatekeeper\AuthTokenModel($this->datasource, array(
            'token' => $token,
            'verifier' => password_hash($auth, PASSWORD_DEFAULT),
            'userId' => $user->id,
            'expires' => $expires->format('Y-m-d H:i:s')
        ));
        return $this->datasource->save($tokenModel);
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
     * Generate the token values
     *
     * @return array Set of two token values (main and auth)
     */
    public function generateTokens()
    {
        $factory = new \RandomLib\Factory;
        $generator = $factory->getMediumStrengthGenerator();

        return array(
            base64_encode($generator->generate(24)),
            base64_encode($generator->generate(24))
        );
    }

    /**
     * Set the cookies with the main and auth tokens
     *
     * @param string $token Main token hash
     * @param string $auth Auth token hash
     * @param boolean $https Enable/disable HTTPS setting on cookies [optional]
     * @param string $domain Domain value to set cookies on
     */
    public function setCookies($token, $auth, $https = false, $domain = null)
    {
        if ($domain === null && isset($_SERVER['HTTP_HOST'])) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        $expires = new \DateTime($this->expireInterval);
        $cookie1 = setcookie('gktoken', $token, $expires->format('U'), '/', $domain, $https, true);
        $cookie2 = setcookie('gkauth', $auth, $expires->format('U'), '/', $domain, $https, true);

        return ($cookie1 === true && $cookie2 === true) ? true : false;
    }
}