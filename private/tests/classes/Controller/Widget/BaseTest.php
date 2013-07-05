<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_stubs' . DIRECTORY_SEPARATOR . 'BaseStub.php';

class Controller_Widget_BaseTest extends TestCase
{

    protected $xmlDataSet = 'base';

    /**
     * @covers Controller_Widget_Base::getModelWidget
     */
    public function testGetModelWidget()
    {
        $target1   = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $expected1 = ORM::factory('Widget', $this->genNumbers['widget1']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getModelWidget());

        $target2   = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget11']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = ORM::factory('Project_Widget', $this->genNumbers['widget11']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getModelWidget());

        $target3   = new Controller_Widget_BaseStub();
        $target3->request->setParam('id', $this->genNumbers['widget21']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target3->request->action(Owaka::WIDGET_BUILD);
        $expected3 = ORM::factory('Build_Widget', $this->genNumbers['widget21']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getModelWidget());
    }

    /**
     * @covers Controller_Widget_Base::getModelWidget
     * @expectedException Exception
     * @expectedExceptionMessage Unexpected type of dashboard
     */
    public function testGetModelWidgetError()
    {
        $target1 = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', 'foo');
        $target1->request->action(Owaka::WIDGET_MAIN);
        $target1->getModelWidget();
    }

    /**
     * @covers Controller_Widget_Base::getParameters
     */
    public function testGetParameters()
    {
        $target0 = new Controller_Widget_BaseStub();
        $target0->request->setParam('id', $this->genNumbers['widget0']);
        $target0->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target0->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals(array(), $target0->getParameters());

        $target1 = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals(array(), $target1->getParameters());

        $target2   = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = array("project" => $this->genNumbers['ProjectFoo']);
        $this->assertEquals($expected2, $target2->getParameters());
    }

    /**
     * @covers Controller_Widget_Base::getParameter
     */
    public function testGetParameter()
    {
        $target = new Controller_Widget_BaseStub();
        $target->request->setParam('id', $this->genNumbers['widget3']);
        $target->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target->request->action(Owaka::WIDGET_MAIN);
        $this->assertEquals($this->genNumbers['ProjectFoo'], $target->getParameter('project'));
        $this->assertEquals($this->genNumbers['build1'], $target->getParameter('build'));
        $this->assertEquals('grunge', $target->getParameter('foo'));
        $this->assertNull($target->getParameter('bar'));
    }

    /**
     * @covers Controller_Widget_Base::getProject
     */
    public function testGetProjectMain()
    {
        $target1 = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target1->getProject());

        $target2   = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_BaseStub();
        $target3->request->setParam('id', $this->genNumbers['widget3']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target3->request->action(Owaka::WIDGET_MAIN);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());
    }

    /**
     * @covers Controller_Widget_Base::getProject
     */
    public function testGetProjectProject()
    {
        $target1   = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $expected1 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getProject());

        $target2   = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_BaseStub();
        $target3->request->setParam('id', $this->genNumbers['widget13']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target3->request->action(Owaka::WIDGET_PROJECT);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());

        $target4   = new Controller_Widget_BaseStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getProject());

        $target5   = new Controller_Widget_BaseStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getProject());
    }

    /**
     * @covers Controller_Widget_Base::getProject
     */
    public function testGetProjectBuild()
    {
        $target1   = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget21']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target1->request->setParam('data', $this->genNumbers['build1']);
        $target1->request->action(Owaka::WIDGET_BUILD);
        $expected1 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getProject());

        $target2   = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget22']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target2->request->action(Owaka::WIDGET_BUILD);
        $expected2 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected2->loaded());
        $this->assertEquals($expected2, $target2->getProject());

        $target3   = new Controller_Widget_BaseStub();
        $target3->request->setParam('id', $this->genNumbers['widget23']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target3->request->action(Owaka::WIDGET_BUILD);
        $expected3 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getProject());

        $target4   = new Controller_Widget_BaseStub();
        $target4->request->setParam('id', $this->genNumbers['widget24']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target4->request->action(Owaka::WIDGET_BUILD);
        $expected4 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getProject());

        $target5   = new Controller_Widget_BaseStub();
        $target5->request->setParam('id', $this->genNumbers['widget25']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target5->request->action(Owaka::WIDGET_BUILD);
        $expected5 = ORM::factory('Project', $this->genNumbers['ProjectBar']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getProject());
    }

    /**
     * @covers Controller_Widget_Base::getBuild
     */
    public function testGetBuildMain()
    {
        $target1 = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget1']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target1->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target1->getBuild());

        $target2 = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget2']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target2->request->action(Owaka::WIDGET_MAIN);
        $this->assertNull($target2->getBuild());

        $target3   = new Controller_Widget_BaseStub();
        $target3->request->setParam('id', $this->genNumbers['widget3']);
        $target3->request->setParam('dashboard', Owaka::WIDGET_MAIN);
        $target3->request->action(Owaka::WIDGET_MAIN);
        $expected3 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected3->loaded());
        $this->assertEquals($expected3, $target3->getBuild());
    }

    /**
     * @covers Controller_Widget_Base::getBuild
     */
    public function testGetBuildProject()
    {
        $target1 = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget11']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target1->request->setParam('data', $this->genNumbers['ProjectFoo']);
        $target1->request->action(Owaka::WIDGET_PROJECT);
        $this->assertNull($target1->getBuild());

        $target2 = new Controller_Widget_BaseStub();
        $target2->request->setParam('id', $this->genNumbers['widget12']);
        $target2->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target2->request->action(Owaka::WIDGET_PROJECT);
        $this->assertNull($target2->getBuild());

        $target4   = new Controller_Widget_BaseStub();
        $target4->request->setParam('id', $this->genNumbers['widget14']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target4->request->action(Owaka::WIDGET_PROJECT);
        $expected4 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getBuild());

        $target5   = new Controller_Widget_BaseStub();
        $target5->request->setParam('id', $this->genNumbers['widget15']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_PROJECT);
        $target5->request->action(Owaka::WIDGET_PROJECT);
        $expected5 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getBuild());
    }

    /**
     * @covers Controller_Widget_Base::getBuild
     */
    public function testGetBuildBuild()
    {
        $target1   = new Controller_Widget_BaseStub();
        $target1->request->setParam('id', $this->genNumbers['widget21']);
        $target1->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target1->request->setParam('data', $this->genNumbers['build1']);
        $target1->request->action(Owaka::WIDGET_BUILD);
        $expected1 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected1->loaded());
        $this->assertEquals($expected1, $target1->getBuild());

        $target4   = new Controller_Widget_BaseStub();
        $target4->request->setParam('id', $this->genNumbers['widget24']);
        $target4->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target4->request->action(Owaka::WIDGET_BUILD);
        $expected4 = ORM::factory('Build', $this->genNumbers['build1']);
        $this->assertTrue($expected4->loaded());
        $this->assertEquals($expected4, $target4->getBuild());

        $target5   = new Controller_Widget_BaseStub();
        $target5->request->setParam('id', $this->genNumbers['widget25']);
        $target5->request->setParam('dashboard', Owaka::WIDGET_BUILD);
        $target5->request->action(Owaka::WIDGET_BUILD);
        $expected5 = ORM::factory('Build', $this->genNumbers['build2']);
        $this->assertTrue($expected5->loaded());
        $this->assertEquals($expected5, $target5->getBuild());
    }

    /**
     * @covers Controller_Widget_Base::before
     * @covers Controller_Widget_Base::initViews
     */
    public function testInitViews()
    {
        $target = new Controller_Widget_BaseStub();
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