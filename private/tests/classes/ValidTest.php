<?php
defined('SYSPATH') or die('No direct access allowed!');

class ValidTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Valid::different
     */
    public function testDifferent()
    {
        $this->assertTrue(Valid::different('Foobar', 'foobar'));
        $this->assertTrue(Valid::different('423,90', '423.90'));
        $this->assertTrue(Valid::different('', 'NULL'));
        $this->assertFalse(Valid::different('helloworld', 'helloworld'));
        $this->assertFalse(Valid::different('54.000', 54));
    }

    /**
     * @covers Valid::decimal
     */
    public function testDecimal()
    {
        $this->assertTrue(Valid::decimal('423.00'));
        $this->assertTrue(Valid::decimal('423,90'));
        $this->assertTrue(Valid::decimal('0.5'));
        $this->assertTrue(Valid::decimal('423'));
        $this->assertFalse(Valid::decimal('.00'));
        $this->assertFalse(Valid::decimal('54.000'));

        $this->assertTrue(Valid::decimal('423.000', 3));
        $this->assertFalse(Valid::decimal('0.5000', 3));

        $this->assertTrue(Valid::decimal('23.00', 3, 2));
        $this->assertFalse(Valid::decimal('450.50', 3, 2));
        $this->assertFalse(Valid::decimal('0.50', 3, 2));
    }

    /**
     * @covers Valid::integer
     */
    public function testInteger()
    {
        $this->assertFalse(intval('-10') >= 0);
        $this->assertTrue(Valid::integer(1));
        $this->assertTrue(Valid::integer('-405'));
        $this->assertTrue(Valid::integer('0'));
        $this->assertFalse(Valid::integer('az'));

        $this->assertTrue(Valid::integer(1, 0, 100));
        $this->assertTrue(Valid::integer(0, 0, 100));
        $this->assertTrue(Valid::integer(100, 0, 100));
        $this->assertFalse(Valid::integer(0, 1, 100));
        $this->assertFalse(Valid::integer(101, 1, 100));
        $this->assertFalse(Valid::integer(-10, 1, 100));
        $this->assertFalse(Valid::integer('-10', 0));
    }

    /**
     * @covers Valid::boolean
     */
    public function testBoolean()
    {
        $this->assertTrue(Valid::boolean("0"));
        $this->assertTrue(Valid::boolean("1"));
        $this->assertTrue(Valid::boolean(0));
        $this->assertTrue(Valid::boolean(1));
        $this->assertFalse(Valid::boolean(""));
        $this->assertFalse(Valid::boolean("foo"));
    }

    /**
     * @covers Valid::is_dir
     */
    public function testIsDir()
    {
        $this->assertTrue(Valid::is_dir(APPPATH));
        $this->assertTrue(Valid::is_dir(APPPATH . 'classes'));
        $this->assertFalse(Valid::is_dir(""));
        $this->assertFalse(Valid::is_dir(APPPATH . 'foo'));             // non-existing file
        $this->assertFalse(Valid::is_dir(APPPATH . 'bootstrap.php'));   // file
    }

    /**
     * @covers Valid::is_readable
     */
    public function testIsReadable()
    {
        $this->assertTrue(Valid::is_readable(APPPATH));
        $this->assertTrue(Valid::is_readable(APPPATH . 'classes'));
        $this->assertTrue(Valid::is_readable(APPPATH . 'bootstrap.php'));   // file
        $this->assertFalse(Valid::is_readable(""));
        $this->assertFalse(Valid::is_readable(APPPATH . 'foo'));            // non-existing file
    }

    /**
     * @covers Valid::is_writable
     */
    public function testIsWritable()
    {
        $this->assertTrue(Valid::is_writable(APPPATH . 'logs'));
        $this->assertFalse(Valid::is_writable(""));
        $this->assertFalse(Valid::is_writable(APPPATH . 'foo'));    // non-existing file
        $this->assertFalse(Valid::is_writable('/'));
    }
}