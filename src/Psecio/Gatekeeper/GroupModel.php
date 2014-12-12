<?php

namespace Psecio\Gatekeeper;

class GroupModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'groups';

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
        ),
        'users' => array(
            'description' => 'Users belonging to this group',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserCollection',
                'method' => 'findByGroupId',
                'local' => 'id'
            )
        )
    );

    /**
     * Add a user to the group
     *
     * @param integer|UserModel $user Either a user ID or a UserModel instance
     */
    public function addUser($user)
    {
        if ($this->id === null) {
            return false;
        }
        if ($user instanceof UserModel) {
            $user = $user->id;
        }
        $data = array(
            'group_id' => $this->id,
            'user_id' => $user
        );
        $groupUser = new UserGroupModel($this->getDb(), $data);
        $groupUser->save();
    }

    /**
     * Check if the user is in the current group
     *
     * @param integer $userId User ID
     * @return boolean Found/not found in group
     */
    public function inGroup($userId)
    {
        $userGroup = new UserGroupModel($this->getDb());
        $result = $userGroup->find(array(
            'group_id' => $this->id,
            'user_id' => $userId
        ));
        return ($userGroup->id !== null) ? true : false;
    }
}