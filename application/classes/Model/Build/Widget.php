<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Build widget model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Build_Widget extends ORM
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
