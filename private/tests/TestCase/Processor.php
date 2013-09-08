<?php

abstract class TestCase_Processor extends TestCase
{

    protected $target;
    protected $buildId;
    protected $xmlDataSet = '../../_files/processor';

    public function setUp()
    {
        parent::setUp();

        $class        = substr(get_called_class(), 0, -4); // remove Test at the end
        $this->target = new $class();
    }

    public function tearDown()
    {

        if (file_exists(APPPATH . 'reports')) {
            File::rrmdir(APPPATH . 'reports');
        }
        parent::tearDown();
    }

    public function testInputReports()
    {
        // Code coverage purpose only
        $class = substr(get_called_class(), 0, -4); // remove Test at the end
        $class::inputReports();
        $class::parameters();
    }

    protected function CopyReport($type, $source)
    {
        $destinationDir = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->buildId . DIRECTORY_SEPARATOR
                . $this->target->_getName() . DIRECTORY_SEPARATOR;
        $reports        = $this->target->inputReports();
        if (!isset($reports[$type]) || !isset($reports[$type]['keep-as'])) {
            throw new Exception("$type not available");
        }

        $destination = $destinationDir . $reports[$type]['keep-as'];

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0700, true);
        }

        File::rcopy($source, $destination);
    }
}