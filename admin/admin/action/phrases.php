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
    case 'edit':
        SX::object('AdminPhrases')->edit(Arr::getRequest('id'), Arr::getRequest('Name'), Arr::getRequest('text_edit'));
        break;

    case 'new':
        SX::object('AdminPhrases')->add(Arr::getRequest('Name'), Arr::getRequest('phrase'));
        break;

    case 'del':
        SX::object('AdminPhrases')->delete(Arr::getRequest('id'));
        break;

    case 'aktiv':
        SX::object('AdminPhrases')->aktive(Arr::getRequest('type'), Arr::getRequest('id'));
        break;

    case 'show':
        SX::object('AdminPhrases')->get(Arr::getRequest('id'));
        break;

    default:
        SX::object('AdminPhrases')->show();
        break;
}
