<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * PHPMD data model
 * 
 * @package Model
 */
class Model_Phpmd_Globaldata extends ORM
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
            'errors'  => array(
                array('not_empty'),
                array('integer'),
            ),
        );
        return $rules;
    }
}
