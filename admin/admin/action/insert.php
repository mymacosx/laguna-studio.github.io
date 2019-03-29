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

if (!perm('settings')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminInsert')->show();
        break;

    case 'edit':
        SX::object('AdminInsert')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminInsert')->add();
        break;
}
