<?php

class Controller_Processors_BaseStub2 extends Controller_Processors_BaseStub
{

    public $analyzeResult = TRUE;

    public function analyze()
    {
        return $this->analyzeResult;
    }
}