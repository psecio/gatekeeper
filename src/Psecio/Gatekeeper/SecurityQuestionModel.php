<?php

namespace Psecio\Gatekeeper;

class SecurityQuestionModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'security_questions';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'id' => array(
            'description' => 'Question ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'question' => array(
            'description' => 'Security Question',
            'column' => 'question',
            'type' => 'varchar'
        ),
        'answer' => array(
            'description' => 'Security Answer',
            'column' => 'answer',
            'type' => 'varchar'
        ),
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'varchar'
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

    /**
     * Hash the answer
     *
     * @param string $value Answer to question
     * @return string Hashed answer
     */
    public function preAnswer($value)
    {
        if (password_needs_rehash($value, PASSWORD_DEFAULT) === true) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        return $value;
    }

    /**
     * Verify the answer to the question
     *
     * @param string $value Answer input from user
     * @return boolean Match/no match on answer
     */
    public function verifyAnswer($value)
    {
        if ($this->id === null) {
            return false;
        }
        return password_verify($value, $this->answer);
    }
}