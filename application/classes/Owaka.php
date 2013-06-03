<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Owaka helper class
 * 
 * @package Core
 */
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
    const ICON_BOOK4       = 'book4';
    const ICON_CLOCK       = 'clock';
    const ICON_DOC         = 'doc';
    const ICON_FLAG        = 'flag';
    const ICON_METER       = 'meter';
    const ICON_PAD         = 'pad';
    const ICON_PIC         = 'pic';
    const ICON_SECURITY    = 'security';
    const ICON_TARGET      = 'target';

    /**
     * Gets the URI to a report
     * @param int $buildId Build ID
     * @param string $processor Processor which generated the report
     * @param string|null $type Type of report to find. If null, returns the first existing report.
     * @return string|null URI to report, or null if not found
     * @see Controller_Processors_Base::getInputReports()
     */
    static public function getReportUri($buildId, $processor, $type = NULL)
    {
        $processorClass = 'Controller_Processors_' . $processor;
        if (!class_exists($processorClass)) {
            throw new Exception("Cannot find processor $processor");
        }
        $reports = $processorClass::getInputReports();
        $root    = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $buildId . DIRECTORY_SEPARATOR . $processor . DIRECTORY_SEPARATOR;
        $uri     = 'reports/' . $buildId . '/' . $processor . '/';
        if ($type != NULL) {
            if (!isset($reports[$type])) {
                throw new Exception("Report type $type is not defined for $processor");
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
     * Processes and formats a link
     * @param string $from Source dashboard (main, project or build)
     * @param array $link Link information
     * @param string $outputAs Output format (html or js)
     * @return string Formatted link
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
                    if ($from == "main") {
                        $url   = 'dashboard/project/' . $link['id'];
                        $title = 'project';
                    } else {
                        return null;
                    }
                    break;

                case 'build':
                    if ($from == "main" || $from == "project") {
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