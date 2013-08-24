<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_UserTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers Model_User::checkChallenge
     */
    public function testCheckChallenge()
    {
        $target = ORM::factory('User', $this->genNumbers['userFoo']);

        $this->assertNotEmpty($target->challenge);
        $this->assertNotEmpty($target->password);
        $oldchallenge = $target->challenge;
        $oldpassword  = $target->password;

        $response = Auth::instance()->hashKey('test', $target->challenge);
        $this->assertNotEmpty($response);
        $this->assertTrue($target->checkChallenge($response));
        $target->reload();

        $this->assertNotEmpty($target->challenge);
        $this->assertNotEmpty($target->password);
        $this->assertNotEquals($oldchallenge, $target->challenge);
        $this->assertNotEquals($oldpassword, $target->password);
    }

    /**
     * @covers Model_User::checkChallenge
     */
    public function testCheckChallengeFail()
    {
        $target = ORM::factory('User', $this->genNumbers['userFoo']);

        $this->assertNotEmpty($target->challenge);
        $this->assertNotEmpty($target->password);
        $oldchallenge = $target->challenge;
        $oldpassword  = $target->password;

        $response = Auth::instance()->hashKey('badpassword', $target->challenge);
        $this->assertNotEmpty($response);
        $this->assertFalse($target->checkChallenge($response));
        $target->reload();

        $this->assertEquals($oldchallenge, $target->challenge);
        $this->assertEquals($oldpassword, $target->password);
    }

    /**
     * @covers Model_User::_generateNewPassword
     */
    public function testGenerateNewPassword()
    {
        $target = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertEquals($target->password, $target->_generateNewPassword($target->challenge, 'test'));
    }

    /**
     * @covers Model_User::_generateNewChallenge
     */
    public function testGenerateNewChallenge()
    {
        $target       = ORM::factory('User', $this->genNumbers['userFoo']);
        $oldchallenge = $target->challenge;
        $oldpassword  = $target->password;
        list($newchallenge, $newpassword) = $target->_generateNewChallenge('mypassword');

        $this->assertNotEmpty($newchallenge);
        $this->assertNotEmpty($newpassword);
        $this->assertNotEquals($oldchallenge, $newchallenge);
        $this->assertNotEquals($oldpassword, $newpassword);

        $response = Auth::instance()->hashKey('mypassword', $newchallenge);
        $this->assertNotEmpty($response);

        $aes      = new Crypt_AES();
        $aes->setKey($response);
        $password = $aes->decrypt(hex2bin($newpassword));
        $this->assertNotEmpty($password);
        $hash     = Auth::instance()->hashKey($password, $newchallenge);
        $this->assertNotEmpty($hash);
        $this->assertEquals($response, $hash);
    }

    /**
     * @covers Model_User::_generateNewChallenge
     */
    public function testGenerateNewChallengeEmpty()
    {
        $target = ORM::factory('User', $this->genNumbers['userFoo']);
        list($newchallenge, $newpassword) = $target->_generateNewChallenge('');

        $this->assertEmpty($newchallenge);
        $this->assertEmpty($newpassword);
    }

    /**
     * @covers Model_User::create
     */
    public function testCreate()
    {
        $target           = ORM::factory('User');
        $target->email    = 'foobar@thomasmuguet.info';
        $target->username = 'foobar';
        $target->password = 'lorem ipsum';
        $target->create();

        $this->assertNotEmpty($target->challenge);
        $this->assertNotEmpty($target->password);
        $this->assertEquals($target->_generateNewPassword($target->challenge, 'lorem ipsum'), $target->password);
    }

    /**
     * @covers Model_User::update
     */
    public function testUpdatePassword()
    {
        $target           = ORM::factory('User', $this->genNumbers['userFoo']);
        $oldchallenge     = $target->challenge;
        $oldpassword      = $target->password;
        $target->password = 'lorem ipsum';
        $target->update();

        $this->assertNotEmpty($target->challenge);
        $this->assertNotEmpty($target->password);
        $this->assertNotEquals($oldchallenge, $target->challenge);
        $this->assertNotEquals($oldpassword, $target->password);
        $this->assertEquals($target->_generateNewPassword($target->challenge, 'lorem ipsum'), $target->password);
    }

    /**
     * @covers Model_User::update
     */
    public function testUpdatePasswordChallenge()
    {
        $target            = ORM::factory('User', $this->genNumbers['userFoo']);
        $target->password  = 'lorem ipsum';
        $target->challenge = 'fake';
        $target->update();

        $this->assertEquals('fake', $target->challenge);
        $this->assertEquals('lorem ipsum', $target->password);
    }

    /**
     * @covers Model_User::update
     */
    public function testUpdate()
    {
        $target         = ORM::factory('User', $this->genNumbers['userFoo']);
        $oldchallenge   = $target->challenge;
        $oldpassword    = $target->password;
        $target->logins = 10;
        $target->update();

        $this->assertEquals($oldchallenge, $target->challenge);
        $this->assertEquals($oldpassword, $target->password);
    }
}