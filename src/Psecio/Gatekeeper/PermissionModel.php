<?php

namespace Psecio\Gatekeeper;

class PermissionModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'permissions';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'name' => array(
            'description' => 'Group Name',
            'column' => 'name',
            'type' => 'varchar'
        ),
        'description' => array(
            'description' => 'Description',
            'column' => 'description',
            'type' => 'text'
        ),
        'id' => array(
            'description' => 'Group ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'created' => array(
            'description' => 'Date Created',
            'column' => 'created',
            'type' => 'datetime'
        ),
        'updated' => array(
            'description' => 'Date Updated',
            'column' => 'updated',
            'type' => 'datetime'
        )
    );
}