<?php
$project = ORM::factory('Project', $widget->project_id);

$build = $project->builds
        ->order_by('id', 'DESC')
        ->limit(1)
        ->find();
?>
<div id="widget_<?php echo $widget->id; ?>" class="grid grid_2 grid_v2 clickable build-<?php echo $build->status; ?>">
    <img src="img/freepik/box.png" width="32" height="32" style="float: right"/>
    <h2><?php echo $project->name; ?></h2>

    <h3 title="<?php echo addslashes($build->message); ?>">r<?php echo $build->revision; ?></h3>

    <?php
    switch ($build->status) {
        case 'ok':
            echo '<img src="img/freepik/right.png" width="64"/>';
            break;

        case 'unstable':
            echo '<img src="img/freepik/warning.png" width="64"/>';
            break;

        case 'error':
            echo '<img src="img/freepik/radiation.png" width="64"/>';
            break;

        case 'building':
            echo '<img src="img/freepik/beaker.png" width="64"/>';
            break;

        case 'queued':
            echo '<img src="img/freepik/sandclock.png" width="64"/>';
            break;
    }
    ?>
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