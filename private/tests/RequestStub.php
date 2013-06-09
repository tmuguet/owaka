<?php

class RequestStub extends Request
{

    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
}