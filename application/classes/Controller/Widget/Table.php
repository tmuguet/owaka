<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all widgets displaying tables
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller_Widget_Table extends Controller_Widget
{

    /**
     * Name of the columns. Columns prefixed by `_` are shown only when the widget is hovered
     * @var array
     */
    protected $columnsHeaders = array();
    
    /**
     * List of rows
     * @var array
     */
    protected $rows           = array();

    /**
     * Gets the preferred size (width, height)
     * 
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(2, 4);
    }

    /**
     * Gets the sizes (width, height) which this widget is optimized for
     * 
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(
            array(2, 2), array(2, 4), array(2, 6),
            array(4, 2), array(4, 4), array(4, 6)
        );
    }

    /**
     * Renders the widget
     */
    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets' . DIR_SEP . 'BaseTable')
                ->set('columns', $this->columnsHeaders)
                ->set('rows', $this->rows);

        $this->response->body($view);
    }
}