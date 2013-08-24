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

        var id = o.attr("id");
        $.owaka.timer_widget[id] = null;

        o.not('.static').hover(
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

        o.filter(".build-unstable").addClass('ui-state-highlight');
        o.filter(".build-error").addClass('ui-state-error');
        o.filter(".build-building").addClass('ui-state-active');
        o.filter(".build-queued").addClass('ui-state-active');

        o.find(".build-unstable").not("a").addClass('ui-state-highlight');
        o.find(".build-error").not("a").addClass('ui-state-error');
        o.find(".build-building").not("a").addClass('ui-state-active');
        o.find(".build-queued").not("a").addClass('ui-state-active');

        o.find("a.build-unstable").addClass('ui-state-highlight-text');
        o.find("a.build-error").addClass('ui-state-error-text');
    },
    computeElements: function() {
        $.each($(".grid-elt").not('.ui-widget-content'), function() {
            $.owaka.computeElement($(this));
        });

        $(".build-unstable").not("a").not("body").addClass('ui-state-highlight');
        $(".build-error").not("a").not("body").addClass('ui-state-error');
        $(".build-building").not("a").not("body").addClass('ui-state-active');
        $(".build-queued").not("a").not("body").addClass('ui-state-active');

        $("a.build-unstable").addClass('ui-state-highlight-text');
        $("a.build-error").addClass('ui-state-error-text');
    },
    refreshElements: function() {
        $.each($(".grid-elt.autorefresh"), function() {
            var o = $(this);
            var id = o.attr("id");
            $.post('w/' + $.owaka.dashboard.from + '/' + o.attr("data-widget-type") + '/display/' + o.attr("data-widget-id"), {}, function(data) {
                var open = o.hasClass('grid-elt-hover');
                o.replaceWith($(data));
                $(document).ready(function() {
                    var o2 = $("#" + id);
                    $.owaka.computeElement(o2);
                    if (open) {
                        o2.addClass('grid-elt-hover');
                        o2.css('width', o2.attr("data-grid-width") * 2 * 80 - 20);
                        o2.find('.widget-detailed').show();
                        $.owaka.timer_widget[id] = null;
                    }
                });
            });
        });
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
    callbacks: {
        init: function(form) {
            var submitbtn = form.find(':submit');
            var icon = submitbtn.attr('data-icon');
            submitbtn.addClass('ui-button-primary');
            submitbtn.after('<span id="' + form.attr('id') + '-helper"></span>');

            submitbtn.button({
                icons: {
                    primary: icon
                }
            });
        },
        before: function(form) {
            form.find(':submit').button('disable');
            $('#' + form.attr('id') + '-helper').html('<i class="icon-spinner icon-spin"></i> Processing...').removeClass('ui-state-error-text');

            $.each(form.serializeArray(), function(idx, o) {
                $("#" + o.name).removeClass('ui-state-error');
                $("label[for=" + o.name + "]").removeClass('ui-state-error-text');
                $("#error_" + o.name).remove();
            });
        },
        apifail: function(form, jqXHR, textStatus, errorThrown) {
            var error = 'An error occurred!';
            switch (jqXHR.status) {
                case 422:
                case 424:
                    var res = $.parseJSON(jqXHR.responseText);
                    if (res.errors) {
                        $.each(res.errors, function(key, o) {
                            $("#" + key).addClass('ui-state-error');
                            $("label[for=" + key + "]").addClass('ui-state-error-text');
                            $("#" + key).after('<span id="error_' + key + '" class="ui-state-error-text details">' + o + '</span>');
                        });
                    }
                    if (res.error) {
                        error = res.error;
                        if (res.details) {
                            error += "<br>Details : " + res.details;
                        }
                    } else {
                        error = 'Some fields are required';
                    }
                    break;

                default:
                    alert('Fail ' + textStatus + ' / ' + errorThrown + ' @ ' + jqXHR.responseText);
            }
            if (form) {
                form.find(':submit').button('enable').removeClass('ui-button-primary').addClass('ui-button-danger');
                $('#' + form.attr('id') + '-helper').html(error).addClass('ui-state-error-text');
            }
        },
    },
    api: function(url, sendData, callback) {
        return $.post(url, sendData, callback, "json").fail(function(jqXHR, textStatus, errorThrown) {
            $.owaka.callbacks.apifail(null, jqXHR, textStatus, errorThrown);
        });
    },
    formapi: function(form, callback) {
        $.owaka.callbacks.init(form);
        form.submit(function() {
            $.owaka.callbacks.before(form);
            $.post(form.attr('action'), form.serialize(), function(data) {
                $('#' + form.attr('id') + '-helper').html('');
                callback(data);
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                $.owaka.callbacks.apifail(form, jqXHR, textStatus, errorThrown);
            });
            return false;
        });
    }
};
$(document).ready(function() {
    $.owaka.computeElements();
    $.owaka.renderForms();
});