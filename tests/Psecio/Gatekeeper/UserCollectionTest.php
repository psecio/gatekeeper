<?php

namespace Psecio\Gatekeeper;

class UserCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of users of a group by ID
     */
    public function testFindUsersByGroupId()
    {
        $groupId = 1;
        $return = array(
            array('username' => 'testuser1', 'email' => 'testuser1@gmail.com'),
            array('username' => 'testuser2', 'email' => 'testuser2@gmail.com'),
            array('username' => 'testuser3', 'email' => 'testuser3@gmail.com')
        );

        $ds = $this->buildMock($return, 'fetch');
        $users = new UserCollection($ds);

        $users->findByGroupId($groupId);
        $this->assertCount(3, $users);
        $this->assertEquals($users->toArray()[1]->username, 'testuser2');
    }
}