<?php
$project = ORM::factory('Project', $widget->project_id);

$builds = $project->builds
        ->order_by('id', 'DESC')
        ->with('phpunit_globaldata')
        ->limit(10)
        ->find_all();
?>
<div id="widget_<?php echo $widget->id; ?>" class="grid grid_2 grid_v4">
    <img src="img/freepik/box.png" width="32" height="32" style="float: right"/>
    <h2><?php echo $project->name; ?></h2>
    <table width="100%" style="font-size: 60%">
        <thead>
            <tr>
                <th>Revision</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($builds as $build) {
                echo '<tr class="clickable build build-' . $build->status;
                if ($build->regression) {
                    echo ' build-regression';
                }
                echo '" title="' . addslashes($build->message) . '"><td><a href="welcome/build/' . $build->id . '">r' . $build->revision . '</a></td>';
                echo '<td>';
                if ($build->status == "building") {
                    echo 'ETA ' . date("H:i", strtotime($build->eta));
                } else if (!empty($build->phpunit_globaldata->tests)) {
                    if ($build->phpunit_globaldata->failures > 0 || $build->phpunit_globaldata->errors > 0) {
                if ($build->phpunit_globaldata->failures > 0) {
                    echo $build->phpunit_globaldata->failures . '<img src="img/freepik/multiply.png" width="16" style="vertical-align: middle"/> ';
                }
                if ($build->phpunit_globaldata->failures > 0 && $build->phpunit_globaldata->errors > 0) {
                    echo " & ";
                }
                if ($build->phpunit_globaldata->errors > 0) {
                    echo $build->phpunit_globaldata->errors . '<img src="img/freepik/radiation.png" width="16" style="vertical-align: middle"/> ';
                }
                echo ' / ';
            }
            echo $build->phpunit_globaldata->tests . '<img src="img/freepik/pad.png" width="16" style="vertical-align: middle"/>';
                }
                echo '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#widget_<?php echo $widget->id; ?> tbody tr").click(function(e) {
            document.location.href = $(this).find('a').attr("href");
            e.stopPropagation();
            return false;
        });
    });
</script>