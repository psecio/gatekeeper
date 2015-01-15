<?php

namespace Psecio\Gatekeeper;

class GroupModelTest extends \Psecio\Gatekeeper\Base
{
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

    /**
     * Test adding a permission by ID
     */
    public function testAddPermissionByIdValid()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds, array('id' => 1234));

        $this->assertTrue($group->addPermission(1));
    }

    /**
     * Test adding a permission by a model instance
     */
    public function testAddPermissionByModelValid()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds, array('id' => 1234));
        $perm = new PermissionModel($ds, array('id' => 1234));

        $this->assertTrue($group->addPermission($perm));
    }

    /**
     * Test adding a permission by ID on an invalid group
     */
    public function testAddPermissionByIdInvalid()
    {
        $ds = $this->buildMock(true, 'save');
        $group = new GroupModel($ds);

        $this->assertFalse($group->addPermission(1));
    }

    /**
     * Test that a user is in a group
     */
    public function testUserInGroup()
    {
        $data = array(
            array('name' => 'group1', 'id' => 1234)
        );
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('fetch'))
            ->getMock();

        $ds->method('fetch')
            ->willReturn($data);

        $group = new GroupModel($ds);

        $this->assertTrue($group->inGroup(1));
    }

    /**
     * Test adding a child by group model instance
     */
    public function testAddChild()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')
            ->willReturn(true);

        $group1 = new GroupModel($ds, array('id' => 1234));
        $group2 = new GroupModel($ds);

        $this->assertTrue($group1->addChild($group2));
    }

    /**
     * Test that false is returned when you try to add a child to a
     *     group not yet loaded
     */
    public function testAddChildNoId()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->getMock();

        $group1 = new GroupModel($ds);
        $group2 = new GroupModel($ds);

        $this->assertFalse($group1->addChild($group2));
    }
}
