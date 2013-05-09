<?php
defined('SYSPATH') or die('No direct script access.');

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

    /**
     * Lists all of the columns in the model
     * 
     * @return array
     */
    /*    public function list_columns()
      {
      $columns = array(
      "id"         => NULL,
      "type"       => NULL,
      "params"     => NULL,
      "project_id" => NULL,
      "width"      => NULL,
      "height"     => NULL,
      "postion"    => NULL,
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
                        ->from('project_widgets')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }
}
