<?php

/**
 * Base class for all widgets
 */
abstract class Controller_Widget_Base extends Controller
{

    /**
     * Status of the widget (ok, unstable, error)
     * @var string
     */
    protected $widgetStatus = NULL;

    /**
     * Links provided by the widget
     * @var array 
     */
    protected $widgetLinks = array();

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
    private $_build = NULL;

    /**
     * Header of the widget (e.g. project name)
     * @var string
     */
    private $_title = NULL;

    /**
     * Subheader of the widget (e.g. revision name)
     * @var string
     */
    private $_subtitle = NULL;

    /**
     * Automatically executed before the controller action.
     * Sets the widget headers.
     *
     * @return  void
     */
    public function before()
    {
        parent::before();
        if ($this->getProject() !== NULL) {
            $this->_title = $this->getProject()->name;

            if ($this->getBuild() !== NULL) {
                $this->_subtitle = $this->getBuild()->getRevision();
            }
        }
    }

    /**
     * Gets the ORM model of the widget, based on the context
     * @return Model_Widget|Model_Project_Widget|Model_Build_Widget
     */
    protected function getModelWidget()
    {
        if ($this->_model === NULL) {
            $widgetId = $this->request->param('id');

            if ($this->request->action() == Owaka::WIDGET_VIRTUAL) {
                $action = Owaka::WIDGET_SAMPLE;
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

    /**
     * Gets the current project, based on the context (or NULL if no project associated)
     * @return Model_Project|null
     * @throws Exception Unexpected type of widget (should NEVER happen)
     */
    protected function getProject()
    {
        if ($this->_project === NULL) {
            $projectId = $this->getParameter('project');
            if (!empty($projectId)) {
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

    /**
     * Gets the current build, based on the context (or NULL if no build associated)
     * @return Model_Build|null
     * @throws Exception Unexpected type of widget (should NEVER happen)
     */
    protected function getBuild()
    {
        if ($this->_build === NULL) {
            $buildId = $this->getParameter('build');
            if (!empty($buildId)) {
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

    /**
     * Gets all the widget parameter values.
     * @return array
     */
    protected function getParameters()
    {
        if (!empty($this->getModelWidget()->params)) {
            return json_decode($this->getModelWidget()->params, TRUE);
        } else {
            return array();
        }
    }

    /**
     * Gets a widget parameter value by its name, or its default value if not set in the parameters, or NULL if not found.
     * @param string $name
     * @return string
     */
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

    /**
     * Gets the width of the widget
     * @return int
     */
    protected function getWidth()
    {
        return $this->getModelWidget()->width;
    }

    /**
     * Gets the height of the widget
     * @return int
     */
    protected function getHeight()
    {
        return $this->getModelWidget()->height;
    }

    /**
     * Renders the widget
     */
    abstract protected function render();

    /**
     * Gets the widget title
     * @return string
     */
    abstract protected function getWidgetTitle();

    /**
     * Gets the widget icon
     * @return string
     */
    abstract protected function getWidgetIcon();

    /**
     * Initializes the views by setting all needed variables
     */
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
        View::set_global('widgetIcon', $this->getWidgetIcon());
        View::set_global('widgetTitle', $this->getWidgetTitle());
        View::set_global('widgetStatus', $this->widgetStatus);
        View::set_global('widgetLinks', $this->widgetLinks);
        View::set_global('title', $this->_title);
        View::set_global('subtitle', $this->_subtitle);
    }

    /**
     * Displays a widget
     * @url http://example.com/w/<dashboard>/<widget>/display
     * @throws HTTP_Exception_500 Type of dashboard not supported for the widget
     */
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

        if ($this->getProject() !== NULL) {
            array_unshift($this->widgetLinks,
                          array(
                "type" => 'project',
                "id"   => $this->getProject()->id
            ));
        }
        if ($this->getBuild() !== NULL) {
            array_unshift($this->widgetLinks,
                          array(
                "type" => 'build',
                "id"   => $this->getBuild()->id
            ));
        }

        $this->render();
    }

    /**
     * Displays a sample widget
     * @url http://example.com/w/<dashboard>/<widget>/sample
     * @throws HTTP_Exception_500 Type of dashboard not supported for the widget
     */
    public function action_sample()
    {
        $name = "sample_" . $this->request->param('dashboard');
        if (method_exists($this, $name)) {
            $this->$name();
        } else if (method_exists($this, "sample_all")) {
            $this->sample_all();
        } else {
            throw new HTTP_Exception_500("Widget " . get_called_class() . " does not support dashboard preview " . $this->request->param('dashboard'));
        }

        // Remove all links
        $this->widgetLinks = array(
            array(
                "title" => 'delete',
                "url"   => 'javascript:void(0)',
                "js"    => 'deleteMe(this);'
            )
        );

        $this->render();
    }

    /**
     * Displays a virtual widget
     * @url http://example.com/w/<dashboard>/<widget>/virtual
     * @throws HTTP_Exception_500 Type of dashboard not supported for the widget
     */
    public function action_virtual()
    {
        $this->action_sample();
    }
}