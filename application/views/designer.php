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
            $_maxId = 0;
            foreach ($widgets as $_widget) {
                echo Request::factory('d/' . $_widget->type . '/' . $from . '/' . $_widget->id)->execute()->body();
                $_maxId = max($_maxId, $_widget->id);
            }
            ?>
        </div>
        <div id="widget_details"></div>
        <div id="list_widgets">
            <ul>
                <?php foreach ($controllers as $_controller): ?>
                    <li class="widget-elt" data-widget="<?php echo $_controller['widget']; ?>" data-width="<?php echo $_controller['size'][0]; ?>" data-height="<?php echo $_controller['size'][1]; ?>"><?php echo $_controller['widget']; ?></li>

                <?php endforeach; ?>
            </ul>
        </div>
        <div id="owaka"><h1>owaka</h1></div>
        <div id="menu">
            <a href="javascrip:void(0)" onclick="$('.grid-elt').toggle();">toggle widgets</a><br>
            <a href="javascript:void(0)" onclick="save()">Save</a>
            <ul>
                <li><a href="welcome/main">Home</a></li>
            </ul>
        </div>
        <script type="text/javascript">
                var c = <?php
                echo ($_maxId + 1);
                ?>;
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
                            $.post('d/' + $("#widget_drag").attr("data-widget") + '/sample/' + c, postData, function(data) {
                                var o = $(data).addClass('widget-added')
                                        .data("params", postData.params)
                                        .data("type", $("#widget_drag").attr("data-widget"));
                                for (var _row = 0; _row < height; _row++) {
                                    for (var _col = 0; _col < width; _col++) {
                                        positions[_row + row][_col + column] = false;
                                    }
                                }
                                $('#grid').append(o);
                                c++;
                                computeElements();
                                $("#widget_hide").trigger('click');
                            });
                        }
                    });
                }

                function deleteMe(o) {
                    var widget = $(o).closest('.grid-elt');
                    var row = parseInt(widget.attr("data-grid-row"));
                    var column = parseInt(widget.attr("data-grid-column"));
                    for (var _row = 0; _row < parseInt(widget.attr("data-grid-height")); _row++) {
                        for (var _col = 0; _col < parseInt(widget.attr("data-grid-width")); _col++) {
                            positions[_row + row][_col + column] = true;
                        }
                    }

                    widget.trigger('mouseleave');
                    if (widget.hasClass('widget-added')) {
                        widget.remove();
                    } else {
                        widget.addClass('widget-deleted');
                    }
                }

                function save() {
                    $.each($('.grid-elt.widget-deleted'), function() {
                        var o = $(this);
                        $.post('api/dashboard/delete/' + $(this).attr("data-widget-id"), function() {
                            o.remove();
                        });
                    });
                    $.each($('.grid-elt.widget-added'), function() {
                        var o = $(this);
                        var postData = {
                            row: o.attr("data-grid-row"),
                            column: o.attr("data-grid-column"),
                            width: o.attr("data-grid-width"),
                            height: o.attr("data-grid-height"),
                            params: o.data("params")
                        };
                        $.post('api/dashboard/add/' + o.data("type"), postData, function() {
                            o.removeClass('widget-added');
                        });
                    });
                }

                $(document).ready(function() {
                    $("#list_widgets, #widget_details").addClass('ui-widget-content');

                    $("#list_widgets .widget-elt").click(function() {
                        var postData = {};
<?php if (isset($projectId)): echo 'postData.projectId = ' . $projectId . ';';
endif; ?>
<?php if (isset($buildId)): echo 'postData.buildId = ' . $buildId . ';';
endif; ?>

                        $("#widget_details").load('designer_details/<?php echo $from; ?>/' + $(this).attr("data-widget"), postData);
                    });
                });
        </script>
    </body>
</html>