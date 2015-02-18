<?php

namespace Psecio\Gatekeeper;

class UserPermissionCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of permissions of a member by ID
     */
    public function testFindPermissionsByUserId()
    {
        $userId = 1;
        $return = array(
            array('name' => 'perm1', 'description' => 'Permission #1'),
            array('name' => 'perm2', 'description' => 'Permission #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $permissions = new UserPermissionCollection($ds);

        $permissions->findByUserId($userId);
        $this->assertCount(2, $permissions);
    }

    /**
     * Test the creation of a user permission relation by permission ID
     */
    public function testCreateUserPermissionById()
    {
        $return = array(
            array('id' => 1, 'name' => 'perm5')
        );

        $ds = $this->buildMock($return, 'fetch');
        $permissions = new UserPermissionCollection($ds);
        $model = new UserPermissionModel($ds);

        $data = array(1, 2, 3);
        $permissions->create($model, $data);
    }

    /**
     * Test the creation of a user permission relation by permission model instance
     */
    public function testCreateUserPermissionByModel()
    {
        $return = array(
            array('id' => 1, 'name' => 'perm5'),
            array('id' => 2, 'name' => 'perm6')
        );

        $ds = $this->buildMock($return, 'fetch');
        $permissions = new UserPermissionCollection($ds);
        $model = new UserPermissionModel($ds);

        $data = array(
            new PermissionModel($ds, array('id' => 1)),
            new PermissionModel($ds, array('id' => 2)),
            new PermissionModel($ds, array('id' => 3))
        );
        $permissions->create($model, $data);
    }
}