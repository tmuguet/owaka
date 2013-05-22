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
                    var positions = {};
                    var max_row = 8;
                    var max_column = 16;

                    for (var _row = 0; _row < max_row; _row++) {
                        positions[_row] = {};
                        for (var _column = 0; _column < max_column; _column++) {
                            positions[_row][_column] = true;
                        }
                    }
<?php
foreach ($widgets as $_widget) {
    for ($_row = 0; $_row < $_widget->height; $_row++) {
        for ($_col = 0; $_col < $_widget->width; $_col++) {
            echo 'positions[' . ($_row + $_widget->row) . '][' . ($_col + $_widget->column) . '] = false;';
        }
    }
}
?>
                    function isAvailable(from_row, from_column, width, height) {
                        if (from_row + height > max_row || from_column + width > max_column) {
                            return false;
                        }

                        for (var _row = 0; _row < height; _row++) {
                            for (var _column = 0; _column < width; _column++) {
                                if (!positions[_row + from_row][_column + from_column]) {
                                    return false;
                                }
                            }
                        }
                        return true;
                    }

                    function updateGridPlaceholders() {
                        $("#grid .grid-placeholder").remove();
                        var size = $("#widget_size").val().split("_");
                        var gridWidth = parseInt(size[0]);
                        var gridHeight = parseInt(size[1]);

                        if (gridWidth == 0) {
                            gridWidth = 1;
                        }
                        if (gridHeight == 0) {
                            gridHeight = 1;
                        }
                        for (var _row = 0; _row < max_row; _row += gridHeight) {
                            for (var _column = 0; _column < max_column; _column += gridWidth) {
                                if (isAvailable(_row, _column, gridWidth, gridHeight)) {
                                    var content = '<div id="placeholder_' + _row + '_' + _column + '" style="display: none" class="grid-elt grid-placeholder" data-grid-width="' + gridWidth + '" data-grid-height="' + gridHeight + '" data-grid-column="' + _column + '" data-grid-row="' + _row + '"></div>';
                                    $("#grid").append(content);
                                    computeElements();
                                    $("#grid .grid-placeholder").fadeIn(500);
                                }
                            }
                        }
                        $("#grid .grid-placeholder").droppable({
                            activeClass: "ui-state-default",
                            hoverClass: "ui-state-hover",
                            accept: ":not(.ui-sortable-helper)",
                            drop: function(event, ui) {
                                var row = parseInt($(this).attr("data-grid-row"));
                                var column = parseInt($(this).attr("data-grid-column"));
                                var size = $("#widget_size").val().split("_");
                                var width = parseInt(size[0]);
                                var height = parseInt(size[1]);

                                if (width == 0) {
                                    width = 1;
                                }
                                if (height == 0) {
                                    height = 1;
                                }
                                var postData = {
                                    row: row,
                                    column: column,
                                    width: width,
                                    height: height,
                                    params: $("#widget_drag").data("params")
                                };
                                
                                $("#widget_hide").trigger('click');
                                
                                var placeholder = "<div id=\"placeholder-adding\" class=\"widget-placeholder\" data-grid-width=\"" + width + "\" data-grid-height=\""+ height + "\" data-grid-column=\"" + column + "\" data-grid-row=\"" + row + "\">Loading widget...</div>";
                                $("#grid").append(placeholder);
                                computeElement($("#placeholder-adding"));
                                
                                $.post('api/dashboard/add/<?php echo $from; ?>/' + $("#widget_drag").attr("data-widget") + '<?php if ($from != 'main') {echo '/' . $projectId;} ?>', postData, function(info) {
                                    $.post('w/<?php echo $from; ?>/' + $("#widget_drag").attr("data-widget") + '/sample/' + info.id, {}, function(data) {
                                        var o = $(data);
                                        for (var _row = 0; _row < height; _row++) {
                                            for (var _col = 0; _col < width; _col++) {
                                                positions[_row + row][_col + column] = false;
                                            }
                                        }
                                        $('#grid').append(o);
                                        computeElements();
                                        $("#placeholder-adding").remove();
                                    });
                                }, "json");
                            }
                        });
                    }

                    function deleteMe(o) {
                        var widget = $(o).closest('.grid-elt');
                        var row = parseInt(widget.attr("data-grid-row"));
                        var column = parseInt(widget.attr("data-grid-column"));
                        var height = parseInt(widget.attr("data-grid-height"));
                        var width = parseInt(widget.attr("data-grid-width"));
                        
                        widget.trigger('mouseleave');
                        var placeholder = "<div id=\"placeholder-deleting\" class=\"widget-placeholder\" data-grid-width=\"" + width + "\" data-grid-height=\""+ height + "\" data-grid-column=\"" + column + "\" data-grid-row=\"" + row + "\">Deleting widget...</div>";
                        $("#grid").append(placeholder);
                        computeElement($("#placeholder-deleting"));
                        
                        for (var _row = 0; _row < height; _row++) {
                            for (var _col = 0; _col < width; _col++) {
                                positions[_row + row][_col + column] = true;
                            }
                        }

                        $.post('api/dashboard/delete/<?php echo $from; ?>/' + widget.attr("data-widget-id") + '<?php if ($from != 'main') {echo '/' . $projectId; } ?>', function() {
                            widget.remove();
                            $("#placeholder-deleting").remove();
                        });
                    }

                    $(document).ready(function() {
                        $("#list_widgets .widget-elt a").click(function() {
                            var postData = {};
<?php if (isset($projectId)) {echo 'postData.projectId = ' . $projectId . ';';} ?>

                            $("#widget_details").load('designer_details/<?php echo $from; ?>/' + $(this).attr("data-widget"), postData);
                        });
                    });
        </script>
    </body>
</html>