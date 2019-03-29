(function($) {
    $.fn.textcopy = function(option) {
        var o = {
            id: 'textcopy',
            text: 'Источник',
            minlen: 50,
            first: false
        };
        $.extend(o, option);
        var copy = document.createElement('span');
        copy.id = o.id;
        copy.style.position = 'absolute';
        copy.style.overflow = 'hidden';
        copy.style.margin = '5px 0 0 -1px';
        copy.style.lineHeight = '0';
        copy.style.opacity = '0';
        copy.style.width = '1px';
        copy.style.height = '1px';
        copy.innerHTML = '<br />' + o.text + ': <a href="' + window.location.href + '">' + window.location.href + '</a><br />';
        var action = function() {
            $(this).on({
                mousedown: function() {
                    var elem = document.getElementById(o.id);
                    if (elem) {
                        elem.parentNode.removeChild(elem);
                    }
                },
                mouseup: function() {
                    var tag = document.activeElement.tagName.toLowerCase();
                    if (tag !== 'textarea' && tag !== 'input') {
                        if (window.getSelection) {
                            var select = window.getSelection();
                            var seltxt = select.toString();
                            if (seltxt && seltxt.length >= o.minlen) {
                                var range = select.getRangeAt(0);
                                seltxt = range.cloneRange();
                                seltxt.collapse(o.first);
                                seltxt.insertNode(copy);
                                if (!o.first) {
                                    range.setEndAfter(copy);
                                }
                                select.removeAllRanges();
                                select.addRange(range);
                            }
                        } else if (document.selection) {
                            var select = document.selection;
                            var range = select.createRange();
                            var seltxt = range.text;
                            if (seltxt && seltxt.length >= o.minlen) {
                                seltxt = range.duplicate();
                                seltxt.collapse(o.first);
                                seltxt.pasteHTML(copy.outerHTML);
                                if (!o.first) {
                                    range.setEndPoint('EndToEnd', seltxt);
                                    range.select();
                                }
                            }
                        }
                    }
                }
            });
        };
        return this.each(action);
    };
})(jQuery);
