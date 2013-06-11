<?php
#ifdef TESTING
require_once DOCROOT . 'private' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'RequestStub.php';
#endif

abstract class Controller extends Kohana_Controller
{
#ifdef TESTING

    public function __construct(Request $request = NULL, Response $response = NULL)
    {
        if ($request === NULL) {
            $request = new RequestStub('fake');
        }
        if ($response === NULL) {
            $response = new Response();
        }

        parent::__construct($request, $response);
    }
#endif
}