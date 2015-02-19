<?php

namespace Psecio\Gatekeeper\Restrict;

class IpTest extends \Psecio\Gatekeeper\Base
{
	/**
	 * Test that an exception is thrown when the remote address
	 * 	cannot be found
	 *
	 * @expectedException \Psecio\Gatekeeper\Exception\DataNotFoundException
	 */
	public function testEvaluateNoAddress()
	{
		$ip = new Ip(array());
		$ip->evaluate();
	}

	/**
	 * Test that an "Allowed" valid match returns true
	 */
	public function testEvaluateAllowMatch()
	{
		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';
		$config = array(
			'ALLOW' => array('192.168.*')
		);

		$ip = new Ip($config);
		$this->assertTrue($ip->evaluate());
	}

	/**
	 * Test that a false is returned when a "deny" match is found
	 */
	public function testEvaluateDenyMatch()
	{
		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';
		$config = array(
			'DENY' => array('192.168.*')
		);

		$ip = new Ip($config);
		$this->assertFalse($ip->evaluate());
	}

	/**
	 * Test that a false is returned on an "allow" with
	 * 	no match
	 */
	public function testEvaluateAllowNoMatch()
	{
		$_SERVER['REMOTE_ADDR'] = '192.168.1.1';
		$config = array(
			'ALLOW' => array('10.0.*')
		);

		$ip = new Ip($config);
		$this->assertFalse($ip->evaluate());
	}
}