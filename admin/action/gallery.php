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
if (!permission('gallery')) {
    SX::object('Core')->noAccess();
}
if (!get_active('gallery')) {
    SX::object('Core')->notActive();
}

switch (Arr::getRequest('action')) {
    default:
    case 'showall':
        SX::object('Gallery')->show();
        break;

    case 'categquicksearch':
        SX::object('Gallery')->search(Arr::getRequest('q'));
        break;

    case 'showincluded':
        SX::object('Gallery')->included(Arr::getRequest('categ'));
        break;

    case 'showgallery':
        SX::object('Gallery')->get(Arr::getRequest('id'));
        break;

    case 'showimage':
        SX::object('Gallery')->image(Arr::getRequest('id'), Arr::getRequest('galid'));
        break;

    case 'ajaxrandom':
        SX::object('Gallery')->slide(Arr::getRequest('id'), Arr::getRequest('ascdesc'), Arr::getRequest('blanc'), Arr::getRequest('first_id'), '', Arr::getRequest('categ'));
        break;

    case 'ajaxtop':
        SX::object('Gallery')->slide(Arr::getRequest('id'), '', '', '', 1, Arr::getRequest('categ'));
        break;

    case 'addfavorite':
        SX::object('Gallery')->addFavorite(Arr::getRequest('img_id'), Arr::getRequest('gal_id'));
        break;

    case 'deletefavorite':
        SX::object('Gallery')->delFavorite(Arr::getRequest('img_id'));
        break;

    case 'delete_allfavorites':
        SX::object('Gallery')->delAllFavorites(Arr::getRequest('galid'), Arr::getRequest('categ'), Arr::getRequest('name'));
        break;
}
