<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Build model
 * 
 * @package Model
 */
class Model_Build extends ORM
{

    // @codingStandardsIgnoreStart
    /**
     * "Has many" relationships
     * @var array
     */
    protected $_belongs_to = array(
        'project' => array(
            'model'       => 'Project',
            'foreign_key' => 'project_id'
        ),
    );

    /**
     * "Has one" relationships
     * @var array
     */
    protected $_has_one    = array(
        'codesniffer_globaldata' => array(
            'model'       => 'codesniffer_globaldata',
            'foreign_key' => 'build_id'
        ),
        'coverage_globaldata'    => array(
            'model'       => 'coverage_globaldata',
            'foreign_key' => 'build_id'
        ),
        'phpmd_globaldata'       => array(
            'model'       => 'phpmd_globaldata',
            'foreign_key' => 'build_id'
        ),
        'phpunit_globaldata'     => array(
            'model'       => 'phpunit_globaldata',
            'foreign_key' => 'build_id'
        ),
        'pdepend_globaldata'     => array(
            'model'       => 'pdepend_globaldata',
            'foreign_key' => 'build_id'
        ),
    );
    // @codingStandardsIgnoreEnd

    /**
     * Gets the previous build. Must be loaded with find()
     * 
     * @return Model_Build
     */
    public function previousBuild()
    {
        return ORM::factory('Build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '<', $this->id)
                        ->order_by('build.id', 'DESC')
                        ->limit(1);
    }

    /**
     * Gets the next build. Must be loaded with find()
     * 
     * @return Model_Build
     */
    public function nextBuild()
    {
        return ORM::factory('Build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '>', $this->id)
                        ->order_by('build.id', 'ASC')
                        ->limit(1);
    }

    /**
     * Gets the 5 previous and 5 next builds, ordered from newest to oldest
     * 
     * @return Model_Build[]
     */
    public function rangeBuild()
    {
        $previous = ORM::factory('Build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '<', $this->id)
                        ->order_by('build.id', 'DESC')
                        ->limit(5)
                        ->find_all()->as_array();
        $next     = ORM::factory('Build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '>', $this->id)
                        ->order_by('build.id', 'ASC')
                        ->limit(5)
                        ->find_all()->as_array();
        return array_merge(array_reverse($next), array($this), $previous);
    }

    /**
     * Gets the formatted revision
     * @return string
     */
    public function getRevision()
    {
        if (ctype_digit($this->revision)) {
            return 'r' . $this->revision;
        } else {
            return substr($this->revision, 0, 10);
        }
    }
}
