<?php
if (!isset($projectId)) {
    $projectId = NULL;
}
if (!isset($buildId)) {
    $buildId = NULL;
}
if ($projectId !== NULL) {
    $project   = ORM::factory('Project', $projectId);
    $lastBuild = $project->lastBuild()->find();
    if (!$lastBuild->loaded()) {
        $lastBuild = NULL;
    }
} else {
    $project   = NULL;
    $lastBuild = NULL;
}
if ($buildId !== NULL) {
    $build = ORM::factory('Build', $buildId);
} else {
    $build = NULL;
}


switch ($from) {
    case "main":
        $title = 'main';
        break;
    case "project" :
        $title = $project->name . ' - latest: ' . ($lastBuild === NULL ? 'none' : $lastBuild->getRevision() . ' from ' . Date::loose_span(strtotime($lastBuild->finished)));
        break;
    case "build":
        $title = $project->name . ' - ' . $build->getRevision() . ' from ' . Date::loose_span(strtotime($build->finished));
        break;
    default:
        $title = '';
}

$menu   = array();
$menu[] = array(
    'title'    => 'Home',
    'href'     => 'dashboard/main',
    'selected' => ($from == 'main')
);

foreach (ORM::factory('Project')->order_by('name', 'ASC')->find_all() as $_project) {
    $_lastBuild = $_project->lastBuild()->find();
    $_status    = ($_lastBuild->loaded() ? $_lastBuild->getIcon() : 'ban-circle');

    $res = array(
        'title'    => $_project->name,
        'href'     => 'dashboard/project/' . $_project->id,
        'selected' => ($from == 'project' || $from == 'build') && $projectId == $_project->id,
        'img'      => $_status,
        'alt'      => $_lastBuild->status,
        'class'    => 'build-' . $_lastBuild->status,
    );

    if (($from == 'project' || $from == 'build') && $projectId == $_project->id) {
        $res['submenu'] = array();

        if ($from == 'project') {
            $_source = ($lastBuild === NULL ? array() : $lastBuild->rangeBuild());
        } else {
            $_source = $build->rangeBuild();
        }
        foreach ($_source as $_build) {
            $res['submenu'][] = array(
                'title'    => $_build->getRevision(),
                'href'     => 'dashboard/build/' . $_build->id,
                'selected' => $buildId == $_build->id,
                'img'      => $_build->getIcon(),
                'alt'      => $_build->status,
                'class'    => 'build-' . $_build->status,
            );
        }
    }
    $menu[] = $res;
}

$menu_bottom = array();

if ($from == 'build') {
    $menu_bottom[] = array(
        'title' => 'delete build',
        'js'    => '$.owaka.dashboard.deletebuild(' . $buildId . ')',
        'img'   => 'trash',
        'alt'   => 'Delete this build'
    );
}

$menu_bottom[] = array(
    'title' => 'new project',
    'href'  => 'manager/add',
    'img'   => 'plus',
    'alt'   => 'Add a new project'
);
if ($from != 'main') {
    $menu_bottom[] = array(
        'title' => 'edit project',
        'href'  => 'manager/edit/' . $projectId,
        'img'   => 'pencil',
        'alt'   => 'Edit project'
    );
}
$menu_bottom[] = array(
    'title' => 'designer',
    'href'  => 'designer/' . $from . ($from == 'main' ? '' : '/' . $projectId),
    'img'   => 'th',
    'alt'   => 'Designer mode'
);

echo View::factory('baseStart')
        ->set('title', $title)
        ->render();
echo View::factory('baseMenu')
        ->set('title', $title)
        ->set('menu', $menu)
        ->set('menu_bottom', $menu_bottom)
        ->render();
?>
<div id="grid">
    <?php if (empty($widgets)) : ?>
        <div id="widget_0" class="grid-elt build-error static" data-grid-width="6" data-grid-height="4" data-grid-column="0" data-grid-row="0">
            <div class="grid-elt-ico">
                <i class="icon-dashboard"></i>
                <span class="grid-elt-ico-label widget-detailed">owaka</span>
            </div>
            <div class="grid-elt-content">
                <i class="icon-dashboard ico" style="font-size: 52px"></i><br>
                No widget in this dashboard<br>
                <a href="designer/<?php echo $from; ?>/<?php echo $projectId; ?>">Go in designer mode</a>
            </div>
        </div>
        <?php
    else:
        foreach ($widgets as $widget) {
            echo $widget->body();
        }
    endif;
    ?>
</div>
<script type="text/javascript">
    $.owaka.dashboard = {
        deletebuild: function(id) {
            $.owaka.api('api/build/delete/' + id, {}, function(data) {
                alert('Build deleted!');
                if (data.next_build != '') {
                    document.location = 'dashboard/build/' + data.next_build;
                } else {
                    document.location = 'dashboard/project/<?php echo $projectId; ?>';
                }
            });
        },
        from: "<?php echo $from; ?>",
    }
    
    $(document).bind('visibilitychange', $.owaka.setRefreshTimer);
    $.owaka.setRefreshTimer();
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();