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
            $c = Kohana::$config->load('owaka');
            $base = $c->get('base');
            return $base . (substr($base, -1) == '/' ? '' : '/');
            // @codeCoverageIgnoreStart
        } else {
            return Kohana_URL::base($protocol, $index);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Fetches an absolute site URL based on a URI segment.
     *
     *     echo URL::site('foo/bar');
     *
     * @param   string  $uri        Site URI to convert
     * @param   mixed   $protocol   Protocol string or [Request] class to use protocol from - NOT USED
     * @param   boolean $index		Include the index_page in the URL - NOT USED
     * 
     * @return  string
     */
    public static function site($uri = '', $protocol = NULL, $index = TRUE)
    {
        $c = Kohana::$config->load('owaka');
        return Kohana_URL::site($uri, ($c->get('https') ? 'https' : 'http'), FALSE);
    }
}