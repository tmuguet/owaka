<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project post-build action model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_Project_Postaction extends ORM
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
-     */
    public function rules()
    {
        $rules = array(
            'postaction'  => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }
}
