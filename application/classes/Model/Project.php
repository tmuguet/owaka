<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project model
 * 
 * @package Model
 */
class Model_Project extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many = array(
        'builds' => array(
            'model'       => 'Build',
            'foreign_key' => 'project_id'),
    );

    // @codingStandardsIgnoreEnd


    /**
     * Gets the last build. Must be loaded with find()
     * 
     * @return Model_Build
     */
    public function lastBuild()
    {
        return ORM::factory('Build')
                        ->where('build.project_id', '=', $this->id)
                        ->order_by('build.id', 'DESC')
                        ->limit(1);
    }
}
