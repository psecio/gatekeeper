<?php

namespace Psecio\Gatekeeper;

class GroupCollectionTest extends \Psecio\Gatekeeper\Base
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
        $groups = new GroupCollection($ds);

        $groups->findByUserId($userId);
        $this->assertCount(2, $groups);
    }

    /**
     * Test the location and conversion of child groups into instances
     */
    public function testFindChildrenGroups()
    {
        $groupId = 1;
        $return = array(
            array('name' => 'group1', 'description' => 'Group #1'),
            array('name' => 'group2', 'description' => 'Group #2')
        );

        $ds = $this->buildMock($return, 'fetch');
        $groups = new GroupCollection($ds);

        $groups->findChildrenByGroupId($groupId);
        $this->assertCount(2, $groups);

        $groups = $groups->toArray();
        $this->assertTrue($groups[0] instanceof GroupModel);
    }
}