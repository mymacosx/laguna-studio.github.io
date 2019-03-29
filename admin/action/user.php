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

if (!get_active('members')) {
    SX::object('Core')->notActive();
}

$id = intval(Arr::getRequest('id'));
if (!permission('showuserpage') && $_SESSION['benutzer_id'] != $id) {
    SX::object('Core')->message('Global_NoPermission', 'Global_NoPermission_t');
}

switch (Arr::getRequest('action')) {
    case 'friends':
        SX::object('Profile')->friends($id);
        break;

    case 'gallery':
        SX::object('Profile')->gallery($id);
        break;

    case 'upload':
        SX::object('Profile')->upload();
        break;

    case 'gal':
        SX::object('Profile')->launch();
        break;

    case 'guestbook':
    default:
        SX::object('Profile')->load($id);
        break;
}
