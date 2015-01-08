<?php

namespace Psecio\Gatekeeper;

class DataSourceTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the getter/setter for the data source configuration
     */
    public function testGetSetConfigFunction()
    {
        $config = array('test' => 'foo');
        $ds = new \Psecio\Gatekeeper\DataSource\Stub(array());

        $ds->setConfig($config);
        $this->assertEquals($ds->getConfig(), $config);
    }

    /**
     * Test the setting for the data source configuration in constructor
     */
    public function testGetSetConfigConstruct()
    {
        $config = array('test' => 'foo');
        $ds = new \Psecio\Gatekeeper\DataSource\Stub($config);
        $this->assertEquals($ds->getConfig(), $config);
    }
}