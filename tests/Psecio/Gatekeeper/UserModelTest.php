<?php

namespace Psecio\Gatekeeper;

class UserModelTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test that a 0 is returned when no throttle record is found (null)
     */
    public function testFindThrottleAttemptsNoLogin()
    {
        $return = (object)array('attempts' => null);

        $ds = $this->buildMock($return);
        $user = new UserModel($ds);

        $result = $user->findAttemptsByUser(1);
        $this->assertEquals(0, $result);
    }

    /**
     * Check to be sure the blocked logic works correctly
     */
    public function testUserIsBlocked()
    {
        $return = (object)array('status' => ThrottleModel::STATUS_BLOCKED);

        $ds = $this->buildMock($return);
        $user = new UserModel($ds);

        $this->assertTrue($user->isBanned());
    }

    /**
     * Test that the user does have the permission
     */
    public function testUserHasPermission()
    {
        $return = (object)array('id' => 1234, 'name' => 'perm1');

        $ds = $this->buildMock($return);
        $user = new UserModel($ds);

        $this->assertTrue($user->hasPermission(1234));
    }

    public function testUserInGroup()
    {
        $return = (object)array('id' => 1234, 'name' => 'group1', 'groupId' => 1234);

        $ds = $this->buildMock($return);
        $user = new UserModel($ds);

        $this->assertTrue($user->inGroup(1234));
    }

    /**
     * Test that false is returned if the group found is invalid (no ID)
     */
    public function testUserInGroupInvalid()
    {
        $return = (object)array('id' => null, 'name' => 'group1', 'groupId' => 1234);

        $ds = $this->buildMock($return);
        $user = new UserModel($ds);

        $this->assertFalse($user->inGroup(1234));
    }

    /**
     * Test the clearing of the password reset handling
     */
    public function testClearPasswordResetCode()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));

        $user->resetCode = sha1(mt_rand());
        $user->resetCodeTimeout = date('m.d.Y H:i:s');

        $user->clearPasswordResetCode();
        $this->assertTrue(
            $user->resetCode === null && $user->resetCodeTimeout === null
        );
    }

    /**
     * Test that when the user isn't valid (no ID), false is returned
     */
    public function testClearPasswordResetCodeBadUser()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $this->assertFalse($user->clearPasswordResetCode());
    }

    /**
     * Test the hash checking on password resets for a valid situation
     */
    public function testCheckPasswordResetCodeValid()
    {
        $code = sha1(mt_rand());
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));
        $user->resetCode = $code;
        $user->resetCodeTimeout = date('Y/m/d H:i:s', strtotime('+1 day'));

        $this->assertTrue($user->checkResetPasswordCode($code));
        $this->assertTrue(
            $user->resetCode === null && $user->resetCodeTimeout === null
        );
    }

    /**
     * If an invalid user is defined (no ID), false is returned
     */
    public function testCheckPasswordResetCodeBadUser()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $this->assertFalse($user->checkResetPasswordCode('hash'));
    }

    /**
     * Test that an exception is thrown when the reset code is not present
     *
     * @expectedException \Psecio\Gatekeeper\Exception\PasswordResetInvalid
     */
    public function testCheckPasswordResetInvalidCode()
    {
        $code = sha1(mt_rand());
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));

        $user->checkResetPasswordCode('code');
    }

    /**
     * Test that an exception is thrown when the reset code has timed out
     *
     * @expectedException \Psecio\Gatekeeper\Exception\PasswordResetTimeout
     */
    public function testCheckPasswordResetCodeTimeout()
    {
        $code = sha1(mt_rand());
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));
        $user->resetCode = $code;
        $user->resetCodeTimeout = date('Y/m/d H:i:s', strtotime('-1 day'));

        $this->assertTrue($user->checkResetPasswordCode($code));
    }

    /**
     * Test the password code generation
     */
    public function testPasswordResetCodeGeneration()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));

        // Defaults to 80, use a custom 100 too
        $this->assertEquals(strlen($user->getResetPasswordCode()), 80);
        $this->assertEquals(strlen($user->getResetPasswordCode(100)), 100);
    }

    /**
     * Test that an invalid user (no ID) returns false on code generation
     *  (the user is required as it saves the code when it generates it)
     */
    public function testPasswordResetCodeGenerationBadUser()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        // Defaults to 80, use a custom 100 too
        $this->assertFalse($user->getResetPasswordCode());
    }

    /**
     * Test that a user is successfully deactivated (status change)
     */
    public function testDeactivateUserValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));

        $user->deactivate();
        $this->assertEquals($user->status, $user::STATUS_INACTIVE);
    }

    /**
     * Test that false is returned when the user is invalid (no ID)
     */
    public function testDeactivateUserInvalid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $this->assertFalse($user->deactivate());
    }

    /**
     * Test that a user is successfully activated (status change)
     */
    public function testActivateUserValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds, array('id' => 1234));

        $user->activate();
        $this->assertEquals($user->status, $user::STATUS_ACTIVE);
    }

    /**
     * Test that false is returned when the user is invalid (no ID)
     */
    public function testActivateUserInvalid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $this->assertFalse($user->activate());
    }

    /**
     * Test that a group can be added by using a Group ID
     */
    public function testAddGroupByIdValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $this->assertTrue($user->addGroup(1));
    }

    /**
     * Test that a group can be added by using a Group model
     */
    public function testAddGroupByModelValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);

        $group = new GroupModel($ds, array('id' => 1234));
        $this->assertTrue($user->addGroup($group));
    }

    /**
     * Test that a permission can be added by ID
     */
    public function testAddPermissionByIdValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);
        $this->assertTrue($user->addPermission(1));
    }

    /**
     * Test that a permission can be added by Permission model instance
     */
    public function testAddPermissionByModelValid()
    {
        $ds = $this->buildMock(true, 'save');
        $user = new UserModel($ds);
        $perm = new PermissionModel($ds, array('id' => 1234));

        $this->assertTrue($user->addPermission($perm));
    }

    /**
     * Test the location of a record by username, mocked fetch
     */
    public function testFindByUsername()
    {
        $username = 'ccornutt';
        $data = array(
            array('username' => $username)
        );
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('fetch'))
            ->getMock();

        $ds->method('fetch')
            ->willReturn($data);

        $user = new UserModel($ds);
        $user->findByUsername($username);

        $this->assertEquals($user->username, $username);
    }

    /**
     * Test that a password needs a rehash
     *     In this case, it's a plain-text password that needs hashing
     */
    public function testPasswordNeedsRehash()
    {
        $password = 'test1234';
        $ds = $this->buildMock(true);
        $user = new UserModel($ds);

        $postPassword = $user->prePassword($password);
        $this->assertNotEquals($password, $postPassword);
    }

    /**
     * Test that a true is returned when you add valid permissions by ID
     */
    public function testGrantPermissionsByIdValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $perms = array(1, 2, 3);
        $user = new UserModel($ds);
        $this->assertTrue($user->grantPermissions($perms));
    }

    /**
     * Try to add a set of permissions with a failed return (false)
     */
    public function testGrantPermissionsByIdInalid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(false);
        $user = new UserModel($ds);
        $this->assertFalse($user->grantPermissions(array(1, 2, 3)));
    }

    /**
     * Test that it understsands how to grant permissions by model instances too
     */
    public function testGrantPermissionsByModelValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $perms = array(
            new PermissionModel($ds, array('id' => 1)),
            new PermissionModel($ds, array('id' => 2)),
            new PermissionModel($ds, array('id' => 3))
        );
        $user = new UserModel($ds);
        $this->assertTrue($user->grantPermissions($perms));
    }

    /**
     * Test the addition of group accesss by ID
     */
    public function testGrantGroupsByIdValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $groups = array(1, 2, 3);
        $user = new UserModel($ds);
        $this->assertTrue($user->grantGroups($groups));
    }

    /**
     * Test the addition of group accesss by ID with failure
     */
    public function testGrantGroupsByIdInvalid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(false);

        $user = new UserModel($ds);
        $this->assertFalse($user->grantGroups(array(1, 2, 3)));
    }

    /**
     * Test the addition of group accesss by Group model
     */
    public function testGrantGroupsByModelValid()
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $ds->method('save')->willReturn(true);

        $groups = array(
            new GroupModel($ds, array('id' => 1)),
            new GroupModel($ds, array('id' => 2)),
            new GroupModel($ds, array('id' => 3))
        );
        $user = new UserModel($ds);
        $this->assertTrue($user->grantGroups($groups));
    }
}