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

if (!perm('stats')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'search':
        SX::object('AdminStats')->showSearchs();
        break;

    case 'search_export':
        SX::object('AdminStats')->exportSearchs();
        break;

    case 'delsearch':
        SX::object('AdminStats')->deleteSearchs(Arr::getRequest('id'));
        break;

    case 'allsearchdel':
        SX::object('AdminStats')->cleanSearchs();
        break;

    case 'autorize':
        SX::object('AdminStats')->showLogins();
        break;

    case 'delautorize':
        SX::object('AdminStats')->deleteLogins(Arr::getRequest('id'));
        break;

    case 'autorizedelall':
        SX::object('AdminStats')->cleanLogins();
        break;

    case 'overview':
        SX::object('AdminStats')->showChart();
        break;

    case 'referer':
        SX::object('AdminStats')->showReferer();
        break;

    case 'user_map':
        SX::object('AdminStats')->userMaps();
        break;

    case 'export_search':
        SX::object('AdminStats')->exportSearch();
        break;
}
