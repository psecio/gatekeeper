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
	 * 	with the type defined (array index)
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
	 * 	with types defined (array index)
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
	 * 	in expression evaluation
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
	 * 	the expression fails
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
	 * 	is currently loaded
	 *
	 * @expectedException \Psecio\Gatekeeper\Exception\InvalidExpressionException
	 */
	public function testFailureWhenNoPolicyLoaded()
	{
		$ds = $this->buildMock(true);
		$policy = new PolicyModel($ds);

		$this->policy->evaluate([]);
	}
}