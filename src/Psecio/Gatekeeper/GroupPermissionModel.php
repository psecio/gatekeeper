<?php

namespace Psecio\Gatekeeper;

class GroupPermissionModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'group_permission';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'group_id' => array(
            'description' => 'Group Id',
            'column' => 'group_id',
            'type' => 'integer'
        ),
        'permission_id' => array(
            'description' => 'Permission ID',
            'column' => 'permission_id',
            'type' => 'integer'
        ),
        'id' => array(
            'description' => 'ID',
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