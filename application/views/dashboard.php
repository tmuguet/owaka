<?php
if (!isset($projectId)) {$projectId = NULL;}
if (!isset($buildId)) {$buildId = NULL;}
if ($projectId !== NULL) {
    $project = ORM::factory('Project', $projectId);
    $lastBuild = $project->lastBuild()->find();
    if (!$lastBuild->loaded()) {
        $lastBuild = NULL;
    }
} else {
    $project = NULL;
    $lastBuild = NULL;
}
if ($buildId !== NULL) {
    $build = ORM::factory('Build', $buildId);
} else {
    $build = NULL;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>owaka</title>
        <base href="/owaka/">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>
        <script type="text/javascript" src="js/jquery.sparkline.min.js"></script>

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div id="overlay" class="ui-widget-overlay ui-helper-hidden"> </div>
        <div id="left-panel">
            <div id="owaka"><h1>owaka</h1></div>
            <div id="menu">
                <?php if ($from == 'main'): ?>
                <a href="manager/add" title="Add a new project"><img src="img/freepik/plus3.png" width="32" alt="Add a new project"/></a>
                <?php else: ?>
                <a href="manager/edit/<?php echo $projectId; ?>" title="Edit project"><img src="img/freepik/pencil.png" width="32" alt="Edit project"/></a>
                <?php endif; ?>
                <a href="designer/<?php echo $from; ?>/<?php if ($from != "main") {echo $projectId;} ?>" title="Go to designer mode"><img src="img/freepik/layout3.png" width="32" alt="Designer mode"/></a>
                <a href="javascript:void(0)" onclick="$('.grid-elt').toggle()" title="Toggle widgets"><img src="img/freepik/layout7.png" width="32" alt="Toggle widgets"/></a>

                <ul>
                    <li><a href="dashboard/main">Home</a></li>
                <?php foreach (ORM::factory('Project')->order_by('name', 'ASC')->find_all() as $_project): ?>
                    <li>
                        <a href="dashboard/project/<?php echo $_project->id; ?>"<?php if (($from == 'project' || $from == 'build') && $projectId == $_project->id) {echo ' style="font-weight: bold"';} ?>><?php echo $_project->name; ?></a>
                        <?php if (($from == 'project' || $from == 'build') && $projectId == $_project->id) {
                            echo '<ul>';
                            if ($from == 'project') {
                                $_source = ($lastBuild === NULL ? array() : $lastBuild->rangeBuild());
                            } else {
                                $_source = $build->rangeBuild();
                            }
                            foreach ($_source as $_build) {
                                echo '<li><a href="dashboard/build/'.$_build->id.'"';
                                if ($buildId == $_build->id) {echo ' style="font-weight: bold"';}
                                echo '>' . $_build->getRevision() .'</a></li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div id="top-panel">
            <h2><?php
            switch ($from) {
                case "main": echo 'main'; break;
                case "project" :
                    echo $project->name;
                    echo ' - latest: ';
                    echo ($lastBuild === NULL ? 'none' : $lastBuild->getRevision());
                    break;
                case "build": echo $project->name . ' - ' .$build->getRevision(); break;
            }
            ?></h2>
        </div>
        <div id="grid">
            <?php if (empty($widgets)) { ?>
                <div id="widget_0" class="grid-elt build-error static" data-grid-width="6" data-grid-height="4" data-grid-column="0" data-grid-row="0">
                    <div class="grid-elt-ico">
                        <img src="img/freepik/warningsign.png" width="32" height="32"/>
                        <span class="grid-elt-ico-label widget-detailed">owaka</span>
                    </div>
                    <div class="grid-elt-content">
                        <img src="img/freepik/warningsign.png" width="128" class="ico"/><br>
                        No widget in this dashboard<br>
                        <a href="designer/<?php echo $from; ?>/<?php if ($from != "main") {echo $projectId;} ?>">Go in designer mode</a>
                    </div>
                </div>
<?php
            } else {
                foreach ($widgets as $widget) {
                    echo $widget->body();
                }
            }
            ?>
        </div>
    </body>
</html>