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
     * @param array $config Configuration settings [optional]
     */
    public static function init($envPath = null, array $config = array())
    {
        $envPath = ($envPath !== null) ? $envPath : getcwd();
        $result = self::loadDotEnv($envPath);

        // If the .env load failed, use the config given
        if ($result === false) {
            if (empty($config)) {
                throw new \InvalidArgumentException('Configuration values must be defined!');
            }
            $result = $config;
        }

        // Now make the PDO connection
        $result['type'] = ($result['type'] === null) ? 'mysql' : $result['type'];
        self::$pdo = new \PDO(
            $result['type'].':dbname='.$result['name'].';host='.$result['host'],
            $result['username'], $result['password']
        );
    }

    /**
     * Load the variables using the .env handling
     *
     * @param string $envPath Path to the .env file
     * @return array|boolean Array of data if found, false if load fails
     */
    protected static function loadDotEnv($envPath)
    {
        try {
            \Dotenv::load($envPath);
            return array(
                'username' => $_SERVER['DB_USER'],
                'password' => $_SERVER['DB_PASS'],
                'name' => $_SERVER['DB_NAME'],
                'type' => (isset($_SERVER['DB_TYPE'])) ? $_SERVER['DB_TYPE'] : 'mysql'
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create and setup a new model instance
     *
     * @param string $type Model class name
     * @throws \InvalidArgumentException If class requested is not valid
     * @return object Model instance
     */
    public static function modelFactory($type)
    {
        $class = '\\Psecio\\Gatekeeper\\'.$type;
        if (!class_exists($class)) {
            throw new \InvalidArgumentException('Model type "'.$class.'" does not exist!');
        }
        $model = new $class(self::$pdo);
        return $model;
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
     * Handle undefined static function calls
     *
     * @param string $name Function name
     * @param arrya $args Arguments set
     * @return mixed Boolean false if function not matched, otherwise Model instance
     */
    public static function __callStatic($name, $args)
    {
        if (substr($name, -4) == 'ById') {
            return self::handleFindById($name, $args);
        } elseif (substr($name, 0, 6) === 'create') {
            return self::handleCreate($name, $args);
        }
        return false;
    }

    /**
     * Handle the calls to create a new instance
     *
     * @param string $name Function name
     * @param array $args Argument set
     * @throws Exception\ModelNotFoundException If model type is not found
     * @return mixed Boolean false if method incorrect, model instance if created
     */
    public static function handleCreate($name, array $args)
    {
        $model = '\\Psecio\\Gatekeeper\\'.str_replace('create', '', $name).'Model';
        if (class_exists($model) === true) {
            $instance = new $model(self::$pdo, $args[0]);
            $instance->save();
            return $instance;
        } else {
            throw new Exception\ModelNotFoundException('Model type '.$model.' could not be found');
        }
        return false;
    }

    /**
     * Handle the "find*ById" calls
     *
     * @param string $name Function name
     * @param array $args Argument set
     * @throws Exception\ModelNotFoundException If model type is not found
     * @return mixed Boolean false if method incorrect, model instance if found
     */
    public static function handleFindById($name, array $args)
    {
        $type = preg_match('/find(.+)ById/', $name, $matches);

        if (isset($matches[1])) {
            $model = '\\Psecio\\Gatekeeper\\'.$matches[1].'Model';
            if (class_exists($model)) {
                $exception = '\\Psecio\\Gatekeeper\\Exception\\'.$matches[1].'NotFoundException';
                $id = $args[0];
                $instance = new $model(self::$pdo);

                $result = $instance->findById($id);
                if ($instance->id === null) {
                    throw new $exception($matches[1].' could not be found for ID '.$id);
                }
                return $instance;
            } else {
                throw new Exception\ModelNotFoundException('Model type '.$model.' could not be found');
            }
        } else {
            return false;
        }
    }
}