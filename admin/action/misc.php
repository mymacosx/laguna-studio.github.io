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

switch (Arr::getRequest('do')) {
    case 'shopimgages':
        SX::object('Shop')->browseImages(Arr::getRequest('prodid'));
        break;

    case 'mypersonaldownloads':
        SX::object('Shop')->personalDownloads(Arr::getGet('oid'));
        break;

    case 'viewmyorder':
        SX::object('Shop')->showMyOrder(Arr::getGet('oid'), '1');
        break;

    case 'viewpayorder':
        SX::object('Shop')->showMyOrder(Arr::getGet('oid'), '2');
        break;

    case 'shippingcost':
        SX::object('Shop')->shippingCosts(1);
        break;

    case 'mergeproduct':
        SX::object('Shop')->mergeProduct(Arr::getRequest('prodid'), Arr::getRequest('cid'));
        break;

    case 'payment_info':
        SX::object('Shop')->paymentInfo(Arr::getRequest('id'));
        break;

    case 'pnpop':
        if (Arr::getSession('user_group') == 2) {
            exit;
        }
        SX::object('Pn')->popup();
        break;

    case 'cancel_popup':
        if (Arr::getSession('user_group') == 2) {
            exit;
        }
        SX::object('Pn')->cancel();
        break;

    case 'searchuser':
        if (Arr::getSession('user_group') == 2) {
            exit;
        }
        SX::object('Pn')->search();
        break;

    case 'skype':
        SX::object('Profile')->skype();
        break;

    case 'icq':
        SX::object('Profile')->icq();
        break;

    case 'email':
        SX::object('Profile')->email();
        break;

    case 'autowords':
        SX::object('Glossar')->autowords(Arr::getRequest('id'));
        break;

    case 'attachment':
        SX::object('Forum')->upload();
        break;

    case 'delattach':
        SX::object('Forum')->delattach(Arr::getGet('id'), Arr::getGet('file'));
        break;

    case 'showposter':
        SX::object('Forum')->showposter();
        break;

    default:
        SX::object('Redir')->redirect();
        break;
}
