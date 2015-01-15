<?php

namespace Psecio\Gatekeeper\DataSource;

include_once __DIR__.'/../MockPdo.php';
include_once __DIR__.'/../MockModel.php';

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

    /**
     * Test the getter/setter of the DB instance
     *     (just uses a basic object)
     */
    public function testGetSetDatabaseInstance()
    {
        $mysql = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('buildPdo'))
            ->getMock();

        $db = (object)array('test' => 'foo');
        $mysql->setDb($db);

        $this->assertEquals($mysql->getDb(), $db);
    }

    /**
     * Test getting the table name for the model instance
     */
    public function testGetTableName()
    {
        $config = array();
        $pdo = $this->getMockBuilder('\Psecio\Gatekeeper\MockPdo')->getMock();

        $ds = $this->getMockBuilder('\Psecio\Gatekeeper\DataSource\Mysql')
            ->setConstructorArgs(array($config, $pdo))
            ->setMethods(array('buildPdo'))
            ->getMock();

        $mysql = new \Psecio\Gatekeeper\MockModel($ds);
        $this->assertEquals('test', $mysql->getTableName());
    }
}