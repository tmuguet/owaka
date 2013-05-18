<?php
defined('SYSPATH') or die('No direct script access.');

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
    );
    // @codingStandardsIgnoreEnd

    /**
     * Lists all of the columns in the model
     * 
     * @return array
     */
    /*    public function list_columns()
      {
      $columns = array(
      "id"       => NULL,
      "revision" => NULL,
      "status"   => NULL,
      );
      //        return array_merge(parent::list_columns(), $columns);
      return $columns;
      } */

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    /* public function rules()
      {
      $rules = array(
      'name'                  => array(
      array('not_empty'),
      array('min_length', array(':value', 3)),
      array('max_length', array(':value', 45)),
      ),
      'description'           => array(
      // Can be empty
      array('min_length', array(':value', 0)),
      array('max_length', array(':value', 2048)),
      ),
      'are_numbers_auto'      => array(
      array('not_empty'),
      array('kValidation::boolean', array(':value'))
      ),
      'next_available_number' => array(
      array('kValidation::integer', array(':value', 0, 4294967295))
      ),
      'is_ready'              => array(
      array('not_empty'),
      array('kValidation::boolean', array(':value'))
      ),
      );
      return $rules;
      } */

    /**
     * Tests existence of an instance
     * 
     * Returns TRUE iff the object with the specified ID exists
     * 
     * @param int $id Id of the object to test
     * 
     * @return boolean
     */
    public static function exists($id)
    {
        if (empty($id)) {
            return FALSE;
        }
        return (DB::select(array(DB::expr('COUNT(id)'), 'total'))
                        ->from('builds')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }

    public function previousBuild()
    {
        return ORM::factory('build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '<', $this->id)
                        ->order_by('build.id', 'DESC')
                        ->limit(1);
    }

    public function nextBuild()
    {
        return ORM::factory('build')
                        ->where('build.project_id', '=', $this->project_id)
                        ->where('build.id', '>', $this->id)
                        ->order_by('build.id', 'ASC')
                        ->limit(1);
    }

    public function rangeBuild()
    {
        $previous = ORM::factory('build')
                ->where('build.project_id', '=', $this->project_id)
                ->where('build.id', '<', $this->id)
                ->order_by('build.id', 'DESC')
                ->limit(5)
                ->find_all()->as_array();
        $next     = ORM::factory('build')
                ->where('build.project_id', '=', $this->project_id)
                ->where('build.id', '>', $this->id)
                ->order_by('build.id', 'ASC')
                ->limit(5)
                ->find_all()->as_array();
        return array_merge(array_reverse($next), array($this), $previous);
    }
}
