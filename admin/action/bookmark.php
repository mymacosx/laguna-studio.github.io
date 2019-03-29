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
if (Arr::getSession('loggedin') != 1 || Arr::getSession('user_group') == 2) {
    SX::object('Core')->noAccess();
}

switch (Arr::getRequest('action')) {
    default:
    case 'add':
        SX::object('Bookmark')->add(Arr::getRequest('document'), Arr::getRequest('document_name'));
        break;

    case 'delete':
        SX::object('Bookmark')->delete();
        break;
}
