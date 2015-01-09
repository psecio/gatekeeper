<?php

namespace Psecio\Gatekeeper\DataSource;

class Mysql extends \Psecio\Gatekeeper\Datasource
{
    /**
     * PDO instance
     * @var \PDO
     */
    protected $db;

    /**
     * Create our PDO connection, then call parent
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config, \PDO $pdo = null)
    {
        $pdo = ($pdo === null) ? $this->buildPdo($config) : $pdo;
        $this->setDb($pdo);
        parent::__construct($config);
    }

    /**
     * Build the PDO instance
     *
     * @param array $config Configuration options
     * @return \PDO instance
     */
    public function buildPdo(array $config)
    {
        return new \PDO(
            'mysql:dbname='.$config['name'].';host='.$config['host'],
            $config['username'], $config['password']
        );
    }

    /**
     * Get the set PDO instance
     *
     * @return \PDO instance
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set the PDO instance
     *
     * @param object $db PDO instance
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * Save the model and its data (either create or update)
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of save action
     */
    public function save(\Modler\Model $model)
    {
        $data = $model->toArray();

        // see if we have any pre-save
        foreach ($data as $name => $value) {
            $preMethod = 'pre'.ucwords($name);
            if (method_exists($model, $preMethod)) {
                $model->$name = $model->$preMethod($value);
            }
        }

        if ($model->id === null) {
            return $this->create($model);
        } else {
            return $this->update($model);
        }
    }

    /**
     * Create the record based on the data from the model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of create action
     */
    public function create(\Modler\Model $model)
    {
        $data = $model->toArray();
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = date('Y-m-d H:i:s');

        list($columns, $bind) = $this->setup($data);
        $properties = $model->getProperties();

        foreach ($columns as $index => $column) {
            $colName = $properties[$column]['column'];
            $columns[$index] = $colName;
        }

        $sql = 'insert into '.$model->getTableName()
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
    public function update(\Modler\Model $model)
    {
        $data = $model->toArray();
        $data['created'] = date('Y-m-d H:i:s');
        $data['updated'] = date('Y-m-d H:i:s');

        list($columns, $bind) = $this->setup($data);
        $update = array();
        $properties = $model->getProperties();

        foreach ($bind as $column => $name) {
            $colName = $properties[$column]['column'];
            $update[] = $colName.' = '.$name;
        }

        $sql = 'update '.$model->getTableName().' set '.implode(',', $update).' where ID = '.$model->id;
        return $this->execute($sql, $data);
    }

    /**
     * Delete a record represented by the model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/failure of deletion
     */
    public function delete(\Modler\Model $model)
    {
        $where = $model->toArray();
        $properties = $model->getProperties();
        list($columns, $bind) = $this->setup($where);
        $update = array();

        foreach ($bind as $column => $name) {
            // See if we keep to transfer it over to a column name
            if (array_key_exists($column, $properties)) {
                $column = $properties[$column]['column'];
            }
            $update[] = $column.' = '.$name;
        }

        $sql = 'delete from '.$model->getTableName().' where '.implode(' and ', $update);
        return $this->execute($sql, $model->toArray());

    }

    /**
     * Find records matching the "where" data given
     *     All "where" options are appended via "and"
     *
     * @param array $where Data to use in "where" statement
     * @return array Fetched data
     */
    public function find(\Modler\Model $model, array $where = array())
    {
        $properties = $model->getProperties();
        list($columns, $bind) = $this->setup($where);
        $update = array();
        foreach ($bind as $column => $name) {
            // See if we keep to transfer it over to a column name
            if (array_key_exists($column, $properties)) {
                $column = $properties[$column]['column'];
            }
            $update[] = $column.' = '.$name;
        }

        $sql = 'select * from '.$model->getTableName().' where '.implode(' and ', $update);
        $result = $this->fetch($sql, $where);

        if ($result !== false && count($result) == 1) {
            $model->load($result[0]);
        }
        return $model;
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
     * Return the last error for the data source
     *
     * @return string Error string
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}