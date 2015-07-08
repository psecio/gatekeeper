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
        'description' => array(
            'description' => 'Group Description',
            'column' => 'description',
            'type' => 'varchar'
        ),
        'id' => array(
            'description' => 'Group ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'name' => array(
            'description' => 'Group name',
            'column' => 'name',
            'type' => 'varchar'
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
        ),
        'users' => array(
            'description' => 'Users belonging to this group',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\UserCollection',
                'method' => 'findByGroupId',
                'local' => 'id'
            )
        ),
        'permissions' => array(
            'description' => 'Permissions belonging to this group',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\PermissionCollection',
                'method' => 'findByGroupId',
                'local' => 'id'
            )
        ),
        'children' => array(
            'description' => 'Child Groups',
            'type' => 'relation',
            'relation' => array(
                'model' => '\\Psecio\\Gatekeeper\\GroupCollection',
                'method' => 'findChildrenByGroupId',
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
        return $this->getDb()->save($groupUser);
    }

    /**
     * Remove a user from a group
     *
     * @param integer|UserModel $user User ID or model instance
     * @return boolean Success/fail of removal
     */
    public function removeUser($user)
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
        return $this->getDb()->delete($groupUser);
    }

    /**
     * Check to see if the group has a permission
     *
     * @param integer|PermissionModel $permission Either a permission ID or PermissionModel
     * @return boolean Permission found/not found
     */
    public function hasPermission($permission)
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }

        $perm = new GroupPermissionModel($this->getDb());
        $perm = $this->getDb()->find($perm, array(
            'permission_id' => $permission,
            'group_id' => $this->id
        ));
        return ($perm->id !== null && $perm->permissionId == $permission) ? true : false;
    }

    /**
     * Add a permission relation for the group
     *
     * @param integer|PermissionModel $permission Either a permission ID or PermissionModel
     * @return boolean Success/fail of removal
     */
    public function addPermission($permission)
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $data = array(
            'permission_id' => $permission,
            'group_id' => $this->id
        );
        $groupPerm = new GroupPermissionModel($this->getDb(), $data);
        return $this->getDb()->save($groupPerm);
    }

    /**
     * Remove a permission from a group
     *
     * @param integer|PermissionModel $permission Permission model or ID
     * @return boolean Success/fail of removal
     */
    public function removePermission($permission)
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $data = array(
            'permission_id' => $permission,
            'group_id' => $this->id
        );
        $groupPerm = new GroupPermissionModel($this->getDb(), $data);
        return $this->getDb()->delete($groupPerm);
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
        $userGroup = $this->getDb()->find($userGroup, array(
            'group_id' => $this->id,
            'user_id' => $userId
        ));
        return ($userGroup->id !== null) ? true : false;
    }

    /**
     * Add the given group or group ID as a child of the current group
     *
     * @param integer|GroupModel $group Group ID or Group model instance
     * @return boolean Result of save operation
     */
    public function addChild($group)
    {
        if ($this->id === null) {
            return false;
        }
        if ($group instanceof GroupModel) {
            $group = $group->id;
        }
        $childGroup = new GroupParentModel(
            $this->getDb(),
            array('groupId' => $group, 'parentId' => $this->id)
        );
        return $this->getDb()->save($childGroup);
    }

    /**
     * Remove a child group either by ID or Group model instance
     *
     * @param integer|GroupModel $group Group ID or Group model instance
     * @return boolean Result of delete operation
     */
    public function removeChild($group)
    {
        if ($this->id === null) {
            return false;
        }
        if ($group instanceof GroupModel) {
            $group = $group->id;
        }
        $childGroup = new GroupParentModel($this->getDb());

        $childGroup = $this->getDb()->find(
            $childGroup,
            array('group_id' => $group, 'parent_id' => $this->id)
        );
        return $this->getDb()->delete($childGroup);
    }

    /**
     * Check to see if the group is expired
     *
     * @return boolean Expired/Not expired result
     */
    public function isExpired()
    {
        return ($this->expire !== null && $this->expire <= time());
    }
}