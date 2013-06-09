<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'BaseStub.php';

class Controller_Processors_BaseTest extends TestCase
{

    protected $xmlDataSet = 'base';
    private $_basePathReports;

    public function setUp()
    {
        parent::setUp();

        mkdir($this->genNumbers['PathFoo']);
        file_put_contents($this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'bar', 'hello-world');

        $this->_basePathReports = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->genNumbers['build1']
                . DIRECTORY_SEPARATOR . 'basestub' . DIRECTORY_SEPARATOR;

        if (!file_exists($this->_basePathReports)) {
            mkdir($this->_basePathReports, 0700, true);
        }
        file_put_contents($this->_basePathReports . 'foo.html', 'hello HTML world');
    }

    public function tearDown()
    {
        parent::tearDown();

        File::rrmdir($this->genNumbers['PathFoo']);
        File::rrmdir(APPPATH . 'reports');
    }

    /**
     * @covers Controller_Processors_Base::_getName
     */
    public function testGetName()
    {
        $target = new Controller_Processors_BaseStub();
        $this->assertEquals('basestub', $target->_getName());
    }

    /**
     * @covers Controller_Processors_Base::_getReportName
     */
    public function testGetReportName()
    {
        $target = new Controller_Processors_BaseStub();
        $this->assertEquals('basestub_hello', $target->_getReportName('hello'));
    }

    /**
     * @covers Controller_Processors_Base::_getInputReportCompletePath
     */
    public function testGetInputReportCompletePath()
    {
        $target = new Controller_Processors_BaseStub();
        $this->assertEquals(
                $this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'bar',
                $target->_getInputReportCompletePath($this->genNumbers['build1'], 'foo'), "Nominal case"
        );
        $this->assertNull(
                $target->_getInputReportCompletePath($this->genNumbers['build1'], 'bat'), "Should not exist in FS"
        );
        $this->assertNull(
                $target->_getInputReportCompletePath($this->genNumbers['build1'], 'bar'), "Should not exist in DB"
        );
    }

    /**
     * @covers Controller_Processors_Base::_getReportCompletePath
     */
    public function testGetReportCompletePath()
    {
        $target = new Controller_Processors_BaseStub();
        $this->assertEquals(
                $this->_basePathReports . 'foo.html',
                $target->_getReportCompletePath($this->genNumbers['build1'], 'file'), "Nominal case"
        );
        $this->assertEquals(
                substr($this->_basePathReports, 0, -1),
                       $target->_getReportCompletePath($this->genNumbers['build1'], 'dir'),
                                                       "Nominal case with directory"
        );
        $this->assertNull(
                $target->_getReportCompletePath($this->genNumbers['build1'], 'file2', "Should not exist in FS")
        );
        $this->assertNull(
                $target->_getReportCompletePath($this->genNumbers['build1'], 'foo', "Should not exist in DB")
        );
    }
}