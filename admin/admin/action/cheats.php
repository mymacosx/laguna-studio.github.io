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

if (!perm('cheats') || !admin_active('cheats')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'cheats':
        SX::object('AdminCheats')->show();
        break;

    case 'plattforms':
        SX::object('AdminCheats')->showPlattforms();
        break;

    case 'delrating':
        SX::object('AdminCheats')->delRating(Arr::getRequest('id'));
        break;

    case 'settings':
        SX::object('AdminCheats')->settings();
        break;

    case 'edit':
        SX::object('AdminCheats')->edit(Arr::getRequest('id'));
        break;

    case 'add':
        SX::object('AdminCheats')->add();
        break;

    case 'copy':
        SX::object('AdminCheats')->copy(Arr::getRequest('id'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/cheats/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'fileupload':
        $options = array(
            'type'   => 'file',
            'result' => 'ajax',
            'upload' => '/uploads/cheats_files/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;
}
