<?php

namespace Psecio\Gatekeeper;

use \Psecio\Gatekeeper\Model\User;

class Gatekeeper
{
    private static $pdo;

    /**
     * Initialize the Gatekeeper instance, set up environment file and PDO connection
     *
     * @param string $envPath Environment file path (defaults to CWD)
     */
    public static function init($envPath = null)
    {
        $envPath = ($envPath !== null) ? $envPath : getcwd();
        \Dotenv::load($envPath);

        $dbType = (isset($_SERVER['DB_TYPE'])) ? $_SERVER['DB_TYPE'] : 'mysql';

        self::$pdo = new \PDO(
            $dbType.':dbname='.$_SERVER['DB_NAME'].';host='.$_SERVER['DB_HOST'],
            $_SERVER['DB_USER'], $_SERVER['DB_PASS']
        );
    }

    /**
     * Authenticate a user given the username/password credentials
     *
     * @param array $credentials Credential information (must include "username" and "password")
     * @param array $config Configuration options [optional]
     * @return boolean Pass/fail of authentication
     */
    public static function authenticate(array $credentials, array $config = array())
    {
        $username = $credentials['username'];
        $user = new UserModel(self::$pdo);
        $user->findByUsername($username);

        return (password_verify($credentials['password'], $user->password));
    }

    /**
     * Register a new user
     *
     * @param array $userData User data
     * @return boolean Success/fail of user create
     */
    public static function register(array $userData)
    {
        $user = new UserModel(self::$pdo, $userData);
        if ($user->save() === false) {
            echo 'ERROR: '.$user->getLastError()."\n";
            return false;
        }
        return true;
    }
}