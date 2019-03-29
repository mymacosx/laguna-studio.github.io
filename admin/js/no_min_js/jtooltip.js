(function($) {
    $.fn.tooltip = function(o) {
        var text;
        var defaults = {
            css: 'status',
            id: 'tooltip',
            speed: 400,
            width: 300,
            x: 12,
            y: 12
        };
        var o = $.extend(defaults, o);
        var action = function() {
            $(this).on({
                mouseenter: function(e) {
                    var open = '#' + o.id;
                    text = $(this).attr('title');
                    if (text !== undefined && text.length > 0) {
                        $(this).attr('title', '');
                        if ($('div').is(open) === false) {
                            $('body').append('<div id="' + o.id + '" class="' + o.css + '" style="display:none;"></div>');
                        }
                        $(open).css('width', 'auto').html(text);
                        $(open).css({
                            'max-width': o.width + 'px',
                            'width': $(open).width() + 'px',
                            'left': e.pageX + o.x + 'px',
                            'top': e.pageY + o.y + 'px'
                        }).stop().fadeIn(o.speed);
                    }
                },
                mouseleave: function() {
                    var open = '#' + o.id;
                    if ($(open).is(':visible')) {
                        $(open).stop().fadeOut(o.speed, function() {
                            $(open).empty();
                        });
                        $(this).attr('title', text);
                    }
                    text = '';
                },
                mousemove: function(e) {
                    if ($('#' + o.id).is(':visible')) {
                        var tipX = e.pageX + o.x;
                        var tipY = e.pageY + o.y;
                        var tipWidth = $('#' + o.id).outerWidth(true);
                        var tipHeight = $('#' + o.id).outerHeight(true);
                        if (tipX + tipWidth > $(window).scrollLeft() + $(window).width()) {
                            tipX = e.pageX - tipWidth;
                        }
                        if ($(window).height() + $(window).scrollTop() < tipY + tipHeight) {
                            tipY = e.pageY - tipHeight;
                        }
                        $('#' + o.id).css({
                            'left': tipX + 'px',
                            'top': tipY + 'px'
                        });
                    }
                }
            });
        };
        return this.each(action);
    };
})(jQuery);