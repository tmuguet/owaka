<?php

/**
 * Displays user-related forms
 * 
 * @package Main
 */
class Controller_User extends Controller
{

    protected $requiredRole = Owaka::AUTH_ROLE_ADMIN;

    /**
     * Displays the list of users
     * 
     * @url http://example.com/user/list
     */
    public function action_list()
    {
        $users = json_decode(Request::factory('api/user/list')->execute()->body(), TRUE);

        $view = View::factory('user/list')
                ->set('users', $users);
        $this->response->body($view);
    }

    /**
     * Displays the add user form
     * 
     * @url http://example.com/user/add
     */
    public function action_add()
    {
        $view = View::factory('user/add');
        $this->response->body($view);
    }

    /**
     * Displays the edit user form
     * 
     * @url http://example.com/user/edit/&lt;user_id&gt;
     */
    public function action_edit()
    {
        $user = ORM::factory('User', $this->request->param('id'));

        $view = View::factory('user/edit')
                ->set('user', $user);
        $this->response->body($view);
    }
}