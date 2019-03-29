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

if (!perm('mediapool')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    case 'thumb':
        SX::object('Browser')->thumb();
        break;

    case 'delfile':
        SX::object('Browser')->delfile();
        break;

    case 'copy':
        SX::object('Browser')->copy();
        break;

    case 'rename':
        SX::object('Browser')->rename();
        break;

    case 'upload':
        SX::object('Browser')->upload();
        break;

    case 'receive':
        SX::object('Browser')->receive();
        break;

    case 'newdir':
        SX::object('Browser')->newdir();
        break;

    case 'left':
        SX::object('Browser')->left();
        break;

    case 'right':
        SX::object('Browser')->right();
        break;

    default:
        SX::object('Browser')->load();
        break;
}
