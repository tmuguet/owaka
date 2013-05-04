<div id="widget_<?php echo $id; ?>" class="grid grid_<?php echo $width; ?> grid_v<?php echo $height; ?><?php if (!empty($status)) {echo ' build-' . $status;} ?>">
    <div class="grid-ico">
        <span class="grid-ico-label widget-detailed"><?php echo $widgetTitle; ?></span>
        <img src="img/freepik/<?php echo $widgetIcon; ?>.png" width="32" height="32"/>
    </div>
    <div class="grid-content">
    
    <?php if (!empty($title)): ?><h2><?php echo $title; ?></h2><?php endif; ?>

    <?php if (!empty($subtitle)): ?><h3><?php echo $subtitle; ?></h3><?php endif; ?>