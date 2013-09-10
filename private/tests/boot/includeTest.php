<?php
defined('SYSPATH') or die('No direct access allowed!');

/**
 * Static test of including all files
 */
class includeTest extends TestCase
{

    protected $useDatabase = FALSE;

    protected function _test($files)
    {
        $pos = strlen(APPPATH . 'classes' . DIR_SEP);

        foreach ($files as $file) {
            if (substr($file, -3) === 'php') {
                $className = str_replace(DIR_SEP, "_", substr($file, $pos, -4));
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
        $files = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Controller');
        $this->_test($files);
    }

    /**
     * Tests all the models
     */
    public function testModels()
    {
        $files = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Model');
        $this->_test($files);
    }

    /**
     * Tests all the helpers
     */
    public function testHelper()
    {
        $files = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Helper');
        $this->_test($files);
    }

    /**
     * Tests all the tasks
     */
    public function testTask()
    {
        $files = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Task');
        $this->_test($files);
    }

    /**
     * Tests all the loggers
     */
    public function testLoggers()
    {
        $files = File::getFiles(APPPATH . 'classes' . DIR_SEP . 'Log');
        $this->_test($files);
    }

    /**
     * Tests all utils
     */
    public function testUtils()
    {
        $files = array(
            APPPATH . 'classes' . DIR_SEP . 'Date.php',
            APPPATH . 'classes' . DIR_SEP . 'File.php',
            APPPATH . 'classes' . DIR_SEP . 'Owaka.php',
        );
        $this->_test($files);
    }
}