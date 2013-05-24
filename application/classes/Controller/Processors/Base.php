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

    public function action_copy()
    {
        $buildId = $this->request->param('id');

        $destinationDirectory = APPPATH . '/reports/' . $buildId . '/' . $this->getName() . '/';
        foreach (static::getInputReports() as $type => $info) {
            $source      = $this->getInputReportCompletePath($buildId, $type);
            $destination = $destinationDirectory . $info['keep-as'];    // TODO: this can get messy if mis-used !
            if (!empty($source) && !empty($destination)) {
                if (!file_exists($destinationDirectory)) {
                    mkdir($destinationDirectory, 0700);
                }

                if ($info['type'] == 'dir') {
                    if (!file_exists($destination)) {
                        exec('cp -R ' . $source . ' ' . $destination);    // TODO: use PHP functions
                    } else {
                        exec('cp -R ' . $source . '/* ' . $destination);    // TODO: use PHP functions
                    }
                } else {
                    exec('cp ' . $source . ' ' . $destination); // TODO: use PHP functions
                }
            }
        }
    }

    protected function getName()
    {
        return strtolower(str_replace("Controller_Processors_", "", get_called_class()));
    }

    protected function getReportName($type)
    {
        return $this->getName() . '_' . $type;
    }

    protected function getInputReportCompletePath($buildId, $type)
    {
        $build = ORM::factory('Build', $buildId);
        $name  = ORM::factory('Project_Report')->search($build->project_id, $this->getReportName($type));
        if (empty($name)) {
            return NULL;
        }

        $reportPath = str_replace(
                array('%rev%', '%build%'), array($build->revision, $build->id), $build->project->reports_path
        );
        if (substr($reportPath, 0, -1) != DIRECTORY_SEPARATOR) {
            $reportPath .= DIRECTORY_SEPARATOR;
        }
        return $reportPath . $name;
    }

    protected function getReportCompletePath($buildId, $type)
    {
        $destination = APPPATH . '/reports/' . $buildId . '/' . $this->getName() . '/';
        foreach (static::getInputReports() as $key => $info) {
            if ($info == $type) {
                $path = realpath($destination . $info['keep-as']);
                if (!empty($path)) {
                    return $path;
                } else {
                    return NULL;
                }
            }
        }
        return NULL;
    }

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

    public function action_analyze()
    {
        $buildId = $this->request->param('id');
        $result  = NULL;
        if (method_exists($this, 'analyze')) {
            $result = $this->analyze(ORM::factory('Build', $buildId));
        }

        $this->response->body($result);
    }
}