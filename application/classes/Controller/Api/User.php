<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing users
 * 
 * @package Api
 */
class Controller_Api_User extends Controller_Api
{

    protected $requiredRole = Owaka::AUTH_ROLE_ADMIN;

    /**
     * Returns the list of users
     * 
     * Returns an array of objects order by username:
     * {id: int, username: string, email: string, logins: int, last_login: int, enabled: bool, admin: bool}
     * 
     * @url http://example.com/api/user/list
     */
    public function action_list()
    {
        $users = ORM::factory('User')
                ->order_by('username', 'ASC')
                ->find_all();

        $admin    = Model_Role::getRole(Owaka::AUTH_ROLE_ADMIN);
        $enabled  = Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN);
        $internal = Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL);

        $output = array();
        foreach ($users as $user) {
            if (!$user->has('roles', $internal)) {
                $output[] = array(
                    "id"         => $user->id,
                    "username"   => $user->username,
                    "email"      => $user->email,
                    "logins"     => $user->logins,
                    "last_login" => $user->last_login,
                    "enabled"    => $user->has('roles', $enabled),
                    "admin"      => $user->has('roles', $admin),
                );
            }
        }
        $this->respondOk($output);
    }

    /**
     * Adds a new user
     * 
     * @url http://example.com/api/user/add
     * @postparam email string Email
     * @postparam username string Username
     * @postparam password string Password, in plain-text
     * @postparam admin bool Indicates whether the user has the admin role
     */
    public function action_add()
    {
        try {
            $user           = ORM::factory('User');
            $user->email    = $this->request->post('email');
            $user->username = $this->request->post('username');
            $user->password = $this->request->post('password');
            $user->create();

            $user->add('roles', Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN));

            if ($this->request->post('admin')) {
                $user->add('roles', Model_Role::getRole(Owaka::AUTH_ROLE_ADMIN));
            }
            $this->respondOk(array('user' => $user->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Changes the password of a user
     * 
     * @url http://example.com/api/user/edit/&lt;user_id&gt;
     * @postparam password string New password, in plain-text
     */
    public function action_edit()
    {
        try {
            $user = ORM::factory('User', $this->request->param('id'));
            if (!$user->loaded() || $user->has('roles', Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL))) {
                throw new HTTP_Exception_404();
            }
            $user->password = $this->request->post('password');
            $user->update();

            $this->respondOk(array('user' => $user->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Enables a user
     * 
     * @url http://example.com/api/user/enable/&lt;user_id&gt;
     */
    public function action_enable()
    {
        $user = ORM::factory('User', $this->request->param('id'));
        if (!$user->loaded() || $user->has('roles', Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL))) {
            throw new HTTP_Exception_404();
        }
        $user->add('roles', Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN));

        $this->respondOk(array('user' => $user->id));
    }

    /**
     * Disables a user
     * 
     * @url http://example.com/api/user/disable/&lt;user_id&gt;
     */
    public function action_disable()
    {
        $user = ORM::factory('User', $this->request->param('id'));
        if (!$user->loaded() || $user->has('roles', Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL))) {
            throw new HTTP_Exception_404();
        }
        $user->remove('roles', Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN));

        $this->respondOk(array('user' => $user->id));
    }

    /**
     * Deletes a user
     * 
     * @url http://example.com/api/user/delete/&lt;user_id&gt;
     */
    public function action_delete()
    {
        $user = ORM::factory('User', $this->request->param('id'));
        if (!$user->loaded() || $user->has('roles', Model_Role::getRole(Owaka::AUTH_ROLE_INTERNAL))) {
            throw new HTTP_Exception_404();
        }
        $user->remove('roles');
        $id = $user->id;
        $user->delete();

        $this->respondOk(array('user' => $id));
    }
}