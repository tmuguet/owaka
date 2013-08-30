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

    /**
     * "Has many" relationships
     * @var array
     */
    protected $_has_many = array(
        'codesniffer_errors' => array(
            'model'       => 'Codesniffer_Error',
            'foreign_key' => 'build_id'
        ),
        'phpunit_errors'     => array(
            'model'       => 'Phpunit_Error',
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
            ),
            'eta'      => array(
            ),
            'finished' => array(
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
     * 
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

    /**
     * Gets the icon corresponding to the build status
     * 
     * @return string
     */
    public function getIcon()
    {
        switch ($this->status) {
            case Owaka::BUILD_OK:
                return 'ok';
                break;

            case Owaka::BUILD_UNSTABLE:
                return 'warning-sign';
                break;

            case Owaka::BUILD_ERROR:
                return 'bug';
                break;

            case Owaka::BUILD_BUILDING:
                return 'beaker';
                break;

            case Owaka::BUILD_QUEUED:
                return 'time';
                break;

            case 'nodata':
            default:
                return 'ban-circle';
        }
    }

    /**
     * Deletes a build with all its data.
     *
     * @chainable
     * @throws Kohana_Exception
     * @return self
     */
    public function delete()
    {
        foreach (array_keys($this->_has_one) as $fk) {
            $relation = $this->$fk;
            if ($relation->loaded()) {
                $relation->delete();
            }
        }
        foreach (array_keys($this->_has_many) as $fk) {
            $relations = $this->$fk->find_all();
            foreach ($relations as $r) {
                $r->delete();
            }
        }

        File::rrmdir(APPPATH . 'reports' . DIRECTORY_SEPARATOR . $this->id);
        return parent::delete();
    }
}
