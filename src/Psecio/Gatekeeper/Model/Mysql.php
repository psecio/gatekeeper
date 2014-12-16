<?php

namespace Psecio\Gatekeeper\Model;

class Mysql extends \Modler\Model
{
    /**
     * Current database object
     * @var object
     */
    private $db;

    /**
     * Last database error
     * @var string
     */
    private $lastError = '';

    /**
     * Init the model and set up the database instance
     *     Optionally load data
     *
     * @param object $db Database instance
     * @param array $data Optional data to load
     */
    public function __construct($db, array $data = array())
    {
        $this->setDb($db);
        parent::__construct($data);
    }

    /**
     * Set the current DB object instance
     *
     * @param object $db Database object
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * Get the current database object instance
     *
     * @return object Database instance
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Get the current model's table name
     *
     * @return string Table name
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the last error from the database requests
     *
     * @return string Error message
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Make a new model instance
     *
     * @param string $model Model namespace "path"
     * @return object Model instance
     */
    public function makeModelInstance($model)
    {
        $instance = new $model($this->getDb());
        return $instance;
    }

    /**
     * Save the current model - switches between create/update
     *     as needed
     *
     * @param array $data Optional data to save with (overwrites, not appends)
     * @return boolean Success/fail of the operation
     */
    public function save(array $data = array())
    {
        $data = (!empty($data)) ? $data : $this->toArray();

        // see if we have any pre-save
        foreach ($data as $name => $value) {
            $preMethod = 'pre'.ucwords($name);
            if (method_exists($this, $preMethod)) {
                $data[$name] = $this->$preMethod($value);
            }
        }

        if ($this->id === null) {
            return $this->create($data);
        } else {
            return $this->update($data);
        }
    }

    /**
     * "Set up" the needed values for the database requests
     *     (for binding to queries)
     *
     * @param array $data Data to "set up"
     * @return array Set containing the columns and bind values
     */
    public function setup(array $data)
    {
        $bind = array();
        foreach ($data as $column => $value) {
            $bind[$column] = ':'.$column;
        }

        return array(array_keys($data), $bind);
    }

    /**
     * Execute the request (not a fetch)
     *
     * @param string $sql SQL statement to execute
     * @param array $data Data to use in execution
     * @return boolean Success/fail of the operation
     */
    public function execute($sql, array $data)
    {
        $sth = $this->getDb()->prepare($sql);
        $result = $sth->execute($data);

        if ($result === false) {
            $error = $sth->errorInfo();
            $this->lastError = 'DB ERROR: ['.$sth->errorCode().'] '.$error[2];
        }
        return $result;
    }

    /**
     * Fetch the data matching the results of the SQL operation
     *
     * @param string $sql SQL statement
     * @param array $data Data to use in fetch operation
     * @param boolean $single Only fetch a single record
     * @return array Fetched data
     */
    public function fetch($sql, $data, $single = false)
    {
        $sth = $this->getDb()->prepare($sql);
        $result = $sth->execute($data);

        if ($result === false) {
            $error = $sth->errorInfo();
            $this->lastError = 'DB ERROR: ['.$sth->errorCode().'] '.$error[2];
            return false;
        }

        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return ($single === true) ? array_shift($results) : $results;
    }

    /**
     * Create the record (new)
     *
     * @param array $data Data to use in create
     * @return boolean Success/fail of the record creation
     */
    public function create(array $data)
    {
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = date('Y-m-d H:i:s');

        list($columns, $bind) = $this->setup($data);

        foreach ($columns as $index => $column) {
            $colName = $this->properties[$column]['column'];
            $columns[$index] = $colName;
        }

        $sql = 'insert into '.$this->getTableName()
            .' ('.implode(',', $columns).') values ('.implode(',', array_values($bind)).')';
        $result = $this->execute($sql, $data);
        if ($result !== false) {
            $this->id = $this->getDb()->lastInsertId();
        }

        return $result;
    }

    /**
     * Update a record
     *
     * @param array $data Data to use in update
     * @return boolean Success/fail of operation
     */
    public function update(array $data = array())
    {
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = date('Y-m-d H:i:s');

        list($columns, $bind) = $this->setup($data);
        $update = array();
        foreach ($bind as $column => $name) {
            $colName = $this->properties[$column]['column'];
            $update[] = $colName.' = '.$name;
        }

        $sql = 'update '.$this->getTableName().' set '.implode(',', $update).' where ID = '.$this->id;
        return $this->execute($sql, $data);
    }

    /**
     * Find records matching the "where" data given
     *     All "where" options are appended via "and"
     *
     * @param array $where Data to use in "where" statement
     * @return array Fetched data
     */
    public function find(array $where = array())
    {
        $properties = $this->getProperties();
        list($columns, $bind) = $this->setup($where);
        $update = array();
        foreach ($bind as $column => $name) {
            // See if we keep to transfer it over to a column name
            if (array_key_exists($column, $properties)) {
                $column = $properties[$column]['column'];
            }
            $update[] = $column.' = '.$name;
        }

        $sql = 'select * from '.$this->getTableName().' where '.implode(' and ', $update);
        $result = $this->fetch($sql, $where);

        if ($result !== false && count($result) == 1) {
            $this->load($result[0]);
        }
        return $result;
    }

    /**
     * Load the given data into the current model
     *
     * @param array $data Property data
     * @return boolean True when complete
     */
    public function load(array $data)
    {
        $loadData = array();
        foreach ($this->getProperties() as $propertyName => $propertyDetail) {
            if (!isset($propertyDetail['column'])) {
                continue;
            }
            $column = $propertyDetail['column'];
            if (isset($data[$column])) {
                $loadData[$propertyName] = $data[$column];
            }
        }
        parent::load($loadData);
        return true;
    }

    /**
     * Find a record by ID
     *
     * @param integer $id ID to locate
     * @return array Matching data
     */
    public function findById($id)
    {
        return $this->find(array('ID' => $id));
    }
}