<?php
$project = ORM::factory('Project', $widget->project_id);

$build = $project->builds
        ->order_by('id', 'DESC')
        ->with('phpunit_globaldata')
        ->limit(1)
        ->find();
?>
<div id="widget_<?php echo $widget->id; ?>" class="grid grid_2 grid_v2 clickable build-<?php echo $build->status; ?> <?php
if ($build->regression) {
    echo ' build-regression';
}
?>">
    <img src="img/freepik/pad.png" width="32" height="32" style="float: right"/>
    <h2><?php echo $project->name; ?></h2>


    <h3 title="<?php echo addslashes($build->message); ?>">r<?php echo $build->revision; ?></h3>
    <h4>
        <?php
        if ($build->status == "building") {
            echo 'ETA ' . date("H:i", strtotime($build->eta));
        } else if ($build->status == "queued") {
            echo 'Queued';
        } else if (!empty($build->phpunit_globaldata->tests)) {
            if ($build->phpunit_globaldata->failures > 0 || $build->phpunit_globaldata->errors > 0) {
                if ($build->phpunit_globaldata->failures > 0) {
                    echo '<img src="img/freepik/warning.png" width="52" style="vertical-align: middle"/> ';
                }
                if ($build->phpunit_globaldata->errors > 0) {
                    echo '<img src="img/freepik/radiation.png" width="52" style="vertical-align: middle"/>';
                }
                echo '<br/>';
                if ($build->phpunit_globaldata->failures > 0) {
                    echo $build->phpunit_globaldata->failures . ' ';
                }
                if ($build->phpunit_globaldata->errors > 0) {
                    echo $build->phpunit_globaldata->errors;
                }
            } else {
                echo '<img src="img/freepik/right.png" width="52"/><br/>' . $build->phpunit_globaldata->tests;
            }
        }
        ?>
    </h4>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#widget_<?php echo $widget->id; ?>").click(function(e) {
            document.location.href = 'welcome/build/<?php echo $build->id; ?>';
            e.stopPropagation();
            return false;
        });
    });
</script>