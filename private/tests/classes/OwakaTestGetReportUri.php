<?php
defined('SYSPATH') or die('No direct access allowed!');

class OwakaTestGetReportUri extends TestCase
{

    protected $useDatabase = FALSE;
    private $_buildId;
    private $_processor;
    private $_basePath;
    private $_baseUri;

    public function setUp()
    {
        parent::setUp();

        $this->_buildId   = '42';
        $this->_processor = 'Coverage';
        $this->_basePath  = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->_buildId
                . DIRECTORY_SEPARATOR . $this->_processor . DIRECTORY_SEPARATOR;
        $this->_baseUri   = 'reports/' . $this->_buildId . '/' . $this->_processor . '/';

        if (!file_exists($this->_basePath)) {
            mkdir($this->_basePath, 0700, true);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        rmdir(APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->_buildId . DIRECTORY_SEPARATOR . $this->_processor);
        rmdir(APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->_buildId);
    }

    /**
     * @covers Owaka::getReportUri
     */
    public function testGetReportUri()
    {
        $this->assertEquals(
                $this->_baseUri . '.',
                Owaka::getReportUri(
                        $this->_buildId, $this->_processor, 'dir'
                ), "Report not found for '{$this->_buildId}', '{$this->_processor}', 'dir'"
        );
        $this->assertNull(
                Owaka::getReportUri(
                        $this->_buildId, $this->_processor, 'raw'
                ), "Report found for '{$this->_buildId}', '{$this->_processor}', 'raw'"
        );
        $this->assertEquals(
                $this->_baseUri . '.',
                Owaka::getReportUri(
                        $this->_buildId, $this->_processor
                ), "Report not found for '{$this->_buildId}', '{$this->_processor}'"
        );
    }

    /**
     * @covers Owaka::getReportUri
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot find processor foo
     */
    public function testGetReportUri_errorProcessor()
    {
        Owaka::getReportUri($this->_buildId, 'foo', 'dir');
    }

    /**
     * @covers Owaka::getReportUri
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Report type foo is not defined for Coverage
     */
    public function testGetReportUri_errorReport()
    {
        Owaka::getReportUri($this->_buildId, $this->_processor, 'foo');
    }
}