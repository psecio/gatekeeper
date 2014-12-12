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

        // If they're inactive, they can't log in
        if ($user->status === UserModel::STATUS_INACTIVE) {
            throw new Exception\UserInactiveException('User "'.$username.'" is inactive and cannot log in.');
            return false;
        }

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

    /**
     * Find a user by the given ID
     *
     * @param integer $userId User ID
     * @return \Psecio\Gatekeeper\UserModel instance
     */
    public static function findUserById($userId)
    {
        $user = new UserModel(self::$pdo);
        $user->findById($userId);
        if ($user->id === null) {
            throw new Exception\UserNotFoundException('User could not be found for ID '.$userId);
        }
        return $user;
    }

    /**
     * Create a new group
     *
     * @param array $data Group data
     * @return GroupModel instance
     */
    public static function createGroup(array $data)
    {
        $group = new GroupModel(self::$pdo, $data);
        $group->save();
        return $group;
    }

    /**
     * Find a group by its ID
     *
     * @param integer $groupId Group ID
     * @return GroupModel instance
     */
    public static function findGroupById($groupId)
    {
        $group = new GroupModel(self::$pdo);
        $group->findById($groupId);
        if ($group->id === null) {
            throw new Exception\GroupNotFoundException('Group could not be found for ID '.$groupId);
        }
        return $group;
    }
}