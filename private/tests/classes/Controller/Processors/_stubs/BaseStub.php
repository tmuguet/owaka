<?php

class Controller_Processors_BaseStub extends Controller_Processors_Base
{

    public $request  = NULL;
    public $response = NULL;

    static public function getInputReports()
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
            'dir2'   => array(
                'title'       => 'Directory report',
                'description' => 'Directory report',
                'type'        => 'dir',
                'keep-as'     => 'subdir'
            ),
        );
    }

    public function __construct()
    {
        $this->request  = new RequestStub('fake');
        $this->response = new Response();

        parent::__construct($this->request, $this->response);
    }

    public function process($buildId)
    {
        
    }
}