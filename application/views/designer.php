<?php
$title  = 'Designer';
$menu   = array();
$menu[] = array(
    'title' => 'Quit designer',
    'href'  => 'dashboard/' . ($from == 'main' ? $from : 'project') . '/' . ($from == 'main' ? '' : $projectId),
    'img'   => 'off',
);

$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'Add a row',
    'js'    => '$.owaka.designer.slots.addRow();',
    'img'   => 'ellipsis-horizontal',
);
$menu_bottom[] = array(
    'title' => 'Add a column',
    'js'    => '$.owaka.designer.slots.addColumn();',
    'img'   => 'ellipsis-vertical',
);

echo View::factory('baseStart')
        ->set('title', $title)
        ->render();
?>
<div id="grid" class="sample">
    <?php
    foreach ($widgets as $_widget) {
        echo Request::factory('w/' . $from . '/' . $_widget->type . '/sample/' . $_widget->id)->execute()->body();
    }
    ?>
</div>
<div id="widget_details"></div>
<div id="list_widgets">
    <label for="search">Search:</label> <input type="text" id="search"/>
    <ul>
        <?php foreach ($controllers as $_controller): ?>
            <li class="widget-elt"><a href="javascript:void(0)" data-widget="<?php echo $_controller; ?>">
                    <?php
                    echo str_replace('_', '/', $_controller);
                    ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<script type="text/javascript">
<?php
$maxwidth  = 0;
$maxheight = 0;
foreach ($widgets as $_widget) {
    if ($_widget->row + $_widget->height > $maxheight) {
        $maxheight = $_widget->row + $_widget->height;
    }
    if ($_widget->column + $_widget->width > $maxwidth) {
        $maxwidth = $_widget->column + $_widget->width;
    }
}
?>
    $.owaka.designer.max_row = Math.max($.owaka.designer.max_row, <?php echo $maxheight; ?>);
    $.owaka.designer.max_column = Math.max($.owaka.designer.max_column, <?php echo $maxwidth; ?>);
    $.owaka.designer.slots._resize();
<?php
foreach ($widgets as $_widget) {
    echo '$.owaka.designer.slots.take(' . $_widget->row . ', ' . $_widget->column . ', ' . $_widget->height . ', ' . $_widget->width . ');';
}
echo '$.owaka.designer.from = "' . $from . '";';
if ($from != 'main') {
    echo '$.owaka.designer.data = "/' . $projectId . '"';
}
?>

    $(document).ready(function() {
        $("#list_widgets .widget-elt a").click(function() {
            var postData = {};
<?php
if (isset($projectId)) {
    echo 'postData.projectId = ' . $projectId . ';';
}
?>

            $("#widget_details").load('designer_details/<?php echo $from; ?>/' + $(this).attr("data-widget"), postData);
        });
    });

    $('#search').keyup(function() {
        var value = $(this).val().toLowerCase().split(' ');
        var count = value.length;
        $.each($("#list_widgets .widget-elt"), function() {
            var res = true;
            for (var i = 0; i < count; i++) {
                res &= $(this).text().toLowerCase().indexOf(value[i]) != -1;
            }
            if (res) {
                $(this).slideDown(100);
            } else {
                $(this).slideUp(100);
            }
        });
    });
</script>
<?php
echo View::factory('baseMenu')
        ->set('title', $title)
        ->set('menu', $menu)
        ->set('menu_bottom', $menu_bottom)
        ->render();
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();