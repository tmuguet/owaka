<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for models
 * 
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class ORM extends Kohana_ORM
{

    /**
     * Tests existence of an instance
     * 
     * Returns TRUE iff the object with the specified ID exists
     * 
     * @param int    $id    Id of the object to test
     * @param string $model Model to use. Tries to determine it automatically if NULL.
     * 
     * @return boolean
     */
    public static function exists($id, $model = NULL)
    {
        if (empty($id)) {
            return FALSE;
        }
        if ($model == NULL) {
            $model = get_called_class();
        }

        return (DB::select(array(DB::expr('COUNT(id)'), 'total'))
                        ->from(strtolower(str_replace('Model_', '', $model)) . 's')
                        ->where('id', '=', $id)
                        ->execute()
                        ->get('total') > 0);
    }

    /**
     * Returns a duplicated object of this instance
     * 
     * @param array $new_values New values to set
     * 
     * @return self
     */
    public function duplicate($new_values = array())
    {
        $new = ORM::factory($this->_object_name);
        $new->values($this->_original_values);
        $new->values($new_values);
        unset($new->id);
        $new->create();
        return $new;
    }
    /**
     * Validates the current model's data
     *
     * @param  Validation $extra_validation Validation object
     * @throws ORM_Validation_Exception
     * @return ORM
     */
    /* public function check(Validation $extra_validation = NULL)
      {
      try {
      return parent::check($extra_validation);
      } catch (ORM_Validation_Exception $e) {
      $objects = $e->objects();
      throw new ORM_Validation_Exception($this->errors_filename(), $objects['_object'],
      $e->getMessage() . var_export($e->errors(), true));
      }
      } */
}
