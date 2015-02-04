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
        'user' => array(
            'description' => 'User related to token',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserModel',
                'method' => 'findByUserId',
                'local' => 'userId'
            )
        ),
        'expires' => array(
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