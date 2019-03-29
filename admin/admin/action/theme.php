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

if (!perm('templates')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'show_all_tpl':
        SX::object('AdminThemes')->loadTpl();
        break;

    case 'show_tpl':
        SX::object('AdminThemes')->showTpl();
        break;

    case 'show_all_css':
        SX::object('AdminThemes')->loadCss();
        break;

    case 'show_css':
        SX::object('AdminThemes')->showCss();
        break;
}
