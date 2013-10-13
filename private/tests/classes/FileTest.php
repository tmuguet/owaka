<?php
defined('SYSPATH') or die('No direct access allowed!');

class FileTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers File::getFiles
     */
    public function testGetFiles()
    {
        $basePath = dirname(__FILE__) . DIR_SEP . '_FileTest'
                . DIR_SEP . 'dummies' . DIR_SEP . 'dummies';
        $actual   = File::getFiles($basePath);
        $expected = array(
            $basePath . DIR_SEP . 'dummy3.php'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::getFiles
     */
    public function testGetFiles_Recursive()
    {
        $basePath = dirname(__FILE__) . DIR_SEP . '_FileTest'
                . DIR_SEP . 'dummies' . DIR_SEP;
        $actual   = File::getFiles($basePath);
        $expected = array(
            $basePath . 'dummy1.php',
            $basePath . 'dummy2.php',
            $basePath . 'dummies' . DIR_SEP . 'dummy3.php'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findClasses
     */
    public function testFindClasses()
    {
        $actual   = File::findClasses('dummies');
        $expected = array(
            'dummies_dummy2',
            'dummies_dummies_dummy3'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findWidgets
     */
    public function testFindWidgets_main()
    {
        $actual   = File::findWidgets('main');
        $expected = array(
            'Controller_Widget_widget1',
            'Controller_Widget_widget2'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findWidgets
     */
    public function testFindWidgets_project()
    {
        $actual   = File::findWidgets('project');
        $expected = array(
            'Controller_Widget_widget1',
            'Controller_Widget_widget3'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findWidgets
     */
    public function testFindWidgets_build()
    {
        $actual   = File::findWidgets('build');
        $expected = array(
            'Controller_Widget_widget1',
            'Controller_Widget_widget4'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findProcessors
     */
    public function testFindProcessors()
    {
        $actual   = File::findProcessors();
        $expected = array(
            'Processor_processor1',
            'Processor_processor2'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::findAnalyzers
     */
    public function testFindAnalyzers()
    {
        $actual   = File::findAnalyzers();
        $expected = array(
            'Processor_processor2'
        );
        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers File::rrmdir
     * @covers File::rcopy
     */
    public function testDir()
    {
        $this->assertFalse(File::rrmdir('/path/does/not/exist'));
        $this->assertFalse(File::rcopy('/path/does/not/exist', APPPATH));
    }
}