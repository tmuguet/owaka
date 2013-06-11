<?php

class Controller_Processors_PhpdocTest extends TestCase_Processors
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build1'];
        $this->target->request->setParam('id', $this->buildId);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Controller_Processors_Phpdoc::process
     */
    public function testProcess()
    {
        $this->target->process($this->buildId);
    }
}