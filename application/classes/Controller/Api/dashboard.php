<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing dashboards
 */
class Controller_Api_dashboard extends Controller
{

    /**
     * Deletes a widget from a dashboard
     * 
     * Returns "ok" if succeeded
     * 
     * @url http://example.com/api/dashboard/delete/<dashboard>/<widget_id>
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
            default: throw new Exception("Unsupported dashboard type");
        }
        $widget->delete();
        $this->response->body(json_encode(array("ok")));
    }

    /**
     * Adds a widget to a dashboard
     * 
     * Returns "ok" if succeeded.
     * 
     * @url http://example.com/api/dashboard/add/main/<dashboard_id>
     * @url http://example.com/api/dashboard/add/project/<dashboard_id>/<project_id>
     * @url http://example.com/api/dashboard/add/build/<dashboard_id>/<project_id>
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
            default: throw new Exception("Unsupported dashboard type");
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
     * @url http://example.com/api/dashboard/move/<dashboard>/<widget_id>
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
            default: throw new Exception("Unsupported dashboard type");
        }

        $widget->column = $this->request->post('column');
        $widget->row    = $this->request->post('row');
        $widget->update();
        $this->response->body(json_encode(array("res" => "ok", "id"  => $widget->id)));
    }
}