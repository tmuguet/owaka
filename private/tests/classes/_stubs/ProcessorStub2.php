<?php
require_once dirname(__FILE__) . DIR_SEP . 'ProcessorStub.php';

class Processor_ProcessorStub2 extends Processor_ProcessorStub
{

    public $analyzeResult = 'ok';

    public function analyze()
    {
        return $this->analyzeResult;
    }
}