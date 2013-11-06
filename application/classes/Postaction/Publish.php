<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Publish reports
 * 
 * @package   Postaction
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Postaction_Publish extends Postaction
{

    static public $parameters = array(
        'codesniffer-xml' => array(
            'title'        => 'Codesniffer file',
            'description'  => 'File where to publish codesniffer report; leave empty to ignore',
            'defaultvalue' => ''
        ),
        'coverage-dir'    => array(
            'title'        => 'Coverage folder',
            'description'  => 'Folder where to publish coverage reports; leave empty to ignore',
            'defaultvalue' => ''
        ),
        'phpdoc-report'   => array(
            'title'        => 'phpdoc folder',
            'description'  => 'Folder where to publish phpdoc; leave empty to ignore',
            'defaultvalue' => ''
        ),
        'phpunit-report'  => array(
            'title'        => 'phpunit folder',
            'description'  => 'Folder where to publish phpunit; leave empty to ignore',
            'defaultvalue' => ''
        ),
    );

    /**
     * Publish reports after build when available
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Post action parameters
     * 
     * @return bool true if at least 1 report published
     */
    public function process(Model_Build &$build, array $parameters)
    {
        $result = false;
        foreach (array_keys(self::$parameters) as $key) {
            $result = $this->publish($key, $build, $parameters) || $result;
        }
        return $result;
    }

    /**
     * Publishes a report
     * 
     * @param string      $key        Key
     * @param Model_Build &$build     Build
     * @param array       $parameters Post action parameters
     * 
     * @return boolean True if copied
     */
    protected function publish($key, Model_Build &$build, array $parameters)
    {
        $type = explode('-', $key);
        $path = APPPATH . Owaka::getReportUri($build->id, $type[0], $type[1]);  // TODO: fix me
        if (!empty($path) && !empty($parameters[$key])) {
            Kohana::$log->add(Log::INFO, 'Publishing ' . $key . ' in ' . $parameters[$key]);

            // @codeCoverageIgnoreStart
            if (file_exists($parameters[$key])) {
                File::rrmdir($parameters[$key]);
            }
            if (!file_exists(dirname($parameters[$key]))) {
                mkdir(dirname($parameters[$key]), 0700, true);
            }
            // @codeCoverageIgnoreEnd

            File::rcopy($path, $parameters[$key]);
            return true;
        }
        return false;
    }
}
