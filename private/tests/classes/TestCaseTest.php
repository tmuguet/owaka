<?php
defined('SYSPATH') or die('No direct access allowed!');

class TestCaseTest extends TestCase
{

    protected static $genNumbersSaved = NULL;
    protected $xmlDataSet      = 'testcase';

    public function test1()
    {
        self::$genNumbersSaved = $this->genNumbers;

        $this->assertEquals(1, $this->genNumbers['Foo'], "Sequence #1 incorrect in structure");
        $this->assertEquals(2, $this->genNumbers['Bar'], "Sequence #2 incorrect in structure");
        $this->assertNotNull($this->genNumbers['Random1'], "Random #1 incorrect in structure");
        $this->assertNotNull($this->genNumbers['Random2'], "Random #2 incorrect in structure");
        $this->assertNotNull($this->genNumbers['TmpPath1'], "Tmp path #1 incorrect in structure");
        $this->assertTrue(is_writable(dirname($this->genNumbers['TmpPath1'])), 'Tmp path #1 not writable');

        $db = DB::select('id', 'name')
                        ->from('ut')
                        ->order_by('id', 'ASC')
                        ->execute()->as_array();
        $this->assertEquals($this->genNumbers['Foo'], $db[0]['id'], "Sequence #1 incorrect in DB");
        $this->assertEquals($this->genNumbers['Bar'], $db[1]['id'], "Sequence #2 incorrect in DB");
        $this->assertEquals($this->genNumbers['Random1'], $db[0]['name'], "Random #1 incorrect in DB");
        $this->assertEquals($this->genNumbers['Random2'], $db[1]['name'], "Random #2 incorrect in DB");
        $this->assertEquals($this->genNumbers['TmpPath1'], $db[2]['name'], "Tmp path #1 incorrect in DB");
    }

    public function test2()
    {
        $this->assertEquals(
                self::$genNumbersSaved['Foo'], $this->genNumbers['Foo'], "Sequence #1 incorrect in structure"
        );
        $this->assertEquals(
                self::$genNumbersSaved['Bar'], $this->genNumbers['Bar'], "Sequence #2 incorrect in structure"
        );
        $this->assertNotNull($this->genNumbers['Random1'], "Random #1 incorrect in structure");
        $this->assertNotNull($this->genNumbers['Random2'], "Random #2 incorrect in structure");
        $this->assertNotNull($this->genNumbers['TmpPath1'], "Tmp path #1 incorrect in structure");
        $this->assertTrue(is_writable(dirname($this->genNumbers['TmpPath1'])), 'Tmp path #1 not writable');

        $db = DB::select('id', 'name')
                        ->from('ut')
                        ->order_by('id', 'ASC')
                        ->execute()->as_array();
        $this->assertEquals($this->genNumbers['Foo'], $db[0]['id'], "Sequence #1 incorrect in DB");
        $this->assertEquals($this->genNumbers['Bar'], $db[1]['id'], "Sequence #2 incorrect in DB");
        $this->assertEquals($this->genNumbers['Random1'], $db[0]['name'], "Random #1 incorrect in DB");
        $this->assertEquals($this->genNumbers['Random2'], $db[1]['name'], "Random #2 incorrect in DB");
        $this->assertEquals($this->genNumbers['TmpPath1'], $db[2]['name'], "Tmp path #1 incorrect in DB");
    }
}