<div id="widget_<?php echo $id; ?>" class="grid-elt<?php
if (!$extensible) {
    echo ' static';
}
if (!empty($widgetStatus)) {
    echo ' build-' . $widgetStatus;
}
?>" data-widget-id="<?php echo $id; ?>" data-grid-width="<?php echo $width; ?>" data-grid-height="<?php echo $height; ?>" data-grid-column="<?php echo $column; ?>" data-grid-row="<?php echo $row; ?>">
    <div class="grid-elt-ico">
        <img src="img/freepik/<?php echo $widgetIcon; ?>.png" width="32" height="32"/>
        <span class="grid-elt-ico-label widget-detailed"><?php echo $widgetTitle; ?></span>
    </div>
    <?php if (!empty($widgetLinks)): ?>
        <div class="grid-elt-more widget-detailed">
            <ul>
                <?php foreach ($widgetLinks as $link): ?>
                    <li>
                        <?php
                        echo Owaka::processLink($from, $link);
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="grid-elt-title">
        <?php if (!empty($title)): ?><h2><?php echo $title; ?></h2><?php endif; ?>
        <?php if (!empty($subtitle)): ?><h3><?php echo $subtitle; ?></h3><?php endif; ?>
    </div>
    <div class="grid-elt-content">