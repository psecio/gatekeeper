<?php

namespace Psecio\Gatekeeper;

use \Psecio\Gatekeeper\Model\User;

class Gatekeeper
{
    /**
     * Database (PDO) instance
     * @var \PDO
     */
    private static $pdo;

    /**
     * Allowed actions
     * @var array
     */
    private static $actions = array(
        'find', 'delete', 'create', 'save'
    );

    /**
     * Current data source
     * @var \Psecio\Gatekeeper\DataSource
     */
    private static $datasource;

    /**
     * Throttling enabled or disabled
     * @var boolean
     */
    private static $throttleStatus = true;

    /**
     * Initialize the Gatekeeper instance, set up environment file and PDO connection
     *
     * @param string $envPath Environment file path (defaults to CWD)
     * @param array $config Configuration settings [optional]
     * @param \Psecio\Gatekeeper\DataSource Custom datasource provider
     */
    public static function init($envPath = null, array $config = array(), \Psecio\Gatekeeper\DataSource $datasource = null)
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

        if ($datasource === null) {
            // Now create the data source
            $dsType = (isset($config['source'])) ? $config['source'] : 'mysql';
            $dsClass = '\\Psecio\\Gatekeeper\\DataSource\\'.ucwords($dsType);
            if (!class_exists($dsClass)) {
                throw new \InvalidArgumentException('Data source type "'.$dsType.'" not valid!');
            }

            $datasource = new $dsClass($result);
        }
        self::$datasource = $datasource;

        if (isset($config['throttle']) && $config['throttle'] === false) {
            self::disableThrottle();
        }
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
                'type' => (isset($_SERVER['DB_TYPE'])) ? $_SERVER['DB_TYPE'] : 'mysql',
                'host' => $_SERVER['DB_HOST']
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
    public static function modelFactory($type, array $data = array())
    {
        $class = '\\Psecio\\Gatekeeper\\'.$type;
        if (!class_exists($class)) {
            throw new \InvalidArgumentException('Model type "'.$class.'" does not exist!');
        }
        $model = new $class(self::$datasource, $data);
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
        $user = new UserModel(self::$datasource);
        $user->findByUsername($username);

        // If they're inactive, they can't log in
        if ($user->status === UserModel::STATUS_INACTIVE) {
            throw new Exception\UserInactiveException('User "'.$username.'" is inactive and cannot log in.');
            return false;
        }

        // Handle some throttle logic, if it's turned on
        if (self::$throttleStatus === true) {
            $throttle = self::getUserThrottle($user->id);
            $throttle->updateAttempts();

            // See if they're blocked
            if ($throttle->status === ThrottleModel::STATUS_BLOCKED) {
                $result = $throttle->checkTimeout();
                if ($result === false) {
                    return false;
                }
            } else {
                $result = $throttle->checkAttempts();
                if ($result === false) {
                    return false;
                }
            }
        }

        // Verify the password!
        $result = password_verify($credentials['password'], $user->password);

        if (self::$throttleStatus === true && $result === true) {
            $throttle->allow();
        }

        return $result;
    }

    /**
     * Disable the throttling
     */
    public static function disableThrottle()
    {
        self::$throttleStatus = false;
    }

    /**
     * Enable the throttling feature
     */
    public static function enableThrottle()
    {
        self::$throttleStatus = true;
    }

    /**
     * Return the enabled/disabled status of the throttling
     *
     * @return boolean Throttle status
     */
    public static function throttleStatus()
    {
        return self::$throttleStatus;
    }

    /**
     * Get the user throttle information
     *     If not found, makes a new one
     *
     * @param integer $userId User ID
     * @return ThrottleModel instance
     */
    public static function getUserThrottle($userId)
    {
        try {
            $throttle = Gatekeeper::findThrottleByUserId($userId);
        } catch (Exception\ThrottleNotFoundException $e) {
            $data = array(
                'user_id' => $userId,
                'attempts' => 1,
                'status' => ThrottleModel::STATUS_ALLOWED,
                'last_attempt' => date('Y-m-d H:i:s'),
                'status_change' => date('Y-m-d H:i:s')
            );
            $throttle = Gatekeeper::modelFactory('ThrottleModel', $data);
        }
        return $throttle;
    }

    /**
     * Register a new user
     *
     * @param array $userData User data
     * @return boolean Success/fail of user create
     */
    public static function register(array $userData)
    {
        $user = new UserModel(self::$datasource, $userData);
        if (self::$datasource->save($user)  === false) {
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
        // find the action first
        $action = 'find';
        foreach (self::$actions as $a) {
            if (strstr($name, $a) !== false) {
                $action = $a;
            }
        }

        if ($action == 'find') {
            return self::handleFindBy($name, $args);
        } elseif ($action == 'create') {
            return self::handleCreate($name, $args);
        } elseif ($action == 'delete') {
            return self::handleDelete($name, $args);
        } elseif ($action == 'save') {
            return self::handleSave($name, $args);
        }
        return false;
    }

    /**
     * Handle the "findBy" calls for data
     *
     * @param string $name Function name called
     * @param array $args Arguments
     * @throws \Exception\ModelNotFoundException If model type is not found
     * @throws \Exception If Data could not be found
     * @return object Model instance
     */
    public function handleFindBy($name, $args)
    {
        $action = 'find';
        $name = str_replace($action, '', $name);
        preg_match('/By(.+)/', $name, $matches);

        $property = lcfirst($matches[1]);
        $model = str_replace($matches[0], '', $name);
        $data = array($property => $args[0]);

        $modelNs = '\\Psecio\\Gatekeeper\\'.$model.'Model';
        if (!class_exists($modelNs)) {
            throw new Exception\ModelNotFoundException('Model type '.$model.' could not be found');
        }
        $instance = new $modelNs(self::$datasource);
        $instance = self::$datasource->$action($instance, $data);

        if ($instance->id === null) {
            $exception = '\\Psecio\\Gatekeeper\\Exception\\'.$model.'NotFoundException';
            throw new $exception($model.' could not be found for criteria');
        }

        return $instance;
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
            $instance = new $model(self::$datasource, $args[0]);
            $instance = self::$datasource->save($instance);
            return $instance;
        } else {
            throw new Exception\ModelNotFoundException('Model type '.$model.' could not be found');
        }
        return false;
    }

    /**
     * Handle the delete requests
     *
     * @param string $name Function name called
     * @param array $args Arguments set
     * @return boolean Success/fail of delete request
     */
    public static function handleDelete($name, array $args)
    {
        $model = self::buildModel('delete', $name, $args);
        return self::$datasource->delete($model);
    }

    /**
     * Handle the saving of a model instance
     *
     * @param string $name Name of funciton called
     * @param array $args Arguments set
     * @return boolean Success/fail of save request
     */
    public static function handleSave($name, array $args)
    {
        return self::$datasource->save($args[0]);
    }

    /**
     * Build the model instance with data given
     *
     * @param string $action Action called (ex: "delete" or "create")
     * @param string $name Function nname
     * @param array $args Arguments set
     * @throws \Exception\ModelNotFoundException If model type is not found
     * @return object Model instance
     */
    protected static function buildModel($action = 'find', $name, array $args)
    {
        $name = str_replace($action, '', $name);
        preg_match('/By(.+)/', $name, $matches);

        if (empty($matches) && $args[0] instanceof \Modler\Model) {
            $model = $name;
            $data = $args[0]->toArray();
        } else {
            $property = lcfirst($matches[1]);
            $model = str_replace($matches[0], '', $name);
            $data = array($property => $args[0]);
        }

        $modelNs = '\\Psecio\\Gatekeeper\\'.$model.'Model';
        if (!class_exists($modelNs)) {
            throw new Exception\ModelNotFoundException('Model type '.$model.' could not be found');
        }

        $instance = new $modelNs(self::$datasource);
        $instance = self::$datasource->find($instance, $data);
        return $instance;
    }
}