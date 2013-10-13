<?php

class Processor_PhpdocTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->build = ORM::factory('Build', $this->genNumbers['build2']);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Processor_Phpdoc::process
     */
    public function testProcess()
    {
        $this->target->process($this->build);
    }
}