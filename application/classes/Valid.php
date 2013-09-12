<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Validation rules.
 *
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Valid extends Kohana_Valid
{

    /**
     * Checks that a field is not the value required.
     *
     * @param string $value    value
     * @param string $required required value
     * 
     * @return  boolean
     */
    public static function different($value, $required)
    {
        return ($value != $required);
    }

    /**
     * Validates a decimal number
     * 
     * @param string $str    String to validate
     * @param int    $places Max number of precision-digits
     * @param int    $digits Max number of digits, or NULL for no limit
     * 
     * @return bool
     */
    public static function decimal($str, $places = 2, $digits = NULL)
    {
        if ($digits !== NULL && $digits > 0) {
            // Specific number of digits
            $digits = '{' . ( (int) $digits) . '}';
        } else {
            // Any number of digits
            $digits = '+';
        }

        return (preg_match(
                        '/^[0-9]' . $digits .
                        '(|(' . preg_quote('.') . '|' . preg_quote(',') . ')' .
                        '[0-9]{0,' . ( (int) $places) . '})$/D', $str
                ) === 1);
    }

    /**
     * Validates an integer number
     * 
     * @param string $str      String to validate
     * @param int    $minValue Min accepted value
     * @param int    $maxValue Max accepted value
     * 
     * @return bool
     */
    public static function integer($str, $minValue = NULL, $maxValue = NULL)
    {
        return is_numeric($str) && preg_match('/^-?[0-9]*$/', $str) === 1 && (($minValue === NULL || intval($str) >= $minValue))
                && (($maxValue === NULL || intval($str) <= $maxValue));
    }

    /**
     * Validates a boolean 0/1
     * 
     * @param string $str String to validate
     * 
     * @return bool
     */
    public static function boolean($str)
    {
        return in_array($str, array(0, 1, '0', '1'), TRUE);
    }

    /**
     * Validates a readable path
     * 
     * @param string $str String to validate
     * 
     * @return bool
     */
    public static function is_dir($str)
    {
        return is_dir($str);
    }

    /**
     * Validates a readable path
     * 
     * @param string $str String to validate
     * 
     * @return bool
     */
    public static function is_readable($str)
    {
        return is_readable($str);
    }

    /**
     * Validates a readable path
     * 
     * @param string $str String to validate
     * 
     * @return bool
     */
    public static function is_writable($str)
    {
        return is_writable($str);
    }
}