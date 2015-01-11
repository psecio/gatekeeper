<?php

namespace Psecio\Gatekeeper;

class PermissionModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'permissions';

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
        'description' => array(
            'description' => 'Description',
            'column' => 'description',
            'type' => 'text'
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
        )
    );

    /**
     * Add a permission as a child of the current instance
     *
     * @param integer|PermissionModel $permission Either permission ID or model instance
     * @return boolean Result of save operation
     */
    public function addChild($permission)
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $childPermission = new PermissionParentModel(
            $this->getDb(),
            array('permission_id' => $permission, 'parent_id' => $this->id)
        );
        return $this->getDb()->save($childPermission);
    }

    /**
     * Remove a permission as a child of this instance
     *
     * @param integer|PermissionModel $permission Either permission ID or model instance
     * @return boolean Resultk of delete operation
     */
    public function removeChild($permission)
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $childPermission = new PermissionParentModel($this->getDb());

        $childPermission = $this->getDb()->find(
            $childPermission,
            array('permission_id' => $permission, 'parent_id' => $this->id)
        );
        return $this->getDb()->delete($childPermission);
    }
}