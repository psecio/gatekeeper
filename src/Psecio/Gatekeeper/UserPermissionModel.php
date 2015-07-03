<?php

namespace Psecio\Gatekeeper;

class UserPermissionModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'user_permission';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'permissionId' => array(
            'description' => 'Permission Id',
            'column' => 'permission_id',
            'type' => 'integer'
        ),
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'integer'
        ),
        'id' => array(
            'description' => 'ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'expire' => array(
            'description' => 'Expiration Date',
            'column' => 'expire',
            'type' => 'datetime'
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