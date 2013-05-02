<div class="grid_2">
    <h2>Latest builds</h2>
    <table width="100%">
        <thead>
            <tr>
                <th>Revision</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $builds = ORM::factory('Build')
                    ->where('project_id', '=', $widget->project_id)
                    ->order_by('id', 'DESC')
                    ->with('phpunit_globaldata')
                    ->limit(10)
                    ->find_all();

            foreach ($builds as $build) {
                echo '<tr class="build-' . $build->status;
                if ($build->regression) {
                    echo ' build-regression';
                }
                echo '" title="' . addslashes($build->message) . '"><td>' . $build->revision . '</td>';
                echo '<td>';
                if ($build->status == "building") {
                    echo 'ETA ' . date("H:i", $build->eta);
                } else if (!empty($build->phpunit_globaldata->tests)) {
                    echo $build->phpunit_globaldata->failures . ' + ' . $build->phpunit_globaldata->errors . ' / ' . $build->phpunit_globaldata->tests;
                }
                echo '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>