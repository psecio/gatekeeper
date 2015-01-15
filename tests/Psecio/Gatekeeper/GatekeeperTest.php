<?php

namespace Psecio\Gatekeeper;

class GatekeeperTest extends \Psecio\Gatekeeper\Base
{
    public function setUp()
    {
        // $config = array('test' => 1);
        // Gatekeeper::init(null, $config);
    }
    public function tearDown()
    {

    }

    /**
     * Test the enable/disable of throttling
     */
    public function testEnableDisableThrottle()
    {
        Gatekeeper::disableThrottle();
        $this->assertFalse(Gatekeeper::throttleStatus());

        Gatekeeper::enableThrottle();
        $this->assertTrue(Gatekeeper::throttleStatus());
    }

    /**
     * Test getting the user's throttle information (model instance)
     */
    public function testGetUserThrottle()
    {
        $userId = 42;

        // This is our model that will be returned
        $ds = $this->buildMock(null);
        $throttle1 = new ThrottleModel($ds, array('userId' => $userId));

        $ds = $this->buildMock($throttle1, 'find');
        $throttle = new ThrottleModel($ds);

        $gk = $this->getMockBuilder('\Psecio\Gatekeeper\Gatekeeper')
            ->setMethods(array('findThrottleByUserId'))
            ->getMock();

        $config = array('name' => 'test');
        $gk::init(null, $config, $ds);

        $gk->method('findThrottleByUserId')
            ->willReturn($throttle);

        $result = $gk::getUserThrottle($userId);
        $this->assertEquals(42, $result->userId);
    }
}