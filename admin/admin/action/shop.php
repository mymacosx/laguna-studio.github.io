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

if (!perm('shop') || !admin_active('shop')) {
    SX::object('AdminCore')->noAccess();
}

switch (Arr::getRequest('sub')) {
    default:
    case 'settings':
        SX::object('AdminShop')->settingsShop();
        break;

    case 'regions':
        SX::object('AdminShop')->showRegion();
        break;

    case 'del_region':
        SX::object('AdminShop')->delRegion();
        break;

    case 'add_region':
        SX::object('AdminShop')->addRegion();
        break;

    case 'tracking':
        SX::object('AdminShop')->codesTracking();
        break;

    case 'ajaxarticles':
        SX::object('AdminShop')->getArticlesAjax();
        break;

    case 'infomsg':
        SX::object('AdminShop')->startInfo();
        break;

    case 'prodvotes':
        SX::object('AdminShop')->prodVotes(Arr::getRequest('id'));
        break;

    case 'showmoney':
        SX::object('AdminShop')->showMoney();
        break;

    case 'categvariants':
        SX::object('AdminShop')->categVariants(Arr::getRequest('id'));
        break;

    case 'new':
        SX::object('AdminShop')->add();
        break;

    case 'shopinfos':
        SX::object('AdminShop')->shopInfo();
        break;

    case 'orders':
        SX::object('AdminShop')->listOrders();
        break;

    case 'edit_order':
        SX::object('AdminShop')->editOrder(Arr::getRequest('id'));
        break;

    case 'getHtmlOrder':
        SX::object('AdminShop')->htmlOrder(Arr::getRequest('id'));
        break;

    case 'getHtmlPay':
        SX::object('AdminShop')->htmlPay(Arr::getRequest('id'));
        break;

    case 'cancel_order':
        SX::object('AdminShop')->cancelOrder(Arr::getRequest('id'));
        break;

    case 'edit_article':
        SX::object('AdminShop')->edit(Arr::getRequest('id'));
        break;

    case 'delete_article':
        SX::object('AdminShop')->delete(Arr::getRequest('id'));
        break;

    case 'copy_article':
        SX::object('AdminShop')->copy(Arr::getRequest('id'));
        break;

    case 'user_downloads':
        SX::object('AdminShop')->userDownloads(Arr::getRequest('user'));
        break;

    case 'user_downloads_personal':
        SX::object('AdminShop')->personalDownloads(Arr::getRequest('order'), Arr::getRequest('user'));
        break;

    case 'user_personal_file':
        $options = array(
            'type'   => 'file',
            'result' => 'ajax',
            'upload' => '/uploads/shop/customerfiles/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'esd_downloads':
        SX::object('AdminShop')->esdDownloads(Arr::getRequest('id'));
        break;

    case 'articles':
        SX::object('AdminShop')->show();
        break;

    case 'get_prods_categs':
        SX::object('AdminShop')->productCategs();
        break;

    case 'get_prods_parts':
        SX::object('AdminShop')->productParts();
        break;

    case 'get_prods_tuning':
        SX::object('AdminShop')->productTuning();
        break;

    case 'article_variants':
        SX::object('AdminShop')->showVariants(Arr::getRequest('id'), Arr::getRequest('cat'));
        break;

    case 'article_stprices':
        SX::object('AdminShop')->showStprices(Arr::getRequest('id'));
        break;

    case 'categories':
        SX::object('AdminShop')->showCategs();
        break;

    case 'name_text':
        SX::object('AdminShop')->categName(Arr::getRequest('langcode'), Arr::getRequest('id'));
        break;

    case 'edit_categ':
        SX::object('AdminShop')->editCateg();
        break;

    case 'new_categ':
        SX::object('AdminShop')->addCateg();
        break;

    case 'del_categ':
        SX::object('AdminShop')->deleteCateg(Arr::getRequest('id'));
        break;

    case 'icons_categs':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/shop/icons_categs/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'navi_categs':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/shop/navi_categs/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'categ_icon':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/shop/icons/',
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

    case 'payment_icons':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/shop/payment_icons/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'shipper_icons':
        $options = array(
            'type'   => 'image',
            'result' => 'ajax',
            'upload' => '/uploads/shop/shipper_icons/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
            'resize' => Arr::getRequest('resize'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'shopfile_upload':
        $options = array(
            'type'   => 'file',
            'result' => 'ajax',
            'upload' => '/uploads/shop/product_downloads/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'esd_upload':
        $options = array(
            'type'   => 'file',
            'result' => 'ajax',
            'upload' => '/uploads/shop/files/',
            'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
        );
        SX::object('Upload')->load($options);
        break;

    case 'categories_addons':
        SX::object('AdminShop')->categAddons();
        break;

    case 'paymentmethods':
        SX::object('AdminShop')->showPayment();
        break;

    case 'editpaymentmethod':
        SX::object('AdminShop')->editPayment(Arr::getRequest('Id'), Arr::getRequest('lc'));
        break;

    case 'editshipper':
        SX::object('AdminShop')->editShipping(Arr::getRequest('Id'), Arr::getRequest('lc'));
        break;

    case 'editshippingvolumes':
        SX::object('AdminShop')->volumesShipping(Arr::getRequest('Id'));
        break;

    case 'shipper':
        SX::object('AdminShop')->showShipping();
        break;

    case 'taxes':
        SX::object('AdminShop')->showTaxes();
        break;

    case 'shippingready':
        SX::object('AdminShop')->showShippingready();
        break;

    case 'availabilities':
        SX::object('AdminShop')->showAvailabilities();
        break;

    case 'couponcodes':
        SX::object('AdminShop')->couponCodes();
        break;

    case 'units':
        SX::object('AdminShop')->showUnits();
        break;

    case 'specifications':
        SX::object('AdminShop')->showSpecifications(Arr::getRequest('id'));
        break;

    case 'openclose':
        SX::object('AdminShop')->active(Arr::getRequest('status'), Arr::getRequest('id'));
        break;

    case 'groupsettings':
        SX::object('AdminShop')->settingsGroups();
        break;

    case 'delzakaz':
        SX::object('AdminShop')->deleteOrder(Arr::getRequest('id'));
        break;

    case 'delallzakaz':
        SX::object('AdminShop')->cleanOrders();
        break;

    case 'yamarket':
        SX::object('AdminYML')->get();
        break;
}
