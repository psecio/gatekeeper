<?php

namespace Psecio\Gatekeeper;

class UserGroupCollection extends \Psecio\Gatekeeper\Collection\Mysql
{
    /**
     * Find the groups that the given user belongs to
     *
     * @param integer $userId User ID
     */
    public function findByUserId($userId)
    {
        $prefix = $this->getPrefix();
        $data = array('userId' => $userId);
        $sql = 'select g.* from '.$prefix.'groups g, '.$prefix.'user_group ug'
            .' where ug.user_id = :userId'
            .' and ug.group_id = g.id';

        $results = $this->getDb()->fetch($sql, $data);

        foreach ($results as $result) {
            $group = new GroupModel($this->getDb(), $result);
            $this->add($group);
        }
    }

    /**
     * Create relational records linking the user and group
     *
     * @param \Gatekeeper\UserModel $model Model instance
     * @param array $data Data to use in create
     */
    public function create($model, array $data)
    {
        foreach ($data as $group) {
            // Determine if it's an integer (permissionId) or name
            if (is_int($group) === true) {
                $where = 'id = :id';
                $dbData = array('id' => $group);
            } else {
                $where = 'name = :name';
                $dbData = array('name' => $group);
            }

            $sql = 'select id, name from '.$this->getPrefix().'groups where '.$where;
            $results = $this->getDb()->fetch($sql, $dbData);
            if (!empty($results) && count($results) == 1) {
                // exists, make the relation
                $model = new UserGroupModel(
                    $this->getDb(),
                    array('groupId' => $results[0]['id'], 'userId' => $model->id)
                );
                if ($this->getDb()->save($model) === true) {
                    $this->add($model);
                }
            }
        }
    }
}