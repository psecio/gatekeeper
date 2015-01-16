<?php

namespace Psecio\Gatekeeper;

class UserModel extends \Psecio\Gatekeeper\Model\Mysql
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'users';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
        'username' => array(
            'description' => 'Username',
            'column' => 'username',
            'type' => 'varchar'
        ),
        'password' => array(
            'description' => 'Password',
            'column' => 'password',
            'type' => 'varchar'
        ),
        'email' => array(
            'description' => 'Email Address',
            'column' => 'email',
            'type' => 'varchar'
        ),
        'firstName' => array(
            'description' => 'First Name',
            'column' => 'first_name',
            'type' => 'varchar'
        ),
        'lastName' => array(
            'description' => 'Last Name',
            'column' => 'last_name',
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
        ),
        'status' => array(
            'description' => 'Status',
            'column' => 'status',
            'type' => 'varchar'
        ),
        'id' => array(
            'description' => 'User ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'resetCode' => array(
            'description' => 'Password Reset Code',
            'column' => 'password_reset_code',
            'type' => 'varchar'
        ),
        'resetCodeTimeout' => array(
            'description' => 'Password Reset Code Timeout',
            'column' => 'password_reset_code_timeout',
            'type' => 'datetime'
        ),
        'groups' => array(
            'description' => 'Groups the User Belongs to',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserGroupCollection',
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'permissions' => array(
            'description' => 'Permissions the user has',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserPermissionCollection',
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'loginAttempts' => array(
            'description' => 'Number of login attempts by user',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserModel',
                'method' => 'findAttemptsByUser',
                'local' => 'id',
                'return' => 'value'
            )
        ),
        'throttle' => array(
            'description' => 'Full throttle information for a user',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\ThrottleModel',
                'method' => 'findByUserId',
                'local' => 'id'
            )
        )
    );

    /**
     * Check to see if the password needs to be rehashed
     *
     * @param string $value Password string
     * @return string Updated string value
     */
    public function prePassword($value)
    {
        if (password_needs_rehash($value, PASSWORD_DEFAULT) === true) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        return $value;
    }

    /**
     * Find the user by username
     *     If found, user is automatically loaded into model instance
     *
     * @param string $username Username
     * @return boolean Success/fail of find operation
     */
    public function findByUsername($username)
    {
        return $this->getDb()->find(
            $this, array('username' => $username)
        );
    }

    /**
     * Attach a permission to a user account
     *
     * @param integer|PermissionModel $perm Permission ID or model isntance
     */
    public function addPermission($perm)
    {
        if ($perm instanceof PermissionModel) {
            $perm = $perm->id;
        }
        $perm = new UserPermissionModel($this->getDb(), array(
            'user_id' => $this->id,
            'permission_id' => $perm
        ));
        return $this->getDb()->save($perm);
    }

    /**
     * Add a group to the user
     *
     * @param integer|GroupModel $group Add the user to a group
     * @return boolean Success/fail of add
     */
    public function addGroup($group)
    {
        if ($group instanceof GroupModel) {
            $group = $group->id;
        }
        $group = new UserGroupModel($this->getDb(), array(
            'group_id' => $group,
            'user_id' => $this->id
        ));
        return $this->getDb()->save($group);
    }

    /**
     * Activate the user (status)
     *
     * @return boolean Success/fail of activation
     */
    public function activate()
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->status = self::STATUS_ACTIVE;
        return $this->getDb()->save($this);
    }

    /**
     * Deactivate the user
     *
     * @return boolean Success/fail of deactivation
     */
    public function deactivate()
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->status = self::STATUS_INACTIVE;
        return $this->getDb()->save($this);
    }

    /**
     * Generate and return the code for a password reset
     *     Also updates the user record
     *
     * @param integer $length Length of returned string
     * @return string Geenrated code
     */
    public function getResetPasswordCode($length = 80)
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        // Generate a random-ish code and save it to the user record
        $code = substr(bin2hex(openssl_random_pseudo_bytes($length)), 0, $length);
        $this->resetCode = $code;
        $this->resetCodeTimeout = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->getDb()->save($this);

        return $code;
    }

    /**
     * Check the given code against he value in the database
     *
     * @param string $resetCode Reset code to verify
     * @return boolean Pass/fail of verification
     */
    public function checkResetPasswordCode($resetCode)
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        if ($this->resetCode === null) {
            throw new Exception\PasswordResetInvalid('No reset code defined for user '.$this->username);
            return false;
        }

        // Verify the timeout
        $timeout = new \DateTime($this->resetCodeTimeout);
        if ($timeout <= new \DateTime()) {
            $this->clearPasswordResetCode();
            throw new Exception\PasswordResetTimeout('Reset code has timeed out!');
        }

        // We made it this far, compare the hashes
        $result = ($this->hash_equals($this->resetCode, $resetCode));
        if ($result === true) {
            $this->clearPasswordResetCode();
        }
        return $result;
    }

    /**
     * Clear all data from the passsword reset code handling
     * @return [type] [description]
     */
    public function clearPasswordResetCode()
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->resetCode = null;
        $this->resetCodeTimeout = null;
        return $this->getDb()->save($this);
    }

    /**
     * Check to see if the user is in the group
     *
     * @param integer $groupId Group ID
     * @return boolean Found/not found in the group
     */
    public function inGroup($groupId)
    {
        $userGroup = new UserGroupModel($this->getDb());
        $userGroup = $this->getDb()->find($userGroup, array(
            'group_id' => $groupId,
            'user_id' => $this->id
        ));
        return ($userGroup->id !== null && $userGroup->id === $groupId) ? true : false;
    }

    /**
     * Check to see if a user has a permission
     *
     * @param integer $permId Permission ID
     * @return boolean Found/not found in user permission set
     */
    public function hasPermission($permId)
    {
        $perm = new UserPermissionModel($this->getDb());
        $perm = $this->getDb()->find($perm, array(
            'permission_id' => $permId,
            'user_id' => $this->id
        ));
        return ($perm->id !== null && $perm->id === $permId) ? true : false;
    }

    /**
     * Safer way to evaluate if hashes equal
     *
     * @param string $hash1 Hash #1
     * @param string $hash2 Hash #1
     * @return boolean Pass/fail on hash equality
     */
    public function hash_equals($hash1, $hash2)
    {
        if (\function_exists('hash_equals')) {
            return \hash_equals($hash1, $hash2);
        }
        if (\strlen($hash1) !== \strlen($hash2)) {
            return false;
        }
        $res = 0;
        $len = \strlen($hash1);
        for ($i = 0; $i < $len; ++$i) {
            $res |= \ord($hash1[$i]) ^ \ord($hash2[$i]);
        }
        return $res === 0;
    }

    /**
     * Check to see if a user is banned
     *
     * @return boolean User is/is not banned
     */
    public function isBanned()
    {
        $throttle = new ThrottleModel($this->getDb());
        $throttle = $this->getDb()->find($throttle, array('user_id' => $this->id));

        return ($throttle->status === ThrottleModel::STATUS_BLOCKED) ? true : false;
    }

    /**
     * Find the number of login attempts for a user
     *
     * @param integer $userId User ID [optional]
     * @return integer Number of login attempts
     */
    public function findAttemptsByUser($userId = null)
    {
        $userId = ($userId === null) ? $this->id : $userId;

        $throttle = new ThrottleModel($this->getDb());
        $throttle = $this->getDb()->find($throttle, array('user_id' => $userId));

        return ($throttle->attempts === null) ? 0 : $throttle->attempts;
    }
}