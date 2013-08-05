<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProcessorStub.php';

class Controller_Processor_ProcessorStub2 extends Controller_Processor_ProcessorStub
{

    public $analyzeResult = 'ok';

    public function analyze()
    {
        return $this->analyzeResult;
    }
}