<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for models
 * 
 * @package Core
 */
abstract class ORM extends Kohana_ORM
{

    /**
     * Tests existence of an instance
     * 
     * Returns TRUE iff the object with the specified ID exists
     * 
     * @param int $id Id of the object to test
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
     * Validates the current model's data
     *
     * @param  Validation $extra_validation Validation object
     * @throws ORM_Validation_Exception
     * @return ORM
     */
    /*public function check(Validation $extra_validation = NULL)
    {
        try {
            return parent::check($extra_validation);
        } catch (ORM_Validation_Exception $e) {
            $objects = $e->objects();
            throw new ORM_Validation_Exception($this->errors_filename(), $objects['_object'],
                                               $e->getMessage() . var_export($e->errors(), true));
        }
    }*/
}
