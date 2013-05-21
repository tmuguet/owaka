<?php
defined('SYSPATH') or die('No direct access allowed!');

/**
 * Static test of including all files
 */
class includeTest extends Kohana_UnitTest_TestCase
{

    private function find_all_files($dir)
    {
        $root = scandir($dir);
        $result = array();
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir/$value")) {
                $result[] = "$dir/$value";
                continue;
            }
            foreach ($this->find_all_files("$dir/$value") as $value) {
                $result[] = $value;
            }
        }
        return $result;
    }

    protected function _test($files)
    {
        $pos = strlen(APPPATH . '/classes/');

        foreach ($files as $file) {
            if (substr($file, -3) === 'php') {
                $className = str_replace("/", "_", substr($file, $pos, -4));
                if (!class_exists($className, FALSE) && !interface_exists($className, FALSE)) {
                    if ((include_once $file) !== 1) {
                        $this->assertFail('Could not include ' . $file);
                    }
                    $this->assertTrue(
                            class_exists($className, FALSE) || interface_exists($className, FALSE),
                                                                                'Class ' . $className . ' does not exist'
                    );
                }
            }
        }
    }

    /**
     * Tests all the controllers
     */
    public function testControllers()
    {
        $files = $this->find_all_files(APPPATH . '/classes/Controller');
        $this->_test($files);
    }

    /**
     * Tests all the models
     */
    public function testModels()
    {
        $files = $this->find_all_files(APPPATH . '/classes/Model');
        $this->_test($files);
    }

    /**
     * Tests all the helper
     */
    public function testHelper()
    {
        $files = $this->find_all_files(APPPATH . '/classes/Helper');
        $this->_test($files);
    }

    /**
     * Tests all utils
     */
    public function testUtils()
    {
        $files = array(
            APPPATH . '/classes/Date.php',
            APPPATH . '/classes/Owaka.php',
        );
        $this->_test($files);
    }
}