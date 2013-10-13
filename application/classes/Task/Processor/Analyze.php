<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for analyzing reports for a processor
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Processor_Analyze extends Minion_Task
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
                echo $this->run($params, $build);
            } else {
                echo $this->runAll($build);
            }
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
        $res       = NULL;
        if (method_exists($processor, 'analyze')) {
            $res = $processor->analyze($build, Processor::projectParameters($build->project_id));
        }
        return $res;
    }

    protected function runAll(Model_Build &$build)
    {
        $status = Owaka::BUILD_OK;
        foreach (File::findProcessors() as $processorClass) {
            $name = str_replace('Processor_', '', $processorClass);

            $processor = new $processorClass();
            $res       = NULL;
            if (method_exists($processor, 'analyze')) {
                Kohana::$log->add(Log::INFO, 'Analyzing reports for ' . $name . '...');
                $res = $processor->analyze($build, Processor::projectParameters($build->project_id));
            }

            Kohana::$log->add(Log::INFO, $name . ' : ' . $res);
            if ($res == Owaka::BUILD_ERROR) {
                $status = Owaka::BUILD_ERROR;
                break;
            } else if ($res == Owaka::BUILD_UNSTABLE) {
                $status = Owaka::BUILD_UNSTABLE;
            } else if ($res != Owaka::BUILD_OK) {
                Kohana::$log->add(Log::ERROR, 'Failed: ' . $res);
            }
        }
        return $status;
    }
}