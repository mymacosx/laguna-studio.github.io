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

if (!perm('content') || !admin_active('content')) {
    SX::object('AdminCore')->noAccess();
}

View::get()->assign('CodeWidgetsAll', SX::object('AdminCodeWidget')->load());
switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminContent')->show();
        break;

    case 'addcontent':
        SX::object('AdminContent')->add();
        break;

    case 'editcontent':
        SX::object('AdminContent')->edit(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('AdminContent')->delete(Arr::getRequest('id'));
        break;

    case 'categories':
        SX::object('AdminContent')->showCategs();
        break;

    case 'delcateg':
        SX::object('AdminContent')->delCateg(Arr::getRequest('id'));
        break;

    case 'openclose':
        SX::object('AdminContent')->active(Arr::getRequest('openclose'), Arr::getRequest('id'));
        break;

    case 'delrating':
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminContent')->delRating(Arr::getRequest('id'));
        break;

    case 'iconupload':
        if (perm('articles_edit_all') || perm('articles_new')) {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/content/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => Arr::getRequest('resize'),
            );
            SX::object('Upload')->load($options);
        }
        break;
}
