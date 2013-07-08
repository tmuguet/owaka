<?php

class Controller_Processors_processor2
{

    static public function getInputReports()
    {
        return array(
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
    }

    public function analyze()
    {
        
    }
}