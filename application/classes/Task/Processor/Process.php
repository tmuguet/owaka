<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for processing reports for a processor
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Processor_Process extends Minion_Task
{

    // @codingStandardsIgnoreStart
    protected $_options = array(
        'processor' => NULL,
        'id'        => NULL,
        'build'     => NULL,
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

            if (!empty($params['processor'])) {
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

    protected function run(array $params, Model_Build &$build)
    {
        $processorClass = 'Processor_' . ucfirst($params['processor']);
        if (!class_exists($processorClass)) {
            echo $params['processor'] . ' is not a valid processor';
        }
        $processor = new $processorClass();
        $processor->process($build);
    }

    protected function runAll(Model_Build &$build)
    {
        foreach (File::findProcessors() as $processorClass) {
            $name      = str_replace('Processor_', '', $processorClass);
            Kohana::$log->add(Log::INFO, 'Processing reports for ' . $name . '...');
            $processor = new $processorClass();
            $processor->process($build);
        }
    }
}