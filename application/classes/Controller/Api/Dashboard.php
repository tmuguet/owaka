<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing dashboards
 * @package    Api
 */
class Controller_Api_Dashboard extends Controller
{

    /**
     * Deletes a widget from a dashboard
     * 
     * Returns "ok" if succeeded
     * 
     * @url http://example.com/api/dashboard/delete/&lt;dashboard&gt;/&lt;widget_id&gt;
     * @throws Exception Unsupported dashboard type (should never happen)
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
            default: throw new Exception("Unsupported dashboard type");
            // @codeCoverageIgnoreEnd
        }
        $widget->delete();
        $this->response->body(json_encode(array("res" => "ok")));
    }

    /**
     * Adds a widget to a dashboard
     * 
     * Returns "ok" if succeeded.
     * 
     * @url http://example.com/api/dashboard/add/main/&lt;dashboard_id&gt;
     * @url http://example.com/api/dashboard/add/project/&lt;dashboard_id&gt;/&lt;project_id&gt;
     * @url http://example.com/api/dashboard/add/build/&lt;dashboard_id&gt;/&lt;project_id&gt;
     * @postparameter project Project ID (optional or mandatory, depending on widget)
     * @postparameter id      Widget type (mandatory)
     * @postparameter width   Widget width (mandatory)
     * @postparameter height  Widget height (mandatory)
     * @postparameter column  Widget column (mandatory)
     * @postparameter row     Widget row (mandatory)
     * @throws Exception Unsupported dashboard type (should never happen)
     */
    public function action_add()
    {
        $params = $this->request->post('params');

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
            default: throw new Exception("Unsupported dashboard type");
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
        $this->response->body(json_encode(array("res" => "ok", "id"  => $widget->id)));
    }

    /**
     * Moves a widget in a dashboard
     * 
     * Returns "ok" if succeeded.
     * 
     * @url http://example.com/api/dashboard/move/&lt;dashboard&gt;/&lt;widget_id&gt;
     * @postparameter column  Widget column (mandatory)
     * @postparameter row     Widget row (mandatory)
     * @throws Exception Unsupported dashboard type (should never happen)
     */
    public function action_move()
    {
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
            default: throw new Exception("Unsupported dashboard type");
            // @codeCoverageIgnoreEnd
        }

        $widget->column = $this->request->post('column');
        $widget->row    = $this->request->post('row');
        $widget->update();
        $this->response->body(json_encode(array("res" => "ok", "id"  => $widget->id)));
    }
}