<?php

/**
 * Auth role model
 * 
 * @package Model
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