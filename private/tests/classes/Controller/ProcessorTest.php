<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'ProcessorStub.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'ProcessorStub2.php';

class Controller_ProcessorTest extends TestCase
{

    protected $xmlDataSet = 'processor';
    private $_basePathReports;

    public function setUp()
    {
        parent::setUp();

        mkdir($this->genNumbers['PathFoo']);
        file_put_contents($this->genNumbers['PathFoo'] . DIRECTORY_SEPARATOR . 'bar', 'hello-world');

        $this->_basePathReports = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->genNumbers['build1']
                . DIRECTORY_SEPARATOR . 'processorstub' . DIRECTORY_SEPARATOR;

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
     * @covers Controller_Processor::inputReports
     * @expectedException Exception
     * @expectedExceptionMessage Not implemented
     */
    public function testInputReports()
    {
        Controller_Processor::inputReports();
    }

    /**
     * @covers Controller_Processor::_getName
     */
    public function testGetName()
    {
        $target = new Controller_Processor_ProcessorStub();
        $this->assertEquals('processorstub', $target->_getName());
    }

    /**
     * @covers Controller_Processor::_getReportName
     */
    public function testGetReportName()
    {
        $target = new Controller_Processor_ProcessorStub();
        $this->assertEquals('processorstub_hello', $target->_getReportName('hello'));
    }

    /**
     * @covers Controller_Processor::projectParameters
     */
    public function testProjectParameters()
    {
        $expectedFound    = array(
            'threshold_errors_error'    => 10,
            'threshold_errors_unstable' => 1
        );
        $expectedNotFound = array(
            'threshold_errors_error'    => 1,
            'threshold_errors_unstable' => -1
        );

        $this->assertEquals(
                $expectedFound, Controller_Processor_ProcessorStub::projectParameters($this->genNumbers['ProjectFoo'])
        );
        $this->assertEquals(
                $expectedNotFound,
                Controller_Processor_ProcessorStub::projectParameters($this->genNumbers['ProjectBar'])
        );
    }

    /**
     * @covers Controller_Processor::_getInputReportCompletePath
     */
    public function testGetInputReportCompletePath()
    {
        $target = new Controller_Processor_ProcessorStub();
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
     * @covers Controller_Processor::getReportCompletePath
     */
    public function testGetReportCompletePath()
    {
        $target = new Controller_Processor_ProcessorStub();
        $this->assertEquals(
                $this->_basePathReports . 'foo.html',
                $target->getReportCompletePath($this->genNumbers['build1'], 'file'), "Nominal case"
        );
        $this->assertEquals(
                substr($this->_basePathReports, 0, -1),
                       $target->getReportCompletePath($this->genNumbers['build1'], 'dir'), "Nominal case with directory"
        );
        $this->assertNull(
                $target->getReportCompletePath($this->genNumbers['build1'], 'file2', "Should not exist in FS")
        );
        $this->assertNull(
                $target->getReportCompletePath($this->genNumbers['build1'], 'foo', "Should not exist in DB")
        );
    }

    /**
     * @covers Controller_Processor::action_process
     */
    public function testActionProcess()
    {
        $target1                = new Controller_Processor_ProcessorStub();
        $target1->request->setParam('id', $this->genNumbers['build1']);
        $target1->processResult = TRUE;
        $target1->action_process();
        $this->assertEquals('true', $target1->response->body());

        $target2                = new Controller_Processor_ProcessorStub();
        $target2->request->setParam('id', $this->genNumbers['build1']);
        $target2->processResult = FALSE;
        $target2->action_process();
        $this->assertEquals('false', $target2->response->body());
    }

    /**
     * @covers Controller_Processor::action_analyze
     */
    public function testActionAnalyze()
    {
        $target1 = new Controller_Processor_ProcessorStub();
        $target1->request->setParam('id', $this->genNumbers['build1']);
        $target1->action_analyze();
        $this->assertEquals('', $target1->response->body());

        $target2                = new Controller_Processor_ProcessorStub2();
        $target2->request->setParam('id', $this->genNumbers['build1']);
        $target2->analyzeResult = 'ok';
        $target2->action_analyze();
        $this->assertEquals('ok', $target2->response->body());

        $target3                = new Controller_Processor_ProcessorStub2();
        $target3->request->setParam('id', $this->genNumbers['build1']);
        $target3->analyzeResult = 'unstable';
        $target3->action_analyze();
        $this->assertEquals('unstable', $target3->response->body());
    }
}