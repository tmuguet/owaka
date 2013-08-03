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
    protected $_has_one = array(
        'codesniffer_globaldata' => array(
            'model'       => 'Codesniffer_Globaldata',
            'foreign_key' => 'build_id'
        ),
        'coverage_globaldata'    => array(
            'model'       => 'Coverage_Globaldata',
            'foreign_key' => 'build_id'
        ),
        'phpmd_globaldata'       => array(
            'model'       => 'Phpmd_Globaldata',
            'foreign_key' => 'build_id'
        ),
        'phpunit_globaldata'     => array(
            'model'       => 'Phpunit_Globaldata',
            'foreign_key' => 'build_id'
        ),
        'pdepend_globaldata'     => array(
            'model'       => 'Pdepend_Globaldata',
            'foreign_key' => 'build_id'
        ),
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
            'revision' => array(
                array('not_empty'),
            ),
            'status'   => array(
                array('not_empty'),
            ),
            'started'  => array(
                array('not_empty'),
                array('date'),
            ),
            'eta'      => array(
                array('date'),
            ),
            'finished' => array(
                array('date'),
            ),
        );
        return $rules;
    }

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

    public function getIcon()
    {
        switch ($this->status) {
            case 'ok':
                return 'right';
                break;

            case 'unstable':
                return 'warning';
                break;

            case 'error':
                return 'radiation';
                break;

            case 'building':
                return 'beaker';
                break;

            case 'queued':
                return 'sandclock';
                break;

            case 'nodata':
            default:
                return 'blocked';
        }
    }
}
