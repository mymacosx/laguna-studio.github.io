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
    case 'new':
        SX::object('AdminBanned')->add(Arr::getRequest('User_id'), Arr::getRequest('Name'), Arr::getRequest('Email'), Arr::getRequest('Ip'), Arr::getRequest('Reson'), Arr::getRequest('TimeStart'), Arr::getRequest('TimeEnd'), Arr::getRequest('edit'));
        break;

    case 'del':
        SX::object('AdminBanned')->delete(Arr::getRequest('id'));
        break;

    case 'aktiv':
        SX::object('AdminBanned')->aktive(Arr::getRequest('id'), Arr::getRequest('type'));
        break;

    case 'show':
        SX::object('AdminBanned')->get(Arr::getRequest('id'));
        break;

    default:
        SX::object('AdminBanned')->show();
        break;
}
