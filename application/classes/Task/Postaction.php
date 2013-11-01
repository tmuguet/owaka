<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for running post-build actions
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Postaction extends Minion_Task
{

    // @codingStandardsIgnoreStart
    protected $_options = array(
        'postaction' => NULL,
        'id'         => NULL,
        'build'      => NULL,
    );

    /**
     * Executes the task
     * 
     * @param array $params Parameters
     */
    protected function _execute(array $params)
    {
        try {
            if (!empty($params['build'])) {
                $build = $params['build'];
            } else {
                $build = ORM::factory('Build', $params['id']);
            }
            if (!$build->loaded()) {
                echo 'No build';
                return;
            }

            if (!empty($params['postaction'])) {
                $this->run($params, $build);
            } else {
                $this->runAll($build);
            }

            echo 'ok';
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    // @codingStandardsIgnoreEnd

    /**
     * Processes build for a post action
     * 
     * @param array       $params Parameters. $params['postaction'] must be defined
     * @param Model_Build &$build Build
     */
    protected function run(array $params, Model_Build &$build)
    {
        $postactionClass = 'Postaction_' . ucfirst($params['postaction']);
        if (!class_exists($postactionClass)) {
            echo $params['postaction'] . ' is not a valid post action';
            return;
        }
        $postaction = new $postactionClass();
        $postaction->process($build, $postaction::projectParameters($build->project_id));
    }

    /**
     * Processes build for all post actions
     * 
     * @param Model_Build &$build Build
     */
    protected function runAll(Model_Build &$build)
    {
        $postactions = ORM::factory('Project_Postaction')
                ->where('project_id', '=', $build->project_id)
                ->find_all();
        foreach ($postactions as $postaction) {
            $postactionClass = 'Postaction_' . $postaction->postaction;
            Kohana::$log->add(Log::INFO, 'Executing post action ' . $postaction->postaction . '...');
            $action          = new $postactionClass();
            $action->process($build, $action::projectParameters($build->project_id));
        }
    }
}