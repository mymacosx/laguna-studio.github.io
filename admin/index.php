<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
error_reporting(-1);                                                    // Вывод ошибок php: 0 - выключено, E_ALL - включено
define('SX_DIR', realpath(dirname(__FILE__)));                         // Устанавливаем полный путь до скрипта
require_once SX_DIR . '/class/class.SX.php';                           // Подключаем основной класс системы
SX::preload('user');                                                   // Инициализируем систему
header('Content-type: text/html; charset=' . CHARSET);                 // Устанавливаем заголовок с текущей кодировкой
$core = SX::object('Core');
$core->ssl();                                                          // Устанавливаем протокол соединения
$core->getSection();                                                   // Проверка и установка в $_REQUEST['area'] и $_SESSION['area'] номера секции
$core->section();                                                      // Подключаем запароленную секцию или подключаем дефолтную секцию
$core->modules();                                                      // Устанавливаем все активные модули
$core->aktiveLangs();                                                  // Получаем список активных языков
$core->selectLangs();                                                  // Переключатель языков на сайте
$core->getLangcode();                                                  // Устанавливаем код языка в $_SESSION['Langcode']
$langs = $core->langSettings();                                        // Получаем настройки текущего языка
SX::setLocale($langs['Sprachcode'], $langs['Locale']);                 // Устанавливаем локаль PHP
$core->control($_SESSION['area']);                                     // Проверяем на запись папку компиляции
$core->template();                                                     // Устанавливаем текущий шаблон
SX::setDefine('THEME', SX_DIR . '/theme/' . SX::get('options.theme')); // Устанавливаем путь к шаблону
SX::loadLang(LANG_DIR . '/' . $_SESSION['lang'] . '/main.txt');        // Загружаем данные из основного ленг файла
SX::loadLang(LANG_DIR . '/' . $_SESSION['lang'] . '/mail.txt');        // Загружаем данные из ленг файла шаблона писем
$core->insert();                                                       // Подгрузка данных свободной вставки значений в шаблон
$core->getModules($langs);                                             // Подключение модулей
$core->extensions();                                                   // Подключение модулей системы

$content = NULL;
if (!defined('AJAX_OUTPUT')) {
    $tpl = '1column.tpl';
    if ($_SESSION['banned'] != 1) {
        $tpl = Arr::getRequest('blanc') == 1 ? 'popup.tpl' : View::get()->template($_REQUEST['p']);
    }
    $content = View::get()->fetch(THEME . '/page/' . $tpl);
    $content = $core->finish($content);
    $content = SX::object('CodeWidget')->get($content);
    if (SX::get('system.use_seo') == 1) {
        $content = SX::object('Rewrite')->get($content);
    }
    if (get_active('highlighter') && in_array($_REQUEST['p'], array('articles', 'news', 'content', 'shop'))) {
        $content = SX::object('Highlight')->get($content);
    }
    $content = $core->cleanup($content);
}
SX::output($content, true);
