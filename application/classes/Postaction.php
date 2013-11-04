<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all post-build actions
 * 
 * @package   Postaction
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Postaction
{

    /**
     * Processor parameters
     * @var array
     * @todo Document this
     */
    public static $parameters = array();

    /**
     * Gets the processor parameters
     * 
     * @param int $projectId Project ID
     * 
     * @return array
     */
    static public function projectParameters($projectId)
    {
        $params     = ORM::factory('Project_Postaction_Parameter')
                ->where('project_id', '=', $projectId)
                ->where('postaction', '=', static::_getName())
                ->find_all();
        $parameters = array();
        foreach (static::$parameters as $key => $info) {
            $parameters[$key] = $info['defaultvalue'];
        }
        foreach ($params as $param) {
            $parameters[$param->type] = $param->value;
        }
        return $parameters;
    }

    /**
     * Processes a build
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Post action parameters
     * 
     * @return bool true if build successfully treated; false otherwise
     */
    abstract public function process(Model_Build &$build, array $parameters);

    /**
     * Gets the name of the action being called
     * 
     * @return string
     */
    static /* private */ final function _getName()
    {
        return strtolower(str_replace('Postaction_', '', get_called_class()));
    }
}