<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHPUnit data model
 * 
 * @package Model
 */
class Model_Phpunit_Globaldata extends ORM
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
            'tests'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'failures' => array(
                array('not_empty'),
                array('integer'),
            ),
            'errors'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'time'     => array(
                array('not_empty'),
                array('numeric'),
            ),
        );
        return $rules;
    }
}
