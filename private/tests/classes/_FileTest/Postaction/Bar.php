<?php

class Postaction_Bar extends Postaction
{

    public static $parameters = array();

    public function process(Model_Build &$build, array $parameters)
    {
        return true;
    }
}