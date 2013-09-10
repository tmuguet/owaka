<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing widgets
 * 
 * @api
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Api_Widget extends Controller_Api
{

    /**
     * Deletes a widget from a dashboard
     * 
     * Returns "ok" if succeeded
     * 
     * @url http://example.com/api/widget/delete/&lt;dashboard&gt;/&lt;widget_id&gt;
     * @throws HTTP_Exception_404 Unsupported dashboard type (should never happen)
     * @throws HTTP_Exception_404 Widget not found
     */
    public function action_delete()
    {
        switch ($this->request->param('dashboard')) {
            case "main":
                $widget = ORM::factory('Widget', $this->request->param('id'));
                break;
            case "project":
                $widget = ORM::factory('Project_Widget', $this->request->param('id'));
                break;
            case "build":
                $widget = ORM::factory('Build_Widget', $this->request->param('id'));
                break;
            // @codeCoverageIgnoreStart
            default:
                throw new HTTP_Exception_404();
            // @codeCoverageIgnoreEnd
        }
        if (!$widget->loaded()) {
            throw new HTTP_Exception_404();
        }

        $id = $widget->id;
        $widget->delete();
        $this->respondOk(array('widget' => $id));
    }

    /**
     * Adds a widget to a dashboard
     * 
     * Returns "ok" if succeeded.
     * 
     * @url http://example.com/api/widget/add/main/&lt;dashboard_id&gt;
     * @url http://example.com/api/widget/add/project/&lt;dashboard_id&gt;/&lt;project_id&gt;
     * @url http://example.com/api/widget/add/build/&lt;dashboard_id&gt;/&lt;project_id&gt;
     * @postparameter project Project ID (optional or mandatory, depending on widget)
     * @postparameter id      Widget type (mandatory)
     * @postparameter width   Widget width (mandatory)
     * @postparameter height  Widget height (mandatory)
     * @postparameter column  Widget column (mandatory)
     * @postparameter row     Widget row (mandatory)
     * @throws HTTP_Exception_404 Unsupported dashboard type (should never happen)
     * 
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function action_add()
    {
        try {
            $params = $this->request->post('params');
            if (!is_array($params)) {
                $params = array();
            }

            switch ($this->request->param('dashboard')) {
                case "main":
                    $widget             = ORM::factory('Widget');
                    break;
                case "project":
                    $widget             = ORM::factory('Project_Widget');
                    $widget->project_id = $this->request->param('data');
                    if (isset($params['project']) && $widget->project_id == $params['project']) {
                        unset($params['project']);
                    }
                    break;
                case "build":
                    $widget             = ORM::factory('Build_Widget');
                    $widget->project_id = $this->request->param('data');
                    if (isset($params['project']) && $widget->project_id == $params['project']) {
                        unset($params['project']);
                    }
                    break;
                // @codeCoverageIgnoreStart
                default:
                    throw new HTTP_Exception_404();
                // @codeCoverageIgnoreEnd
            }
            foreach ($params as $key => $value) {
                if (empty($value)) {
                    unset($params[$key]);
                }
            }
            $widget->type   = $this->request->param('id');
            $widget->params = json_encode($params);
            $widget->width  = $this->request->post('width');
            $widget->height = $this->request->post('height');
            $widget->column = $this->request->post('column');
            $widget->row    = $this->request->post('row');
            $widget->create();
            $this->respondOk(array('widget' => $widget->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Moves a widget in a dashboard
     * 
     * Returns "ok" if succeeded.
     * 
     * @url http://example.com/api/widget/move/&lt;dashboard&gt;/&lt;widget_id&gt;
     * @postparameter column  Widget column (mandatory)
     * @postparameter row     Widget row (mandatory)
     * @throws HTTP_Exception_404 Unsupported dashboard type (should never happen)
     * @throws HTTP_Exception_404 Widget not found
     */
    public function action_move()
    {
        try {
            $widgetId = $this->request->param('id');

            switch ($this->request->param('dashboard')) {
                case "main":
                    $widget = ORM::factory('Widget', $widgetId);
                    break;
                case "project":
                    $widget = ORM::factory('Project_Widget', $widgetId);
                    break;
                case "build":
                    $widget = ORM::factory('Build_Widget', $widgetId);
                    break;
                // @codeCoverageIgnoreStart
                default:
                    throw new HTTP_Exception_404();
                // @codeCoverageIgnoreEnd
            }
            if (!$widget->loaded()) {
                throw new HTTP_Exception_404();
            }

            $widget->column = $this->request->post('column');
            $widget->row    = $this->request->post('row');
            $widget->update();
            $this->respondOk(array('widget' => $widget->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }
}