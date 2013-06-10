<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * File helper class
 * 
 * @package Core
 */
class File extends Kohana_File
{

    public static function rrmdir($path)
    {
        // @codeCoverageIgnoreStart
        if (empty($path) || $path == DIRECTORY_SEPARATOR) {
            // Avoid deleting root
            return;
        }
        // @codeCoverageIgnoreEnd

        foreach (glob($path . '/*') as $file) {
            if (is_dir($file)) {
                self::rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }

    public static function rcopy($source, $dest)
    {
        if (is_dir($source)) {
            if (!file_exists($dest)) {
                mkdir($dest, 0700);
            }
            $objects = scandir($source);
            $result  = true;
            foreach ($objects as $file) {
                if ($file == "." || $file == "..") {
                    continue;
                }

                if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                    $result &= self::rcopy($source . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file);
                } else {
                    $result &= copy($source . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file);
                }
            }
            return $result;
        } elseif (is_file($source)) {
            return copy($source, $dest);
        } else {
            return false;
        }
    }

    /**
     * Finds all files in a path
     * @param string $path Absolute path where to search for files
     * @return string[] List of absolute paths to PHP files, unfiltered
     */
    public static function getFiles($path)
    {
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
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

#if TESTING
        $basePath = DOCROOT . 'private' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . '_FileTest' . DIRECTORY_SEPARATOR;
#else
        $basePath = APPPATH . 'classes' . DIRECTORY_SEPARATOR;
#endif
        $files    = self::getFiles($basePath . $path);
        $classes  = array();
        foreach ($files as $file) {
            $nameClass = str_replace(
                    DIRECTORY_SEPARATOR, '_', str_replace($basePath, '', substr($file, 0, -4))
            );
            // @codeCoverageIgnoreStart
            if (!class_exists($nameClass, FALSE)) {
                include_once $file;
            }
            // @codeCoverageIgnoreEnd

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
        $allWidgets = self::findClasses('Controller' . DIRECTORY_SEPARATOR . 'Widget' . DIRECTORY_SEPARATOR);
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
        return self::findClasses('Controller' . DIRECTORY_SEPARATOR . 'Processors' . DIRECTORY_SEPARATOR);
    }

    /**
     * Finds all processors
     * @return string[] Name of processors
     */
    public static function findAnalyzers()
    {
        $allProcessors = self::findProcessors();
        $analyzers     = array();
        foreach ($allProcessors as $processor) {
            $class = new ReflectionClass($processor);
            if ($class->hasMethod('analyze')) {
                $analyzers[] = $processor;
            }
        }
        return $analyzers;
    }
}