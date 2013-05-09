<?php

class Controller_Api_dashboard extends Controller
{
    public function action_delete() {
        $widget = ORM::factory('Widget', $this->request->param('id'));
        $widget->delete();
        $this->response->body(json_encode(array("ok")));
    }
    
    public function action_add() {
        $widget = ORM::factory('Widget');
        $widget->type = $this->request->param('id');
        $widget->params = json_encode($this->request->post('params'));
        $widget->width = $this->request->post('width');
        $widget->height = $this->request->post('height');
        $widget->column = $this->request->post('column');
        $widget->row = $this->request->post('row');
        $widget->create();
        $this->response->body(json_encode(array("ok")));
    }
}