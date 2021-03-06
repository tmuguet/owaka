<?php

class Processor_ProcessorStub extends Processor
{

    public $processResult = TRUE;
    public static $inputReports  = array(
        'file'  => array(
            'title'       => 'File report',
            'description' => 'File report',
            'type'        => 'file',
            'keep-as'     => 'foo.html'
        ),
        'dir'   => array(
            'title'       => 'Directory report',
            'description' => 'Directory report',
            'type'        => 'dir',
            'keep-as'     => '.'
        ),
        'file2' => array(
            'title'       => 'File report',
            'description' => 'File report',
            'type'        => 'file',
            'keep-as'     => 'bar.html'
        ),
        'dir2'  => array(
            'title'       => 'Directory report',
            'description' => 'Directory report',
            'type'        => 'dir',
            'keep-as'     => 'subdir'
        ),
    );
    public static $parameters    = array(
        'threshold_errors_error'    => array(
            'title'        => 'Number of errors to trigger build error',
            'defaultvalue' => 1
        ),
        'threshold_errors_unstable' => array(
            'title'        => 'Number of errors to trigger unstable build',
            'defaultvalue' => -1
        ),
    );

    public function process(Model_Build &$build)
    {
        return $this->processResult;
    }
}