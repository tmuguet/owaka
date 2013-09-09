<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * URL helper class.
 *
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class URL extends Kohana_URL
{

    /**
     * Stub for CLI
     * 
     * @param mixed   $protocol Protocol string, [Request], or boolean
     * @param boolean $index    Add index file to URL?
     * 
     * @return  string
     */
    public static function base($protocol = NULL, $index = FALSE)
    {
        if (!isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['SERVER_NAME'])) {
            // CLI
            return '/';
            // @codeCoverageIgnoreStart
        } else {
            return Kohana_URL::base($protocol, $index);
        }
        // @codeCoverageIgnoreEnd
    }
}