<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Abstract controller class.
 *
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller extends Kohana_Controller
{

    /**
     * Required role for using methods
     * @var string
     */
    protected $requiredRole = Owaka::AUTH_ROLE_LOGIN;
#ifdef TESTING

    /**
     * Constructor for tests.
     * 
     * Initialises Request and Response if not provided
     * 
     * @param Request  $request  Request
     * @param Response $response Response
     */
    public function __construct(Request $request = NULL, Response $response = NULL)
    {
        if ($request === NULL) {
            $request = new Request('fake');
        }
        if ($response === NULL) {
            $response = new Response();
        }

        parent::__construct($request, $response);
    }
#endif

    /**
     * Checks credentials according to requiredRole
     */
    public function before()
    {
        $role   = $this->requiredRole;
        $action = 'requiredRole_' . $this->request->action();
        if (property_exists($this, $action)) {
            $role = $this->$action;
        }
        if ($role != Owaka::AUTH_ROLE_NONE) {
            if (!Auth::instance()->logged_in($role)) {
                $uri = $this->request->uri();
                if (!empty($uri) && $uri != '/') {
                    Session::instance()->set('requested_url', $uri);
                }
                $this->redirect('login');
                // @codeCoverageIgnoreStart
            }
            // @codeCoverageIgnoreEnd
        }

        $this->beginTransaction();
    }

    /**
     * Responds and commits the DB transaction
     * 
     * @param mixed $content Content
     */
    /* protected */ function success($content)
    {
        $this->commitTransaction();
        $this->response->body($content);
    }

    /**
     * Responds and rolls back the DB transaction
     * 
     * @param mixed $content Content
     */
    /* protected */ function error($content)
    {
        $this->rollbackTransaction();
        $this->response->body($content);
    }

    /**
     * Begins a new DB transaction
     */
    /* protected */ function beginTransaction()
    {
        Database::instance()->begin();
    }

    /**
     * Commits the current DB transaction and starts a new one
     */
    /* protected */ function commitTransaction()
    {
        Database::instance()->commit();
    }

    /**
     * Rolls back the current DB transaction and starts a new one
     */
    /* protected */ function rollbackTransaction()
    {
        Database::instance()->rollback();
    }
}