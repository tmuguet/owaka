<?php

class Postaction_PublishTest extends TestCase_Postaction
{

    protected $xmlDataSet = 'publish';

    /**
     * @covers Postaction_Publish::process
     * @covers Postaction_Publish::publish
     */
    public function testProcess()
    {
        $parameters = Postaction_Publish::projectParameters($this->genNumbers['ProjectFoo']);
        $build1     = ORM::factory('Build', $this->genNumbers['build1']);

        // Prepare report
        $destinationDir = APPPATH . 'reports' . DIR_SEP . $build1->id . DIR_SEP . 'codesniffer' . DIR_SEP;
        $reports        = Processor_Codesniffer::$inputReports;
        $destination    = $destinationDir . $reports['xml']['keep-as'];

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0700, true);
        }
        File::rcopy(dirname(__FILE__) . DIR_SEP . '_files' . DIR_SEP . 'codesniffer-report.xml', $destination);

        // Test
        $this->assertTrue(
                $this->target->process($build1, $parameters)
        );
        $this->assertTrue(
                file_exists($this->genNumbers['reportPath']), 'File ' . $this->genNumbers['reportPath'] . ' not created'
        );
        File::rrmdir(dirname($destination));
    }
}