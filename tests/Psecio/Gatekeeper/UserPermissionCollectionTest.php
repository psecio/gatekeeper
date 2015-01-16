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
}