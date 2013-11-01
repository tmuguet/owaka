<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for copying all available reports for a processor
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Processor_Copy extends Minion_Task
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

            echo Owaka::BUILD_OK;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // @codingStandardsIgnoreEnd

    /**
     * Copies reports for one processor
     * 
     * @param array       $params Parameters. $params['processor'] must be defined
     * @param Model_Build &$build Build
     */
    protected function run(array $params, Model_Build &$build)
    {
        $processorClass = 'Processor_' . ucfirst($params['processor']);
        if (!class_exists($processorClass)) {
            echo $params['processor'] . ' is not a valid processor';
            return;
        }
        $this->copy(new $processorClass(), $build);
    }

    /**
     * Copies report for all processors
     * 
     * @param Model_Build &$build Build
     */
    protected function runAll(Model_Build &$build)
    {
        foreach (File::findProcessors() as $processorClass) {
            $name = str_replace('Processor_', '', $processorClass);
            Kohana::$log->add(Log::INFO, 'Copying reports for ' . $name . '...');
            $this->copy(new $processorClass(), $build);
        }
    }

    /**
     * Copy reports for a processor
     * 
     * @param Processor   &$processor Processor
     * @param Model_Build &$build     Build
     * 
     * @return boolean Always true
     */
    protected function copy(Processor &$processor, Model_Build &$build)
    {
        $command = new Command($build->project);

        foreach ($processor::$inputReports as $type => $info) {
            $source          = $processor->getInputReportCompleteRealPath($build, $type);
            $rootDestination = $processor->getReportRootPath($build);
            $destination     = $processor->getReportCompletePath($build, $type); // TODO: this can get messy if mis-used !
            
            if (!empty($source) && !empty($destination)) {
                if ($info['type'] == 'dir' && !$command->is_dir($source)) {
                    continue;
                } else if ($info['type'] == 'file' && !$command->is_file($source)) {
                    continue;
                }

                Kohana::$log->add(Log::INFO, 'Copying ' . $source . ' to ' . $destination);
                if (!file_exists($rootDestination)) {
                    // Create the directory only if at least one report is available
                    mkdir($rootDestination, 0700, true);
                }

                $command->rcopy($source, $destination);
            }
        }
        return true;
    }
}