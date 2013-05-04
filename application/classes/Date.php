<?php

class Date extends Kohana_Date
{

    public static function toMySql($date)
    {
        if ($date instanceof DateTime) {
            return $date->format("Y-m-d H:i:s");
        } else {
            return @date("Y-m-d H:i:s", $date);
        }
    }
}