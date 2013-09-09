<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays account-related forms
 * 
 * @package   Main
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Account extends Controller
{

    /**
     * Displays the password edit form
     * 
     * @url http://example.com/account/edit
     */
    public function action_edit()
    {
        $view = View::factory('account/edit');
        $this->response->body($view);
    }

    /**
     * Displays the delete account form
     * 
     * @url http://example.com/account/delete
     */
    public function action_delete()
    {
        $view = View::factory('account/delete');
        $this->response->body($view);
    }
}