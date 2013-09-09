<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Parameters for project report model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
            'type'      => array(
                array('not_empty'),
            ),
            'value'     => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }
}
