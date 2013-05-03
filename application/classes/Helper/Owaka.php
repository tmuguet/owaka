<?php

class Helper_Owaka
{

    static public function getReportsPath($buildId, $reportType)
    {
        $build = ORM::factory('Build', $buildId);

        $reportPath = str_replace(array('%rev%'), array($build->revision), $build->project->reports_path);
        if (substr($reportPath, 0, -1) != DIRECTORY_SEPARATOR) {
            $reportPath .= DIRECTORY_SEPARATOR;
        }

        if (strpos($reportPath, '%subfolder%') !== 0) {
            $paths = glob(str_replace('%subfolder%', '*', $reportPath) . '/.owaka', GLOB_MARK);
            foreach ($paths as $path) {
                if (trim(file_get_contents($path)) == $buildId) {
                    $reportPath = substr($path, 0, -6);
                    break;
                }
            }
            if ($reportPath === FALSE) {
                throw new Exception('Cannot find report path for build ' . $buildId . ' in ' . $build->project->reports_path);
            }
        }

        $report = $reportType . '_report';
        $reportPath .= $build->project->$report;

        return $reportPath;
    }
}