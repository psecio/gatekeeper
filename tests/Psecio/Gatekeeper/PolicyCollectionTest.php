<?php

namespace Psecio\Gatekeeper;

class PolicyCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of policies in the system
     */
    public function testFindPoliciesList()
    {
        $return = array(
            array('name' => 'policy1', 'expression' => 'test expression'),
            array('name' => 'policy2', 'expression' => '"group1" in user.groups.getName()')
        );

        $ds = $this->buildMock($return, 'fetch');
        $policies = new PolicyCollection($ds);

        $policies->getList();
        $this->assertCount(2, $policies);

        $policies = $policies->toArray();
        $this->assertTrue($policies[0] instanceof PolicyModel);
    }

    /**
     * Test the location of policies in the system
     */
    public function testFindPoliciesListLimit()
    {
        $return = array(
            array('name' => 'policy1', 'expression' => 'test expression')
        );

        $ds = $this->buildMock($return, 'fetch');
        $policies = new PolicyCollection($ds);

        $policies->getList(1);
        $this->assertCount(1, $policies);

        $policies = $policies->toArray();
        $this->assertTrue($policies[0] instanceof PolicyModel);
    }
}