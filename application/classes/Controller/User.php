<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays user-related forms
 * 
 * @package   Main
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
        $this->success($view);
    }

    /**
     * Displays the add user form
     * 
     * @url http://example.com/user/add
     */
    public function action_add()
    {
        $view = View::factory('user/add');
        $this->success($view);
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
        $this->success($view);
    }
}