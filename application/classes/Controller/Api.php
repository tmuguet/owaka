<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base class for API
 * 
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller_Api extends Controller
{

    /**
     * Responds with an OK status
     * 
     * @param array $data Data to provide in response
     * 
     * @return self
     */
    /* protected */ function respondOk(array $data = array())
    {
        $this->response->headers('Content-Type', 'application/json');
        $this->response->status(Response::OK);
        $this->success(json_encode($data));
        return $this;
    }

    /**
     * Responds with an error status
     * 
     * @param int   $status HTTP Status
     * @param array $data   Data to provide in response
     * 
     * @return self
     */
    /* protected */ function respondError($status, array $data = array())
    {
        $this->response->headers('Content-Type', 'application/json');
        $this->response->status($status);
        $this->error(json_encode($data));
        return $this;
    }
}