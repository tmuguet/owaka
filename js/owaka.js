$(document).ready(function() {
    $(window).trigger('resize');

    $(".container").addClass('ui-widget');
    $(".grid_1, .grid_2, .grid_3, .grid_4").addClass('ui-widget-content');
    
    $(".build-ok").addClass('ui-state-focus');
    $(".build-unstable").addClass('ui-state-highlight');
    $(".build-error").addClass('ui-state-error');
    $(".build-building").addClass('ui-state-active');
    $(".build-queued").addClass('ui-state-active');
});
$(window).resize(function() {
    $(".container").height($(".container").width());
    console.log($(".container").width());
/*    var fontsize = $(".container").width()/12.00;
    $(".container").css("font-size", fontsize+"%");*/
});