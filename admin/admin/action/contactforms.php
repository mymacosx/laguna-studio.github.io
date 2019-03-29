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

if (!perm('contact_forms')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminContactform')->show();
        break;

    case 'new':
        SX::object('AdminContactform')->add();
        break;

    case 'edit':
        SX::object('AdminContactform')->edit(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('AdminContactform')->delete(Arr::getRequest('id'));
        break;

    case 'copy':
        SX::object('AdminContactform')->copy(Arr::getRequest('id'));
        break;

    case 'save':
        SX::object('AdminContactform')->save();
        break;
}
