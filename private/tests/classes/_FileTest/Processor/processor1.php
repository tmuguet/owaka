<?php

class Processor_processor1 extends Processor
{

    public static $inputReports = array(
        'xml' => array(
            'title'       => 'Code Sniffer report',
            'description' => 'Code Sniffer XML report in checkstyle format',
            'type'        => 'file',
            'keep-as'     => 'index.xml'
        )
    );
    public static $parameters   = array(
        'threshold_warnings_unstable' => array(
            'title'        => 'Warnings to trigger unstable',
            'description'  => 'Number of warnings to trigger unstable build',
            'defaultvalue' => 1
        )
    );

    public function process(Model_Build &$build)
    {
        
    }
}