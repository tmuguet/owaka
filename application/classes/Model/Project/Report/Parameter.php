<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Parameters for project report model
 * 
 * @package Model
 */
class Model_Project_Report_Parameter extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_belongs_to = array(
        'project' => array(
            'model'       => 'Project',
            'foreign_key' => 'project_id'),
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
            'processor' => array(
                array('not_empty'),
            ),
            'params'    => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }
}
