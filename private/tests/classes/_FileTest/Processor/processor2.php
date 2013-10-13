<?php

class Processor_processor2 extends Processor
{

    public static $inputReports = array(
        'raw' => array(
            'title'       => 'Coverage raw report',
            'description' => 'Coverage XML report',
            'type'        => 'file',
            'keep-as'     => 'coverage.xml'
        ),
        'dir' => array(
            'title'       => 'Coverage report directory',
            'description' => 'Coverage HTML report directory',
            'type'        => 'dir',
            'keep-as'     => '.'
        )
    );
    public static $parameters   = array(
        'threshold_errors_error'    => array(
            'title'        => 'Errors to trigger error',
            'description'  => 'Number of errors to trigger build error',
            'defaultvalue' => 1
        ),
        'threshold_errors_unstable' => array(
            'title'        => 'Errors to trigger unstable',
            'description'  => 'Number of errors to trigger unstable build',
            'defaultvalue' => -1
        ),
    );

    public function analyze(Model_Build &$build, $params)
    {
        
    }

    public function process(Model_Build &$build)
    {
        
    }
}