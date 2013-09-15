<?php

class Controller_Widget_GenericTest extends TestCase
{

    protected $xmlDataSet = 'widgets';

    private function _testClass($nameClass, $reflectionClass)
    {
        $nameClassShort = str_replace('Controller_Widget_', '', $nameClass);

        $this->assertLessThanOrEqual(
                40, strlen($nameClassShort),
                           'Widget name must be shorter than 40 characters (' . $nameClassShort . ': ' . strlen($nameClassShort) . ' chars)'
        );  // 40 is the limit of widgets.type

        if ($reflectionClass->hasMethod('display_main') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::expectedParameters('main');

            $response = Request::factory('w/main/' . $nameClassShort . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/main/$nameClassShort/display/" . $this->genNumbers['widget3'] . "'");
        }
        if ($reflectionClass->hasMethod('display_project') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::expectedParameters('project');

            $response = Request::factory('w/project/' . $nameClassShort . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/project/$nameClassShort/display/" . $this->genNumbers['widget3'] . "'");
        }
        if ($reflectionClass->hasMethod('display_build') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::expectedParameters('build');

            $response = Request::factory('w/build/' . $nameClassShort . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/build/$nameClassShort/display/" . $this->genNumbers['widget3'] . "'");
        }
    }

    public function testGeneric()
    {
        $basePath = APPPATH . 'classes' . DIR_SEP;

        $files   = File::getFiles($basePath . 'Controller' . DIR_SEP . 'Widget' . DIR_SEP);
        $classes = array();
        foreach ($files as $file) {
            $nameClass = str_replace(
                    DIR_SEP, '_', str_replace($basePath, '', substr($file, 0, -4))
            );
            if (!class_exists($nameClass, FALSE)) {
                include_once $file;
            }

            $class = new ReflectionClass($nameClass);
            if ($class->isInstantiable()) {
                $this->_testClass($nameClass, $class);
            }
        }
        return $classes;
    }
}