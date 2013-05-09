<?php

abstract class Controller_Widget_Base extends Controller
{

    /**
     * Icon to be displayed
     * @var string
     */
    protected $widgetIcon = NULL;

    /**
     * Title of the widget
     * @var string 
     */
    protected $widgetTitle  = NULL;
    protected $widgetStatus = NULL;
    protected $widgetLinks  = array();

    /**
     * Reference to the widget model, should not be used in final widgets
     * @var Model_Widget|Model_Project_Widget|Model_Build_Widget
     */
    private $_model = NULL;

    /**
     * Reference to the project
     * @var Model_Project 
     */
    private $_project = NULL;

    /**
     * Reference to the build
     * @var Model_Build
     */
    private $_build    = NULL;
    private $_title    = NULL;
    private $_subtitle = NULL;

    public function before()
    {
        parent::before();
        if ($this->getModelWidget() instanceof Model_Widget) {
            if ($this->getProject() !== NULL) {
                $this->_title = $this->getProject()->name;

                /* if ($this->getBuild() !== NULL) {
                  $this->_subtitle = 'r' . $this->getBuild()->revision;
                  } */
            }
        }
    }

    protected function getModelWidget()
    {
        if ($this->_model === NULL) {
            $widgetId = $this->request->param('id');

            if ($this->request->action() == 'virtual') {
                $action = 'sample';
            } else {
                $action = $this->request->param('dashboard');
            }
            switch ($action) {
                case Owaka::WIDGET_MAIN:
                    $this->_model = ORM::factory('Widget', $widgetId);
                    break;

                case Owaka::WIDGET_PROJECT:
                    $this->_model = ORM::factory('Project_Widget', $widgetId);
                    break;

                case Owaka::WIDGET_BUILD:
                    $this->_model = ORM::factory('Build_Widget', $widgetId);
                    break;

                case Owaka::WIDGET_SAMPLE:
                    $post = $this->request->post();

                    $this->_model         = ORM::factory('Widget');
                    $this->_model->width  = $post['width'];
                    $this->_model->height = $post['height'];
                    $this->_model->column = $post['column'];
                    $this->_model->row    = $post['row'];
                    $this->_model->id     = $widgetId;
                    $this->_model->params = json_encode($post['params']);
                    $this->_model->type   = get_called_class();
                    break;
            }
        }
        return $this->_model;
    }

    protected function getProject()
    {
        if ($this->_project === NULL) {
            $projectId = $this->getParameter('project');
            if ($projectId !== NULL) {
                $this->_project = ORM::factory('project', $projectId);
            } else {
                $model = $this->getModelWidget();
                if ($model instanceof Model_Widget) {
                    // No project here
                } else if ($model instanceof Model_Project_Widget) {
                    // Project ID is available via URI parameters
                    $projectId      = $this->request->param('data');
                    $this->_project = ORM::factory('Project', $projectId);
                } else if ($model instanceof Model_Build_Widget) {
                    // Get the project via the build
                    $this->_project = $this->getBuild()->project;
                } else {
                    throw new Exception("Unexpected type of widget");
                }
            }
        }
        return $this->_project;
    }

    protected function getBuild()
    {
        if ($this->_build === NULL) {
            $buildId = $this->getParameter('build');
            if ($buildId !== NULL) {
                $this->_build = ORM::factory('build', $buildId);
            } else {
                $model = $this->getModelWidget();
                if ($model instanceof Model_Widget || $model instanceof Model_Project_Widget) {
                    // No build here
                } else if ($model instanceof Model_Build_Widget) {
                    // Build ID is available via URI parameters
                    $buildId      = $this->request->param('data');
                    $this->_build = ORM::factory("Build", $buildId);
                } else {
                    throw new Exception("Unexpected type of widget");
                }
            }
        }
        return $this->_build;
    }

    protected function getParameters()
    {
        if (!empty($this->getModelWidget()->params)) {
            return json_decode($this->getModelWidget()->params, TRUE);
        } else {
            return array();
        }
    }

    protected function getParameter($name)
    {
        $params = $this->getParameters();
        if (isset($params[$name]) && !empty($params[$name])) {
            return $params[$name];
        } else {
            // Find default value
            $class              = get_called_class();
            $expectedParameters = $class::getExpectedParameters($this->request->param('dashboard'));
            if (isset($expectedParameters[$name]) && isset($expectedParameters[$name]['default'])) {
                return $expectedParameters[$name]['default'];
            } else {
                return NULL;
            }
        }
    }

    protected function getWidth()
    {
        return $this->getModelWidget()->width;
    }

    protected function getHeight()
    {
        return $this->getModelWidget()->height;
    }

    abstract protected function render();

    protected function initViews()
    {
        View::set_global('from', $this->request->param('dashboard'));
        View::set_global('widgetType',
                         str_replace("_", "/", str_replace("Controller_Widget_", "", $this->getModelWidget()->type)));
        View::set_global('id', $this->getModelWidget()->id);
        View::set_global('width', $this->getWidth());
        View::set_global('height', $this->getHeight());
        View::set_global('column', $this->getModelWidget()->column);
        View::set_global('row', $this->getModelWidget()->row);
        View::set_global('widgetIcon', $this->widgetIcon);
        View::set_global('widgetTitle', $this->widgetTitle);
        View::set_global('widgetStatus', $this->widgetStatus);
        View::set_global('widgetLinks', $this->widgetLinks);
        View::set_global('title', $this->_title);
        View::set_global('subtitle', $this->_subtitle);
    }

    public function action_display()
    {
        $name = "display_" . $this->request->param('dashboard');
        if (method_exists($this, $name)) {
            $this->$name();
        } else if (method_exists($this, "display_all")) {
            $this->display_all();
        } else {
            throw new HTTP_Exception_500("Widget " . get_called_class() . " does not support dashboard " . $this->request->param('dashboard'));
        }

        $this->render();
    }

    public function action_sample()
    {
        array_unshift($this->widgetLinks,
                      array(
            "title" => 'delete',
            "url"   => 'javascript:void(0)',
            "js"    => 'deleteMe(this);'
        ));

        $name = "sample_" . $this->request->param('dashboard');
        if (method_exists($this, $name)) {
            $this->$name();
        } else if (method_exists($this, "sample_all")) {
            $this->sample_all();
        } else {
            throw new HTTP_Exception_500("Widget " . get_called_class() . " does not support dashboard preview " . $this->request->param('dashboard'));
        }
        
        $this->render();
    }
    
    public function action_virtual() {
        $this->action_sample();
    }
}