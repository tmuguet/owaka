<?php

class Controller_Processor_PhpdocTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build2'];
        $this->target->request->setParam('id', $this->buildId);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Controller_Processor_Phpdoc::process
     */
    public function testProcess()
    {
        $this->target->process($this->buildId);
    }
}