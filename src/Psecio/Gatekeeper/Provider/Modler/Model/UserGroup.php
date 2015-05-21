<?php

namespace Psecio\Gatekeeper\Provider\Modler\Model;

class UserGroup extends \Psecio\Gatekeeper\Model\Mysql implements \Psecio\Gatekeeper\User\Model\GroupProviderInterface
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'user_group';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'groupId' => array(
            'description' => 'Group Id',
            'column' => 'group_id',
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