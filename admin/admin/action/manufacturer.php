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

if (!perm('manufacturer') || !admin_active('manufacturer')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminManufacturer')->show();
        break;

    case 'new':
        SX::object('AdminManufacturer')->add();
        break;

    case 'edit':
        SX::object('AdminManufacturer')->edit(Arr::getRequest('id'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/manufacturer/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
