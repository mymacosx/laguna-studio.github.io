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

if (!perm('adminpanel')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    case 'cache':
        $_REQUEST['noout'] = 1;
        if (!perm('settings')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminMain')->delCaches();
        break;

    case 'compiled':
        $_REQUEST['noout'] = 1;
        if (!perm('settings')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminMain')->delCompiled();
        break;

    case 'db':
        $_REQUEST['noout'] = 1;
        if (!perm('settings')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminMain')->db(Arr::getRequest('what'), PREFIX);
        break;

    case 'phpinfo':
        View::get()->assign('phpinfo', phpinfo());
        View::get()->content('/other/phpinfo.tpl');
        exit;
        break;

    case 'showall':
        SX::object('AdminMain')->online(true);
        break;

    default:
    case 'start':
        SX::object('AdminMain')->start(true);
        break;
}
