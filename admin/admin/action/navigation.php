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

if (!perm('navigation_edit')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'list':
        SX::object('AdminNavi')->showMenu();
        break;

    case 'speedbar':
        SX::object('AdminNavi')->speedbar();
        break;

    case 'edit':
        SX::object('AdminNavi')->editMenu(Arr::getRequest('id'));
        break;

    case 'editnavidoc':
        SX::object('AdminNavi')->editResource(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('AdminNavi')->deleteResource(Arr::getRequest('id'), Arr::getRequest('navi'));
        break;

    case 'newnaviitem':
        SX::object('AdminNavi')->addResource(Arr::getRequest('id'));
        break;

    case 'deletenavi':
        SX::object('AdminNavi')->deleteMenu(Arr::getRequest('id'));
        break;

    case 'edit_ft':
        SX::object('AdminNavi')->editFlashtag(Arr::getRequest('id'), Arr::getRequest('title_edit'), Arr::getRequest('size_edit'), Arr::getRequest('url_edit'));
        break;

    case 'new_ft':
        SX::object('AdminNavi')->addFlashtag(Arr::getRequest('title_add'), Arr::getRequest('size_add'), Arr::getRequest('url_add'));
        break;

    case 'del_ft':
        SX::object('AdminNavi')->deleteFlashtag(Arr::getRequest('id'));
        break;

    case 'delall_ft':
        SX::object('AdminNavi')->cleanFlashtag();
        break;

    case 'aktiv_ft':
        SX::object('AdminNavi')->activeFlashtag(Arr::getRequest('type'), Arr::getRequest('id'));
        break;

    case 'edit_show_ft':
        SX::object('AdminNavi')->getFlashtag(Arr::getRequest('id'));
        break;

    case 'flashtag':
        SX::object('AdminNavi')->showFlashtag();
        break;
}
