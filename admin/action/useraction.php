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

if ($_SESSION['loggedin'] != 1 || $_SESSION['user_group'] == 2) {
    SX::object('Core')->message('Global_NoPermission', 'Global_NoPermission_t');
}

switch (Arr::getRequest('action')) {
    default:
    case 'profile':
        SX::object('User')->profile();
        break;

    case 'changepass':
        SX::object('Login')->changepass();
        break;

    case 'deleteaccount':
        if (!permission('deleteaccount')) {
            SX::object('Core')->noAccess();
        }
        SX::object('User')->delete();
        break;

    case 'avatarupload':
        if (permission('own_avatar')) {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/avatars/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => SX::get('system.AvatarGroesse'),
            );
            SX::object('Upload')->load($options);
        }
        break;
}
