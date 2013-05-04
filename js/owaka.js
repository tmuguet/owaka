$(document).ready(function() {
    //$(window).trigger('resize');

    $(".grid-elt").addClass('ui-widget-content');
    $.each($(".grid-elt"), function() {
        $(this).css("width", $(this).attr("data-grid-width")*80-20);
        $(this).css("height", $(this).attr("data-grid-height")*80-20);
        $(this).css("top", $(this).attr("data-grid-row")*80);
        $(this).css("left", $(this).attr("data-grid-column")*80);
    });
    
    $(".build-ok").addClass('ui-state-focus');
    $(".build-unstable").addClass('ui-state-highlight');
    $(".build-error").addClass('ui-state-error');
    $(".build-building").addClass('ui-state-active');
    $(".build-queued").addClass('ui-state-active');
});
$(window).resize(function() {
    //$(".container").height($(".container").width());
    //console.log($(".container").width());
/*    var fontsize = $(".container").width()/12.00;
    $(".container").css("font-size", fontsize+"%");*/
});