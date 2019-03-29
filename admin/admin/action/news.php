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

if (!perm('news') || !admin_active('News')) {
    SX::object('AdminCore')->noAccess();
}

View::get()->assign('CodeWidgetsAll', SX::object('AdminCodeWidget')->load());
switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminNews')->show();
        break;

    case 'settings':
        SX::object('AdminNews')->settings();
        break;

    case 'addnews':
        SX::object('AdminNews')->add();
        break;

    case 'editcateg':
        SX::object('AdminNews')->editCateg(Arr::getRequest('id'));
        break;

    case 'addcateg':
        SX::object('AdminNews')->addCateg();
        break;

    case 'categories':
        SX::object('AdminNews')->showCateg();
        break;

    case 'delcateg':
        SX::object('AdminNews')->delCateg(Arr::getRequest('id'));
        break;

    case 'openclose':
        SX::object('AdminNews')->active(Arr::getRequest('openclose'), Arr::getRequest('id'));
        break;

    case 'editnews':
        SX::object('AdminNews')->edit(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('AdminNews')->delete(Arr::getRequest('id'));
        break;

    case 'delrating':
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminNews')->delRating(Arr::getRequest('id'));
        break;

    case 'iconupload':
        if (perm('news_edit') || perm('news_new')) {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/news/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => Arr::getRequest('resize'),
            );
            SX::object('Upload')->load($options);
        }
        break;
}
