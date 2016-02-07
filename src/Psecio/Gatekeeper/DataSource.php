<?php

namespace Psecio\Gatekeeper;

abstract class DataSource
{
    /**
     * Data source configuration options
     * @var array
     */
    public $config = array();

    /**
     * Last error from a datasource request
     * @var string
     */
    public $lastError = '';

    /**
     * Init the object and set the configuration
     * @param array $config Configuration settings
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * Set the configuration
     *
     * @param array $config Config settings
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the configuration settings
     *
     * @return array Config settings
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Save the given model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public abstract function save(\Modler\Model $model);

    /**
     * Create a new record with model given
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public abstract function create(\Modler\Model $model);

    /**
     * Update the record for the given model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public abstract function update(\Modler\Model $model);

    /**
     * Delete the record defined by the model data
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public abstract function delete(\Modler\Model $model);

    /**
     * Find and populate a model based on the model type and where criteria
     *
     * @param \Modler\Model $model Model instance
     * @param array $where "Where" data to locate record
     * @return object Either a collection or model instance
     */
    public abstract function find(\Modler\Model $model, array $where = array());

    /**
     * Return the number of entities in DB per condition or in general
     *
     * @param \Modler\Model $model Model instance
     * @param array $where
     * @return bool Success/fail of action
     * @internal param array $where "Where" data to locate record
     */
    public abstract function count(\Modler\Model $model, array $where = array());

    /**
     * Return the last error from action taken on the datasource
     *
     * @return string Error string
     */
    public abstract function getLastError();
}