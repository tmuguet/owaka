<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Number helper class
 * 
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Num extends Kohana_Num
{

    /**
     * Returns the percentage of 2 numbers
     * 
     * @param float $amount Amount
     * @param float $total  Total
     * 
     * @return float Percentage of Amount / Total. If total equals 0, returns 100. If amount or total are negative, result is unspecified.
     */
    public static function percent($amount, $total)
    {
        return ($total != 0 ? ($amount * 100 / $total) : 100);
    }
}