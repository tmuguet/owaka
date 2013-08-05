<?php

/**
 * Base class for all widgets using icons
 * 
 * @package Widgets
 */
abstract class Controller_Widget_Baseicon extends Controller_Widget_Base
{

    /**
     * Main status of the widget, among ok/unstable/error/nodata
     * @var string
     */
    protected $status = NULL;

    /**
     * Main status data (optional)
     * @var string
     */
    protected $statusData = NULL;

    /**
     * Main status label for data (optional; used only if statusData not empty)
     * This is shown only when widget is hovered
     * @var string
     */
    protected $statusDataLabel = NULL;

    /**
     * Secondary status of the widget, among ok/unstable/error/nodata (optional)
     * @var string 
     */
    protected $substatus          = NULL;

    /**
     * Secondary status data (optional; used only if substatus and statusData not empty)
     * @var string
     */
    protected $substatusData      = NULL;

    /**
     * Secondary status label for data (optional; used only if substatusData not empty)
     * This is shown only when widget is hovered
     * @var string
     */
    protected $substatusDataLabel = NULL;

    /**
     * Gets the preferred size (width, height)
     * 
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(2, 2);
    }

    /**
     * Gets the sizes (width, height) which this widget is optimized for
     * 
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(array(2, 2));
    }

    /**
     * Renders the widget
     */
    protected function render()
    {
        if (empty($this->widgetStatus) && !empty($this->status) && empty($this->substatus)) {
            $this->widgetStatus = $this->status;
        }

        parent::initViews();

        $view = View::factory('widgets' . DIRECTORY_SEPARATOR . 'BaseIcon')
                ->set('status', $this->status)
                ->set('statusData', $this->statusData)
                ->set('statusDataLabel', $this->statusDataLabel)
                ->set('substatus', $this->substatus)
                ->set('substatusData', $this->substatusData)
                ->set('substatusDataLabel', $this->substatusDataLabel);

        $this->response->body($view);
    }
}