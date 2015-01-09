<?php

namespace Psecio\Gatekeeper\DataSource;

include_once __DIR__.'/../MockPdo.php';

class MysqlTest extends \Psecio\Gatekeeper\Base
{
    public function testCreatePdoOnConstruct()
    {
        $config = array(
            'username' => 'foo',
            'password' => 'bar',
            'name' => 'dbname',
            'host' => '127.0.0.1'
        );
        $pdo = $this->getMockBuilder('\Psecio\Gatekeeper\MockPdo')->getMock();

        $mysql = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->setConstructorArgs(array($config, $pdo))
            ->setMethods(array('buildPdo'))
            ->getMock();

        $this->assertEquals($mysql->getDb(), $pdo);
    }
}