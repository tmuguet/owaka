<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * API entry for managing installs and updates
 * 
 * @api
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Api_Install extends Controller_Api
{

    protected $requiredRole = Owaka::AUTH_ROLE_NONE;

    /**
     * Installs owaka by adding an admin.
     * 
     * Fails if users already exist in database.
     * 
     * @url http://example.com/api/install/do
     * @postparam email string Email
     * @postparam username string Username
     * @postparam password string Password, in plain-text
     */
    public function action_do()
    {
        $usersCount = ORM::factory('User')->count_all();
        if ($usersCount > 1) {
            $this->respondError(Response::GONE, array('error' => 'Already installed'));
            return;
        }

        try {
            $user           = ORM::factory('User');
            $user->email    = $this->request->post('email');
            $user->username = $this->request->post('username');
            $user->password = $this->request->post('password');
            $user->create();

            $user->add('roles', Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN));
            $user->add('roles', Model_Role::getRole(Owaka::AUTH_ROLE_ADMIN));

            @unlink(DOCROOT . 'install.php');

            $this->respondOk();
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }
}