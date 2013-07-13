<?php

/**
 * Displays account-related forms
 * 
 * @package Main
 */
class Controller_Account extends Controller
{

    /**
     * Displays the password edit form
     * @url http://example.com/account/edit
     */
    public function action_edit()
    {
        $view = View::factory('account/edit');
        $this->response->body($view);
    }

    /**
     * Displays the delete account form
     * @url http://example.com/account/delete
     */
    public function action_delete()
    {
        $view = View::factory('account/delete');
        $this->response->body($view);
    }
}