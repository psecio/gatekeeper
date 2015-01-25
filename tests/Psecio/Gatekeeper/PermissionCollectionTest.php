<?php

namespace Psecio\Gatekeeper;

class PermissionCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of permissions of a group by ID
     */
    public function testFindPermissionsByGroupId()
    {
        $groupId = 1;
        $return = array(
            array('name' => 'perm1', 'description' => 'Permission #1'),
            array('name' => 'perm2', 'description' => 'Permission #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $permissions = new PermissionCollection($ds);

        $permissions->findByGroupId($groupId);
        $this->assertCount(2, $permissions);
    }

    /**
     * Test to ensure permissions are returned when searched by child id
     */
    public function testFindChidlrenByPermissionId()
    {
        $permId = 1;
        $return = array(
            array('name' => 'perm1', 'description' => 'Permission #1'),
            array('name' => 'perm2', 'description' => 'Permission #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $permissions = new PermissionCollection($ds);

        $permissions->findChildrenByPermissionId($permId);
        $this->assertCount(2, $permissions);
    }
}