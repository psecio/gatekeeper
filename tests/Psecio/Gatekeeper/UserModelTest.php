<?php

namespace Psecio\Gatekeeper;

class UserModelTest extends \PHPUnit_Framework_TestCase
{
    public function buildFindMock($return)
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Stub')
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method('find')
            ->willReturn($return);

        return $ds;
    }

    /**
     * Test that a 0 is returned when no throttle record is found (null)
     */
    public function testFindThrottleAttemptsNoLogin()
    {
        $return = (object)array('attempts' => null);

        $ds = $this->buildFindMock($return);
        $this->user = new UserModel($ds);

        $result = $this->user->findAttemptsByUser(1);
        $this->assertEquals(0, $result);
    }

    /**
     * Check to be sure the blocked logic works correctly
     */
    public function testUserIsBlocked()
    {
        $return = (object)array('status' => ThrottleModel::STATUS_BLOCKED);

        $ds = $this->buildFindMock($return);
        $this->user = new UserModel($ds);

        $this->assertTrue($this->user->isBanned());
    }

    /**
     * Test the hash equality checking
     */
    public function testHashEquals()
    {
        $hash = sha1(mt_rand());
        $ds = $this->buildFindMock(null);
        $this->user = new UserModel($ds);

        $this->assertTrue($this->user->hash_equals($hash, $hash));
    }
}