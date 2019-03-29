<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}
error_reporting(0);                                                    // Вывод ошибок php: 0 - выключено, E_ALL - включено
define('SX_DIR', realpath(dirname(dirname(__FILE__))));
require_once SX_DIR . '/class/class.SX.php';                           // Подключаем основной класс системы
SX::preload('user');                                                   // Инициализируем систему
$core = SX::object('Core');
$core->getSection();                                                   // Проверка и установка в $_REQUEST['area'] и $_SESSION['area'] номера секции
$core->section();                                                      // Подключаем запароленную секцию или подключаем дефолтную секцию
$core->modules();                                                      // Устанавливаем все активные модули
$core->aktiveLangs();                                                  // Получаем список активных языков
$core->selectLangs();                                                  // Переключатель языков на сайте
$core->getLangcode();                                                  // Устанавливаем код языка в $_SESSION['Langcode']
$langs = $core->langSettings();                                        // Получаем настройки текущего языка
SX::setLocale($langs['Sprachcode'], $langs['Locale']);                 // Устанавливаем локаль PHP
$core->template();                                                     // Устанавливаем текущий шаблон
SX::setDefine('THEME', SX_DIR . '/theme/' . SX::get('options.theme')); // Устанавливаем путь к шаблону
SX::loadLang(LANG_DIR . '/' . $_SESSION['lang'] . '/main.txt');        // Загружаем данные из основного ленг файла
SX::loadLang(LANG_DIR . '/' . $_SESSION['lang'] . '/mail.txt');        // Загружаем данные из ленг файла шаблона писем
header('Content-type: text/html; charset=' . CHARSET);                 // Устанавливаем заголовок с текущей кодировкой
SX::object('Cron')->get('cron');
SX::output('ok', true);
