<?php

namespace Psecio\Gatekeeper\DataSource;

class Stub extends \Psecio\Gatekeeper\DataSource
{
    /**
     * Save the given model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public function save(\Modler\Model $model){}

    /**
     * Create a new record with model given
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public function create(\Modler\Model $model){}

    /**
     * Update the record for the given model
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public function update(\Modler\Model $model){}

    /**
     * Delete the record defined by the model data
     *
     * @param \Modler\Model $model Model instance
     * @return boolean Success/fail of action
     */
    public function delete(\Modler\Model $model){}

    /**
     * Find and populate a model based on the model type and where criteria
     *
     * @param \Modler\Model $model Model instance
     * @param array $where "Where" data to locate record
     * @return boolean Success/fail of action
     */
    public function find(\Modler\Model $model, array $where = array()){}

    /**
     * Return the number of entities in DB per condition or in general
     *
     * @param \Modler\Model $model Model instance
     * @param array $where
     * @return bool Success/fail of action
     * @internal param array $where "Where" data to locate record
     */
    public function count(\Modler\Model $model, array $where = array()){}

    /**
     * Return the last error from action taken on the datasource
     *
     * @return string Error string
     */
    public function getLastError(){}

    /**
     * Fetch the data from the source
     *
     * @return boolean Success/fail of action
     */
    public function fetch(){}
}