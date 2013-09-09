<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Request
 * 
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Request extends Kohana_Request
{
#ifdef TESTING

    /**
     * Short-hand method for loggin in with a certain role.
     * 
     * @param string $role Role
     * 
     * @return self
     */
    public function login($role = Owaka::AUTH_ROLE_LOGIN)
    {
        $user = ORM::factory('User', array('username' => 'ut-' . $role));
        if (!$user->loaded()) {
            $user->email     = 'ut-' . $role . '@thomasmuguet.info';
            $user->username  = 'ut-' . $role;
            $user->password  = 'test';
            $user->challenge = 'none';
            $user->create();
            $user->add('roles', ORM::factory('Role', array('name' => $role)));
        }

        Auth::instance()->force_login($user);
        return $this;
    }

    /**
     * Sets a parameter of the request
     * 
     * @param string $key   Key
     * @param string $value Value
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
#endif
}