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
     * Current database table name
     * @var string
     */
    protected $tableName;

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
        $dbConfig = $this->db->config;
        return (isset($dbConfig['prefix']))
            ? $dbConfig['prefix'].'_'.$this->tableName : $this->tableName;
    }

    public function getPrefix()
    {
        $dbConfig = $this->db->config;
        return (isset($dbConfig['prefix'])) ? $dbConfig['prefix'].'_' : '';
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
}