<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHPUnit error model
 * 
 * @package Model
 */
class Model_Phpunit_Error extends ORM
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
            'testsuite' => array(
                array('not_empty'),
            ),
            'testcase'  => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }
}
