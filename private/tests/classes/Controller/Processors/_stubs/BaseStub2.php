<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BaseStub.php';

class Controller_Processors_BaseStub2 extends Controller_Processors_BaseStub
{

    public $analyzeResult = 'ok';

    public function analyze()
    {
        return $this->analyzeResult;
    }
}