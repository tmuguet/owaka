<?php

/**
 * API entry for managing authentication
 * 
 * @package Api
 */
class Controller_Api_Auth extends Controller_Api
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
            $this->respondOk(array('goto' => $goto));
        } else {
            $this->respondError(Response::UNPROCESSABLE, array('error' => 'Bad credentials'));
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
            $this->respondOk();
        } else {
            $this->respondError(Response::ERROR);
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
        $this->respondOk(array('loggedin' => ($success ? 'ok' : 'ko')));
    }
}