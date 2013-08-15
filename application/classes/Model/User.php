<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User model
 * 
 * @package Model
 */
class Model_User extends Model_Auth_User
{

    /**
	 * Rules for the user model.
	 *
	 * @return array Rules
	 */
	public function rules()
    {
        return array(
            'username' => array(
                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
                array('not_empty'),
                array('different', array(':value', Auth::instance()->hash(''))),    // True non-empty test
            ),
            'email'    => array(
                array('not_empty'),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
        );
    }
}