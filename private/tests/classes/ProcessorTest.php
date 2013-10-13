<?php
require_once dirname(__FILE__) . DIR_SEP . '_stubs' . DIR_SEP . 'ProcessorStub.php';
require_once dirname(__FILE__) . DIR_SEP . '_stubs' . DIR_SEP . 'ProcessorStub2.php';

class ProcessorTest extends TestCase
{

    protected $xmlDataSet = 'processor';
    private $_basePathReports;

    public function setUp()
    {
        parent::setUp();

        mkdir($this->genNumbers['PathFoo']);
        file_put_contents($this->genNumbers['PathFoo'] . DIR_SEP . 'bar', 'hello-world');

        $this->_basePathReports = APPPATH . 'reports' . DIR_SEP . $this->genNumbers['build1']
                . DIR_SEP . 'processorstub' . DIR_SEP;

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
     * @covers Processor::_getName
     */
    public function testGetName()
    {
        $target = new Processor_ProcessorStub();
        $this->assertEquals('processorstub', $target->_getName());
    }

    /**
     * @covers Processor::projectParameters
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
                $expectedFound, Processor_ProcessorStub::projectParameters($this->genNumbers['ProjectFoo'])
        );
        $this->assertEquals(
                $expectedNotFound, Processor_ProcessorStub::projectParameters($this->genNumbers['ProjectBar'])
        );
    }

    /**
     * @covers Processor::getInputReportCompleteRealPath
     */
    public function testGetInputReportCompleteRealPath()
    {
        $target = new Processor_ProcessorStub();
        $build  = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertEquals(
                $this->genNumbers['PathFoo'] . DIR_SEP . 'bar', $target->getInputReportCompleteRealPath($build, 'foo'),
                                                                                                        "Nominal case"
        );
        $this->assertNull(
                $target->getInputReportCompleteRealPath($build, 'bat'), "Should not exist in FS"
        );
        $this->assertNull(
                $target->getInputReportCompleteRealPath($build, 'bar'), "Should not exist in DB"
        );
    }

    /**
     * @covers Processor::getReportRootPath
     */
    public function testGetReportRootPath()
    {
        $target = new Processor_ProcessorStub();
        $build  = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertEquals(
                $this->_basePathReports, $target->getReportRootPath($build), "Nominal case"
        );
    }

    /**
     * @covers Processor::getReportCompletePath
     */
    public function testGetReportCompletePath()
    {
        $target = new Processor_ProcessorStub();
        $build  = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertEquals(
                $this->_basePathReports . 'foo.html', $target->getReportCompletePath($build, 'file'), "Nominal case"
        );
        $this->assertEquals(
                $this->_basePathReports . '.', $target->getReportCompletePath($build, 'dir'),
                                                                              "Nominal case with directory"
        );
        $this->assertEquals(
                $this->_basePathReports . 'bar.html', $target->getReportCompletePath($build, 'file2'),
                                                                                     "Not existing in FS"
        );
        $this->assertNull(
                $target->getReportCompletePath($build, 'foo'), "Should not exist in DB"
        );
    }

    /**
     * @covers Processor::getReportCompleteRealPath
     */
    public function testGetReportCompleteRealPath()
    {
        $target = new Processor_ProcessorStub();
        $build  = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertEquals(
                $this->_basePathReports . 'foo.html', $target->getReportCompleteRealPath($build, 'file'), "Nominal case"
        );
        $this->assertEquals(
                substr($this->_basePathReports, 0, -1), $target->getReportCompleteRealPath($build, 'dir'),
                                                                                           "Nominal case with directory"
        );
        $this->assertNull(
                $target->getReportCompleteRealPath($build, 'file2'), "Should not exist in FS"
        );
        $this->assertNull(
                $target->getReportCompleteRealPath($build, 'foo'), "Should not exist in DB"
        );
    }
    /**
     * @covers Processor::action_process
     */
    /*    public function testActionProcess()
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
      } */

    /**
     * @covers Processor::action_analyze
     */
    /*    public function testActionAnalyze()
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
      } */
}