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
     * "Has many" relationships
     * @var array
     */
    protected $_has_one = array(
        'build' => array(
            'model'       => 'Build',
            'foreign_key' => 'build_id'),
    );
    // @codingStandardsIgnoreEnd
}
