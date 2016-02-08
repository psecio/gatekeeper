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
        'find', 'delete', 'create', 'save', 'clone', 'count'
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
     * Current set of restrictions
     * @var array
     */
    private static $restrictions = array();

    /**
     * Current configuration options
     * @var array
     */
    private static $config = array();

    /**
     * Current set of policies (callback versions)
     * @var array
     */
    private static $policies = array();

    private static $logger = null;

    /**
     * Initialize the Gatekeeper instance, set up environment file and PDO connection
     *
     * @param string $envPath Environment file path (defaults to CWD)
     * @param array $config Configuration settings [optional]
     * @param \Psecio\Gatekeeper\DataSource $datasource Custom datasource provider
     */
    public static function init(
        $envPath = null,
        array $config = array(),
        \Psecio\Gatekeeper\DataSource $datasource = null,
        $logger = null
    )
    {
        $result = self::loadConfig($config, $envPath);
        if ($datasource === null) {
            $datasource = self::buildDataSource($config, $result);
        }
        self::$datasource = $datasource;

        if (isset($config['throttle']) && $config['throttle'] === false) {
            self::disableThrottle();
        }
        self::setLogger($logger);
    }

    /**
     * Get the configuration either from the config given or .env path
     *
     * @param array $config Configuration values
     * @param string $envPath Path to .env file
     * @return array Set of configuration values
     */
    public static function loadConfig(array $config, $envPath = null)
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
        self::setConfig($result);
        return $result;
    }

    /**
     * Set the current configuration options
     *
     * @param array $config Set of configuration settings
     */
    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    /**
     * Get the current configuration information
     *     If an index is given, it tries to find it. If found,
     *     returns just that value. If not, returns null. Otherwise
     *     returns all config values
     *
     * @param string $index Index to locate [optional]
     * @return mixed Single value if index found, otherwise array of all
     */
    public static function getConfig($index = null)
    {
        if ($index !== null) {
            return (isset(self::$config[$index])) ? self::$config[$index] : null;
        } else {
            return self::$config;
        }
    }

    /**
     * Set the current logger interface
     *     If the logger value is null, a Monolog instance will be created
     *
     * @param \Psr\Log\LoggerInterface|null $logger PSR logger or null
     */
    public static function setLogger(\Psr\Log\LoggerInterface $logger = null)
    {
        if ($logger === null) {
            // make a monolog logger that logs to /tmp by default
            if (class_exists('\Monolog\Logger') === true) {
                $logger = new \Monolog\Logger('gatekeeper');
                $logger->pushHandler(
                    new \Monolog\Handler\StreamHandler('/tmp/gatekeeper.log')
                );
            }
        }
        self::$logger = $logger;
    }

    /**
     * Get the current logger instance
     *
     * @return \Psr\Log\LoggerInterface object
     */
    public static function getLogger()
    {
        return self::$logger;
    }

    /**
     * Build a datasource object
     *
     * @param array $config Configuration settings
     * @param array $result Environment data
     * @throws \Exception If data source type is not valid
     * @return \Psecio\Gatekeeper\DataSource instance
     */
    public static function buildDataSource(array $config, $result)
    {
        $dsType = (isset($config['source'])) ? $config['source'] : 'mysql';
        $dsClass = '\\Psecio\\Gatekeeper\\DataSource\\'.ucwords($dsType);
        if (!class_exists($dsClass)) {
            throw new \InvalidArgumentException('Data source type "'.$dsType.'" not valid!');
        }

        try {
            $datasource = new $dsClass($result);
            return $datasource;
        } catch (\Exception $e) {
            throw new \Exception('Error creating data source "'.$dsType.'" ('.$e->getMessage().')');
        }
    }

    /**
     * Get the current datasource
     *
     * @return \Psecio\Gatekeeper\DataSource instance
     */
    public static function getDatasource()
    {
        return self::$datasource;
    }

    /**
     * Set the current data source to the one given
     *
     * @param \Psecio\Gatekeeper\DataSource $ds Data source instance
     */
    public static function setDatasource($ds)
    {
        self::$datasource = $ds;
    }

    /**
     * Get the last error message from the current datasource
     *
     * @return string Error message (or empty string)
     */
    public static function getLastError()
    {
        return self::getDatasource()->lastError;
    }

    /**
     * Get the current list of restrictions
     *
     * @return array Set of restrictions
     */
    public static function getRestrictions()
    {
        return self::$restrictions;
    }

    /**
     * Clear out the current restrictions
     */
    public static function clearRestrictions()
    {
        self::$restrictions = [];
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
            $dotenv = new \Dotenv\Dotenv($envPath);
            $dotenv->load();

            $config = array(
                'username' => $_SERVER['DB_USER'],
                'password' => $_SERVER['DB_PASS'],
                'name' => $_SERVER['DB_NAME'],
                'type' => (isset($_SERVER['DB_TYPE'])) ? $_SERVER['DB_TYPE'] : 'mysql',
                'host' => $_SERVER['DB_HOST']
            );
            if (isset($_SERVER['DB_PREFIX'])) {
                $config['prefix'] = $_SERVER['DB_PREFIX'];
            }
            return $config;
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
     * Safer way to evaluate if hashes equal
     *
     * @param string $hash1 Hash #1
     * @param string $hash2 Hash #1
     * @return boolean Pass/fail on hash equality
     */
    public static function hash_equals($hash1, $hash2)
    {
        if (\function_exists('hash_equals')) {
            return \hash_equals($hash1, $hash2);
        }
        if (\strlen($hash1) !== \strlen($hash2)) {
            return false;
        }
        $res = 0;
        $len = \strlen($hash1);
        for ($i = 0; $i < $len; ++$i) {
            $res |= \ord($hash1[$i]) ^ \ord($hash2[$i]);
        }
        return $res === 0;
    }

    /**
     * Authenticate a user given the username/password credentials
     *
     * @param array $credentials Credential information (must include "username" and "password")
     * @param boolean $remember Flag to activate the "remember me" functionality
     * @return boolean Pass/fail of authentication
     */
    public static function authenticate(array $credentials, $remember = false)
    {
        $username = $credentials['username'];
        $user = new UserModel(self::$datasource);
        $user->findByUsername($username);

        self::getLogger()->info('Authenticating user.', array('username' => $username));

        // If they're inactive, they can't log in
        if ($user->status === UserModel::STATUS_INACTIVE) {
            self::getLogger()->error('User is inactive and cannot login.', array('username' => $username));
            throw new Exception\UserInactiveException('User "'.$username.'" is inactive and cannot log in.');
        }

        // Handle some throttle logic, if it's turned on
        if (self::$throttleStatus === true) {
            // Set up our default throttle restriction
            $instance = new \Psecio\Gatekeeper\Restrict\Throttle(array('userId' => $user->id));
            self::$restrictions[] = $instance;
        }

        // Check any restrictions
        if (!empty(self::$restrictions)) {
            foreach (self::$restrictions as $restriction) {
                if ($restriction->evaluate() === false) {
                    self::getLogger()->error('Restriction failed.', array('restriction' => get_class($restriction)));
                    throw new Exception\RestrictionFailedException('Restriction '.get_class($restriction).' failed.');
                }
            }
        }

        // Verify the password!
        $result = password_verify($credentials['password'], $user->password);

        if (self::$throttleStatus === true && $result === true) {
            self::getLogger()->info('User login verified.', array('username' => $username));

            // If throttling is enabled, set the user back to allow
            if (isset($instance)) {
                $instance->model->allow();
            }

            $user->updateLastLogin();

            if ($remember === true) {
                self::getLogger()->info('Activating remember me.', array('username' => $username));
                self::rememberMe($user);
            }
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
        $throttle = Gatekeeper::findThrottleByUserId($userId);

        if ($throttle === false) {
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
            return false;
        }
        // Add groups if they're given
        if (isset($userData['groups'])) {
            foreach ($userData['groups'] as $group) {
                $group = (is_int($group)) ? self::findGroupById($group) : self::findGroupByName($group);
                $user->addGroup($group);
            }
        }
        // Add permissions if they're given
        if (isset($userData['permissions'])) {
            foreach ($userData['permissions'] as $perm) {
                $perm = (is_int($perm)) ? self::findPermissionById($perm) : self::findPermissionByName($perm);
                $user->addPermission($perm);
            }
        }
        return $user;
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
            $action = new \Psecio\Gatekeeper\Handler\FindBy($name, $args, self::$datasource);
        } elseif ($action == 'create') {
            $action = new \Psecio\Gatekeeper\Handler\Create($name, $args, self::$datasource);
        } elseif ($action == 'delete') {
            $action = new \Psecio\Gatekeeper\Handler\Delete($name, $args, self::$datasource);
        } elseif ($action == 'save') {
            $action = new \Psecio\Gatekeeper\Handler\Save($name, $args, self::$datasource);
        } elseif ($action == 'clone') {
            $action = new \Psecio\Gatekeeper\Handler\CloneInstance($name, $args, self::$datasource);
        } elseif ($action == 'count') {
            $action = new \Psecio\Gatekeeper\Handler\Count($name, $args, self::$datasource);
        }
        return $action->execute();
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
    public static function buildModel($action = 'find', $name, array $args)
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

    /**
     * Create a restriction and add it to be evaluated
     *
     * @param string $type Restriction type
     * @param array $config Restriction configuration
     */
    public static function restrict($type, array $config)
    {
        $classNs = '\\Psecio\\Gatekeeper\\Restrict\\'.ucwords(strtolower($type));
        if (!class_exists($classNs)) {
            throw new \InvalidArgumentException('Restriction type "'.$type.'" is invalid');
        }
        $instance = new $classNs($config);
        self::$restrictions[] = $instance;
    }

    /**
     * Enable and set up the "Remember Me" cookie token handling for the given user
     *
     * @param \Psecio\Gatekeeper\UserModel|string $user User model instance
     * @param array $config Set of configuration settings
     * @return boolean Success/fail of sesssion setup
     */
    public static function rememberMe($user, array $config = array())
    {
        if (is_string($user)) {
            $user = Gatekeeper::findUserByUsername($user);
        }

        $data = array_merge($_COOKIE, $config);
        $remember = new Session\RememberMe(self::$datasource, $data, $user);
        return $remember->setup();
    }

    /**
     * Check the "Remember Me" token information (if it exists)
     *
     * @return boolean|\Psecio\Gatekeeper\UserModel Success/fail of token validation or User model instance
     */
    public static function checkRememberMe()
    {
        $remember = new Session\RememberMe(self::$datasource, $_COOKIE);
        return $remember->verify();
    }

    /**
     * Evaluate the policy (found by name) against the data provided
     *
     * @param string $name Name of the policy
     * @param mixed $data Data to use in evaluation (single object or array)
     * @return boolean Pass/fail status of evaluation
     */
    public static function evaluatePolicy($name, $data)
    {
        // See if it's a closure policy first
        if (array_key_exists($name, self::$policies)) {
            $policy = self::$policies[$name];
            $result = $policy($data);
            return (!is_bool($result)) ? false : $result;
        } else {
            $policy = Gatekeeper::findPolicyByName($name);
            return $policy->evaluate($data);
        }
    }

    /**
     * Allow for the creation of a policy as a callback too
     *
     * @param array $policy Policy settings
     * @return boolean Success/fail of policy creation
     */
    public static function createPolicy(array $policy)
    {
        if (is_callable($policy['expression'])) {
            $name = $policy['name'];
            self::$policies[$name] = $policy['expression'];
            return true;
        } else {
            return self::handleCreate('createPolicy', array($policy));
        }
    }
}
