<?php

/**
 * Abstract controller class.
 *
 * @package Core
 */
abstract class Controller extends Kohana_Controller
{

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
        if ($this->requiredRole != Owaka::AUTH_ROLE_NONE) {
            if (!Auth::instance()->logged_in($this->requiredRole)) {
                Session::instance()->set("requested_url", $this->request->uri());
                $this->redirect('login');
                // @codeCoverageIgnoreStart
            }
            // @codeCoverageIgnoreEnd
        }
    }
}