<?php

/**
 * API entry for managing authentication
 * @package    Api
 */
class Controller_Api_auth extends Controller
{

    protected $requiredRole = Owaka::AUTH_ROLE_NONE;

    /**
     * Logs in
     * 
     * @url http://example.com/api/auth/login
     * @postparam user string Username
     * @postparam password string Plain password
     */
    public function action_login()
    {
        $post    = $this->request->post();
        $success = Auth::instance()->login($post['user'], $post['password']);

        if ($success) {
            $goto = Session::instance()->get('requested_url', 'dashboard/main');
            if (isset($post['plain'])) {
                $this->redirect($goto);
                // @codeCoverageIgnoreStart
            } else {
                // @codeCoverageIgnoreEnd
                $this->response->body(json_encode(array("res"  => "ok", "goto" => $goto)));
            }
        } else {
            if (isset($post['plain'])) {
                $this->redirect('login');
                // @codeCoverageIgnoreStart
            } else {
                // @codeCoverageIgnoreEnd
                $this->response->body(json_encode(array("res" => "ko")));
            }
        }
    }

    /**
     * Logs out
     * 
     * @url http://example.com/api/auth/logout
     */
    public function action_logout()
    {
        $success = Auth::instance()->logout(true, true);

        // @codeCoverageIgnoreStart
        // Can't reproduce !$success
        if ($success) {
            $this->response->body(json_encode(array("res" => "ok")));
        } else {
            $this->response->body(json_encode(array("res" => "ko")));
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns whether the user is logged in or not
     * 
     * @url http://example.com/api/auth/loggedin
     */
    public function action_loggedin()
    {
        $success = Auth::instance()->logged_in();

        if ($success) {
            $this->response->body(json_encode(array("res" => "ok")));
        } else {
            $this->response->body(json_encode(array("res" => "ko")));
        }
    }
}