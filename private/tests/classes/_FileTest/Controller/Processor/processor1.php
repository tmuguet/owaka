<?php

class Controller_Processor_processor1
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
}