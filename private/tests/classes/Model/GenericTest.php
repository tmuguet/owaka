<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_GenericTest extends TestCase
{

    protected $useDatabase = FALSE;

    public function testRules()
    {
        $models = File::getFiles(APPPATH . 'classes' . DIRECTORY_SEPARATOR . 'Model');
        $pos = strlen(APPPATH . 'classes' . DIRECTORY_SEPARATOR);
        foreach ($models as $file) {
            $className = str_replace(DIRECTORY_SEPARATOR, "_", substr($file, $pos, -4));
            $model = new $className;
            $model->rules();
        }
    }
}