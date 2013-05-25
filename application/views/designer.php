<!DOCTYPE html>
<html>
    <head>
        <title>owaka/Designer</title>
        <base href="/owaka/">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>
        <script type="text/javascript" src="js/owaka.designer.js"></script>
        <script type="text/javascript" src="js/jquery.sparkline.min.js"></script>

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div id="overlay" class="ui-widget-overlay ui-helper-hidden"> </div>
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
                <li class="widget-elt"><a href="javascript:void(0)" data-widget="<?php echo $_controller['widget']; ?>" data-width="<?php echo $_controller['size'][0]; ?>" data-height="<?php echo $_controller['size'][1]; ?>"><?php echo str_replace("_", "/", $_controller['widget']); ?></a></li>

<?php endforeach; ?>
            </ul>
        </div>
        <div id="left-panel">
            <div id="owaka"><h1>owaka</h1></div>
            <div id="menu">
                <a href="dashboard/<?php echo ($from == 'main' ? $from : 'project'); ?>/<?php if ($from != "main") {echo $projectId;} ?>" title="Quit designer mode"><img src="img/freepik/powerbutton.png" width="32" alt="Quit designer mode"/></a>
                <a href="javascript:void(0)" onclick="$('.grid-elt').toggle()" title="Toggle widgets"><img src="img/freepik/layout7.png" width="32" alt="Toggle widgets"/></a>
            </div>
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
    </body>
</html>