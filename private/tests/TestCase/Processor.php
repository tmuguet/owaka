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

    public function testClass()
    {
        $class         = substr(get_called_class(), 0, -4); // remove Test at the end
        $processorname = str_replace('Controller_Processor_', '', $class);
        $this->assertLessThanOrEqual(
                30, strlen($processorname),
                           'Processor name must be shorter than 30 characters (' . $processorname . ': ' . strlen($processorname) . ' chars)'
        );  // 30 is the limit of project_reports_parameters.processor
    }

    public function testInputReports()
    {
        $class         = substr(get_called_class(), 0, -4); // remove Test at the end
        $processorname = str_replace('Controller_Processor_', '', $class);
        $this->assertLessThanOrEqual(
                30, strlen($processorname),
                           'Processor name must be shorter than 30 characters (' . $processorname . ': ' . strlen($processorname) . ' chars)'
        );  // 30 is the limit of project_reports_parameters.processor

        $reports = $class::$inputReports;
        foreach (array_keys($reports) as $type) {
            $completeType = $processorname . '_' . $type;
            $this->assertLessThanOrEqual(
                    80, strlen($completeType),
                               'Processor name concatenated to report type must be shorter than 80 characters (' . $completeType . ': ' . strlen($completeType) . ' chars)'
            );  // 80 is the limit of project_reports.type
        }
    }

    public function testParameters()
    {
        $class      = substr(get_called_class(), 0, -4); // remove Test at the end
        $parameters = $class::$parameters;
        foreach (array_keys($parameters) as $type) {
            $this->assertLessThanOrEqual(
                    255, strlen($type),
                                'Processor parameter name must be shorter than 255 characters (' . $type . ': ' . strlen($type) . ' chars)'
            );  // 255 is the limit of project_report_parameters.type
        }
    }

    protected function CopyReport($type, $source)
    {
        $destinationDir = APPPATH . 'reports' . DIR_SEP . $this->buildId . DIR_SEP
                . $this->target->_getName() . DIR_SEP;
        $t              = $this->target;
        $reports        = $t::$inputReports;
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