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

if (!perm('user_groups')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'useroverview':
        SX::object('AdminGroups')->show();
        break;

    case 'groupedit':
        SX::object('AdminGroups')->edit(Arr::getRequest('id'));
        break;

    case 'delgroup':
        SX::object('AdminGroups')->delete(Arr::getRequest('id'));
        break;

    case 'permissions':
        SX::object('AdminGroups')->showGroup();
        break;

    case 'editpermissions':
        SX::object('AdminGroups')->editGroup(Arr::getRequest('id'), Arr::getRequest('area'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/avatars/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
