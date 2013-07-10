<?php

/**
 * Displays authentication forms
 * 
 * @package Main
 */
class Controller_Auth extends Controller
{

    protected $requiredRole = Owaka::AUTH_ROLE_NONE;

    /**
     * Displays the login form
     * @url http://example.com/login
     */
    public function action_login()
    {
        if (Auth::instance()->logged_in()) {
            $this->redirect(Session::instance()->get('requested_url', 'dashboard/main'));
            // @codeCoverageIgnoreStart
        }
        // @codeCoverageIgnoreEnd

        $view = View::factory('login');

        $this->response->body($view);
    }

    /**
     * Logs out and redirects the user to the login page
     * @url http://example.com/logout
     */
    public function action_logout()
    {
        Request::factory('api/auth/logout')->execute();
        $this->redirect('login');
    }
}