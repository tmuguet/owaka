<?php

class URL extends Kohana_URL
{

    /**
     * Stub for CLI
     */
    public static function base($protocol = NULL, $index = FALSE)
    {
        if (!isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['SERVER_NAME'])) {
            // CLI
            return '/';
        } else {
            return Kohana_URL::base($protocol, $index);
        }
    }
}