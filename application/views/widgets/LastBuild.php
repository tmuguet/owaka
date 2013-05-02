<?php
$build = ORM::factory('Build')
        ->where('project_id', '=', $widget->project_id)
        ->order_by('id', 'DESC')
        ->with('phpunit_globaldata')
        ->limit(1)
        ->find();
?>
<div class="grid_2 build-<?php echo $build->status; ?> <?php
     if ($build->regression) {
         echo ' build-regression';
     }
     ?>">
    <h2>Last build</h2>


    <h3 title="<?php echo addslashes($build->message); ?> '"><?php echo $build->revision; ?></h3>
    <h4>
        <?php
        if ($build->status == "building") {
            echo 'ETA ' . date("H:i", $build->eta);
        } else if (!empty($build->phpunit_globaldata->tests)) {
            echo $build->phpunit_globaldata->failures . ' + ' . $build->phpunit_globaldata->errors . ' / ' . $build->phpunit_globaldata->tests;
        }
        ?>
    </h4>
</div>