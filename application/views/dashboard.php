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
        <div id="owaka"><h1>owaka</h1></div>
        <div id="menu">
            <a href="#" onclick="$('.grid-elt').toggle(); return false;">toggle widgets</a>
            <ul>
                <li><a href="welcome/main">Home</a></li>
            <?php foreach (ORM::factory('Project')->order_by('name', 'ASC')->find_all() as $project): ?>
                <li<?php if (($from == 'project' || $from == 'build') && $projectId == $project->id) {echo ' style="font-weight: bold"';} ?>><a href="welcome/project/<?php echo $project->id; ?>"><?php echo $project->name; ?></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <div id="grid">
            <?php if (empty($widgets)) { ?>
                <div id="widget_0" class="grid-elt build-error" data-grid-width="6" data-grid-height="4" data-grid-column="0" data-grid-row="0">
                    <div class="grid-elt-ico">
                        <img src="img/freepik/warningsign.png" width="32" height="32"/>
                        <span class="grid-elt-ico-label widget-detailed">owaka</span>
                    </div>
                    <div class="grid-elt-content">
                        <img src="img/freepik/warningsign.png" width="128" class="ico"/><br>
                        No widget in this dashboard<br>
                        <a href="designer/<?php echo $from; ?>/<?php if ($from == "project") {echo $projectId;} else if ($from == "build") {echo $buildId;} ?>">Go in designer mode</a>
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