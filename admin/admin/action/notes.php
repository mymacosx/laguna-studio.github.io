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

if (!perm('notes') || SX::get('admin.Aktiv_Notes') != '1') {
    SX::object('AdminCore')->noAccess();
}

$_REQUEST['noout'] = 1;
switch (Arr::getRequest('sub')) {
    case 'addnotes':
        SX::object('AdminNotes')->add();
        break;

    case 'delnotes':
        SX::object('AdminNotes')->delete(intval(Arr::getRequest('notid')));
        break;

    case 'editnotes':
        SX::object('AdminNotes')->edit(intval(Arr::getRequest('notid')));
        break;

    default:
    case 'shownotes':
        SX::object('AdminNotes')->show();
        break;
}
