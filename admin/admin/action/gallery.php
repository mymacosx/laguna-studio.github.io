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

if (!perm('gallery_overview') || !admin_active('gallery')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'overview':
        SX::object('AdminGallery')->showCategs();
        break;

    case 'showincluded':
        SX::object('AdminGallery')->included(Arr::getRequest('id'));
        break;

    case 'editgallery':
        SX::object('AdminGallery')->edit(Arr::getRequest('id'));
        break;

    case 'editimages':
        SX::object('AdminGallery')->editImages(Arr::getRequest('id'));
        break;

    case 'addimages':
        SX::object('AdminGallery')->addImages(Arr::getRequest('id'));
        break;

    case 'gallerydel':
        SX::object('AdminGallery')->delete(Arr::getRequest('id'));
        break;

    case 'addgallery':
        SX::object('AdminGallery')->addGallery(Arr::getRequest('id'));
        break;

    case 'editcateg':
        SX::object('AdminGallery')->editCateg(Arr::getRequest('id'));
        break;

    case 'delcategory':
        SX::object('AdminGallery')->deleteCateg(Arr::getRequest('id'));
        break;

    case 'addcategory':
        SX::object('AdminGallery')->addCateg();
        break;

    case 'editimage':
        SX::object('AdminGallery')->editImage(Arr::getRequest('id'));
        break;

    case 'gallerysettings':
        SX::object('AdminGallery')->settings();
        break;

    case 'categicon':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/galerie_icons/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'watermark':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/watermarks/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;
}
