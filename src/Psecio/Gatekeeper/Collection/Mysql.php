<?php

namespace Psecio\Gatekeeper\Collection;

class Mysql extends \Modler\Collection
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
     * Init the collection and set up the database instance
     *
     * @param object $db Database instance
     */
    public function __construct($db)
    {
        $this->setDb($db);
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
}