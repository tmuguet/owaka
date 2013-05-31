<?php
defined('SYSPATH') or die('No direct access allowed!');

class TestCaseTest extends TestCase
{

    protected static $genNumbersSaved = NULL;
    
    protected $xmlDataSet = 'testcase';

    public function test1()
    {
        self::$genNumbersSaved = $this->genNumbers;
        
        $this->assertEquals(1, $this->genNumbers['Foo']);
        $this->assertEquals(2, $this->genNumbers['Bar']);
        $this->assertNotNull($this->genNumbers['Random1']);
        $this->assertNotNull($this->genNumbers['Random2']);
        
        $db = DB::select('id', 'name')
                ->from('ut')
                ->order_by('id', 'ASC')
                ->execute()->as_array();
        $this->assertEquals($this->genNumbers['Foo'], $db[0]['id']);
        $this->assertEquals($this->genNumbers['Bar'], $db[1]['id']);
        $this->assertEquals($this->genNumbers['Random1'], $db[0]['name']);
        $this->assertEquals($this->genNumbers['Random2'], $db[1]['name']);
    }

    public function test2()
    {
        $this->assertEquals(self::$genNumbersSaved['Foo'], $this->genNumbers['Foo']);
        $this->assertEquals(self::$genNumbersSaved['Bar'], $this->genNumbers['Bar']);
        $this->assertNotNull($this->genNumbers['Random1']);
        $this->assertNotNull($this->genNumbers['Random2']);
        
        $db = DB::select('id', 'name')
                ->from('ut')
                ->order_by('id', 'ASC')
                ->execute()->as_array();
        $this->assertEquals($this->genNumbers['Foo'], $db[0]['id']);
        $this->assertEquals($this->genNumbers['Bar'], $db[1]['id']);
        $this->assertEquals($this->genNumbers['Random1'], $db[0]['name']);
        $this->assertEquals($this->genNumbers['Random2'], $db[1]['name']);
    }
}