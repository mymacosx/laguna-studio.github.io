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

if (!perm('articles') || !admin_active('articles')) {
    SX::object('AdminCore')->noAccess();
}

View::get()->assign('CodeWidgetsAll', SX::object('AdminCodeWidget')->load());
switch (Arr::getRequest('sub')) {
    default:
    case 'articles':
        SX::object('AdminArticles')->show();
        break;

    case 'copy':
        SX::object('AdminArticles')->copy(Arr::getRequest('id'));
        break;

    case 'edit':
        SX::object('AdminArticles')->edit(Arr::getRequest('id'));
        break;

    case 'delete':
        SX::object('AdminArticles')->delete(Arr::getRequest('id'));
        break;

    case 'active':
        SX::object('AdminArticles')->active(Arr::getRequest('openclose'), Arr::getRequest('id'));
        break;

    case 'add':
        SX::object('AdminArticles')->add();
        break;

    case 'showcategs':
        SX::object('AdminArticles')->showCategs();
        break;

    case 'editcateg':
        SX::object('AdminArticles')->editCateg(Arr::getRequest('id'));
        break;

    case 'addcateg':
        SX::object('AdminArticles')->addCateg();
        break;

    case 'deletecateg':
        SX::object('AdminArticles')->deleteCateg(Arr::getRequest('id'));
        break;

    case 'delrating':
        SX::object('AdminArticles')->delRating(Arr::getRequest('id'));
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/articles/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
