<?php
defined('SYSPATH') or die('No direct access allowed!');

class OwakaTestProcessLink extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Owaka::processLink
     */
    public function testProcessLinkClassic()
    {
        $link = array(
            "url"   => 'foo/bar',
            "title" => 'my title',
            "class" => 'myclass'
        );
        $this->assertEquals(
                '<a href="foo/bar" class="myclass">my title</a>', Owaka::processLink('main', $link)
        );
        $this->assertEquals(
                '<a href="foo/bar" class="myclass">my title</a>', Owaka::processLink('main', $link, 'html')
        );
        $this->assertEquals(
                'document.location.href=\'foo/bar\';', Owaka::processLink('main', $link, 'js')
        );
    }

    /**
     * @covers Owaka::processLink
     */
    public function testProcessLinkClassicOnclick()
    {
        $link = array(
            "url"   => 'foo/bar',
            "title" => 'my title',
            "class" => 'myclass',
            "js"    => 'hello'
        );
        $this->assertEquals(
                '<a href="foo/bar" onclick="hello" class="myclass">my title</a>', Owaka::processLink('main', $link)
        );
        $this->assertEquals(
                'hello', Owaka::processLink('main', $link, 'js')
        );
    }

    /**
     * @covers Owaka::processLink
     */
    public function testProcessLinkClassicOnclick2()
    {
        $link = array(
            "url"   => '',
            "title" => 'my title',
            "class" => 'myclass',
            "js"    => 'hello'
        );
        $this->assertEquals(
                '<a href="javascript:void(0)" onclick="hello" class="myclass">my title</a>',
                Owaka::processLink('main', $link)
        );
        $this->assertEquals(
                'hello', Owaka::processLink('main', $link, 'js')
        );
    }

    /**
     * @covers Owaka::processLink
     */
    public function testProcessLinkProject()
    {
        $link = array(
            "type"  => 'project',
            "id"    => "1",
            "title" => 'my title',
            "class" => 'myclass'
        );
        $this->assertEquals(
                '<a href="dashboard/project/1" class="myclass">project</a>', Owaka::processLink('main', $link)
        );
        $this->assertEquals(
                'document.location.href=\'dashboard/project/1\';', Owaka::processLink('main', $link, 'js')
        );


        $this->assertNull(Owaka::processLink('project', $link));
        $this->assertNull(Owaka::processLink('project', $link, 'js'));

        $this->assertNull(Owaka::processLink('build', $link));
        $this->assertNull(Owaka::processLink('build', $link, 'js'));
    }

    /**
     * @covers Owaka::processLink
     */
    public function testProcessLinkBuild()
    {
        $link = array(
            "type"  => 'build',
            "id"    => "42",
            "title" => 'my title',
            "class" => 'myclass'
        );
        $this->assertEquals(
                '<a href="dashboard/build/42" class="myclass">build</a>', Owaka::processLink('main', $link)
        );
        $this->assertEquals(
                'document.location.href=\'dashboard/build/42\';', Owaka::processLink('main', $link, 'js')
        );


        $this->assertEquals(
                '<a href="dashboard/build/42" class="myclass">build</a>', Owaka::processLink('project', $link)
        );
        $this->assertEquals(
                'document.location.href=\'dashboard/build/42\';', Owaka::processLink('project', $link, 'js')
        );

        $this->assertNull(Owaka::processLink('build', $link));
        $this->assertNull(Owaka::processLink('build', $link, 'js'));
    }
}