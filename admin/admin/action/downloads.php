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

if (!perm('downloads') || !admin_active('downloads')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminDownloads')->show();
        break;

    case 'delrating':
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->delRating(Arr::getRequest('id'));
        break;

    case 'edit':
        if (!perm('downloads_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        if (!perm('downloads_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->add();
        break;

    case 'copy':
        if (!perm('downloads_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->copy(Arr::getRequest('id'));
        break;

    case 'settings':
        if (!perm('downloads_settings')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->settings();
        break;

    case 'categs':
        if (!perm('downloads_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->showCategs();
        break;

    case 'delcateg':
        if (!perm('downloads_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->delCateg(Arr::getRequest('id'));
        break;

    case 'editcateg':
        if (!perm('downloads_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->editCateg(Arr::getRequest('id'));
        break;

    case 'filesuggest':
        $_REQUEST['noout'] = 1;
        SX::object('AdminDownloads')->search(Arr::getRequest('q'));
        break;

    case 'addcateg':
        if (!perm('downloads_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminDownloads')->addCateg();
        break;

    case 'fileupload':
        $options = array(
            'type'   => 'file',
            'result' => 'ajax',
            'upload' => '/uploads/downloads_files/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'iconupload':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/downloads/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
