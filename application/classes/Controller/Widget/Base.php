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
            switch ($this->request->action()) {
                case Owaka::WIDGET_MAIN:
                    $this->_model = ORM::factory('Widget', $widgetId);
                    break;

                case Owaka::WIDGET_PROJECT:
                    // not supported yet
                    break;

                case Owaka::WIDGET_BUILD:
                    $this->_model = ORM::factory('Build_Widget', $widgetId);
                    break;
                
                case Owaka::WIDGET_SAMPLE:
                    $this->_model = ORM::factory('Widget');
                    $size = static::getPreferredSize();
                    $this->_model->width = $size[0];
                    $this->_model->height = $size[1];
                    $this->_model->column = 0;
                    $this->_model->row = 0;
                    $this->_model->id = $this->request->param('id');
                    $this->_model->type = get_called_class();
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
        if (isset($params[$name])) {
            return $params[$name];
        } else {
            // Find default value
            $class              = get_called_class();
            $expectedParameters = $class::getExpectedParameters($this->request->action());
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
        View::set_global('from', $this->request->action());
        View::set_global('widgetType', str_replace("_", "/", str_replace("Controller_Widget_", "", $this->getModelWidget()->type)));
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
}