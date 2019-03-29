var toggleElements_animating = false;
(function($) {
    jQuery.fn.toggleElements = function(a) {
        a = jQuery.extend({fxAnimation: "slide", fxSpeed: "normal", className: "toggler", removeTitle: true, showTitle: false, onClick: null, onHide: null, onShow: null}, a);
        var b = a.onClick, onHide = a.onHide, onShow = a.onShow;
        if ((a.fxAnimation != 'slide') && (a.fxAnimation != 'show') && (a.fxAnimation != 'fade'))
            a.fxAnimation = 'slide';
        this.each(function() {
            if (jQuery(this).attr('class').indexOf("opened") == -1) {
                jQuery(this).hide()
            }
        });
        this.each(function() {
            wtitle = '';
            wlinktext = jQuery(this).attr('title');
            if (a.showTitle == true)
                wtitle = wlinktext;
            if (a.removeTitle == true)
                jQuery(this).attr('title', '');
            if (jQuery(this).attr('class').indexOf("opened") != -1) {
                jQuery(this).before('<a class="' + a.className + ' ' + a.className + '-opened" href="#" title="' + wtitle + '">' + wlinktext + '</a>');
                jQuery(this).addClass(a.className + '-c-opened')
            } else {
                jQuery(this).before('<a class="' + a.className + ' ' + a.className + '-closed" href="#" title="' + wtitle + '">' + wlinktext + '</a>');
                jQuery(this).addClass(a.className + '-c-closed')
            }
            jQuery(this).prev('a.' + a.className).click(function() {
                if (toggleElements_animating)
                    return false;
                thelink = this;
                jQuery(thelink)[0].blur();
                if (thelink.animating || toggleElements_animating)
                    return false;
                toggleElements_animating = true;
                thelink.animating = true;
                if (typeof b == 'function' && b(thelink) === false) {
                    toggleElements_animating = false;
                    thelink.animating = false;
                    return false
                }
                if (jQuery(this).next().css('display') == 'block') {
                    jQuery(this).next().each(function() {
                        if (a.fxAnimation == 'slide')
                            jQuery(this).slideUp(a.fxSpeed, function() {
                                jQuery.toggleElementsHidden(this, a.className, onHide, thelink)
                            });
                        if (a.fxAnimation == 'show')
                            jQuery(this).hide(a.fxSpeed, function() {
                                jQuery.toggleElementsHidden(this, a.className, onHide, thelink)
                            });
                        if (a.fxAnimation == 'fade')
                            jQuery(this).fadeOut(a.fxSpeed, function() {
                                jQuery.toggleElementsHidden(this, a.className, onHide, thelink)
                            })
                    })
                } else {
                    jQuery(this).next().each(function() {
                        if (a.fxAnimation == 'slide')
                            jQuery(this).slideDown(a.fxSpeed, function() {
                                jQuery.toggleElementsShown(this, a.className, onShow, thelink)
                            });
                        if (a.fxAnimation == 'show')
                            jQuery(this).show(a.fxSpeed, function() {
                                jQuery.toggleElementsShown(this, a.className, onShow, thelink)
                            });
                        if (a.fxAnimation == 'fade')
                            jQuery(this).fadeIn(a.fxSpeed, function() {
                                jQuery.toggleElementsShown(this, a.className, onShow, thelink)
                            })
                    })
                }
                return false
            })
        })
    };
    jQuery.toggleElementsHidden = function(a, b, c, d) {
        jQuery(a).prev('a.' + b).removeClass(b + '-opened').addClass(b + '-closed').blur();
        if (typeof c == 'function')
            c(this);
        jQuery(a).removeClass(b + '-c-opened').addClass(b + '-c-closed');
        toggleElements_animating = false;
        d.animating = false
    };
    jQuery.toggleElementsShown = function(a, b, c, d) {
        jQuery(a).prev('a.' + b).removeClass(b + '-closed').addClass(b + '-opened').blur();
        if (typeof c == 'function')
            c(this);
        jQuery(a).removeClass(b + '-c-closed').addClass(b + '-c-opened');
        toggleElements_animating = false;
        d.animating = false
    }
})(jQuery);