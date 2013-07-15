<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'BaseStub.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'BaseStub2.php';

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
     * @covers Controller_Processors_Base::getInputReports
     * @expectedException Exception
     * @expectedExceptionMessage Not implemented
     */
    public function testGetInputReports()
    {
        Controller_Processors_Base::getInputReports();
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
     * @covers Controller_Processors_Base::getReportCompletePath
     */
    public function testGetReportCompletePath()
    {
        $target = new Controller_Processors_BaseStub();
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
     * @covers Controller_Processors_Base::action_process
     */
    public function testActionProcess()
    {
        $target1                = new Controller_Processors_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['build1']);
        $target1->processResult = TRUE;
        $target1->action_process();
        $this->assertEquals('true', $target1->response->body());

        $target2                = new Controller_Processors_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['build1']);
        $target2->processResult = FALSE;
        $target2->action_process();
        $this->assertEquals('false', $target2->response->body());
    }

    /**
     * @covers Controller_Processors_Base::action_analyze
     */
    public function testActionAnalyze()
    {
        $target1 = new Controller_Processors_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['build1']);
        $target1->action_analyze();
        $this->assertEquals('', $target1->response->body());

        $target2                = new Controller_Processors_BaseStub2();
        $target2->request->setParam('id', $this->genNumbers['build1']);
        $target2->analyzeResult = 'ok';
        $target2->action_analyze();
        $this->assertEquals('ok', $target2->response->body());

        $target3                = new Controller_Processors_BaseStub2();
        $target3->request->setParam('id', $this->genNumbers['build1']);
        $target3->analyzeResult = 'unstable';
        $target3->action_analyze();
        $this->assertEquals('unstable', $target3->response->body());
    }
}