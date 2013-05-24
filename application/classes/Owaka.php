<?php
defined('SYSPATH') OR die('No direct access allowed.');

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
    const ICON_PAD         = 'pad';
    const ICON_PIC         = 'pic';
    const ICON_SECURITY    = 'security';
    const ICON_TARGET      = 'target';

    static public function getReportUri($buildId, $processor, $type = NULL)
    {
        $processorClass = 'Controller_Processors_' . $processor;
        $reports        = $processorClass::getInputReports();
        $root           = APPPATH . '/reports/' . $buildId . '/' . $processor . '/';
        $uri            = 'reports/' . $buildId . '/' . $processor . '/';
        if ($type != NULL) {
            $path = realpath($root . $reports[$type]['keep-as']);
            if (!empty($path)) {
                return $uri . $reports[$type]['keep-as'];
            }
        } else {
            // Find first available
            foreach ($reports as $key => $info) {
                $path = realpath($root . $info['keep-as']);
                if (!empty($path)) {
                    return $uri . $info['keep-as'];
                }
            }
        }
        return NULL;
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