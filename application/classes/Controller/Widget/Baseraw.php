<?php

/**
 * Base class for all widgets displaying text
 * 
 * @package Widgets
 */
abstract class Controller_Widget_Baseraw extends Controller_Widget_Base
{

    /**
     * Raw HTML content to display
     * @var string
     */
    protected $content = NULL;

    /**
     * Gets the preferred size (width, height)
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(6, 4);
    }

    /**
     * Gets the sizes (width, height) which this widget is optimized for
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(
            array(4, 2), array(4, 4), array(4, 6),
            array(6, 2), array(6, 4), array(6, 6)
        );
    }

    /**
     * Renders the widget
     */
    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets' . DIRECTORY_SEPARATOR . 'BaseText')
                ->set('content', $this->content);

        $this->response->body($view);
    }
}