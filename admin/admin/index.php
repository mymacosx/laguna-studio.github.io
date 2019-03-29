<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
error_reporting(0);                                                    // Вывод ошибок php: 0 - выключено, E_ALL - включено
define('SX_DIR', realpath(dirname(dirname(__FILE__))));                // Устанавливаем полный путь до скрипта
require_once SX_DIR . '/class/class.SX.php';                           // Подключаем основной класс системы
SX::preload('admin');                                                  // Инициализируем систему
$ACore = SX::object('AdminCore');
$ACore->setSection();                                                  // Устанавливаем секцию
$ACore->checkLogin();                                                  // Проверяем на авторизацию в куках
$ACore->settings();
$ACore->sessionLang();
$ACore->sessionLangcode();
SX::setLocale($_SESSION['admin_lang'], $ACore->getLocale());           // Устанавливаем локаль PHP
$ACore->theme();
$CS = View::get();
SX::loadLang(LANG_DIR . '/' . $_SESSION['admin_lang'] . '/admin.txt'); // Загружаем языковые переменные
SX::loadLang(LANG_DIR . '/' . $_SESSION['admin_lang'] . '/mail.txt');  // Загружаем языковые переменные писем
header('Content-type: text/html; charset=' . CHARSET);                 // Устанавливаем заголовок с текущей кодировкой
$CS->assign('shop_aktiv', SX::get('admin_active.shop'));               // Устанавливаем в шаблоне активен ли магазин
$CS->assign('section_switch', $ACore->switchSection());                // Передаем в шаблон переключатель секций
$CS->assign('theme_switch', $ACore->switchTheme());                    // Передаем в шаблон переключатель шаблонов
$CS->assign('admin_settings', SX::get('admin'));                       // Передаем в шаблон глобальные настройки админ панели
$CS->assign('area', $_SESSION['a_area']);                              // Передаем в шаблон номер секции
$CS->assign('backurl', base64_encode(SX::object('Redir')->link()));    // Передаем в шаблон адрес текущей страницы
$CS->assign('helpquery', $ACore->helpQuery());                         // Передаем в шаблон имя модуля для справки
register_shutdown_function(array('SX', 'sendMail'));                   // Отправка писем из очереди
$ACore->access();                                                      // Проверяем разрешен ли доступ системой бана
$ACore->naviModules();                                                 // Подключаем меню и ленги активных внешних модулей
$ACore->languages();                                                   // Грузим доступные языки
$ACore->logout();                                                      // Выходим из амин панели
$ACore->checkIp();                                                     // Проверяем разрешен ли доступ в админ панель с определенного IP
$ACore->permisson();                                                   // Устанавливаем права доступа
$ACore->extensions();                                                  // Проверка прав и подключение модуля
$load = 'login.tpl';
if (Arr::getSession('loggedin') == 1) {
    $load = Arr::getRequest('noframes') == 1 ? 'noframes.tpl' : 'main.tpl';
    $load = !perm('adminpanel') || (!isset($_SESSION['benutzer_id']) || $_SESSION['benutzer_id'] == 0) ? 'login.tpl' : $load;
}
$out = Arr::getRequest('noout') == 1 ? NULL : $CS->fetch(THEME . '/' . $load);
SX::output($out, true);
