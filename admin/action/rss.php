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
    case 'news':
        if (!get_active('News')) {
            SX::object('Core')->notActive();
        }
        SX::object('RSS')->news();
        break;

    case 'articles':
        if (!get_active('articles')) {
            SX::object('Core')->notActive();
        }
        SX::object('RSS')->articles();
        break;

    case 'forum':
        if (!get_active('forums')) {
            SX::object('Core')->notActive();
        }
        SX::object('RSS')->forum();
        break;

    default:
        SX::object('RSS')->show();
        break;
}
