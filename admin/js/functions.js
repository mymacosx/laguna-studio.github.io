function focusArea(elem, height) {
    var cache = elem.style.height;
    elem.style.height = height + 'px';
    elem.onblur = function() {
        elem.style.height = cache;
    };
}
function uploadBrowser(typ, target, elemid) {
    var w = 1000;
    var h = 520;
    var left = screen.width ? (screen.width - w) / 2 : 0;
    var top = screen.height ? (screen.height - h) / 2 : 0;
    var url = '?pop=1&do=browser&typ=' + typ + '&target=' + target + '&mode=system&elemid=' + elemid;
    var features = 'scrollbars=no,width=' + w + ',height=' + h + ',top=' + top + ',left=' + left;
    window.open(url, 'mpool', features);
}
function openWindow(seite, name, w, h, scroll) {
    if (typeof w === 'undefined' || w === '') {
        w = screen.width;
    }
    if (typeof h === 'undefined' || h === '') {
        h = screen.height;
    }
    var left = screen.width ? (screen.width - w) / 2 : 0;
    var top = screen.height ? (screen.height - h) / 2 : 0;
    var settings = 'height=' + h + ',width=' + w + ',top=' + top + ',left=' + left + ',scrollbars=' + scroll + ',resizable';
    window.open(seite, name, settings);
}
function printWindow(id) {
    var html = document.getElementById(id).innerHTML;
    html = html.replace(/&lt;/gi, '<');
    html = html.replace(/&gt;/gi, '>');
    var p = window.open('', null, 'height=520,width=780,toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
    var c = '<html><head></head><body style="font-family:arial,verdana;font-size:12px" onload="window.print();">' + html + '</body></html>';
    p.document.write(c);
    p.document.close();
}
function multiCheck() {
    var obj = document.kform;
    for (var i = 0; i < obj.elements.length; i++) {
        var e = obj.elements[i];
        if (e.name !== 'allbox' && e.type === 'checkbox' && !e.disabled) {
            e.checked = obj.allbox.checked;
        }
    }
}
function insertEditor(name, text) {
    var editor = CKEDITOR.instances[name];
    editor.insertHtml(text);
}
function mergeProduct(id, cid, url, blanc) {
    window.open(url + 'index.php?redir=1&p=misc&do=mergeproduct&prodid=' + id + '&cid=' + cid + '&blanc=' + blanc, 'merge_win', 'scrollbars=1,width=950,height=750,top=0,left=0');
}

// Ниже функции с jquery
function togglePanel(id, name, expires, basepath) {
    $(document).ready(function() {
        if ($.cookie(id) === 'hide') {
            $('#' + id).removeClass('opened');
        }
        $('#' + id).toggleElements({
            fxAnimation: 'slide', fxSpeed: 'normal', className: name,
            onClick: function() {
                $.cookie(id, '', { expires: expires, path: basepath });
            },
            onHide: function() {
                $.cookie(id, 'hide', { expires: expires, path: basepath });
            }
        });
    });
}
function toggleContent(click, target) {
    $(document).ready(function() {
        var timer = 0;
        $('#' + target).css({
            'position': 'absolute',
            'left': $('#' + click).offset().left + 'px',
            'top': ($('#' + click).offset().top + $('#' + click).height()) + 'px'
        }).slideToggle(300);
        $(document).on('click', function(e) {
            if ($(e.target).closest('#' + click + ', #' + target).length === 0) {
                $('#' + target).slideUp(300);
                e.stopPropagation();
            }
        });
        $('#' + click + ', #' + target).mouseover(function() {
            clearTimeout(timer);
        }).mouseout(function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                $('#' + target).slideUp(300);
            }, 1000);
        });
    });
}
function toggleCookie(click, target, expires, basepath) {
    $(document).ready(function() {
        if ($.cookie(click) === 'hide') {
            $('#' + target).css('display', 'none');
        }
        $('#' + click).on('click', function() {
            $('#' + target).slideToggle(
                500,
                function() {
                    var display = $('#' + target).css('display') === 'none' ? 'hide' : 'ss';
                    $.cookie(click, display, { expires: expires, path: basepath });
                }
            );
        });
    });
}
function showNotice(text, time, overlay) {
    $(document).ready(function() {
        if (typeof overlay === 'undefined' || overlay === '') {
            overlay = true;
        }
        $.blockUI({ showOverlay: overlay, message: text, css: { cursor: 'pointer' } });
        setTimeout($.unblockUI, time);
    });
}
function toggleSpoiler(elem) {
    $(document).ready(function() {
        $(elem).next().slideToggle();
        $(elem).toggleClass('spoilerheader_open');
    });
}
function closeWindow(reload) {
    $(document).ready(function() {
        parent.$.colorbox.close();
        if (reload === true) {
            parent.location.href = parent.location;
        }
    });
}
function newWindow(url, width, height) {
    $(document).ready(function() {
        $.colorbox({
            href: url,
            width: width + 'px',
            height: height + 'px',
            iframe: true
        });
    });
}
