<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing a user's own account
 * @package    Api
 */
class Controller_Api_account extends Controller
{

    /**
     * Changes the password
     * 
     * @url http://example.com/api/account/edit
     * @postparam password string New password, in plain-text
     */
    public function action_edit()
    {
        $user           = Auth::instance()->get_user();
        $user->password = $this->request->post('password');
        $user->update();

        $this->response->body(json_encode(array("res" => "ok")));
    }

    /**
     * Deletes the account
     * 
     * @url http://example.com/api/account/delete
     */
    public function action_delete()
    {
        $user = Auth::instance()->get_user();
        $user->remove('roles');
        $user->delete();

        $this->response->body(json_encode(array("res" => "ok")));
    }
}