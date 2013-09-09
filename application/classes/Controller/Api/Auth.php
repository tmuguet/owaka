<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * API entry for managing authentication
 * 
 * @api
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Api_Auth extends Controller_Api
{

    protected $requiredRole = Owaka::AUTH_ROLE_NONE;

    /**
     * Gets a new challenge
     * 
     * @url http://example.com/api/auth/challenge
     * @postparam user string Username
     */
    public function action_challenge()
    {
        $post = $this->request->post();
        $user = ORM::factory('User', array('username' => $post['user']));
        if ($user->loaded()) {
            $challenge = $user->challenge;
        } else {
            // Give a fake challenge so it's less easy to distinguish if a user exists or not
            // The challenge must depend only on the username because it must be the same if called twice
            $challenge = Auth::instance()->hash($post['user']);
        }

        $this->respondOk(array('challenge' => $challenge));
    }

    /**
     * Logs in
     * 
     * @url http://example.com/api/auth/login
     * @postparam user string Username
     * @postparam response string Response to challenge
     */
    public function action_login()
    {
        $post    = $this->request->post();
        $success = Auth::instance()->login($post['user'], $post['response']);

        if ($success) {
            $goto = Session::instance()->get('requested_url', 'dashboard/main');
            $this->respondOk(array('goto' => $goto));
        } else {
            $this->respondError(
                    Response::UNPROCESSABLE, array('errors' => array('user'     => 'Bad credentials', 'password' => ''))
            );
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