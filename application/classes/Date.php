<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Date helper class
 * 
 * @package Core
 */
class Date extends Kohana_Date
{

    /**
     * Formats a date to MySQL format
     * @param DateTime|int $date Date to format
     * @return int
     */
    public static function toMySql($date)
    {
        if ($date instanceof DateTime) {
            return $date->format("Y-m-d H:i:s");
        } else {
            return @date("Y-m-d H:i:s", $date);
        }
    }

    /**
     * Returns the difference between a time and now using only the biggest unit.
     *
     *     $span = Date::loose_span(time() - 10); // "+ 10second"
     *     $span = Date::loose_span(time() - 100000); // "+ 1day"
     *
     * A second parameter is available to manually set the "local" timestamp,
     * however this parameter shouldn't be needed in normal usage and is only
     * included for unit tests
     *
     * @param   integer $timestamp          "remote" timestamp
     * @param   integer $local_timestamp    "local" timestamp, defaults to time()
     * @return  string
     */
    public static function loose_span($timestamp, $local_timestamp = NULL)
    {
        $span = self::span($timestamp, $local_timestamp);

        if ($span['years'] > 0) {
            $span = '+ ' . $span['years'] . 'year';
        } else if ($span['months'] > 0) {
            $span = '+ ' . $span['months'] . 'month';
        } else if ($span['weeks'] > 0) {
            $span = '+ ' . $span['weeks'] . 'week';
        } else if ($span['days'] > 0) {
            $span = '+ ' . $span['days'] . 'day';
        } else if ($span['hours'] > 0) {
            $span = '+ ' . $span['hours'] . 'hour';
        } else if ($span['minutes'] > 0) {
            $span = '+ ' . $span['minutes'] . 'minute';
        } else {
            $span = '+ ' . $span['seconds'] . 'second';
        }

        return $span;
    }
}