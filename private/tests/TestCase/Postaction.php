<?php

abstract class TestCase_Postaction extends TestCase
{

    protected $target;

    public function setUp()
    {
        parent::setUp();

        $class        = substr(get_called_class(), 0, -4); // remove Test at the end
        $this->target = new $class();
    }

    public function testClass()
    {
        $class      = substr(get_called_class(), 0, -4); // remove Test at the end
        $postaction = str_replace('Postaction_', '', $class);
        $this->assertLessThanOrEqual(
                30, strlen($postaction),
                           'Post action name must be shorter than 30 characters (' . $postaction . ': ' . strlen($postaction) . ' chars)'
        );  // 30 is the limit of project_postaction_parameters.postaction
    }

    public function testParameters()
    {
        $class      = substr(get_called_class(), 0, -4); // remove Test at the end
        $parameters = $class::$parameters;
        foreach (array_keys($parameters) as $type) {
            $this->assertLessThanOrEqual(
                    255, strlen($type),
                                'Post action parameter name must be shorter than 255 characters (' . $type . ': ' . strlen($type) . ' chars)'
            );  // 255 is the limit of project_postaction_parameters.type
        }
    }
}