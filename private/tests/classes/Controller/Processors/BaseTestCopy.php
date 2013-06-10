<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'BaseStub.php';

class Controller_Processors_BaseTestCopy extends TestCase
{

    protected $xmlDataSet = 'base-copy';

    public function setUp()
    {
        parent::setUp();

        mkdir($this->genNumbers['PathFoo']);
        file_put_contents(
                $this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'bar', 'hello-world'
        );
        mkdir($this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'baz');
        file_put_contents(
                $this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'baz' . DIRECTORY_SEPARATOR . 'hello',
                'hello-world2'
        );

        mkdir($this->genNumbers['PathBar']);
        file_put_contents(
                $this->genNumbers['PathBar'] . DIRECTORY_SEPARATOR . 'file', 'hello-world'
        );
        mkdir($this->genNumbers['PathBar'] . DIRECTORY_SEPARATOR . 'dir');
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
     * @covers Controller_Processors_Base::action_copy
     */
    public function testCopy()
    {
        $target = new Controller_Processors_BaseStub();
        $target->request->setParam('id', $this->genNumbers['build1']);

        $target->action_copy();

        $basedir = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->genNumbers['build1']
                . DIRECTORY_SEPARATOR . 'basestub' . DIRECTORY_SEPARATOR;
        $this->assertTrue(is_dir($basedir));

        // file
        $this->assertTrue(is_readable($basedir . 'foo.html'));
        $this->assertEquals('hello-world', file_get_contents($basedir . 'foo.html'));

        // dir
        $this->assertTrue(is_readable($basedir . 'hello'));
        $this->assertEquals('hello-world2', file_get_contents($basedir . 'hello'));

        // dir2
        $this->assertTrue(is_dir($basedir . 'subdir'));
        $this->assertTrue(is_readable($basedir . 'subdir' . DIRECTORY_SEPARATOR . 'hello'));
        $this->assertEquals('hello-world2', file_get_contents($basedir . 'subdir' . DIRECTORY_SEPARATOR . 'hello'));
    }

    /**
     * @covers Controller_Processors_Base::action_copy
     */
    public function testCopyFail()
    {
        $target = new Controller_Processors_BaseStub();
        $target->request->setParam('id', $this->genNumbers['build2']);

        $target->action_copy();

        $basedir = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->genNumbers['build2']
                . DIRECTORY_SEPARATOR . 'basestub' . DIRECTORY_SEPARATOR;
        $this->assertFalse(is_dir($basedir));
    }
}