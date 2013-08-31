<?php
echo View::factory('widgets/BaseStart')->render();

if (!isset($data) || sizeof($data) == 0) {
    echo View::factory('icon')->set('status', $widgetStatus)->set('size', 64)->render();
} else {
    $size = 100 / sizeof($data) - 5;
    for ($i = 0; $i < sizeof($data); $i++) {
        echo '<div style="float: left; width: ' . $size . '%; text-align: center;">';
        echo View::factory('icon')->set('status', $data[$i]['status'])->set('size', 52)->render();
        echo '<br>';
        if (!empty($data[$i]['data'])) {
            echo $data[$i]['data'];
        }
        if (!empty($data[$i]['label'])) {
            echo ' <span class="widget-detailed">' . $data[$i]['label'] . '</span>';
        }
        echo '</div>';
    }
}
echo View::factory('widgets/BaseEnd')->render();
