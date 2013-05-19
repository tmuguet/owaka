<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Accessor to reports
 */
class Controller_Report extends Controller
{

    /**
     * Accessor to reports
     * @url http://example.com/reports/<build>/<type>/<page>
     * @throws HTTP_Exception_404 Report not found or out of reports' path
     */
    public function action_index()
    {
        $buildId    = $this->request->param('id');
        $reportType = $this->request->param('type');
        $page       = $this->request->param('page');

        $path = realpath(APPPATH . 'reports/' . $buildId . '/' . $reportType . '/' . $page);
        if (!file_exists($path) || strpos($path, APPPATH . 'reports/' . $buildId . '/' . $reportType . '/') !== 0) {
            throw new HTTP_Exception_404();
        }

        $this->response->body(file_get_contents($path));
    }
}
