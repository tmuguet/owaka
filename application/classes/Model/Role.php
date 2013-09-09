<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Auth role model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Role extends Model_Auth_Role
{

    /**
     * Gets role based on its name
     * 
     * @param string $role Role
     * 
     * @return Model_Role
     */
    static public function getRole($role)
    {
        return ORM::factory('Role', array("name" => $role));
    }
}