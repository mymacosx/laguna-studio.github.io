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

if (!perm('links') || !admin_active('links')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminLinks')->show();
        break;

    case 'delrating':
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->delRating(Arr::getRequest('id'));
        break;

    case 'edit':
        if (!perm('links_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->edit(Arr::getRequest('id'));
        break;

    case 'new':
        if (!perm('links_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->add();
        break;

    case 'copy':
        if (!perm('links_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->copy(Arr::getRequest('id'));
        break;

    case 'settings':
        if (!perm('links_settings')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->settings();
        break;

    case 'categs':
        if (!perm('links_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->showCategs();
        break;

    case 'delcateg':
        if (!perm('links_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->delCateg(Arr::getRequest('id'));
        break;

    case 'editcateg':
        if (!perm('links_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->editCateg(Arr::getRequest('id'));
        break;

    case 'addcateg':
        if (!perm('links_categs')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminLinks')->addCateg();
        break;

    case 'iconupload':
        if (perm('links_edit')) {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/links/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => Arr::getRequest('resize'),
            );
            SX::object('Upload')->load($options);
        }
        break;

    case 'snapshot':
        if (!perm('links_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        SX::object('AdminSnapShot')->get(Arr::getRequest('data'), Arr::getRequest('resize'));
        break;
}
