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
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        $rules = array(
            'name'                  => array(
                array('not_empty'),
            ),
            'is_active'             => array(
                array('boolean'),
            ),
            'scm'                   => array(
                array('not_empty'),
            ),
            'path'                  => array(
                array('not_empty'),
                array('is_dir'),
            ),
            'phing_path'            => array(
                array('not_empty'),
                array('is_dir'),
            ),
            'phing_target_validate' => array(
                array('not_empty'),
            ),
            'phing_target_nightly'  => array(
            ),
            'reports_path'          => array(
                array('not_empty'),
            ),
        );
        return $rules;
    }

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
