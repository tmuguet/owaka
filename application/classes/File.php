<?php

class File extends Kohana_File
{

    /**
     * Finds all widgets in application
     * @param string $path Absolute path where to search widgets
     * @return string[] List of absolute paths to PHP files of widgets, unfiltered
     */
    protected static function getFiles($path)
    {
        $phpFiles = glob($path . '*.php');

        $dirs = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $phpFiles = array_merge($phpFiles, self::getFiles($dir));
        }
        return $phpFiles;
    }

    /**
     * Finds all files
     * @param string $path Path to search
     * @return string[] Classes found
     */
    public static function findClasses($path)
    {
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $files   = self::getFiles(APPPATH . 'classes/' . $path);
        $classes = array();
        foreach ($files as $file) {
            $nameClass = str_replace(
                    '/', '_', str_replace(APPPATH . 'classes/', '', substr($file, 0, -4))
            );
            if (!class_exists($nameClass, FALSE)) {
                include_once $file;
            }

            $class = new ReflectionClass($nameClass);
            if ($class->isInstantiable()) {
                $classes[] = $nameClass;
            }
        }
        return $classes;
    }

    /**
     * Finds all widgets
     * @param string $dashboard Type of dashboard
     * @return string[] Name of widgets
     */
    public static function findWidgets($dashboard)
    {
        $allWidgets = self::findClasses('Controller/Widget/');
        $widgets    = array();
        foreach ($allWidgets as $widget) {
            $class = new ReflectionClass($widget);
            if ($class->hasMethod('display_' . $dashboard) || $class->hasMethod('display_all')) {
                $widgets[] = $widget;
            }
        }
        return $widgets;
    }

    /**
     * Finds all processors
     * @return string[] Name of processors
     */
    public static function findProcessors()
    {
        return self::findClasses('Controller/Processors/');
    }

    /**
     * Finds all processors
     * @return string[] Name of processors
     */
    public static function findAnalyzers()
    {
        $allProcessors = self::findProcessors();
        $analyzers    = array();
        foreach ($allProcessors as $processor) {
            $class = new ReflectionClass($processor);
            if ($class->hasMethod('analyze')) {
                $analyzers[] = $processor;
            }
        }
        return $analyzers;
    }
}