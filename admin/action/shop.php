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
if (!get_active('shop')) {
    SX::object('Redir')->redirect();
}
if (!permission('shop')) {
    SX::object('Core')->noAccess();
}

if (SX::object('Shop')->noNettoDisplay(Arr::getSession('user_country'))) {
    View::get()->assign('hide_vat_details', 1);
}

switch (Arr::getRequest('action')) {
    default:
    case 'start':
        switch (SX::get('shop.StartSeite')) {
            case 'shopstart':
                SX::object('Shop')->shopStart();
                break;
            case 'startartikel':
                SX::object('Shop')->browseShop(1);
                break;
            default:
                SX::object('Shop')->browseShop();
                break;
        }
        break;

    case 'showproducts':
        SX::object('Shop')->browseShop();
        break;

    case 'showproduct':
        SX::object('Shop')->displayProduct();
        break;

    case 'to_cart':
        SX::object('Shop')->addToCart();
        break;

    case 'to_wishlist':
        SX::object('Shop')->addToCart(1);
        break;

    case 'showseenproducts':
        SX::object('Shop')->listAllSeenProducts('showall');
        break;

    case 'showbasket':
        SX::object('Shop')->showBasket();
        break;

    case 'mylist':
        SX::object('Shop')->showMyList();
        break;

    case 'delitem':
        SX::object('Shop')->deleteItem(Arr::getRequest('product_id'));
        break;

    case 'delitem_mylist':
        SX::object('Shop')->deleteItem(Arr::getRequest('product_id'), 1);
        break;

    case 'new_list':
        SX::object('Shop')->newList();
        break;

    case 'shoporder':
        SX::object('Shop')->shopOrder();
        break;

    case 'myorders':
        SX::object('Shop')->myOrders();
        break;

    case 'shippingcost':
        SX::object('Shop')->shippingCosts();
        break;

    case 'mydownloads':
        SX::object('Shop')->myDownloads();
        break;

    case 'refusal':
        SX::object('Shop')->shopInfpage('refusal');
        break;

    case 'privacy':
        SX::object('Shop')->shopInfpage('privacy');
        break;

    case 'agb':
        SX::object('Shop')->shopInfpage('agb');
        break;

    case 'payment_info':
        SX::object('Shop')->paymentInfo(Arr::getRequest('id'));
        break;

    case 'showsavedbaskets':
        SX::object('Shop')->showSavedBaskets();
        break;

    case 'delsavedbasket':
        SX::object('Shop')->DatabaseBasketDel(Arr::getPost('bid'));
        break;

    case 'delsavedbasket_all':
        SX::object('Shop')->DatabaseBasketDelAll();
        break;

    case 'loadsavedbasket':
        SX::object('Shop')->DatabaseBasketLoad(Arr::getPost('bid'));
        break;

    case 'delbasket':
        SX::object('Shop')->unsetShopSessions();
        SX::object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        break;

    case 'callback':
        $payment = strtolower(Arr::getRequest('payment'));
        switch ($payment) {
            case 'pp':
            case 'wp':
            case 'mb':
            case 'as':
            case 'ik':
            case 'wm':
            case 'zp':
            case 'rk':
            case 'p2p':
            case 'w1':
            case 'ep':
                if (Arr::getRequest('reply') == 'result') {
                    $class = 'Payment' . strtoupper($payment);
                    SX::object($class)->callback();
                }
                break;

            case 'rb':
                if (Arr::getRequest('reply') == 'result') {
                    SX::object('PaymentRBK')->callback();
                }
                break;

            case 'lp':
                if (Arr::getRequest('reply') == 'result') {
                    SX::object('PaymentLP')->callback();
                }
                if (Arr::getRequest('reply') == 'reset') {
                    SX::object('PaymentLP')->reset();
                }
                break;
            default:
                SX::object('Redir')->redirect();
                break;
        }
        SX::object('Shop')->CallBackMsg();
        break;

    case 'ajaxcoupon':
        SX::object('Shop')->AjaxCouponCode();
        break;

    case 'ajaxcoupondel':
        SX::object('Shop')->AjaxCouponCodeDel();
        break;

    case 'prais':
        SX::object('Shop')->ShopPrais();
        break;
}
