<?php

namespace Psecio\Gatekeeper;

class ThrottleModelTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the update of the login attempt properties on a throttle record
     */
    public function testUpdateLoginAttempts()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel($ds, array('attempts' => 1));

        $throttle->updateAttempts();
        $this->assertEquals(2, $throttle->attempts);
        $this->assertNotNull($throttle->lastAttempt);
    }

    /**
     * Test the changes made when a user is set back to allowed
     */
    public function testSetUserToAllow()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds, array('attempts' => 1, 'status' => ThrottleModel::STATUS_BLOCKED)
        );

        $throttle->allow();
        $this->assertEquals($throttle->status, ThrottleModel::STATUS_ALLOWED);
        $this->assertNotNull($throttle->statusChange);
    }

    /**
     * Test that a user is allowed after the default timeout (1 minute) has passed
     */
    public function testCheckDefaultTimeoutAllowUser()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds,
            array(
                'status' => ThrottleModel::STATUS_BLOCKED,
                'statusChange' => date('Y/m/d H:i:s', strtotime('-5 minutes'))
            )
        );
        $throttle->checkTimeout();

        $this->assertEquals($throttle->status, ThrottleModel::STATUS_ALLOWED);
    }

    /**
     * Test that, when the status change time hasn't reached the timeout, no
     *     status change is made.
     */
    public function testCheckDefaultTimeoutNoChange()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds,
            array(
                'status' => ThrottleModel::STATUS_BLOCKED,
                'statusChange' => date('Y/m/d H:i:s', strtotime('-10 seconds'))
            )
        );
        $throttle->checkTimeout();

        $this->assertEquals($throttle->status, ThrottleModel::STATUS_BLOCKED);
    }

    /**
     * Test that a user is allowed after the given timeout (-10 minutes) has passed
     */
    public function testCheckInputTimeoutAllowUser()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds,
            array(
                'status' => ThrottleModel::STATUS_BLOCKED,
                'statusChange' => date('Y/m/d H:i:s', strtotime('-12 minutes'))
            )
        );
        $throttle->checkTimeout('-10 minutes');

        $this->assertEquals($throttle->status, ThrottleModel::STATUS_ALLOWED);
    }

    /**
     * Check that when the user has reached or gone over the number of attempts
     *     (default is 5) they're set to blocked
     */
    public function testCheckAttemptsBlockUser()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds, array('attempts' => 6)
        );
        $throttle->checkAttempts();

        $this->assertEquals($throttle->status, ThrottleModel::STATUS_BLOCKED);
    }

    /**
     * Check that when the user hasn't reached or gone over the number of attempts
     *     (default is 5) they're not blocked
     */
    public function testCheckAttemptsNotBlockUser()
    {
        $ds = $this->buildMock(true, 'save');
        $throttle = new ThrottleModel(
            $ds, array('attempts' => 2, 'status' => ThrottleModel::STATUS_ALLOWED)
        );
        $throttle->checkAttempts();

        $this->assertEquals($throttle->status, ThrottleModel::STATUS_ALLOWED);
    }

    /**
     * Test that the find by user ID works correctly and populates the model
     */
    public function testFindByUserId()
    {
        $userId = 10;
        $data = array(
            array('userId' => $userId, 'attempts' => 1, 'status' => ThrottleModel::STATUS_ALLOWED)
        );

        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('fetch'))
            ->getMock();

        $ds->method('fetch')
            ->willReturn($data);

        $throttle = new ThrottleModel($ds);
        $throttle->findByUserId($userId);

        $this->assertEquals($throttle->attempts, 1);
        $this->assertEquals($throttle->userId, 10);
        $this->assertEquals($throttle->status, ThrottleModel::STATUS_ALLOWED);
    }
}