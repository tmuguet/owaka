<?php

/**
 * Displays user-related forms
 * 
 * @package Main
 */
class Controller_User extends Controller
{

    /**
     * Displays the add user form
     * @url http://example.com/user/add
     */
    public function action_add()
    {
        $view = View::factory('user/add');
        $this->response->body($view);
    }

    /**
     * Displays the edit user form
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