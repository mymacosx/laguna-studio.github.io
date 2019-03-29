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
error_reporting(0);
define('SX_DIR', realpath(dirname(dirname(__FILE__))));
require_once SX_DIR . '/class/class.SX.php'; // Подключаем основной класс системы
SX::preload('user');                         // Инициализируем систему

$_REQUEST['action'] = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($_REQUEST['action']) {
    case 'validate':
        SX::object('Captcha')->validate();
        break;

    case 'reload':
        SX::object('Captcha')->ajax();
        break;

    default:
        SX::object('Captcha')->load();
        break;
}
