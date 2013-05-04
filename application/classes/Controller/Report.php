<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Report extends Controller
{

    public function action_index()
    {
        $buildId    = $this->request->param('id');
        $reportType = $this->request->param('type');
        $page       = $this->request->param('page');

        $path = realpath(APPPATH . 'reports/' . $buildId . '/' . $reportType . '/' . $page);
        if (strpos($path, APPPATH . 'reports/' . $buildId . '/' . $reportType . '/') !== 0) {
            throw new HTTP_Exception_404();
        }
        
        $this->response->body(file_get_contents($path));
    }
}

// End Welcome
