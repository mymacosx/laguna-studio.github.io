<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

$config['debug'] = '0'; // Настройка включает режим отладки, включено - 1, сайт в рабочем режиме - 0
$config['loger'] = '0'; // Режим записи логов, не пишем - 0, пишем в папку temp/logs - 1, пишем по настройкам PHP - 2, шлем на мыло - впишите адрес
$config['cron']  = '1'; // Настройка работы системы по расписанию, подсчитывая хиты - 1, через cron сервера -  0
$config['ssl']   = '0'; // Настройка отвечает за работу SSL, http:// - 0, https:// - 1, http:// и https:// - 2
$config['https'] = array('*:*'); // Включает https только на определенных страницах.
## Например array('shop:callback') включит https только на странице index.php?p=shop&action=callback, * - любое значение. Пример array('shop:*', '*:callback','*:*')

## Настройка отвечает за вывод в браузер ид шаблонов типа <!-- имя/путь шаблона SX CMS --> и <!-- End имя/путь шаблона SX CMS -->
$config['tplcleanid']   = '1';          // Удаляет ид - 1, выводит - 0, при изменении очистка кеша шаблона обязательна

## Отключение сайта, безопасно для работы с базой, сайт работает - 1, сайт отключен - 0
$config['site']['aktiv'] = '1';
$config['site']['time']  = 'Идет обновление системы. Через 30 минут сайт будет снова доступен'; // Информация для посетителей
$config['site']['ip']    = '127.0.0.2'; // IP адрес для которого сайт остается активным, несколько адресов разделяются запятой без пробелов

## Страницы на которых запрещаем использовать магазин
$config['shop'] = array('showforums', 'showforum', 'showtopic', 'forum', 'forums', 'members', 'newpost', 'pn', 'addpost', 'user');

## Страницы на которых запрещаем использовать календарь
$config['kalendar'] = array('shop', 'showforums', 'showforum', 'showtopic', 'forum', 'forums', 'members', 'newpost', 'pn', 'addpost', 'user');

## Выдача тега noindex,nofollow для указанных страниц (запрет индексирования поисковиками)
$config['noindex'][] = 'q=empty';
$config['noindex'][] = 'title_asc';
$config['noindex'][] = 'title_desc';
$config['noindex'][] = 'price_asc';
$config['noindex'][] = 'price_desc';
$config['noindex'][] = 'klick_asc';
$config['noindex'][] = 'klick_desc';
$config['noindex'][] = 'date_asc';
$config['noindex'][] = 'date_desc';
$config['noindex'][] = 'art_asc';
$config['noindex'][] = 'art_desc';
$config['noindex'][] = 'messages';
$config['noindex'][] = 'search';
$config['noindex'][] = 'forum-action';
$config['noindex'][] = 'print.html';
$config['noindex'][] = 'print=1';

error_reporting($config['debug'] != '1' ? 0 : E_ALL);
ini_set('magic_quotes_runtime', 0);
ini_set('magic_quotes_sybase', 0);
ini_set('arg_separator.input', '&amp;');
ini_set('arg_separator.output', '&amp;');
ini_set('url_rewriter.tags', '1');
ini_set('session.use_cookies', 1);            // Храним session id куках
ini_set('session.use_only_cookies', 1);       // Храним session id только в куках
ini_set('session.use_trans_sid', 0);          // Отключаем прозрачную поддержку sid
