<?php

namespace Psecio\Gatekeeper;

class GroupModelTest extends \PHPUnit_Framework_TestCase
{
    public function buildMock($return, $type = 'find')
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Stub')
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method($type)
            ->willReturn($return);

        return $ds;
    }

    /**
     * Test the addition of a user by ID to a group
     */
    public function testAddUserByIdValid()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds, array('id' => 1234));

        $this->assertTrue($group->addUser(1));
    }

    /**
     * Test the addiiton of a user by model to a group
     */
    public function testAddUserByModelValid()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds, array('id' => 1234));
        $user = new UserModel($ds, array('id' => 1234));

        $this->assertTrue($group->addUser($user));
    }

    /**
     * Test that a return of false is given when the group is invalid
     *     (missing an ID)
     */
    public function testAddUserInvalidGroup()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds);

        $this->assertFalse($group->addUser(1));
    }
}
