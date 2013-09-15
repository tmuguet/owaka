<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Owaka helper class
 * 
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Owaka
{

    const AUTH_ROLE_NONE     = 'none';
    const AUTH_ROLE_LOGIN    = 'login';
    const AUTH_ROLE_ADMIN    = 'admin';
    const AUTH_ROLE_INTERNAL = 'internal';
    const BUILD_OK           = 'ok';
    const BUILD_UNSTABLE     = 'unstable';
    const BUILD_ERROR        = 'error';
    const BUILD_BUILDING     = 'building';
    const BUILD_QUEUED       = 'queued';
    const BUILD_NODATA       = 'nodata';
    const WIDGET_MAIN        = 'main';
    const WIDGET_PROJECT     = 'project';
    const WIDGET_BUILD       = 'build';
    const WIDGET_SAMPLE      = 'sample';
    const WIDGET_VIRTUAL     = 'virtual';
    const GRIDCELL_SIZE      = 80;
    const GRIDCELL_SPACE     = 20;

    /**
     * Gets the URI to a report
     * 
     * @param int         $buildId   Build ID
     * @param string      $processor Processor which generated the report
     * @param string|null $type      Type of report to find. If null, returns the first existing report.
     * 
     * @return string|null URI to report, or null if not found
     * @throws InvalidArgumentException Invalid processor
     * @throws InvalidArgumentException Invalid report type
     * @see Controller_Processor::inputReports
     */
    static public function getReportUri($buildId, $processor, $type = NULL)
    {
        $processorClass = 'Controller_Processor_' . ucfirst($processor);
        if (!class_exists($processorClass)) {
            throw new InvalidArgumentException('Cannot find processor ' . $processor);
        }
        $reports = $processorClass::$inputReports;
        $root    = APPPATH . 'reports' . DIR_SEP . $buildId . DIR_SEP . $processor . DIR_SEP;
        $uri     = 'reports/' . $buildId . '/' . $processor . '/';
        if ($type != NULL) {
            if (!isset($reports[$type])) {
                throw new InvalidArgumentException('Report type ' . $type . ' is not defined for ' . $processor);
            }

            $path = realpath($root . $reports[$type]['keep-as']);
            if (!empty($path)) {
                return $uri . $reports[$type]['keep-as'];
            }
        } else {
            // Find first available
            foreach ($reports as $info) {
                $path = realpath($root . $info['keep-as']);
                if (!empty($path)) {
                    return $uri . $info['keep-as'];
                }
            }
        }
        return NULL;
    }

    /**
     * Gets the processor parameters for a project
     * 
     * @param int    $projectId Project ID
     * @param string $processor Processor
     * 
     * @return array
     * @throws InvalidArgumentException Invalid processor
     * @see Controller_Processor::parameters
     */
    static public function getReportParameters($projectId, $processor)
    {
        $processorClass = 'Controller_Processor_' . ucfirst($processor);
        if (!class_exists($processorClass)) {
            throw new InvalidArgumentException('Cannot find processor ' . $processor);
        }
        return $processorClass::projectParameters($projectId);
    }

    /**
     * Processes and formats a link
     * 
     * @param string $from     Source dashboard (main, project or build)
     * @param array  $link     Link information
     * @param string $outputAs Output format (html or js)
     * 
     * @return string Formatted link
     * 
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    static public function processLink($from, $link, $outputAs = 'html')
    {
        $url     = '';
        $title   = '';
        $onclick = '';
        $class   = '';
        if (isset($link['type'])) {
            switch ($link['type']) {
                case 'project':
                    if ($from == 'main') {
                        $url   = 'dashboard/project/' . $link['id'];
                        $title = 'project';
                    } else {
                        return null;
                    }
                    break;

                case 'build':
                    if ($from == 'main' || $from == 'project') {
                        $url   = 'dashboard/build/' . $link['id'];
                        $title = 'build';
                    } else {
                        return null;
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
        if (isset($link['class'])) {
            $class = $link['class'];
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
                if (!empty($class)) {
                    $content .= ' class="' . $class . '"';
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