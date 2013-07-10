<?php

/**
 * @codeCoverageIgnore
 */
class URL extends Kohana_URL
{
#ifdef TESTING

    /**
     * Stub (not supported in unit tests)
     */
    public static function base($protocol = NULL, $index = FALSE)
    {
        return '/';
    }
#endif
}