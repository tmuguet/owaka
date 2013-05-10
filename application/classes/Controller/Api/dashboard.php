<?php

class Controller_Api_dashboard extends Controller
{

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
            if (empty($value)) {unset($params[$key]);}
        }
        $widget->type   = $this->request->param('id');
        $widget->params = json_encode();
        $widget->width  = $this->request->post('width');
        $widget->height = $this->request->post('height');
        $widget->column = $this->request->post('column');
        $widget->row    = $this->request->post('row');
        $widget->create();
        $this->response->body(json_encode(array("ok")));
    }
}