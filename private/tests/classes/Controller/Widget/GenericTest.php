<?php

class Controller_Widget_GenericTest extends TestCase
{

    protected $xmlDataSet = 'base';

    private function _testClass($nameClass, $reflectionClass)
    {
        $nameClass::getPreferredSize();
        $nameClass::getOptimizedSizes();

        $nameClass2 = str_replace('Controller_Widget_', '', $nameClass);

        if ($reflectionClass->hasMethod('display_main') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::getExpectedParameters('main');

            $response = Request::factory('w/main/' . $nameClass2 . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/main/$nameClass2/display/" . $this->genNumbers['widget3'] . "'");
        }
        if ($reflectionClass->hasMethod('display_project') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::getExpectedParameters('project');

            $response = Request::factory('w/project/' . $nameClass2 . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/project/$nameClass2/display/" . $this->genNumbers['widget3'] . "'");
        }
        if ($reflectionClass->hasMethod('display_build') || $reflectionClass->hasMethod('display_all')) {
            $nameClass::getExpectedParameters('build');

            $response = Request::factory('w/build/' . $nameClass2 . '/display/' . $this->genNumbers['widget3'])->login()->execute();
            $this->assertResponseOK($response,
                                    "Request failed for $nameClass : 'w/build/$nameClass2/display/" . $this->genNumbers['widget3'] . "'");
        }
    }

    /**
     * @coversNothing
     */
    public function testGeneric()
    {
        $basePath = APPPATH . 'classes' . DIRECTORY_SEPARATOR;

        $files   = File::getFiles($basePath . 'Controller' . DIRECTORY_SEPARATOR . 'Widget' . DIRECTORY_SEPARATOR);
        $classes = array();
        foreach ($files as $file) {
            $nameClass = str_replace(
                    DIRECTORY_SEPARATOR, '_', str_replace($basePath, '', substr($file, 0, -4))
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