<?php

class Owaka
{

    const WIDGET_MAIN      = 'main';
    const WIDGET_PROJECT   = 'project';
    const WIDGET_BUILD     = 'build';
    const WIDGET_SAMPLE    = 'sample';
    const WIDGET_VIRTUAL   = 'virtual';
    const GRIDCELL_SIZE    = 80;
    const GRIDCELL_SPACE   = 20;
    const ICON_ADDRESSBOOK = 'addressbook';
    const ICON_AEROPLANE   = 'aeroplane';
    const ICON_ALARM       = 'alarm';
    const ICON_ANCHOR      = 'anchor';
    const ICON_ANNOUNCER   = 'announcer';
    const ICON_CLOCK       = 'clock';
    const ICON_DOC         = 'doc';
    const ICON_FLAG        = 'flag';
    const ICON_PAD         = 'pad';
    const ICON_PIC         = 'pic';
    const ICON_SECURITY    = 'security';
    const ICON_TARGET      = 'target';

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

    static public function processLink($from, $link, $outputAs = 'html')
    {
        $url     = '';
        $title   = '';
        $onclick = '';
        if (isset($link['type'])) {
            switch ($link['type']) {
                case 'project':
                    if ($from == "main") {
                        $url   = 'dashboard/project/' . $link['id'];
                        $title = 'project';
                    }
                    break;

                case 'build':
                    if ($from == "main" || $from == "project") {
                        $url   = 'dashboard/build/' . $link['id'];
                        $title = 'build';
                    }
                    break;
            }
        } else {
            $url   = $link['url'];
            $title = $link['title'];
            if (isset($link['js'])) {
                $onclick = $link['js'];
            }
        }

        $content = '';
        switch ($outputAs) {
            case 'html':
                $content = '<a href="';
                if (empty($url) && !empty($onclick)) {
                    $content .= 'javascript:void(0)';
                } else {
                    $content .= $url;
                }
                $content .= '"';
                if (!empty($onclick)) {
                    $content .= ' onclick="' . $onclick . '"';
                }
                $content .= '>' . $title . '</a>';
                break;

            case 'js':
                if (!empty($onclick)) {
                    $content = $onclick;
                } else {
                    $content = 'document.location.href=\'' . $url . '\';';
                }
                break;
        }

        return $content;
    }
}