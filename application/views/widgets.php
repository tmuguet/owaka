<!DOCTYPE html>
<html>
    <head>
        <title>owaka</title>
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
        <div id="owaka"><h1>owaka</h1></div>
        <div id="menu">
            <a href="#" onclick="$('.grid-elt').toggle();
                    return false;">toggle widgets</a>
            <ul>
                <li><a href="welcome/main">Home</a></li>
            </ul>
        </div>
        <div id="grid" class="sample"></div>
        <div id="samples">
            <ul>
                <?php foreach ($widgets as $widget): ?>
                    <li class="widget-elt" data-widget="<?php echo $widget['widget']; ?>" data-width="<?php echo $widget['size'][0]; ?>" data-height="<?php echo $widget['size'][1]; ?>"><?php echo $widget['widget']; ?></li>

                <?php /* $result = Request::factory('w/' . $widget . '/sample')->execute();
                      if ($result->status() == 200) {
                      echo $result->body();
                      } */endforeach; ?>
            </ul>
        </div>
        <div id="samples_details">
            Name: <span id="samples_name"></span><br/>
            Size: <select id="samples_size"></select><br/>
            <hr/>
            <span class="drag">Drag me!</span>
            <hr/>
            <a href="javascript:void(0)" id="samples_hide">Back</a>
        </div>
        <script type="text/javascript">
                var c = 1;
                var positions = {};
                var max_row = 8;
                var max_column = 16;

                for (var _row = 0; _row < max_row; _row++) {
                    positions[_row] = {};
                    for (var _column = 0; _column < max_column; _column++) {
                        positions[_row][_column] = true;
                    }
                }

                function isAvailable(from_row, from_column, width, height) {
                    for (var _row = 0; _row < height && _row + from_row < max_row; _row++) {
                        for (var _column = 0; _column < width && _column + from_column < max_column; _column++) {
                            if (!positions[_row + from_row][_column + from_column]) {
                                return false;
                            }
                        }
                    }
                    return true;
                }

                function updateGridPlaceholders(width, height) {
                    $("#grid .grid-placeholder").remove();
                    var gridWidth = width;
                    var gridHeight = height;
                    if (gridWidth == 0) {
                        gridWidth = 1;
                    }
                    if (gridHeight == 0) {
                        gridHeight = 1;
                    }
                    for (var _row = 0; _row < max_row; _row += gridHeight) {
                        for (var _column = 0; _column < max_column; _column += gridWidth) {
                            if (isAvailable(_row, _column, gridWidth, gridHeight)) {
                                var content = '<div id="placeholder_' + _row + '_' + _column + '" class="grid-elt grid-placeholder" data-grid-width="' + gridWidth + '" data-grid-height="' + gridHeight + '" data-grid-column="' + _column + '" data-grid-row="' + _row + '"></div>';
                                $("#grid").append(content);
                                computeElements();
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
                            var size = $("#samples_size").val().split("_");
                            var width = parseInt(size[0]);
                            var height = parseInt(size[1]);

                            $.get('w/' + ui.draggable.attr("data-widget") + '/sample/' + c, function(data) {
                                var o = $(data).attr({
                                    "data-grid-row": row,
                                    "data-grid-column": column,
                                    "data-grid-width": width,
                                    "data-grid-height": height
                                });
                                for (var _row = 0; _row < height; _row++) {
                                    for (var _col = 0; _col < width; _col++) {
                                        positions[_row+row][_col+column] = false;
                                    }
                                }
                                $("#grid .grid-placeholder").remove();
                                $('#grid').append(o);
                                c++;
                                computeElements();
                            });
                        }
                    });
                }

                $(document).ready(function() {
                    $("#samples_hide").click(function() {
                        $("#samples").show('slide', {direction: 'left'}, 500);
                        $("#samples_details").hide('slide', {direction: 'right'}, 500);
                        $("#grid .grid-placeholder").remove();
                    });
                    $("#samples .widget-elt").click(function() {
                        var o = $(this);
                        $.get('widgets/info/' + $(this).attr("data-widget"), function(data) {
                            $("#samples").hide("slide", {direction: 'left'}, 500);
                            $("#samples_details").show('slide', {direction: 'right'}, 500);
                            $("#samples_name").html(o.attr("data-widget"));

                            var options = '';
                            $.each(data.availableSizes, function(i) {
                                options += '<option value="' + data.availableSizes[i][0] + '_' + data.availableSizes[i][1] + '">' + data.availableSizes[i][0] + '*' + data.availableSizes[i][1] + '</option>';
                            });
                            $("#samples_size").html(options);
                            $("#samples_size").val(data.size[0] + '_' + data.size[1]);
                            updateGridPlaceholders(data.size[0], data.size[1]);
                            $("#samples_details .drag").attr("data-widget", o.attr("data-widget"));
                            console.log(JSON.stringify(data));
                        }, "json");
                    });
                    $("#samples_details .drag").draggable({
                        appendTo: "body",
                        helper: "clone"
                    });
                    $("#samples_size").change(function() {
                        var size = $("#samples_size").val().split("_");
                        var width = parseInt(size[0]);
                        var height = parseInt(size[1]);
                        updateGridPlaceholders(width, height);
                    });
                });
        </script>
    </body>
</html>