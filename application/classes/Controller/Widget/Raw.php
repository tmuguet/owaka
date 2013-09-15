<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all widgets displaying text
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
abstract class Controller_Widget_Raw extends Controller_Widget
{

    public static $preferredSize  = array(6, 4);
    public static $availableSizes = array(
        array(4, 2), array(4, 4), array(4, 6),
        array(6, 2), array(6, 4), array(6, 6)
    );

    /**
     * Raw HTML content to display
     * @var string
     */
    protected $content = NULL;

    /**
     * Renders the widget
     */
    protected function render()
    {
        parent::initViews();
        $view = View::factory('widgets' . DIR_SEP . 'BaseText')
                ->set('content', $this->content);

        $this->success($view);
    }
}