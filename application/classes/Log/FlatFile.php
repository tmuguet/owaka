<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Flat file log writer. Writes out messages and stores them in a file.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Log_FlatFile extends Log_Writer
{

    /**
     * @var  string  File to place log messages in
     */
    protected $_file;

    /**
     * Creates a new file logger. Checks that the parent directory exists and
     * is writable.
     *
     *     $writer = new Log_FlatFile($file);
     *
     * @param   string  $file  log file
     * @return  void
     */
    public function __construct($file)
    {
        $directory = dirname($file);
        if (!is_dir($directory) OR !is_writable($directory)) {
            throw new Kohana_Exception('Directory :dir must be writable', array(':dir' => Debug::path($directory)));
        }

        // Determine the file path
        $this->_file = $file;
    }

    /**
     * Writes each of the messages into the log file.
     *
     *     $writer->write($messages);
     *
     * @param   array   $messages
     * @return  void
     */
    public function write(array $messages)
    {
        if (!file_exists($this->_file)) {
            // Create the log file
            file_put_contents($this->_file, Kohana::FILE_SECURITY . ' ?>' . PHP_EOL);
        }

        foreach ($messages as $message) {
            // Write each message into the log file
            file_put_contents($this->_file, PHP_EOL . $this->format_message($message), FILE_APPEND);
        }
    }
}

// End Log_FlatFile