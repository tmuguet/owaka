<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project widget model
 * 
 * @package Model
 */
class Model_Project_Widget extends ORM
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
}
