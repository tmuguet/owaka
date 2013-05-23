<?php

/**
 * Base class for all processors
 */
abstract class Controller_Processors_Base extends Controller
{

    /**
     * Processes a report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     * @throws Exception Broken report (unexpected format)
     */
    abstract public function process($buildId);

    /**
     * Processes a report
     * @url http://example.com/processors_<processor>/process/<build_id>
     */
    public function action_process()
    {
        $buildId = $this->request->param('id');
        $result  = $this->process($buildId);
        if ($result) {
            $this->response->body("true");
        } else {
            $this->response->body("false");
        }
    }
    
    public function action_analyze() {
        $buildId = $this->request->param('id');
        $result = NULL;
        if (method_exists($this, 'analyze')) {
            $result = $this->analyze(ORM::factory('Build', $buildId));
        }
        
        $this->response->body($result);
    }
}