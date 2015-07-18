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
        $dbConfig = $this->db->config;
        return (isset($dbConfig['prefix']))
            ? $dbConfig['prefix'].'_'.$this->tableName : $this->tableName;
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
     * @param boolean $enforceGuard Enforce guarded properties
     * @return boolean True when complete
     */
    public function load(array $data, $enforceGuard = true)
    {
        $loadData = array();
        foreach ($this->getProperties() as $propertyName => $propertyDetail) {
            // If it's a normal column
            if (isset($propertyDetail['column'])) {
                $column = $propertyDetail['column'];
                if (isset($data[$column]) || isset($data[$propertyName])) {
                    $value = isset($data[$column]) ? $data[$column] : $data[$propertyName];
                    $loadData[$propertyName] = $value;
                }
            // Or for relations...
            } elseif ($propertyDetail['type'] == 'relation') {
                if (isset($data[$propertyName])) {
                    $loadData[$propertyName] = $data[$propertyName];
                }
            }
        }
        parent::load($loadData, $enforceGuard);
        return true;
    }

    /**
     * Save the current model instance (gets datasource and calls save)
     *
     * @return boolean Success/fail result of save
     */
    public function save()
    {
        $ds = \Psecio\Gatekeeper\Gatekeeper::getDatasource();
        return $ds->save($this);
    }
}