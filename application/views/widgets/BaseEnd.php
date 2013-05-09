</div>
</div>

<script type="text/javascript">
    var timer_widget_<?php echo $id; ?> = null;

    function open_widget_<?php echo $id; ?>() {
        var o = $("#widget_<?php echo $id; ?>");
        o.addClass('grid-elt-hover');
        $("#overlay").fadeIn(200);

        o.animate({
            width: o.attr("data-grid-width")*2*80-20
        }, 300);
        o.find('.widget-detailed').delay(200).fadeIn(200);
        timer_widget_<?php echo $id; ?> = null;
    }
    function close_widget_<?php echo $id; ?>() {
        var o = $("#widget_<?php echo $id; ?>");
        o.find('.widget-detailed').fadeOut(100);
        o.animate({
            width: o.attr("data-grid-width")*80-20
        }, 100, function() {
            $("#overlay").fadeOut(100);
            o.removeClass('grid-elt-hover');
        });
    }

    $("#widget_<?php echo $id; ?>").hover(
            function() {
                timer_widget_<?php echo $id; ?> = setTimeout('open_widget_<?php echo $id; ?>()', 400);
            },
            function() {
                if (timer_widget_<?php echo $id; ?> == null) {
                    close_widget_<?php echo $id; ?>();
                } else {
                    clearTimeout(timer_widget_<?php echo $id; ?>);
                    timer_widget_<?php echo $id; ?> = null;
                }
            }
    );
</script>