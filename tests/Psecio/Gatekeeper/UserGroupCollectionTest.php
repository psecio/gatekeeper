<?php

namespace Psecio\Gatekeeper;

class UserGroupCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of groups a member is a part of
     */
    public function testFindGroupsByUserId()
    {
        $userId = 1;
        $return = array(
            array('name' => 'group1', 'description' => 'Group #1'),
            array('name' => 'group2', 'description' => 'Group #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $groups = new UserGroupCollection($ds);

        $groups->findByUserId($userId);
        $this->assertCount(2, $groups);
    }
}