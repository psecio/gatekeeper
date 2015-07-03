<?php

namespace Psecio\Gatekeeper;

class UserPermissionCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
    /**
     * Find the permissions for a given user ID
     *
     * @param integer $userId User ID
     */
    public function findByUserId($userId)
    {
        $prefix = $this->getPrefix();
        $data = array('userId' => $userId);
        $sql = 'select p.* from '.$prefix.'permissions p, '.$prefix.'user_permission up'
            .' where p.id = up.permission_id'
            .' and up.user_id = :userId'
            .' and (up.expire >= UNIX_TIMESTAMP(NOW()) or up.expire is null)';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $perm = new PermissionModel($this->getDb(), $result);
            $this->add($perm);
        }
    }

    /**
     * Create relational records linking the user and permission
     *
     * @param \Gatekeeper\UserModel $model Model instance
     * @param array $data Data to use in create
     */
    public function create($model, array $data)
    {
        foreach ($data as $permission) {
            // Determine if it's an integer (permissionId) or name
            if (is_int($permission) === true) {
                $where = 'id = :id';
                $dbData = array('id' => $permission);
            } else {
                $where = 'name = :name';
                $dbData = array('name' => $permission);
            }

            $sql = 'select id, name from '.$this->getPrefix().'permissions where '.$where;
            $results = $this->getDb()->fetch($sql, $dbData);
            if (!empty($results) && count($results) == 1) {
                // exists, make the relation
                $model = new UserPermissionModel(
                    $this->getDb(),
                    array('permissionId' => $results[0]['id'], 'userId' => $model->id)
                );
                $this->getDb()->save($model);
            }
        }
    }
}