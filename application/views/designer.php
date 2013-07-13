<?php
$title = 'Designer';
$menu  = array();

$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'quit designer',
    'href'  => 'dashboard/' . ($from == 'main' ? $from : 'project') . '/' . ($from == "main" ? '' : $projectId),
    'img'   => 'freepik/layout3',
    'alt'   => 'Quit designer mode'
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
    <ul>
        <?php foreach ($controllers as $_controller): ?>
            <li class="widget-elt"><a href="javascript:void(0)" data-widget="<?php echo $_controller; ?>">
                    <?php
                    echo str_replace("_", "/", $_controller);
                    ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<script type="text/javascript">
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

        $(".widget-move").draggable({
            appendTo: "body",
            helper: "clone",
            start: function(event, ui) {
                $.owaka.designer.widget.prepareToMove($(this).closest('.grid-elt'));
            },
            stop: function(event, ui) {
                $.owaka.designer.slots.hide();
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