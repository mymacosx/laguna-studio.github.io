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
if (!get_active('newsletter')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    default:
    case 'abonew':
        SX::object('Newsletter')->create();
        break;

    case 'activate':
        SX::object('Newsletter')->activate();
        break;

    case 'unsubscribe':
        SX::object('Newsletter')->delete();
        break;
}
