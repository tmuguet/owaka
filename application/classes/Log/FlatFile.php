<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Flat file log writer. Writes out messages and stores them in a file.
 *
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Log_FlatFile extends Log_Writer
{

    /**
     * @var string File to place log messages in
     */
    protected $file;

    /**
     * Creates a new file logger. Checks that the parent directory exists and
     * is writable.
     *
     *     $writer = new Log_FlatFile($file);
     *
     * @param string $file log file
     * 
     * @return void
     */
    public function __construct($file)
    {
        $directory = dirname($file);
        if (!is_dir($directory) OR !is_writable($directory)) {
            throw new Kohana_Exception('Directory :dir must be writable', array(':dir' => Debug::path($directory)));
        }

        // Determine the file path
        $this->file = $file;
    }

    /**
     * Writes each of the messages into the log file.
     *
     *     $writer->write($messages);
     *
     * @param array $messages Messages
     * 
     * @return void
     */
    public function write(array $messages)
    {
        if (!file_exists($this->file)) {
            // Create the log file
            file_put_contents($this->file, Kohana::FILE_SECURITY . ' ?>' . PHP_EOL);
        }

        foreach ($messages as $message) {
            // Write each message into the log file
            file_put_contents($this->file, PHP_EOL . $this->format_message($message), FILE_APPEND);
        }
    }
}
