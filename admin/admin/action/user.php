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

if (!perm('users')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'showusers':
        SX::object('AdminUsers')->show();
        break;

    case 'settings':
        SX::object('AdminUsers')->settings();
        break;

    case 'edituser':
        SX::object('AdminUsers')->edit(Arr::getRequest('user'));
        break;

    case 'openclose':
        SX::object('AdminUsers')->active(Arr::getRequest('openclose'), Arr::getRequest('user'));
        break;

    case 'checkuserdata':
        $_REQUEST['noout'] = 1;
        SX::object('AdminUsers')->сheck(Arr::getRequest('ext'));
        break;

    case 'convertguesttouser':
        SX::object('AdminUsers')->convert(Arr::getGet('order'));
        break;

    case 'adduser':
        SX::object('AdminUsers')->add();
        break;
}
