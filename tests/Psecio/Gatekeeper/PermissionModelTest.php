<?php

namespace Psecio\Gatekeeper;

class PermissionModelTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the addition of a child by ID to a permission
     */
    public function testAddChildByIdValid()
    {
        $ds = $this->buildMock(true, 'save');
        $perm = new PermissionModel($ds, array('id' => 1234));

        $this->assertTrue($perm->addChild(1));
    }

    /**
     * Test the addition of a child by model instance to a permission
     */
    public function testAddChildByModelValid()
    {
        $ds = $this->buildMock(true, 'save');
        $perm1 = new PermissionModel($ds, array('id' => 1234));
        $perm2 = new PermissionModel($ds, array('id' => 4321));

        $this->assertTrue($perm1->addChild($perm2));
    }

    /**
     * Test the invalid addition of a permission by ID
     */
    public function testAddChildByIdInvalid()
    {
        $ds = $this->buildMock(true, 'save');
        $perm = new PermissionModel($ds);

        $this->assertFalse($perm->addChild(1));
    }

    /**
     * Test the valid addition of a child by ID
     */
    public function testRemoveChildByIdValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('find', 'delete'))
            ->getMock();

        $perm = new PermissionModel($ds, array('id' => 1234));

        $ds->method('find')->willReturn($perm);
        $ds->method('delete')->willReturn(true);

        $this->assertTrue($perm->removeChild(1));
    }

    /**
     * Test the valid removal of a child by model instance
     */
    public function testRemoveChildByModelValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('find', 'delete'))
            ->getMock();

        $perm1 = new PermissionModel($ds, array('id' => 1234));
        $perm2 = new PermissionModel($ds);

        $ds->method('find')->willReturn($perm1);
        $ds->method('delete')->willReturn(true);

        $this->assertTrue($perm1->removeChild($perm2));
    }

    /**
     * Test the invalid removal of a child by ID
     */
    public function testRemoveChildByIdInvalid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->getMock();

        $perm = new PermissionModel($ds);
        $this->assertFalse($perm->removeChild(1));
    }

    /**
     * Test that a permission is not expired
     */
    public function testPermissionNotExpired()
    {
        $ds = $this->buildMock(true);
        $perm = new PermissionModel($ds, [
            'id' => 1234,
            'expire' => strtotime('+1 day')
        ]);

        $this->assertFalse($perm->isExpired());
    }

    /**
     * Test that a permission is marked as expired
     */
    public function testPermissionIsExpired()
    {
        $ds = $this->buildMock(true);
        $perm = new PermissionModel($ds, [
            'id' => 1234,
            'expire' => strtotime('-1 day')
        ]);

        $this->assertTrue($perm->isExpired());
    }
}