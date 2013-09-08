<?php

class Controller_Processor_processor1 extends Controller_Processor
{

    static public function inputReports()
    {
        return array(
            'xml' => array(
                'title'       => 'Code Sniffer report',
                'description' => 'Code Sniffer XML report in checkstyle format',
                'type'        => 'file',
                'keep-as'     => 'index.xml'
            )
        );
    }
    
    static public function parameters() {
          return array(
            'threshold_warnings_unstable' => array(
                'title'        => 'Warnings to trigger unstable',
                'description'        => 'Number of warnings to trigger unstable build',
                'defaultvalue' => 1
            )
        );
    }

    public function process($buildId)
    {
        
    }
}