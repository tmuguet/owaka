<?php

class Request extends Kohana_Request
{
#ifdef TESTING

    /**
     * Short-hand method for loggin in with a certain role.
     * @param $role string Role
     */
    public function login($role = Owaka::AUTH_ROLE_LOGIN)
    {
        $user = ORM::factory('User', array('username' => 'ut-' . $role));
        if (!$user->loaded()) {
            $user->email    = 'ut-' . $role . '@thomasmuguet.info';
            $user->username = 'ut-' . $role;
            $user->password = 'test';
            $user->create();
            $user->add('roles', ORM::factory('Role', array('name' => $role)));
        }

        Auth::instance()->force_login($user);
        return $this;
    }

    /**
     * Sets a parameter of the request
     * @param $key string Key
     * @param $value string Value
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
#endif
}