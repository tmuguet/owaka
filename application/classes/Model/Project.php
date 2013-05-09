<?php
defined('SYSPATH') or die('No direct script access.');

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
     * Lists all of the columns in the model
     * 
     * @return array
     */
    /*    public function list_columns()
      {
      $columns = array(
      "id"                    => NULL,
      "name"                  => NULL,
      "is_active"             => NULL,
      "scm"                   => NULL,
      "path"                  => NULL,
      "phing_target_validate" => NULL,
      "phing_target_nightly"  => NULL,
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
                        ->from('projects')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }

    public function lastBuild()
    {
        return ORM::factory('build')
                        ->where('build.project_id', '=', $this->id)
                        ->order_by('build.id', 'DESC')
                        ->limit(1);
    }
}
