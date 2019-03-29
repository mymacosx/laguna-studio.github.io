$(document).ready(function() { // Пишем позицию каретки
    $('#msgform').on('click select keyup', function() {
        saveCaret();
    });
});

function toggleSmiles(click, target) { // Меню выбора смайлов
    $(document).ready(function() {
        var timer = 0;
        $('#' + target).slideToggle(300);
        $(document).on('click', function(e) {
            if ($(e.target).closest('#' + click + ', #' + target).length === 0) {
                setCaret(0);
                $('#' + target).slideUp(300);
                e.stopPropagation();
            }
        });
        $('#' + target).on('click', function() {
            setCaret(0);
            $('#' + target).slideUp(300);
        });
        $('#' + click + ', #' + target).mouseover(function() {
            clearTimeout(timer);
        }).mouseout(function() {
            setCaret(0);
            clearTimeout(timer);
            timer = setTimeout(function() {
                $('#' + target).slideUp(300);
            }, 1000);
        });
    });
}

var BBcode   = new Array();
BBcode.array = new Array(); // Хранятся открытые теги
BBcode.caret = 0;           // Хранится позиция каретки
BBcode.elem  = 'msgform';   // Идентификатор формы

function saveCaret() { // Получаем позицию каретки и сохраняем
    var elem = document.getElementById(BBcode.elem);
    if (document.selection) {
        var select = document.selection.createRange();
        var duplic = select.duplicate();
        select.collapse(true);
        duplic.moveToElementText(elem);
        duplic.setEndPoint('EndToEnd', select);
        BBcode.caret = duplic.text.length;
    } else if (elem.selectionStart || elem.selectionStart === '0') {
        BBcode.caret = elem.selectionStart;
    } else {
        BBcode.caret = elem.value.length;
    }
}
function setCaret(pos, set) { // Задаем позицию установки каретки
    if (set === true) {
        BBcode.caret = 0;
    }
    BBcode.caret += pos;
    var elem = document.getElementById(BBcode.elem);
    if (elem.setSelectionRange) {
        elem.focus();
        elem.setSelectionRange(BBcode.caret, BBcode.caret);
    } else if (elem.createTextRange) {
        var range = elem.createTextRange();
        range.collapse(true);
        range.moveEnd('character', BBcode.caret);
        range.moveStart('character', BBcode.caret);
        range.select();
    }
    elem.focus();
}
function loadCaret(load) { // Добавляем в позицию каретки
    var elem = document.getElementById(BBcode.elem);
    var length = elem.value.length;
    var before = elem.value.substring(0, BBcode.caret);
    var ending = elem.value.substring(BBcode.caret, length);
    elem.value = before + load + ending;
}
function isSelect(open, close) { // При наличии выделения окружаем тегами
    var select = '';
    var elem = document.getElementById(BBcode.elem);
    if (document.selection) {
        var select = document.selection.createRange().text;
        if (select.length > 0) {
            document.selection.createRange().text = open + select + close;
            setCaret(open.length);
        }
    } else if (elem.selectionStart) {
        var end = elem.selectionEnd;
        var start = elem.selectionStart;
        var length = elem.textLength;
        var before = elem.value.substring(0, start);
        var select = elem.value.substring(start, end);
        var ending = elem.value.substring(end, length);
        if (select.length > 0) {
            elem.value = before + open + select + close + ending;
            setCaret(open.length);
        }
    }
    return select.length > 0 ? true : false;
}
function loadSelect(open, close) { // Окружаем тегами выделенный текст
    if (isSelect(open, close) === false) {
        loadCaret(open + close);
        setCaret(open.length);
    }
}
function loadCode(code) { // Вставляем теги
    loadCaret(code);
    setCaret(code.length);
}
function closeCode(code) { // Закрываем тег
    if (BBcode.array.length > 0) {
        for (var key in BBcode.array) {
            if (BBcode.array[key] == code) {
                delete BBcode.array[key];
                loadCode('[/' + code + '] ');
                return true;
            }
        }
    }
    return false;
}
function closeCodes() { // Закрываем все открытые теги
    var ende = '';
    var elem = document.getElementById(BBcode.elem);
    while (BBcode.array[0]) {
        ende = ' ';
        elem.value += '[/' + BBcode.array.pop() + ']';
    }
    elem.value += ende;
    setCaret(elem.value.length + 1, true);
}
function addCode(code) { // Основной метод работы с тегами
    code = code.toLowerCase();
    switch (true) {
        case closeCode(code):                               // Если тег открыт закрываем
            break;
        case isSelect('[' + code + ']', '[/' + code + ']'): // Если есть выделение окружаем тегами
            break;
        default:
            switch (code) {
                case 'list':
                    addList();
                    break;
                case 'img':
                    addImg();
                    break;
                case 'url':
                    addUrl();
                    break;
                case 'hide':
                    addHide();
                    break;
                case 'email':
                case 'mail':
                    addMail();
                    break;
                case 'video':
                    addVideo();
                    break;
                default:
                    BBcode.array.push(code);
                    loadCode('[' + code + ']');
            }
            break;
    }
}
function addMail() {
    var mail = prompt('{#Validate_email#}', '');
    if (mail) {
        return loadCode('[mail]' + mail + '[/mail] ');
    }
    alert('{#Format_error_no_email#}');
    return false;
}
function addVideo() {
    var url = prompt('{#Format_text_enter_youtube#}', '');
    if (!url) {
        alert('{#Format_error_no_url#}');
        return false;
    }
    var video = prompt('{#Format_text_enter_youtubeName#}', '');
    if (url.indexOf('youtu.be') !== -1) {
        var param = url.match(/^http[s]*:\/\/youtu\.be\/([a-z0-9_-]+)/i);
    } else {
        var param = url.match(/^http[s]*:\/\/www\.youtube\.com\/watch\?.*?v=([a-z0-9_-]+)/i);
    }
    if (param && param.length === 2) {
        if (video) {
            return loadCode('[youtube:' + video + ']' + param[1] + '[/youtube] ');
        } else {
            return loadCode('[youtube]' + param[1] + '[/youtube] ');
        }
    }
    alert('{#Error#}');
    return false;
}
function addUrl() {
    var error = '';
    var url = prompt('{#Format_enterUrl#}', 'http://');
    var name = prompt('{#Format_enterUrlName#}', '{#Format_enterUrlName2#}');
    if (!url) {
        error += '{#Format_error_no_url#}\n';
    }
    if (!name) {
        error += '{#Format_error_no_title#}\n';
    }
    if (!error) {
        return loadCode('[url=' + url + ']' + name + '[/url] ');
    }
    alert(error);
    return false;
}
function addHide() {
    var error = '';
    var hide = prompt('{#Format_enterHide#}', '{#Format_enterHide2#}');
    var text = prompt('{#Format_enterHideName#}', '{#Format_enterHideName2#}');
    if (!hide) {
        error += '{#Format_error_no_hide#}\n';
    }
    if (!text) {
        error += '{#Format_error_no_title_hide#}\n';
    }
    if (!error) {
        return loadCode('[hide=' + hide + ']' + text + '[/hide] ');
    }
    alert(error);
    return false;
}
function addImg() {
    var url = prompt('{#Format_text_enter_image#}', 'http://');
    if (url) {
        return loadCode('[img]' + url + '[/img] ');
    }
    alert('{#Format_error_no_url#}');
    return false;
}
function addList() {
    var type = prompt('{#Format_listprompt#}', '');
    switch (type) {
        case 'a':
        case 'i':
        case 'A':
        case 'I':
        case '1':
            var list = '[list=' + type + ']\n';
            break;
        default:
            var list = '[list]\n';
            break;
    }
    var entry = '';
    while (true) {
        entry = prompt('{#Format_listprompt2#}', '');
        if (!entry) {
            break;
        }
        list = list + '[*] ' + entry + '\n';
    }
    return loadCode(list + '[/list]\n');
}
function countComments(postmaxchars) {
    var elem = document.getElementById(BBcode.elem);
    var message = '';
    if (postmaxchars > 0) {
        message = '{#Forums_max_Forums_post_length_t#} ' + postmaxchars;
    }
    alert('{#Forums_post_length_t#} ' + elem.value.length + '\n' + message);
}
