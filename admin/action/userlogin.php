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
if (!get_active('Login')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    case 'newlogin':
        SX::object('Login')->newLogin();
        break;

    case 'logout':
        SX::object('Login')->logout();
        break;

    case 'ajaxlogin':
        SX::setDefine('AJAX_OUTPUT', '1');
        SX::object('Login')->ajaxLogin();
        break;

    case 'login':
        SX::object('Login')->newLogin(1);
        SX::object('Login')->pageLogin();
        break;

    default:
        SX::object('Login')->pageLogin();
        break;
}
