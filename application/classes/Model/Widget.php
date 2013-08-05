<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Widget model
 * 
 * @package Model
 */
class Model_Widget extends ORM
{

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'type'   => array(
                array('not_empty'),
            ),
            'params' => array(
                array('not_empty'),
                array('min_length', array(':value', 2)),
            ),
            'width'  => array(
                array('not_empty'),
                array('integer', array(':value', 1)),
            ),
            'height' => array(
                array('not_empty'),
                array('integer', array(':value', 1)),
            ),
            'column' => array(
                array('not_empty'),
                array('integer', array(':value', 0)),
            ),
            'row'    => array(
                array('not_empty'),
                array('integer', array(':value', 0)),
            ),
        );
        return $rules;
    }
}
