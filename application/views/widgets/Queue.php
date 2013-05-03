<?php
$builds = ORM::factory('Build')
        ->where('status', 'IN', array('queued', 'building'))
        ->order_by('status', 'ASC')
        ->order_by('started', 'ASC')
        ->find_all();
?>
<div id="widget_<?php echo $widget->id; ?>" class="grid grid_2 grid_v4">
    <img src="img/freepik/clock.png" width="32" height="32" style="float: right"/>
    <table width="100%" style="font-size: 60%">
        <thead>
            <tr>
                <th>Project</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($builds as $build) {
                echo '<tr><td>' . $build->project->name . '</a></td>';
                echo '<td>';
                if ($build->status == "building") {
                    echo 'ETA ' . date("H:i", strtotime($build->eta));
                }
                echo '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>