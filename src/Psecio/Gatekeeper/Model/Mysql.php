<?php

namespace Psecio\Gatekeeper\Model;

class Mysql extends \Modler\Model
{
    /**
     * Data source instance
     * @var \Psecio\Gatekeeper\DataSource
     */
    private $db;

    /**
     * Init the object with the datasource and optional data
     *
     * @param \Psecio\Gatekeeper\DataSource $db Datasource instance
     * @param array $data Optional data to populate in model
     */
    public function __construct(\Psecio\Gatekeeper\DataSource $db, array $data = array())
    {
        $this->setDb($db);
        parent::__construct($data);
    }

    /**
     * Get the current data source instance
     *
     * @return \Psecio\Gatekeeper\DataSource instance
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set the datasource instance
     *
     * @param \Psecio\Gatekeeper\DataSource $db Data source instance
     */
    public function setDb(\Psecio\Gatekeeper\DataSource $db)
    {
        $this->db = $db;
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
            if (isset($data[$column]) || isset($data[$propertyName])) {
                $value = isset($data[$column]) ? $data[$column] : $data[$propertyName];
                $loadData[$propertyName] = $value;
            }
        }
        parent::load($loadData);
        return true;
    }
}