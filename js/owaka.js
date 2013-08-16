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

        $(".build-unstable").not("a").not("body").addClass('ui-state-highlight');
        $(".build-error").not("a").not("body").addClass('ui-state-error');
        $(".build-building").not("a").not("body").addClass('ui-state-active');
        $(".build-queued").not("a").not("body").addClass('ui-state-active');
        
        $("a.build-unstable").addClass('ui-state-highlight-text');
        $("a.build-error").addClass('ui-state-error-text');
    },
    renderForms: function() {
        if ($(".ui-form").size() > 0) {
            var max = 0;
            $.each($(".ui-form label"), function() {
                if ($(this).width() > max) {
                    max = $(this).width();
                }
            });
            $(".ui-form label").width(max * 1.1);
            $(".ui-form div.details").css('margin-left', max / 2);
        }
    },
    api: function(url, sendData, callback) {
        return $.post(url, sendData, callback, "json").fail(function(jqXHR, textStatus, errorThrown) {
            switch (jqXHR.status) {
                case 422:
                    var res = $.parseJSON(jqXHR.responseText);
                    $.each(res.errors, function(key, o) {
                        $("#" + key).addClass('ui-state-error');
                        $("label[for=" + key + "]").addClass('ui-state-error-text');
                    });
                    break;

                default:
                    alert('Fail ' + textStatus + ' / ' + errorThrown + ' @ ' + jqXHR.responseText);
            }
        });
    },
    formapi: function(form, callback) {
        form.submit(function() {
            form.find(':submit').button('disable');
            $.each(form.serializeArray(), function(idx, o) {
                $("#" + o.name).removeClass('ui-state-error');
                $("label[for=" + o.name + "]").removeClass('ui-state-error-text');
                $("#error_" + o.name).remove();
            });
            $.post(form.attr('action'), form.serialize(), callback, "json").fail(function(jqXHR, textStatus, errorThrown) {
                switch (jqXHR.status) {
                    case 422:
                        var res = $.parseJSON(jqXHR.responseText);
                        if (res.errors) {
                            $.each(res.errors, function(key, o) {
                                $("#" + key).addClass('ui-state-error');
                                $("label[for=" + key + "]").addClass('ui-state-error-text');
                                $("#" + key).after('<span id="error_' + key + '" class="ui-state-error-text details">' + o + '</span>');
                            });
                        }
                        if (res.error) {
                            alert(res.error);
                        }
                        break;

                    default:
                        alert('Fail ' + textStatus + ' / ' + errorThrown + ' @ ' + jqXHR.responseText);
                }
                form.find(':submit').button('enable').addClass('ui-state-error');
            });
            return false;
        });
    }
};
$(document).ready(function() {
    $.owaka.computeElements();
    $.owaka.renderForms();
});