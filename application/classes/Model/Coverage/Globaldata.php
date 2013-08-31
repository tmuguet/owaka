<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Coverage data model
 * 
 * @package Model
 */
class Model_Coverage_Globaldata extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Belongs to" relationships
     * @var array
     */
    protected $_belongs_to = array(
        'build' => array(
            'model'       => 'Build',
            'foreign_key' => 'build_id'
            ),
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
            'methodcount'       => array(
                array('not_empty'),
                array('integer'),
            ),
            'methodscovered'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'methodcoverage'    => array(
                array('not_empty'),
                array('numeric'),
            ),
            'statementcount'    => array(
                array('not_empty'),
                array('integer'),
            ),
            'statementscovered' => array(
                array('not_empty'),
                array('integer'),
            ),
            'statementcoverage' => array(
                array('not_empty'),
                array('numeric'),
            ),
            'totalcount'        => array(
                array('not_empty'),
                array('integer'),
            ),
            'totalcovered'      => array(
                array('not_empty'),
                array('integer'),
            ),
            'totalcoverage'     => array(
                array('not_empty'),
                array('numeric'),
            ),
        );
        return $rules;
    }
}
