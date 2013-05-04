</div>
</div>
<script type="text/javascript">
    var timer_widget_<?php echo $id; ?> = null;

    function open_widget_<?php echo $id; ?>() {
        var o = $("#widget_<?php echo $id; ?>");
        var position = o.position();
        o.css('top', position.top);
        o.css('left', position.left);
        o.addClass('grid-hover');

        o.addClass('grid_<?php echo $width * 2; ?>', 200);
        o.find('.widget-detailed').fadeIn(200);
        timer_widget_<?php echo $id; ?> = null;
    }
    function close_widget_<?php echo $id; ?>() {
        var o = $("#widget_<?php echo $id; ?>");
        o.css('top', '');
        o.css('left', '');
        o.find('.widget-detailed').fadeOut(100);
        o.removeClass("grid_<?php echo ($width * 2); ?>", 100, function() {
            o.removeClass('grid-hover');
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