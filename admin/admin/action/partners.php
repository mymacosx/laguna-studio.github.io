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

if (!perm('partners') || !admin_active('partners')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminPartner')->show();
        break;

    case 'del':
        SX::object('AdminPartner')->delete(Arr::getRequest('id'));
        break;

    case 'edit':
        SX::object('AdminPartner')->edit(Arr::getRequest('id'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/partner/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
