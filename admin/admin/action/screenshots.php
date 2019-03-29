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

if (!perm('screenshots')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('action')) {
    case 'add':
        SX::object('Screenshots')->add();
        break;

    case 'choice':
        SX::object('Screenshots')->choice();
        break;

    default:
    case 'screenshots':
        SX::object('Screenshots')->load();
        break;
}
