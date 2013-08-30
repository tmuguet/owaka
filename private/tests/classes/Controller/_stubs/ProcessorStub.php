<?php

class Controller_Processor_ProcessorStub extends Controller_Processor
{

    public $processResult = TRUE;

    static public function inputReports()
    {
        return array(
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
    }

    public function process($buildId)
    {
        return $this->processResult;
    }
}