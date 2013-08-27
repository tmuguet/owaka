<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_RoleTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Model_Role::getRole
     */
    public function testGetRole()
    {
        $expected1 = ORM::factory('Role', 1);
        $actual1   = Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN);
        $this->assertEquals($expected1, $actual1);

        $expected2 = ORM::factory('Role', 2);
        $actual2   = Model_Role::getRole(Owaka::AUTH_ROLE_ADMIN);
        $this->assertEquals($expected2, $actual2);

        $expected3 = ORM::factory('Role', 3);
        $actual3   = Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL);
        $this->assertEquals($expected3, $actual3);
    }
}