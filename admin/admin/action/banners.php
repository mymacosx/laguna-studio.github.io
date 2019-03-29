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

if (!perm('bannerperm')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminBanners')->show();
        break;

    case 'delete':
        SX::object('AdminBanners')->delete(Arr::getRequest('id'));
        break;

    case 'edit':
        SX::object('AdminBanners')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminBanners')->add();
        break;

    case 'categs':
        SX::object('AdminBanners')->showCateg();
        break;

    case 'delcateg':
        SX::object('AdminBanners')->delCateg(Arr::getRequest('id'));
        break;
}
