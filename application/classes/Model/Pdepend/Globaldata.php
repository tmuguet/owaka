<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHP Depend data model
 * 
 * @package Model
 */
class Model_Pdepend_Globaldata extends ORM
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
            'ahh'    => array(
                array('not_empty'),
                array('numeric'),
            ),
            'andc'   => array(
                array('not_empty'),
                array('numeric'),
            ),
            'calls'  => array(
                array('not_empty'),
                array('integer'),
            ),
            'ccn'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'ccn2'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'cloc'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'clsa'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'clsc'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'eloc'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'fanout' => array(
                array('not_empty'),
                array('integer'),
            ),
            'leafs'  => array(
                array('not_empty'),
                array('integer'),
            ),
            'lloc'   => array(
                array('not_empty'),
                array('integer'),
            ),
            'maxdit' => array(
                array('not_empty'),
                array('integer'),
            ),
            'ncloc'  => array(
                array('not_empty'),
                array('integer'),
            ),
            'noc'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'nof'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'noi'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'nom'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'nop'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'roots'  => array(
                array('not_empty'),
                array('integer'),
            ),
        );
        return $rules;
    }
}
