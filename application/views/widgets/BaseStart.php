<div id="widget_<?php echo $id; ?>" class="grid-elt<?php if (!empty($status)) {echo ' build-' . $status;} ?>" data-grid-width="<?php echo $width; ?>" data-grid-height="<?php echo $height; ?>" data-grid-column="<?php echo $column; ?>" data-grid-row="<?php echo $row; ?>">
    <div class="grid-elt-ico">
        <span class="grid-elt-ico-label widget-detailed"><?php echo $widgetTitle; ?></span>
        <img src="img/freepik/<?php echo $widgetIcon; ?>.png" width="32" height="32"/>
    </div>
    <div class="grid-elt-content">
    
    <?php if (!empty($title)): ?><h2><?php echo $title; ?></h2><?php endif; ?>

    <?php if (!empty($subtitle)): ?><h3><?php echo $subtitle; ?></h3><?php endif; ?>