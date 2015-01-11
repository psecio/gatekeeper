<?php

namespace Psecio\Gatekeeper;

class PermissionParentModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'permission_parent';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'id' => array(
            'description' => 'Record ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'permissionId' => array(
            'description' => 'Permission ID',
            'column' => 'permission_id',
            'type' => 'integer'
        ),
        'parentId' => array(
            'description' => 'Parent ID',
            'column' => 'parent_id',
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