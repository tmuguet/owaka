var timer_widget = {};
$(document).ready(function() {
    //$(window).trigger('resize');

    computeElements();
});
function open_widget(id) {
    var o = $("#" + id);
    o.addClass('grid-elt-hover');
    $("#overlay").fadeIn(200);

    o.animate({
        width: o.attr("data-grid-width") * 2 * 80 - 20
    }, 300);
    o.find('.widget-detailed').delay(200).fadeIn(200);
    timer_widget[id] = null;
}
function close_widget(id) {
    var o = $("#" + id);
    o.find('.widget-detailed').fadeOut(100);
    o.animate({
        width: o.attr("data-grid-width") * 80 - 20
    }, 100, function() {
        $("#overlay").fadeOut(100);
        o.removeClass('grid-elt-hover');
    });
}

function computeElement(o) {
    o.addClass('ui-widget-content');
    o.css("width", o.attr("data-grid-width") * 80 - 20);
    o.css("height", o.attr("data-grid-height") * 80 - 20);
    o.css("top", o.attr("data-grid-row") * 80);
    o.css("left", o.attr("data-grid-column") * 80);
}

function computeElements() {
    $.each($(".grid-elt").not('.ui-widget-content'), function() {
        var id = $(this).attr("id");
        computeElement($(this));

        timer_widget[id] = null;

        $(this).not('.static').hover(
                function() {
                    timer_widget[id] = setTimeout('open_widget("' + id + '")', 400);
                },
                function() {
                    if (timer_widget[id] == null) {
                        close_widget(id);
                    } else {
                        clearTimeout(timer_widget[id]);
                        timer_widget[id] = null;
                    }
                }
        );
    });

    $(".build-ok").addClass('ui-state-focus');
    $(".build-unstable").addClass('ui-state-highlight');
    $(".build-error").addClass('ui-state-error');
    $(".build-building").addClass('ui-state-active');
    $(".build-queued").addClass('ui-state-active');
}
$(window).resize(function() {
    //$(".container").height($(".container").width());
    //console.log($(".container").width());
    /*    var fontsize = $(".container").width()/12.00;
     $(".container").css("font-size", fontsize+"%");*/
});