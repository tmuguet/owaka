<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Accessor to reports
 * 
 * @package   Reports
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Report extends Controller
{

    /**
     * Accessor to reports
     * 
     * @url http://example.com/reports/&lt;build&gt;/&lt;type&gt;/&lt;page&gt;
     * @throws HTTP_Exception_404 Report not found or out of reports' path
     */
    public function action_index()
    {
        $buildId    = $this->request->param('id');
        $reportType = $this->request->param('type');
        $page       = $this->request->param('page');

        $path = realpath(APPPATH . 'reports' . DIR_SEP . $buildId . DIR_SEP . $reportType . DIR_SEP . $page);
        if (empty($path) || strpos($path, APPPATH . 'reports' . DIR_SEP . $buildId . DIR_SEP . $reportType . DIR_SEP) !== 0) {
            throw new HTTP_Exception_404();
        }
        
        $mime = File::mime_by_ext(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
        $this->response->headers('Content-Type', $mime);
        $this->success(file_get_contents($path));
    }
}
