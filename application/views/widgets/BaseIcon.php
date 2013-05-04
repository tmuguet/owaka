<?php
echo View::factory('widgets/BaseStart')->render();

if (empty($statusData) && empty($substatus)) {
    echo View::factory('icon')->set('status', $status)->set('size', 64)->render();
} else if (!empty($statusData) && empty($substatus)) {
    echo View::factory('icon')->set('status', $status)->set('size', 52)->render();
    echo '<br>' . $statusData;
    if (!empty($statusDataLabel)) {
        echo ' <span class="widget-detailed">' . $statusDataLabel . '</span>';
    }
} else if (empty($statusData) && !empty($substatus)) {
    echo View::factory('icon')->set('status', $status)->set('size', 52)->render();
    echo '<br>';
    echo View::factory('icon')->set('status', $substatus)->set('size', 24)->render();
} else {
    echo '<div style="float: left; width: 45%; text-align: center;">';
    echo View::factory('icon')->set('status', $status)->set('size', 52)->render();
    echo '<br>' . $statusData;
    if (!empty($statusDataLabel)) {
        echo ' <span class="widget-detailed">' . $statusDataLabel . '</span>';
    }
    echo '</div><div style="float: left; width: 45%; text-align: center;">';
    echo View::factory('icon')->set('status', $substatus)->set('size', 52)->render();
    echo '<br>' . $substatusData;
    if (!empty($substatusDataLabel)) {
        echo ' <span class="widget-detailed">' . $substatusDataLabel . '</span>';
    }
    echo '</div>';
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#widget_<?php echo $id; ?>").click(function(e) {
            document.location.href = 'welcome/build/<?php echo $id; ?>';
            e.stopPropagation();
            return false;
        });
    });
</script>
<?php
echo View::factory('widgets/BaseEnd')->render();
?>