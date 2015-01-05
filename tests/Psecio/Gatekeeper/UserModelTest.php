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
}