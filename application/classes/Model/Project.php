<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Project model
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
            'scm_url'               => array(
                array('not_empty'),
            ),
            'scm_branch'            => array(
                array('not_empty'),
            ),
            'is_remote'             => array(
                array('boolean'),
            ),
            'path'                  => array(
                array('not_empty'),
            ),
            'phing_path'            => array(
                array('not_empty'),
            ),
            'phing_target_validate' => array(
                array('not_empty'),
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
