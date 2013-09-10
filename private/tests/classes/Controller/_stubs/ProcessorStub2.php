<?php
require_once dirname(__FILE__) . DIR_SEP . 'ProcessorStub.php';

class Controller_Processor_ProcessorStub2 extends Controller_Processor_ProcessorStub
{

    public $analyzeResult = 'ok';

    public function analyze()
    {
        return $this->analyzeResult;
    }
}