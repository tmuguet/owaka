<?php
require_once dirname(__FILE__) . DIR_SEP . '..' . DIR_SEP . '..' . DIR_SEP . '_stubs' . DIR_SEP . 'ProcessorStub.php';

class Task_Processor_CopyTest extends TestCase
{

    protected $xmlDataSet = 'processor-copy';

    public function setUp()
    {
        parent::setUp();

        mkdir($this->genNumbers['PathFoo']);
        file_put_contents(
                $this->genNumbers['PathFoo'] . DIR_SEP . 'bar', 'hello-world'
        );
        mkdir($this->genNumbers['PathFoo'] . DIR_SEP . 'baz');
        file_put_contents(
                $this->genNumbers['PathFoo'] . DIR_SEP . 'baz' . DIR_SEP . 'hello', 'hello-world2'
        );

        mkdir($this->genNumbers['PathBar']);
        file_put_contents(
                $this->genNumbers['PathBar'] . DIR_SEP . 'file', 'hello-world'
        );
        mkdir($this->genNumbers['PathBar'] . DIR_SEP . 'dir');
    }

    public function tearDown()
    {
        parent::tearDown();

        File::rrmdir($this->genNumbers['PathFoo']);
        File::rrmdir($this->genNumbers['PathBar']);
        if (is_dir(APPPATH . 'reports')) {
            File::rrmdir(APPPATH . 'reports');
        }
    }

    /**
     * @covers Task_Processor_Copy::_execute
     * @covers Task_Processor_Copy::run
     * @covers Task_Processor_Copy::copy
     */
    public function testCopy()
    {
        $copy = Minion_Task::factory(
                        array(
                            'task'      => 'Processor:Copy',
                            'id'        => $this->genNumbers['build1'],
                            'processor' => 'ProcessorStub'
                        )
        );
        $copy->execute();

        $basedir = APPPATH . 'reports' . DIR_SEP . $this->genNumbers['build1']
                . DIR_SEP . 'processorstub' . DIR_SEP;
        $this->assertTrue(is_dir($basedir));

        // file
        $this->assertTrue(is_readable($basedir . 'foo.html'));
        $this->assertEquals('hello-world', file_get_contents($basedir . 'foo.html'));

        // file2
        $this->assertFalse(is_readable($basedir . 'bar.html'));

        // dir
        $this->assertTrue(is_readable($basedir . 'hello'));
        $this->assertEquals('hello-world2', file_get_contents($basedir . 'hello'));

        // dir2
        $this->assertTrue(is_dir($basedir . 'subdir'));
        $this->assertTrue(is_readable($basedir . 'subdir' . DIR_SEP . 'hello'));
        $this->assertEquals('hello-world2', file_get_contents($basedir . 'subdir' . DIR_SEP . 'hello'));
    }

    /**
     * @covers Task_Processor_Copy::_execute
     * @covers Task_Processor_Copy::runAll
     * @covers Task_Processor_Copy::copy
     */
    public function testCopyFail()
    {
        $copy = Minion_Task::factory(
                        array('task'  => 'Processor:Copy', 'build' => ORM::factory('Build', $this->genNumbers['build2']))
        );
        $copy->execute();

        $basedir = APPPATH . 'reports' . DIR_SEP . $this->genNumbers['build2'];
        $this->assertFalse(is_dir($basedir));
    }
}