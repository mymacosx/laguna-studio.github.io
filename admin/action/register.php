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

if (!get_active('Register')) {
    SX::object('Core')->notActive();
}
if (Arr::getRequest('sub') == 'ok') {
    SX::object('Login')->success();
} else {
    switch (Arr::getRequest('do')) {
        default:
        case 'register':
            SX::object('Login')->register();
            break;

        case 'checkuserdata':
            SX::setDefine('AJAX_OUTPUT', 1);
            SX::object('Login')->сheck();
            break;

        case 'activate':
            SX::object('Login')->activate();
            break;
    }
}
