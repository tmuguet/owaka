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
        $title = $project->name . ' - latest: ' . ($lastBuild === NULL ? 'none' : $lastBuild->getRevision());
        break;
    case "build":
        $title = $project->name . ' - ' . $build->getRevision();
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
    $res = array(
        'title'    => $_project->name,
        'href'     => 'dashboard/project/' . $_project->id,
        'selected' => ($from == 'project' || $from == 'build') && $projectId == $_project->id
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
                'selected' => $buildId == $_build->id
            );
        }
    }
    $menu[] = $res;
}

$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'new project',
    'href'  => 'manager/add',
    'img'   => 'freepik/plus3',
    'alt'   => 'Add a new project'
);
if ($from != 'main') {
    $menu_bottom[] = array(
        'title' => 'edit project',
        'href'  => 'manager/edit/' . $projectId,
        'img'   => 'freepik/pencil',
        'alt'   => 'Edit project'
    );
}
$menu_bottom[] = array(
    'title' => 'designer',
    'href'  => 'designer/' . $from . ($from == 'main' ? '' : '/' . $projectId),
    'img'   => 'freepik/layout3',
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
                <img src="img/freepik/warningsign.png" width="32" height="32"/>
                <span class="grid-elt-ico-label widget-detailed">owaka</span>
            </div>
            <div class="grid-elt-content">
                <img src="img/freepik/warningsign.png" width="128" class="ico"/><br>
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
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();