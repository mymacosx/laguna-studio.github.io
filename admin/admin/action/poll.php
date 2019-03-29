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

if (!perm('polls') || !admin_active('poll')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminPoll')->show();
        break;

    case 'edit':
        SX::object('AdminPoll')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminPoll')->add();
        break;

    case 'delete':
        SX::object('AdminPoll')->delete(Arr::getRequest('id'));
        break;

    case 'openclose':
        SX::object('AdminPoll')->active(Arr::getRequest('id'), Arr::getRequest('op'));
        break;

    case 'delstats':
        SX::object('AdminPoll')->clean(Arr::getRequest('id'));
        break;
}
