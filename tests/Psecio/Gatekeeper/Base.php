<?php

namespace Psecio\Gatekeeper;

class Base extends \PHPUnit_Framework_TestCase
{
    public function buildMock($return, $type = 'find')
    {
        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Stub')
            ->setConstructorArgs(array(array()))
            ->getMock();
        $ds->method($type)
            ->willReturn($return);

        return $ds;
    }
}