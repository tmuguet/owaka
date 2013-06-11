$.owaka = {
    timer_widget: {},
    open_widget: function(id) {
        var o = $("#" + id);
        o.addClass('grid-elt-hover');
        $("#overlay").fadeIn(200);

        o.animate({
            width: o.attr("data-grid-width") * 2 * 80 - 20
        }, 300);
        o.find('.widget-detailed').delay(200).fadeIn(200);
        $.owaka.timer_widget[id] = null;
    },
    close_widget: function(id) {
        var o = $("#" + id);
        o.find('.widget-detailed').fadeOut(100);
        o.animate({
            width: o.attr("data-grid-width") * 80 - 20
        }, 100, function() {
            $("#overlay").fadeOut(100);
            o.removeClass('grid-elt-hover');
        });
    },
    computeElement: function(o) {
        o.addClass('ui-widget-content');
        o.css("width", o.attr("data-grid-width") * 80 - 20);
        o.css("height", o.attr("data-grid-height") * 80 - 20);
        o.css("top", o.attr("data-grid-row") * 80);
        o.css("left", o.attr("data-grid-column") * 80);
    },
    computeElements: function() {
        $.each($(".grid-elt").not('.ui-widget-content'), function() {
            var id = $(this).attr("id");
            $.owaka.computeElement($(this));

            $.owaka.timer_widget[id] = null;

            $(this).not('.static').hover(
                    function() {
                        $.owaka.timer_widget[id] = setTimeout('$.owaka.open_widget("' + id + '")', 400);
                    },
                    function() {
                        if ($.owaka.timer_widget[id] == null) {
                            $.owaka.close_widget(id);
                        } else {
                            clearTimeout($.owaka.timer_widget[id]);
                            $.owaka.timer_widget[id] = null;
                        }
                    }
            );
        });

        $(".build-unstable").addClass('ui-state-highlight');
        $(".build-error").addClass('ui-state-error');
        $(".build-building").addClass('ui-state-active');
        $(".build-queued").addClass('ui-state-active');
    }
};
$(document).ready(function() {
    $.owaka.computeElements();
});