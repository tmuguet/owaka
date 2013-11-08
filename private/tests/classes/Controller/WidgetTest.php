<?php
require_once dirname(__FILE__) . DIR_SEP . '_stubs' . DIR_SEP . 'WidgetStub.php';

class Controller_WidgetTest extends TestCase
{

    protected $xmlDataSet = 'widgets';

    /**
     * @covers Controller_Widget::getModelWidget
     */
    public function testGetModelWidget()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $expected1 = ORM::factory('Widget', $this->genNumbers['widget1']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getModelWidget());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget11']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = ORM::factory('Project_Widget', $this->genNumbers['widget11']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getModelWidget());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget21']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target3->request->action(Owaka::WIDGET_BUILD);
        $expected3 = ORM::factory('Build_Widget', $this->genNumbers['widget21']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getModelWidget());
    }

    /**
     * @covers Controller_Widget::getModelWidget
     * @expectedException HTTP_Exception_404
     * @expectedExceptionMessage Unexpected type of dashboard
     */
    public function testGetModelWidgetError()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', 'foo');
        $target1->request->action(Owaka::WIDGET_MAIN);
        $target1->getModelWidget();
    }

    /**
     * @covers Controller_Widget::getParameters
     */
    public function testGetParameters()
    {
        $target0 = new Controller_Widget_WidgetStub();
        $target0->request->setParam('id', $this->genNumbers['widget0']);
        $target0->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target0->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals(array(), $target0->getParameters());

        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals(array(), $target1->getParameters());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = array("project" => $this->genNumbers['ProjectFoo']);
        $this->assertEquals($expected2, $target2->getParameters());
    }

    /**
     * @covers Controller_Widget::getParameter
     */
    public function testGetParameter()
    {
        $target = new Controller_Widget_WidgetStub();
        $target->request->setParam('id', $this->genNumbers['widget3']);
        $target->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals($this->genNumbers['ProjectFoo'], $target->getParameter('project'));
        $this->assertEquals($this->genNumbers['build1'], $target->getParameter('build'));
        $this->assertEquals('grunge', $target->getParameter('foo'));
        $this->assertNull($target->getParameter('bar'));
    }

    /**
     * @covers Controller_Widget::getProject
     */
    public function testGetProjectMain()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target1->getProject());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget3']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target3->request->action(Owaka::WIDGET_MAIN);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());
    }

    /**
     * @covers Controller_Widget::getProject
     */
    public function testGetProjectProject()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $expected1 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getProject());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget13']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target3->request->action(Owaka::WIDGET_PROJECT);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getProject());

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getProject());
    }

    /**
     * @covers Controller_Widget::getProject
     */
    public function testGetProjectBuild()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget21']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target1->request->setParam('data', $this->genNumbers['build1']);
        $target1->request->action(Owaka::WIDGET_BUILD);
        $expected1 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getProject());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget22']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target2->request->action(Owaka::WIDGET_BUILD);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget23']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target3->request->action(Owaka::WIDGET_BUILD);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget24']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target4->request->action(Owaka::WIDGET_BUILD);
        $expected4 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getProject());

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget25']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target5->request->action(Owaka::WIDGET_BUILD);
        $expected5 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getProject());
    }

    /**
     * @covers Controller_Widget::getBuild
     */
    public function testGetBuildMain()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target1->getBuild());

        $target2 = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target2->getBuild());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget3']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target3->request->action(Owaka::WIDGET_MAIN);
        $expected3 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getBuild());
    }

    /**
     * @covers Controller_Widget::getBuild
     */
    public function testGetBuildProject()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $this->assertNull($target1->getBuild());

        $target2 = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $this->assertNull($target2->getBuild());

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getBuild());

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getBuild());
    }

    /**
     * @covers Controller_Widget::getBuild
     */
    public function testGetBuildBuild()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget21']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target1->request->setParam('data', $this->genNumbers['build1']);
        $target1->request->action(Owaka::WIDGET_BUILD);
        $expected1 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getBuild());

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget24']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target4->request->action(Owaka::WIDGET_BUILD);
        $expected4 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getBuild());

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget25']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target5->request->action(Owaka::WIDGET_BUILD);
        $expected5 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getBuild());
    }

    /**
     * @covers Controller_Widget::getLastBuild
     */
    public function testGetLastBuildMain()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target1->getLastBuild());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getLastBuild());

        $expected3 = ORM::factory('Build', $this->genNumbers['build3']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target2->getLastBuild(array(Owaka::BUILD_QUEUED)));
    }

    /**
     * @covers Controller_Widget::getLastBuild
     */
    public function testGetLastBuildProject()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $expected1 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getLastBuild());

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getLastBuild());

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget13']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target3->request->action(Owaka::WIDGET_PROJECT);
        $expected3 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getLastBuild());

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getLastBuild());

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getLastBuild());
    }

    /**
     * @covers Controller_Widget::getLastBuilds
     */
    public function testGetLastBuildsMain()
    {
        $target1 = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $expected1 = array(
            ORM::factory('Build', $this->genNumbers['build2']),
            ORM::factory('Build', $this->genNumbers['build1'])
        );
        $actual1 = $target1->getLastBuilds(10);
        $this->assertEquals(2, sizeof($actual1));
        $this->assertEquals($expected1[0], $actual1[0]);
        $this->assertEquals($expected1[1], $actual1[1]);

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = array(
            ORM::factory('Build', $this->genNumbers['build1'])
        );
        $actual2 = $target2->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual2));
        $this->assertEquals($expected2[0], $actual2[0]);
        
        $expected3 = array(
            ORM::factory('Build', $this->genNumbers['build3'])
        );
        $actual3 = $target2->getLastBuilds(10, array(Owaka::BUILD_QUEUED));
        $this->assertEquals(1, sizeof($actual3));
        $this->assertEquals($expected3[0], $actual3[0]);
    }

    /**
     * @covers Controller_Widget::getLastBuilds
     */
    public function testGetLastBuildsProject()
    {
        $target1   = new Controller_Widget_WidgetStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $expected1 = array(
            ORM::factory('Build', $this->genNumbers['build1'])
        );
        $actual1 = $target1->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual1));
        $this->assertEquals($expected1[0], $actual1[0]);

        $target2   = new Controller_Widget_WidgetStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = array(
            ORM::factory('Build', $this->genNumbers['build1'])
        );
        $actual2 = $target2->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual2));
        $this->assertEquals($expected2[0], $actual2[0]);

        $target3   = new Controller_Widget_WidgetStub();
        $target3->request->setParam('id', $this->genNumbers['widget13']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target3->request->action(Owaka::WIDGET_PROJECT);
        $expected3 = array(
            ORM::factory('Build', $this->genNumbers['build2'])
        );
        $actual3 = $target3->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual3));
        $this->assertEquals($expected3[0], $actual3[0]);

        $target4   = new Controller_Widget_WidgetStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = array(
            ORM::factory('Build', $this->genNumbers['build1'])
        );
        $actual4 = $target4->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual4));
        $this->assertEquals($expected4[0], $actual4[0]);

        $target5   = new Controller_Widget_WidgetStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = array(
            ORM::factory('Build', $this->genNumbers['build2'])
        );
        $actual5 = $target5->getLastBuilds(10);
        $this->assertEquals(1, sizeof($actual5));
        $this->assertEquals($expected5[0], $actual5[0]);
    }

    /**
     * @covers Controller_Widget::before
     * @covers Controller_Widget::initViews
     */
    public function testInitViews()
    {
        $target = new Controller_Widget_WidgetStub();
        $target->request->login();
        $target->request->setParam('id', $this->genNumbers['widget3']);
        $target->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target->request->action(Owaka::WIDGET_MAIN);
        $target->before();
        $target->initViews();

        $view = new View();
        $this->assertEquals(Owaka::WIDGET_MAIN, $view->from);
        $this->assertEquals('stub', $view->widgetType);
        $this->assertEquals($this->genNumbers['widget3'], $view->id);
        $this->assertEquals(1, $view->width);
        $this->assertEquals(2, $view->height);
        $this->assertEquals(3, $view->column);
        $this->assertEquals(4, $view->row);
        $this->assertEquals('icon', $view->widgetIcon);
        $this->assertEquals('title', $view->widgetTitle);
        $this->assertNull($view->widgetStatus);
        $this->assertEmpty($view->widgetLinks);
        $this->assertEquals('active-hg', $view->title);
        $this->assertEquals('r40', $view->subtitle);
    }
}