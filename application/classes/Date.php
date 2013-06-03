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
}