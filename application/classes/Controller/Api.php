<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base class for API
 * @package    Api
 */
abstract class Controller_Api extends Controller
{

    /**
     * Responds with an OK status
     * @return self
     */
    /* protected */ function respondOk(array $data = array())
    {
        $this->response->headers('Content-Type', 'application/json');
        $this->response->status(Response::OK);
        $this->response->body(json_encode($data));
        return $this;
    }

    /**
     * Responds with an error status
     * @return self
     */
    /* protected */ function respondError($status, array $data = array())
    {
        $this->response->headers('Content-Type', 'application/json');
        $this->response->status($status);
        $this->response->body(json_encode($data));
        return $this;
    }
}