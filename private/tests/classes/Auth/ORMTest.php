<?php
defined('SYSPATH') or die('No direct access allowed!');

class Auth_ORMTest extends TestCase
{

    protected $xmlDataSet = 'auth';

    /**
     * @covers Auth_ORM::hashKey
     */
    public function testHashKey()
    {
        $target   = Auth::instance();
        $expected = hash_hmac('sha256', 'foo', 'bar');
        $actual   = $target->hashKey('foo', 'bar');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Auth_ORM::_login
     */
    public function testLogin()
    {
        $target = Auth::instance();

        $user     = ORM::factory('User', $this->genNumbers['userFoo']);
        $response = Auth::instance()->hashKey('test', $user->challenge);
        $this->assertTrue($target->_login('userFoo', $response, false));
    }

    /**
     * @covers Auth_ORM::_login
     */
    public function testLoginObject()
    {
        $target = Auth::instance();

        $user     = ORM::factory('User', $this->genNumbers['userFoo']);
        $response = Auth::instance()->hashKey('test', $user->challenge);
        $this->assertTrue($target->_login($user, $response, false));
    }

    /**
     * @covers Auth_ORM::_login
     */
    public function testLoginRemember()
    {
        $target = Auth::instance();

        $user     = ORM::factory('User', $this->genNumbers['userFoo']);
        $response = Auth::instance()->hashKey('test', $user->challenge);
        $this->assertTrue($target->_login('userFoo', $response, true));
    }

    /**
     * @covers Auth_ORM::_login
     */
    public function testLoginBadPassword()
    {
        $target = Auth::instance();

        $user     = ORM::factory('User', $this->genNumbers['userFoo']);
        $response = Auth::instance()->hashKey('badpassword', $user->challenge);
        $this->assertFalse($target->_login('userFoo', $response, false));
    }

    /**
     * @covers Auth_ORM::_login
     */
    public function testLoginBadRole()
    {
        $target = Auth::instance();

        $user     = ORM::factory('User', $this->genNumbers['userBar']);
        $response = Auth::instance()->hashKey('test', $user->challenge);
        $this->assertFalse($target->_login($user, $response, false));
    }

    /**
     * @covers Auth_ORM::check_password
     * @expectedException HTTP_Exception_500
     */
    public function testCheckPassword()
    {
        Auth::instance()->check_password('hello');
    }
}