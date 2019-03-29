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

if (!perm('faq')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminFaq')->show();
        break;

    case 'delete':
        SX::object('AdminFaq')->delete(Arr::getRequest('id'));
        break;

    case 'edit':
        SX::object('AdminFaq')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminFaq')->add();
        break;

    case 'editcateg':
        SX::object('AdminFaq')->editCateg(Arr::getRequest('id'));
        break;

    case 'addcateg':
        SX::object('AdminFaq')->addCateg();
        break;

    case 'categories':
        SX::object('AdminFaq')->showCategs();
        break;

    case 'delcateg':
        SX::object('AdminFaq')->delCateg(Arr::getRequest('id'));
        break;

    case 'sendfaq':
        SX::object('AdminFaq')->showSend();
        break;

    case 'editsendfaq':
        SX::object('AdminFaq')->editSend(Arr::getRequest('id'));
        break;
}
