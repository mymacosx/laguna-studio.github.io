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

if (!perm('products') || !admin_active('products')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminProducts')->show();
        break;

    case 'settings':
        SX::object('AdminProducts')->settings();
        break;

    case 'del':
        SX::object('AdminProducts')->Delete(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminProducts')->add();
        break;

    case 'edit':
        SX::object('AdminProducts')->edit(Arr::getRequest('id'));
        break;

    case 'copy':
        SX::object('AdminProducts')->copy(Arr::getRequest('id'));
        break;

    case 'genres':
        SX::object('AdminProducts')->showGenres();
        break;

    case 'delrating':
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminProducts')->delRating(Arr::getRequest('id'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/products/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
