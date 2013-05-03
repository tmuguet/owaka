<?php
$project = ORM::factory('Project', $widget->project_id);

$build = $project->builds
        ->order_by('id', 'DESC')
        ->with('coverage_globaldata')
        ->limit(1)
        ->find();
?>
<div id="widget_<?php echo $widget->id; ?>" class="grid grid_2 grid_v2 clickable build-<?php echo $build->status; ?>">
    <img src="img/freepik/target.png" width="32" height="32" style="float: right"/>
    <h2><?php echo $project->name; ?></h2>


    <h3 title="<?php echo addslashes($build->message); ?>">r<?php echo $build->revision; ?></h3>
    <h4>
        <?php
        if ($build->status == "building") {
            echo 'ETA ' . date("H:i", strtotime($build->eta));
        } else if ($build->status == "queued") {
            echo 'Queued';
        } else if (!empty($build->coverage_globaldata->id)) {
            echo $build->coverage_globaldata->totalcoverage . ' %';
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