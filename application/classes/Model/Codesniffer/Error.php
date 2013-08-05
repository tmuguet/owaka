<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Code sniffer errors model
 * 
 * @package Model
 */
class Model_Codesniffer_Error extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_one = array(
        'build' => array(
            'model'       => 'Build',
            'foreign_key' => 'build_id'),
    );
    // @codingStandardsIgnoreEnd

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'file'     => array(
                array('not_empty'),
            ),
            'severity' => array(
                array('not_empty'),
                array('in_array', array(':value', array('error', 'warning'))),
            ),
            'message'  => array(
                array('not_empty'),
            ),
            'line'     => array(
                array('not_empty'),
                array('integer'),
            ),
        );
        return $rules;
    }
}
