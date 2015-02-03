<?php

namespace Psecio\Gatekeeper;

class AuthTokenModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'auth_tokens';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'id' => array(
            'description' => 'Token ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'token' => array(
            'description' => 'Token value',
            'column' => 'token',
            'type' => 'varchar'
        ),
        'verifier' => array(
            'description' => 'Verifier value',
            'column' => 'verifier',
            'type' => 'varchar'
        ),
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'integer'
        ),
        'created' => array(
            'description' => 'Date Token Expires',
            'column' => 'expires',
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
        ),
    );
}