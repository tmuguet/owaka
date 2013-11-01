<?php
require_once dirname(__FILE__) . DIR_SEP . '_stubs' . DIR_SEP . 'PostactionStub.php';

class PostactionTest extends TestCase
{

    protected $xmlDataSet = 'postaction';

    /**
     * @covers Postaction::_getName
     */
    public function testGetName()
    {
        $target = new Postaction_PostactionStub();
        $this->assertEquals('postactionstub', $target->_getName());
    }

    /**
     * @covers Postaction::projectParameters
     */
    public function testProjectParameters()
    {
        $expectedFound    = array(
            'recipients'  => 'foo',
            'on_error'    => 0,
            'on_unstable' => 1,
            'on_ok'       => 1
        );
        $expectedNotFound = array(
            'recipients'  => '',
            'on_error'    => 1,
            'on_unstable' => 1,
            'on_ok'       => 1
        );

        $this->assertEquals(
                $expectedFound, Postaction_PostactionStub::projectParameters($this->genNumbers['ProjectFoo'])
        );
        $this->assertEquals(
                $expectedNotFound, Postaction_PostactionStub::projectParameters($this->genNumbers['ProjectBar'])
        );
    }
}