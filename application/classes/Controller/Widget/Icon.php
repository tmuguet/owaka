<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all widgets using icons
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller_Widget_Icon extends Controller_Widget
{

    public static $preferredSize  = array(2, 2);
    public static $availableSizes = array(array(2, 2));

    /**
     * Array of data to show
     * 
     * For each data, the following keys are expected:
     * - status (ok, unstable, error, nodata)
     * - data: value to show (optional)
     * - label: label to show when the widget is hovered (optional)
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Renders the widget
     */
    protected function render()
    {
        if (empty($this->widgetStatus)) {
            $this->widgetStatus = (sizeof($this->data) > 0) ? $this->data[0]['status'] : Owaka::BUILD_NODATA;
        }

        parent::initViews();

        if (empty($this->data)) {
            $this->data[] = array('status' => Owaka::BUILD_NODATA, 'data'   => 'No data');
        }

        $view = View::factory('widgets' . DIR_SEP . 'BaseIcon')
                ->set('data', $this->data);

        $this->success($view);
    }
}