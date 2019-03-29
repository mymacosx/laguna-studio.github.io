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

switch (Arr::getRequest('action')) {
    default:
    case 'form':
        SX::object('AdminLogin')->formLogin();
        break;

    case 'login':
        SX::object('AdminLogin')->newLogin();
        break;

    case 'sectionswitch':
        SX::object('AdminLogin')->sectionSwitch();
        break;

    case 'themeswitch':
        SX::object('AdminLogin')->themeSwitch();
        break;
}
