<?php

namespace Psecio\Gatekeeper;

class PolicyModelTest extends \Psecio\Gatekeeper\Base
{
    private $policy;

    public function setUp()
    {
        $ds = $this->buildMock(true, 'save');
        $this->policy = new PolicyModel($ds, ['id' => 1]);
    }
    public function tearDown()
    {
        unset($this->policy);
    }

    /**
     * Test that the evaluation passes for a single object
     *  with the type defined (array index)
     */
    public function testPolicyEvaluateSingleObject()
    {
        $this->policy->load(['expression' => 'user.test == "foo"']);

        $user = (object)['test' => 'foo'];
        $data = ['user' => $user];

        $this->assertTrue($this->policy->evaluate($data));
    }

    /**
     * Test that the evaluation passes with multiple objects
     *  with types defined (array index)
     */
    public function testPolicyEvaluateMultipleObject()
    {
        $this->policy->load([
            'expression' => 'user.test == "foo" and group.name == "test"'
        ]);

        $data = [
            'user' => (object)['test' => 'foo'],
            'group' => (object)['name' => 'test']
        ];
        $this->assertTrue($this->policy->evaluate($data));
    }

    /**
     * Test that the resolution of type by classname works
     *  in expression evaluation
     */
    public function testPolicyEvaluateObjectByClassname()
    {
        $ds = $this->buildMock(true);
        $user = new UserModel($ds, ['username' => 'ccornutt']);

        $this->policy->load([
            'expression' => 'user.username == "ccornutt"'
        ]);
        $data = [$user];
        $this->assertTrue($this->policy->evaluate($data));
    }

    /**
     * Test that an testInvalidExpressionFailure is thrown when
     *  the expression fails
     *
     * @expectedException \Psecio\Gatekeeper\Exception\InvalidExpressionException
     */
    public function testInvalidExpressionFailure()
    {
        $this->policy->load([
            'expression' => 'user.username == "ccornutt"'
        ]);
        $data = [];
        $this->policy->evaluate($data);
    }

    /**
     * Test that an exception is thrown when no policy expression (by ID)
     *  is currently loaded
     *
     * @expectedException \Psecio\Gatekeeper\Exception\InvalidExpressionException
     */
    public function testFailureWhenNoPolicyLoaded()
    {
        $ds = $this->buildMock(true);
        $policy = new PolicyModel($ds);

        $this->policy->evaluate([]);
    }

    /**
     * Test the expression matching when a method is involved
     *  In this case, get a User's groups list and return just
     *  the names to see if a group exists/doesn't exist
     */
    public function testPolicyEvaluateObjectWithFunction()
    {
        $ds = $this->buildMock(true);
        $groups = new GroupCollection($ds);
        $group = new GroupModel($ds, ['name' => 'group1']);
        $groups->add($group);

        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Stub')
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method('fetch')
            ->willReturn($groups->toArray(true));

        $user = new UserModel($ds, ['username' => 'ccornutt42']);

        // "group1" does exist
        $this->policy->load(['expression' => '"group1" in user.groups.getName()']);
        $this->assertTrue($this->policy->evaluate($user));

        // "group2" does not exist
        $this->policy->load(['expression' => '"group2" in user.groups.getName()']);
        $this->assertFalse($this->policy->evaluate($user));
    }
}