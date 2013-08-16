$.owaka.designer = {
    slots: {
        update: function(slotWidth, slotHeight) {
            $("#grid .grid-placeholder").remove();

            if (slotWidth == 0) {
                slotWidth = 1;
            }
            if (slotHeight == 0) {
                slotHeight = 1;
            }
            $("#grid .grid-elt").addClass('ui-state-disabled');
            for (var _row = 0; _row < $.owaka.designer.max_row; _row += slotHeight) {
                for (var _column = 0; _column < $.owaka.designer.max_column; _column += slotWidth) {
                    if ($.owaka.designer.slots.isAvailable(_row, _column, slotWidth, slotHeight)) {
                        var content = '<div id="placeholder_' + _row + '_' + _column + '" style="display: none" class="grid-elt grid-placeholder static" data-grid-width="' + slotWidth + '" data-grid-height="' + slotHeight + '" data-grid-column="' + _column + '" data-grid-row="' + _row + '"></div>';
                        $("#grid").append(content);
                        $.owaka.computeElements();
                        $("#grid .grid-placeholder").fadeIn(500);
                    }
                }
            }
        },
        hide: function() {
            $("#grid .grid-placeholder").fadeOut(200, function() {
                $(this).remove();
            });
            $("#grid .grid-elt").removeClass('ui-state-disabled');
        },
        take: function(row, column, height, width) {
            $.owaka.designer.slots._change(row, column, height, width, false);
        },
        free: function(row, column, height, width) {
            $.owaka.designer.slots._change(row, column, height, width, true);
        },
        isAvailable: function(from_row, from_column, width, height) {
            if (from_row + height > $.owaka.designer.max_row || from_column + width > $.owaka.designer.max_column) {
                return false;
            }

            for (var _row = 0; _row < height; _row++) {
                for (var _column = 0; _column < width; _column++) {
                    if (!$.owaka.designer.slots._available[_row + from_row][_column + from_column]) {
                        return false;
                    }
                }
            }
            return true;
        },
        _change: function(row, column, height, width, available) {
            for (var _row = 0; _row < height; _row++) {
                for (var _column = 0; _column < width; _column++) {
                    $.owaka.designer.slots._available[_row + row][_column + column] = available;
                }
            }
        },
        _resize: function() {
            for (var _row = 0; _row < $.owaka.designer.max_row; _row++) {
                if (typeof $.owaka.designer.slots._available[_row] == 'undefined') {
                    $.owaka.designer.slots._available[_row] = {};
                }
                for (var _column = 0; _column < $.owaka.designer.max_column; _column++) {
                    if (typeof $.owaka.designer.slots._available[_row][_column] == 'undefined') {
                        $.owaka.designer.slots._available[_row][_column] = true;
                    }
                }
            }
        },
        _available: {
        },
        deleteRow: function() {
            $.owaka.designer.max_row -= 2;
            $.owaka.designer.slots._resize();
            $.owaka.designer.widget.prepareToAdd();
        },
        addRow: function() {
            $.owaka.designer.max_row += 2;
            $.owaka.designer.slots._resize();
            $.owaka.designer.widget.prepareToAdd();
        },
        deleteColumn: function() {
            $.owaka.designer.max_column -= 2;
            $.owaka.designer.slots._resize();
            $.owaka.designer.widget.prepareToAdd();
        },
        addColumn: function() {
            $.owaka.designer.max_column += 2;
            $.owaka.designer.slots._resize();
            $.owaka.designer.widget.prepareToAdd();
        }
    },
    widget: {
        prepareToAdd: function() {
            var size = $("#widget_size").val().split("_");
            var width = parseInt(size[0]);
            var height = parseInt(size[1]);
            $.owaka.designer.slots.update(width, height);

            if ($("#grid .grid-placeholder").size() > 0) {
                $("#widget_drag").removeClass('ui-state-disabled');

                $("#grid .grid-placeholder").droppable({
                    activeClass: "ui-state-default",
                    hoverClass: "ui-state-hover",
                    accept: ":not(.ui-sortable-helper)",
                    drop: function(event, ui) {
                        var row = parseInt($(this).attr("data-grid-row"));
                        var column = parseInt($(this).attr("data-grid-column"));

                        $("#widget_hide").trigger('click');
                        $.owaka.designer.slots.hide();
                        $.owaka.designer.widget._generatePlaceholder('adding', row, column, height, width);

                        var postData = {
                            row: row,
                            column: column,
                            width: width,
                            height: height,
                            params: $("#widget_drag").data("params")
                        };
                        $.owaka.api('api/dashboard/add/' + $.owaka.designer.from + '/' + $("#widget_drag").attr("data-widget") + $.owaka.designer.data, postData, function(info) {
                            $.post('w/' + $.owaka.designer.from + '/' + $("#widget_drag").attr("data-widget") + '/sample/' + info.widget, {}, function(data) {
                                var o = $(data);
                                $.owaka.designer.slots.take(row, column, height, width);
                                $('#grid').append(o);
                                $.owaka.computeElements();
                                $("#placeholder-adding").remove();
                                $.owaka.designer.widget._draggable(o.find(".widget-move"));
                            });
                        });
                    }
                });
            } else {
                $("#widget_drag").addClass('ui-state-disabled');
            }
        },
        prepareToMove: function(o) {
            var width = parseInt(o.attr("data-grid-width"));
            var height = parseInt(o.attr("data-grid-height"));
            $.owaka.designer.slots.update(width, height);

            $("#grid .grid-placeholder").droppable({
                activeClass: "ui-state-default",
                hoverClass: "ui-state-hover",
                accept: ":not(.ui-sortable-helper)",
                drop: function(event, ui) {
                    var row = parseInt($(this).attr("data-grid-row"));
                    var column = parseInt($(this).attr("data-grid-column"));
                    var oldRow = parseInt(o.attr("data-grid-row"));
                    var oldColumn = parseInt(o.attr("data-grid-column"));
                    var width = parseInt(o.attr("data-grid-width"));
                    var height = parseInt(o.attr("data-grid-height"));

                    if (width == 0) {
                        width = 1;
                    }
                    if (height == 0) {
                        height = 1;
                    }

                    $.owaka.designer.slots.hide();
                    $.owaka.designer.widget._generatePlaceholder('moving', row, column, height, width);
                    var postData = {
                        row: row,
                        column: column
                    };
                    $.owaka.api('api/dashboard/move/' + $.owaka.designer.from + '/' + o.attr("data-widget-id"), postData, function(info) {
                        $.owaka.designer.slots.free(oldRow, oldColumn, height, width);
                        $.owaka.designer.slots.take(row, column, height, width);
                        o.attr("data-grid-row", row);
                        o.attr("data-grid-column", column);
                        $("#placeholder-moving").remove();
                        $.owaka.computeElement(o);
                    });
                }
            });
        },
        remove: function(o) {
            var widget = $(o).closest('.grid-elt');
            var row = parseInt(widget.attr("data-grid-row"));
            var column = parseInt(widget.attr("data-grid-column"));
            var height = parseInt(widget.attr("data-grid-height"));
            var width = parseInt(widget.attr("data-grid-width"));

            widget.trigger('mouseleave');
            $.owaka.designer.widget._generatePlaceholder('deleting', row, column, height, width);
            $.owaka.designer.slots.free(row, column, height, width);

            $.owaka.api('api/dashboard/delete/' + $.owaka.designer.from + '/' + widget.attr("data-widget-id"), function() {
                widget.remove();
                $("#placeholder-deleting").remove();
            });
        },
        _generatePlaceholder: function(id, row, column, height, width) {
            var placeholder = '<div id="placeholder-' + id + '" class="widget-placeholder static" data-grid-width=' + width + ' data-grid-height="' + height + '" data-grid-column="' + column + '" data-grid-row="' + row + '">Loading widget...</div>';
            $("#grid").append(placeholder);
            $.owaka.computeElement($('#placeholder-' + id));
        },
        _cancel: function() {
            if ($("#list_widgets:hidden").size() > 0) {
                $("#list_widgets").show('slide', {direction: 'left'}, 500);
                $("#widget_details").fadeOut(500);
            }
        },
        _draggable: function(o) {
            o.draggable({
                appendTo: "body",
                helper: "clone",
                start: function(event, ui) {
                    $.owaka.designer.widget.prepareToMove($(this).closest('.grid-elt'));
                },
                stop: function(event, ui) {
                    $.owaka.designer.slots.hide();
                }
            });
        },
    },
    max_row: 8,
    max_column: 16,
    from: 'main',
    data: '',
};


$(document).ready(function() {
    $.owaka.designer.widget._draggable($(".widget-move"));
});