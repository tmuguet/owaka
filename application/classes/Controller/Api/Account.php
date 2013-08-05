<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing a user's own account
 * 
 * @package Api
 */
class Controller_Api_Account extends Controller_Api
{

    /**
     * Changes the password
     * 
     * @url http://example.com/api/account/edit
     * @postparam password string New password, in plain-text
     */
    public function action_edit()
    {
        try {
            $user           = Auth::instance()->get_user();
            $user->password = $this->request->post('password');
            $user->update();

            $this->respondOk();
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors()));
        }
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

        $this->respondOk();
    }
}