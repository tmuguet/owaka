<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_ReportTest extends TestCase
{

    protected $useDatabase = FALSE;
    private $_basePath;

    public function setUp()
    {
        parent::setUp();

        $this->_basePath = APPPATH . 'reports' . DIRECTORY_SEPARATOR . '42'
                . DIRECTORY_SEPARATOR . 'myreport' . DIRECTORY_SEPARATOR;

        if (!file_exists($this->_basePath)) {
            mkdir($this->_basePath, 0700, true);
        }
        file_put_contents($this->_basePath . 'index.html', 'hello HTML world');
        file_put_contents($this->_basePath . 'index.xml', 'hello XML world');
    }

    public function tearDown()
    {
        parent::tearDown();

        unlink($this->_basePath . 'index.html');
        unlink($this->_basePath . 'index.xml');

        rmdir(APPPATH . 'reports' . DIRECTORY_SEPARATOR . '42' . DIRECTORY_SEPARATOR . 'myreport');
        rmdir(APPPATH . 'reports' . DIRECTORY_SEPARATOR . '42');
    }

    /**
     * @covers Controller_Report::action_index
     */
    public function testActionIndex()
    {
        $responseHtml = Request::factory('reports/42/myreport')->login()->execute();
        $this->assertResponseOK($responseHtml, "Request for HTML failed");
        $this->assertEquals('text/html', $responseHtml->headers('Content-Type'), 'Incorrect content-type for HTML');
        $this->assertEquals('hello HTML world', $responseHtml->body(), "Incorrect HTML body");

        $responseXml = Request::factory('reports/42/myreport/index.xml')->login()->execute();
        $this->assertResponseOK($responseXml, "Request for XML failed");
        $this->assertEquals('text/xml', $responseXml->headers('Content-Type'), 'Incorrect content-type for HTML');
        $this->assertEquals('hello XML world', $responseXml->body(), "Incorrect XML body");

        $responseNotFound = Request::factory('reports/42/myreport/non-existing')->login()->execute();
        $this->assertResponseStatusEquals(404, $responseNotFound, "Request failed, found something");

        $responseOut = Request::factory('reports/42/myreport/../')->login()->execute();
        $this->assertResponseStatusEquals(404, $responseOut, "Request failed, found something outside directory");
    }
}