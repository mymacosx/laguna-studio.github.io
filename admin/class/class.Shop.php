<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404);
    exit;
}

class Shop extends Magic {

    public $_gast_warenkorb_erinnerung = 1;
    public $_gast_warenkorb_erinnerung_tage = 7;
    public $_canorder_emptybasket = 1;
    public $_basket_cookietime = 7;
    public $_product_detail_tpl = 'product.tpl';
    public $_shop_start_tpl = 'shop_start.tpl';
    public $_shop_browse_tpl = 'shop_browse.tpl';
    public $_product_new_tpl = 'shop_start_products_new.tpl';
    public $_shop_topseller_tpl = 'shop_start_topseller.tpl';
    public $_start_angebote_tpl = 'shop_start_offers.tpl';
    public $_colums;
    protected $lc;
    protected $stime;
    protected $no_pagenav = false;
    protected $settings = array();
    protected $vailmsg = array();
    protected $_shop_params = array();
    protected $_shop_categs = array();

    public function __construct() {
        $this->lc = Arr::getSession('Langcode', 1);
        $this->settings = SX::get('shop');
        $this->stime = time();
        $this->defParam();
        $this->ShopInit();
    }

    protected function defParam() {
        $_REQUEST['parent'] = intval(Arr::getRequest('parent'));
        $_REQUEST['navop'] = intval(Arr::getRequest('navop'));
        $_REQUEST['man'] = intval(Arr::getRequest('man'));
        $_REQUEST['payment_id'] = intval(Arr::getRequest('payment_id'));
    }

    public function ShopInit() {
        $this->_colums = 1;
        $shipper = array();
        $LC = $this->lc;
        $this->MyShopNavi();
        if (Arr::getSession('loggedin') != 1) {
            $this->ShopWarenkorbGuest();
        }
        $shipper_active = false;
        $query = "SELECT Id FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Aktiv = '1' ; ";
        $query .= "SELECT Id, Titel_{$LC} AS Name, Text_{$LC} AS Text FROM " . PREFIX . "_shop_verfuegbarkeit ORDER BY Id ASC ; ";
        $query .= "SELECT StartText_{$LC} AS InfoText, Name_{$LC} AS ShopName, Name_{$LC}_zeigen AS TitelZeigen, StartText_{$LC}_zeigen AS MeldungZeigen FROM " . PREFIX . "_shop_eigenschaften WHERE Sektion = '" . AREA . "' ; ";
        $query .= "SELECT Id, Name_{$LC} AS Name, Beschreibung_{$LC} AS Beschreibung, Icon FROM " . PREFIX . "_shop_versandarten WHERE Aktiv = '1' ORDER BY Position ASC";

        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row_payments = $result->fetch_object()) {
                    $this->_view->assign('payment_' . $row_payments->Id, 1);
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_Available = $result->fetch_object()) {
                    $this->vailmsg[] = $row_Available;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $shop_sett = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_shipper = $result->fetch_object()) {
                    if (!empty($row_shipper->Icon) && is_file(UPLOADS_DIR . '/shop/shipper_icons/' . $row_shipper->Icon)) {
                        $row_shipper->Text = strip_tags($row_shipper->Name . ' - ' . $row_shipper->Beschreibung);
                        $shipper[] = $row_shipper;
                        $shipper_active = true;
                    }
                }
                $result->close();
            }
        }

        if ($shipper_active) {
            ;
            $this->_view->assign('ShipperAll', $shipper);
        }
        if ($this->settings['Gutscheine'] == 1) {
            $this->_view->assign('shop_coupons', 1);
        }

        $tpl_array = array(
            'fsk_user'      => Tool::userSettings('Fsk18', 0),
            'ShopInfoPanel' => $this->getInfoPanel(),
            'shopsettings'  => (object) $this->settings,
            'best_max'      => $this->settings['BestMax'],
            'best_min'      => $this->settings['BestMin'],
            'shop_land'     => strtoupper($this->settings['ShopLand']),
            'curr_change'   => ($this->settings['MultiWaehrung'] == 1) ? 1 : 0);
        $this->_view->assign($tpl_array);

        if (!empty($_REQUEST['subaction']) && isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'showbasket' || $_REQUEST['subaction'] == 'step2' || $_REQUEST['subaction'] == 'step3' || $_REQUEST['subaction'] == 'step4') {
                $_SESSION['currency'] = 1;
            }
        }

        $this->checkDiscount();
        $this->_view->assign('widerruf_belehrung', str_replace('__SHOPADRESSE__', $this->settings['ShopAdresse'], $this->settings['Widerruf']));
        $this->_view->assign('navi_title', $this->_lang['Title_Navi']);
        $this->_view->assign('shop_navigation', $this->_view->fetch(THEME . '/shop/shopnavi.tpl'));

        $this->_view->assign('ShopMsg', $shop_sett);
        $this->_view->assign('ShopInfo', $this->_view->fetch(THEME . '/shop/start_msg.tpl'));

        if (!empty($shop_sett->ShopName)) {
            $ShopName = sanitize($shop_sett->ShopName);
            SX::set('user_shop.ShopStartUrl', 'index.php?p=shop&amp;area=' . AREA . '&amp;start=1&amp;name=' . translit($ShopName));
            SX::$lang['Shop'] = $ShopName;
        }

        $currency_symbol = $this->setCurrency();
        SX::set('options.CurrSymbol', $currency_symbol);

        $tpl_array = array(
            'cu_array'        => $this->settings,
            'currency_symbol' => $currency_symbol,
            'available_array' => $this->vailmsg);
        $this->_view->assign($tpl_array);

        if (get_active('shop_topseller')) {
            $this->_view->assign('small_topseller_array', $this->listProducts(0, 1, 1, $this->settings['Topseller_Navi_Limit'], 1));
            $this->_view->assign('small_topseller', $this->_view->fetch(THEME . '/shop/small_topseller.tpl'));
        }

        if (isset($_SESSION['prod_seen']) && is_array($_SESSION['prod_seen']) && get_active('shop_seenproducts') && $_REQUEST['p'] != 'misc') {
            $this->_view->assign('seen_products_array', $this->showProductsVarious());
            $this->_view->assign('small_seen_products', $this->_view->fetch(THEME . '/shop/small_seen_products.tpl'));
        } else {
            $this->_view->assign('small_seen_products', '');
        }

        if (get_active('shop_currency')) {
            $this->_view->assign('curreny_selector', $this->_view->fetch(THEME . '/shop/curreny_selector.tpl'));
        }

        $tpl_array = array(
            'payment_images'    => $this->paymentInfoAll(),
            'basket_small'      => $this->initBasket(),
            'shop_manufaturers' => $this->getManufacturer());
        $this->_view->assign($tpl_array);

        $this->_view->assign('status_legend', $this->_view->fetch(THEME . '/shop/products_legend.tpl'));
        $this->_view->assign('shop_search_small_action', 'index.php?s=1&amp;area=' . AREA . '&amp;lang=' . $_SESSION['lang'] . '&amp;p=shop&amp;action=showproducts');
        $this->_view->assign('shop_search_small', $this->showSearchSmall(THEME));
    }

    /* Метод обработки параметров валюты */
    public function setCurrency() {
        if (!empty($_SESSION['currency']) && empty($_REQUEST['currency'])) {
            $result = $this->optionsCurrency(intval($_SESSION['currency']));
        } else if (!empty($_REQUEST['currency']) && $_REQUEST['currency'] > 0) {
            $currency = intval($_REQUEST['currency']);
            $_SESSION['currency'] = $currency;
            $result = $this->optionsCurrency($currency);
            $this->__object('Redir')->seoRedirect($this->__object('Redir')->referer(true));
        } else {
            $result = $this->optionsCurrency();
        }
        return $this->settings[$result];
    }

    /* Метод установки параметров валюты */
    public function optionsCurrency($currency = null) {
        if (empty($currency) || empty($this->settings['WaehrungSymbol_' . $currency]) || $this->settings['MultiWaehrung'] != 1) {
            $currency = 1;
        }
        $_SESSION['Multiplikator'] = $currency > 1 ? $this->settings['Multiplikator_' . $currency] : 1;
        $_SESSION['currency_registered'] = $this->settings['Waehrung_' . $currency];
        return 'WaehrungSymbol_' . $currency;
    }

    /* Метод проверки заказов гостями */
    protected function ShopWarenkorbGuest() {
        $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb_gaeste WHERE Ablauf < '" . $this->stime . "'");
        $query = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_warenkorb_gaeste WHERE BenutzerId='" . $this->setBasketId() . "' LIMIT 1");
        if (is_object($query) && !empty($query->BenutzerId)) {
            $VarsCookie = explode('|||', base64_decode($query->InhaltConfig));
            foreach ($VarsCookie as $varcId) {
                $newVarsCookie = unserialize($varcId);
                $_SESSION['product_' . $newVarsCookie['ProdId']] = $newVarsCookie;
            }
            $_SESSION['products'] = unserialize($query->Inhalt);
        }
    }

    /* Метод формирует уникальный id */
    protected function setBasketId() {
        static $agent = false;
        if ($agent === false) {
            $agent = $this->__object('Agent')->agent;
        }
        return md5(IP_USER . $agent . 'SX CMS');
    }

    /* Подключаем модуль хранения неоформленных товаров в корзине */
    public function ShopWarenkorb() {
        $TimeTill = 86400 * 14;
        $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE ZeitBis+{$TimeTill} < '" . $this->stime . "'");
        $_SESSION['visitor_key'] = Arr::getSession('visitor_key');
        if (empty($_SESSION['visitor_key'])) {
            $_SESSION['visitor_key'] = Tool::random(10, 'alfa');
        }
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $check = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Bcount FROM " . PREFIX . "_shop_warenkorb WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND Gesperrt != '1' AND EingeloestAm = '0' AND Code != '" . $_SESSION['visitor_key'] . "'");
            if (is_object($check) && $check->Bcount >= 1) {
                $this->_view->assign(array('Bcc' => $check, 'Baskets' => true));
            }
        }
    }

    /* Подключаем модуль новинки магазина */
    public function NewShop() {
        $this->_view->assign('NewShopProducts', $this->NewShopProducts('page_extern_products_new.tpl'));
    }

    /* Подключаем модуль новинки магазина в навигации */
    public function NewShopNavi() {
        $this->_view->assign('NewShopProductsNavi', $this->NewShopProducts('page_extern_products_new_navi.tpl'));
    }

    public function NewShopProducts($tpl) {
        $tpl_array = array(
            'currency_symbol'       => SX::get('options.CurrSymbol'),
            'extern_products_array' => $this->listProducts(0, 1, 0, $this->settings['LimitExternNeu'], 1));
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/shop/' . $tpl);
    }

    public function shopStart() {
        unset($_SESSION['r_land']);
        $this->no_pagenav = true;
        $ShopLink = SX::get('options.ShopStartUrl', 'index.php?p=shop&amp;start=1');
        $this->_view->assign('products_array', $this->listProducts(3, 1, 0, $this->settings['Start_Limit'], 1));
        if (get_active('shop_topseller')) {
            $this->_view->assign('topseller_array', $this->listProducts($this->settings['Spalten_Topseller'], 1, 1, $this->settings['Topseller_Limit'], 1));
        }
        if (get_active('shop_angebote')) {
            $this->_view->assign('angebote_array', $this->listProducts(3, 1, 0, $this->settings['Angebote_Limit'], 1, 1, 1));
            $this->_view->assign('angebote_in_shop', $this->_view->fetch(THEME . '/shop/' . $this->_start_angebote_tpl));
        }
        if (get_active('shop_newinshop')) {
            $this->_view->assign('new_in_shop', $this->_view->fetch(THEME . '/shop/' . $this->_product_new_tpl));
        }
        if (get_active('shop_topseller')) {
            $this->_view->assign('topseller_in_shop', $this->_view->fetch(THEME . '/shop/' . $this->_shop_topseller_tpl));
        }

        $seo_array = array(
            'headernav' => '<a href="' . $ShopLink . '">' . $this->_lang['Shop'] . '</a>',
            'pagetitle' => $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/' . $this->_shop_start_tpl));
        $this->_view->finish($seo_array);
    }

    public function myOrders() {
        if ($_SESSION['user_group'] != 2) {
            if (Arr::getPost('sub') == 'sendrequest') {
                $error = '';
                if (empty($_POST['subject'])) {
                    $error[] = $this->_lang['Shop_status_rNoSubj'];
                }
                if (empty($_POST['text'])) {
                    $error[] = $this->_lang['Shop_status_rNoMsg'];
                }
                $this->_view->assign('error', $error);

                if (empty($error)) {
                    $msg = $this->_text->replace($this->_lang['Shop_oder_request_ty'], '__NAME__', Tool::fullName());
                    $tex = $this->_text->replace($_POST['text'], '__N__', "\n");

                    // Отправляем вопрос пользователя о заказе по списку адресов для уведомления о заказе
                    $array_mail = explode(';', $this->settings['Email_Bestellung']);
                    foreach ($array_mail as $send_mail) {
                        if (!empty($send_mail)) {
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $send_mail,
                                'to_name'   => '',
                                'text'      => $tex,
                                'subject'   => $_POST['subject'] . ' (' . $_SESSION['login_email'] . ')',
                                'fromemail' => $_SESSION['login_email'],
                                'from'      => $_SESSION['benutzer_vorname'],
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 3));
                        }
                    }
                    $this->__object('Core')->message('Shop_zapros', $msg, BASE_URL . '/index.php?p=shop&amp;action=myorders', 7);
                }
            }
            $this->_view->assign('whole_name', Tool::fullName());

            $db_where = $nav_where = '';
            switch (Arr::getGet('show')) {
                case 'ok':
                    $db_where = "AND Status = 'ok'";
                    $nav_where = "&amp;show=ok";
                    break;

                case 'oksend':
                    $db_where = "AND Status = 'oksend'";
                    $nav_where = "&amp;show=oksend";
                    break;

                case 'wait':
                    $db_where = "AND Status = 'wait'";
                    $nav_where = "&amp;show=wait";
                    break;

                case 'failed':
                    $db_where = "AND Status = 'failed'";
                    $nav_where = "&amp;show=failed";
                    break;

                case 'progress':
                    $db_where = "AND Status = 'progress'";
                    $nav_where = "&amp;show=progress";
                    break;

                case 'oksendparts':
                    $db_where = "AND Status = 'oksendparts'";
                    $nav_where = "&amp;show=oksendparts";
                    break;
            }

            $limit = 10;
            $a = Tool::getLimit($limit);
            $orders_query = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' {$db_where} ORDER BY Datum DESC LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $orders = array();
            while ($orders_row = $orders_query->fetch_object()) {
                $query = "SELECT * FROM " . PREFIX . "_shop_tracking WHERE Id = '" . $orders_row->Tracking_Id . "' ; ";
                $query .= "SELECT * FROM " . PREFIX . "_shop_kundendownloads WHERE Bestellung = '" . $orders_row->Id . "'";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $tracking = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $downloads = $result->fetch_object();
                        $result->close();
                    }
                }

                if (is_object($tracking)) {
                    $orders_row->TrackingName = $tracking->Name;
                    $orders_row->TrackingLink = str_replace('[TRACKING]', $orders_row->Tracking_Code, $tracking->Hyperlink);
                }
                if (is_object($downloads)) {
                    $orders_row->DownloadsCustom = 1;
                }

                $items = '';
                $BestNr = $orders_row->TransaktionsNummer;
                $items = array();
                $sql_items = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_bestellungen_items WHERE Bestellnummer='$BestNr'");
                while ($row_items = $sql_items->fetch_object()) {
                    $row_items->ArtlName = translit($this->getAttribute($row_items->Artikelnummer, 'Titel_1'));
                    $row_items->ArtId = $this->getAttribute($row_items->Artikelnummer, 'Id');
                    $row_items->CatId = $this->getAttribute($row_items->Artikelnummer, 'Kategorie');
                    $row_items->ArtName = $this->getAttribute($row_items->Artikelnummer, 'Titel_' . $this->lc);
                    $items[] = $row_items;
                }

                $orders_row->Items = $items;
                $orders_row->Verschickt = (empty($orders_row->Verschickt)) ? 'leer' : explode(',', $orders_row->Verschickt);
                $orders_row->Betrag = $this->getPrice($orders_row->Betrag);
                $orders_row->Bestellung = base64_decode($orders_row->Bestellung);
                $orders_row->Viewpayorder = (!empty($orders_row->Order_Type)) ? 1 : 0;
                $orders_row->SText = $this->_lang['Shop_status_' . $orders_row->Status];
                $orders[] = $orders_row;
            }
            $orders_query->close();
            $this->getSummStatus();
            $this->_view->assign('orders_array', $orders);
            if ($num > $limit) {
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\" href=\"index.php?p=shop&amp;action=myorders{$nav_where}&amp;page={s}\">{t}</a>"));
            }
        }

        $headernav = '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=userlogin">' . $this->_lang['Login'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=shop&amp;action=myorders">' . $this->_lang['Shop_go_myorders'] . '</a>';

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $this->_lang['Shop_go_myorders'] . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/myorders.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Выводим суммы по различным статусам заказов */
    protected function getSummStatus() {
        $user = $_SESSION['benutzer_id'];
        $betrag = $this->_db->fetch_assoc_all("
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'ok'
          UNION ALL
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'oksend'
          UNION ALL
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'progress'
          UNION ALL
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'wait'
          UNION ALL
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'failed'
          UNION ALL
        SELECT SUM(Betrag) AS Betrag FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . $user . "' AND Status = 'oksendparts'");

        $array = array(
            'ok'          => $this->getMultiplikator($betrag[0]['Betrag']),
            'progres'     => $this->getMultiplikator($betrag[2]['Betrag']),
            'oksend'      => $this->getMultiplikator($betrag[1]['Betrag']),
            'oksendparts' => $this->getMultiplikator($betrag[5]['Betrag']),
            'wait'        => $this->getMultiplikator($betrag[3]['Betrag']),
            'failed'      => $this->getMultiplikator($betrag[4]['Betrag']));
        $this->_view->assign('orders_summ', $array);
    }

    /* Получаем параметр по артикулу */
    public function getAttribute($Artikelnummer, $Attribute) {
        $res = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $this->_db->escape($Artikelnummer) . "' LIMIT 1");
        return isset($res[$Attribute]) ? $res[$Attribute] : '';
    }

    public function showTopByCateg($method = 'new') {
        $dbw = '';
        switch ($method) {
            case 'new':
                $oby = 'a.Id DESC';
                break;

            case 'topseller':
                $oby = 'a.Verkauft DESC';
                break;

            case 'offers':
                $dbw = "AND (Preis != '0.00' AND Preis_Liste > Preis AND Preis_Liste_Gueltig >= " . $this->stime . ")";
                $oby = ' RAND()';
                break;

            default:
                $oby = 'a.Id DESC';
                break;
        }

        $cid = intval(Arr::getRequest('cid'));
        $db_cid = !empty($cid) ? " AND (Kategorie = '" . $cid . "'" . $this->get_child_items($cid) . ")" : '';
        $products_items = array();
        $sql_products = $this->_db->query("SELECT
                 a.Preis,
                 a.Preis_Liste_Gueltig,
                 a.Preis_Liste,
                 a.Bild,
                 a.Id,
                 a.Kategorie,
                 b.Name_1 AS KatDefault,
                 a.Titel_" . $this->lc . " AS Titel,
                 b.Name_" . $this->lc . " AS Kategorie_Name
          FROM
                 " . PREFIX . "_shop_produkte AS a,
                 " . PREFIX . "_shop_kategorie AS b
          WHERE
                 a.Sektion=" . $_SESSION['area'] . "
          AND
                 b.Aktiv = '1'
          AND
                 a.Aktiv = '1'
                 {$db_cid}
          AND
                 b.Id = a.Kategorie
          AND
                 b.Sektion = " . $_SESSION['area'] . "
                 {$dbw}
                 " . $this->whereGroup('a.Gruppen') . "
                 " . $this->whereGroup('b.Gruppen') . "
          ORDER BY {$oby} LIMIT " . $this->settings['Tab_Limit']);

        while ($row_products = $sql_products->fetch_assoc()) {
            if (!$row_products['Kategorie_Name']) {
                $row_products['Kategorie_Name'] = $row_products['KatDefault'];
            }
            if (($row_products['Preis_Liste'] > $row_products['Preis']) && ($row_products['Preis_Liste'] > 0) && ($row_products['Preis_Liste_Gueltig'] == 0) || ($row_products['Preis_Liste'] > $row_products['Preis'] && $row_products['Preis_Liste_Gueltig'] != 0 && $row_products['Preis_Liste_Gueltig'] < $this->stime)) {
                $row_products['Preis'] = $row_products['Preis_Liste'];
            }

            $row_products['Bild_Klein'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_small']);
            $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']);
            $row_products['Preis'] = $this->getPrice($row_products['Preis']);
            $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
            $row_products['Preis'] = ($this->justNetto($_SESSION['user_country'])) ? $row_products['netto_price'] : $row_products['Preis'];
            if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1) {
                $row_products['price_ust_ex'] = $row_products['netto_price_orig'] - $row_products['netto_price'];
            }
            $row_products['product_ust'] = $this->getUst($row_products['Kategorie']);
            $row_products['ProdLink'] = "index.php?p=shop&amp;action=showproduct&amp;id=" . $row_products['Id'] . "&amp;cid=" . $row_products['Kategorie'] . "&amp;pname=" . translit($row_products['Titel']);
            $products_items[] = $row_products;
        }
        $sql_products->close();

        $tpl_array = array(
            'price_onlynetto' => ($this->justNetto($_SESSION['user_country']) ? 1 : 0),
            'no_nettodisplay' => ($this->noNettoDisplay($_SESSION['user_country']) ? 1 : 0));
        $this->_view->assign($tpl_array);
        return $products_items;
    }

    public function whereGroup($field = 'Gruppen') {
        $group = intval($_SESSION['user_group']);
        $where = " AND (" . $field . " = '' OR " . $field . " = '" . $group . "' OR " . $field . " LIKE '%," . $group . "' OR " . $field . " LIKE '" . $group . ",%' OR " . $field . " LIKE '%," . $group . ",%') ";
        return $where;
    }

    public function browseShop($shop_start = 0) {
        if ($this->settings['TopNewOffers'] == 1) {
            $new = $this->showTopByCateg('new');
            if (!empty($new)) {
                $this->_view->assign('tab_items', $new);
                $this->_view->assign('newin_shop', $this->_view->fetch(THEME . '/shop/shop_browse_tabs.tpl'));
            }
            $topseller = $this->showTopByCateg('topseller');
            if (!empty($topseller)) {
                $this->_view->assign('tab_items', $topseller);
                $this->_view->assign('topseller_shop', $this->_view->fetch(THEME . '/shop/shop_browse_tabs.tpl'));
            }
            $offers = $this->showTopByCateg('offers');
            if (!empty($offers)) {
                $this->_view->assign('tab_items', $offers);
                $this->_view->assign('offers_shop', $this->_view->fetch(THEME . '/shop/shop_browse_tabs.tpl'));
            }
        }

        $this->_view->assign('products', $this->listProducts($this->_colums, $shop_start, 0));
        $this->_view->assign('shop_products', $this->_view->fetch(THEME . '/shop/' . $this->settings['Template_Produkte'] . '.tpl'));

        if (empty($_REQUEST['cid'])) {
            $headernav = '<a href="index.php?p=shop&amp;start=1">' . $this->_lang['Shop'] . '</a>';
            $pagetitle = $this->_lang['ShopAllProducts'];
        } else {
            $headernav = $this->__object('Navigation')->path($_REQUEST['cid'], 'shop_kategorie', 'shop', 'cid', 'Id', "Name_{$this->lc}", '', $this->_lang['Shop']);
            $pagetitle = SX::get('options.shop_title_seo', $this->_lang['ShopAllProducts']);
        }

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($pagetitle . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Shop']),
            'content'   => $this->_view->fetch(THEME . '/shop/' . $this->_shop_browse_tpl));
        $this->_view->finish($seo_array);
    }

    public function getSubCategs($cid) {
        $this->getLoadShopCategs();
        $subcategs = array();
        foreach ($this->_shop_categs as $row) {
            if ($row->Parent_Id == $cid) {
                if (!$row->Name) {
                    $row->Name = $row->DefName;
                }
                $row->NumCount = $row->catid;
                $row->ParentC = $this->get_parent_shopcateg($row->catid);
                $get_parent_shopcateg = $this->get_parent_shopcateg($row->ParentC);
                $row->NavOp = ($get_parent_shopcateg == 0) ? $cid : $get_parent_shopcateg;
                $row->BildKategorie = (!empty($row->Bild_Kategorie) && is_file(UPLOADS_DIR . '/shop/icons_categs/' . $row->Bild_Kategorie)) ? $row->Bild_Kategorie : '';
                $subcategs[] = $row;
            }
        }
        return $subcategs;
    }

    public function getPrice($price) {
        if (!isset($this->_shop_params['Rabatt'])) {
            $this->getCurentUserGroupParams();
        }
        $price = $this->getRabatt($price, $this->_shop_params['Rabatt']);
        return $this->getMultiplikator($price);
    }

    public function getMultiplikator($price) {
        $price = !empty($_SESSION['Multiplikator']) ? $price * $_SESSION['Multiplikator'] : $price;
        return str_replace(',', '.', $price);
    }

    public function checkDiscount() {
        if (!isset($this->_shop_params['ShopAnzeige'])) {
            $this->getCurentUserGroupParams();
        }
        if ($this->_shop_params['ShopAnzeige'] >= 0.01) {
            $this->_view->assign(array('Discount' => 1, 'Discount_Val' => $this->_shop_params['ShopAnzeige']));
        }
    }

    public function userNettoshop() {
        if (!isset($this->_shop_params['ShopAnzeige'])) {
            $this->getCurentUserGroupParams();
        }
        if ($this->_shop_params['ShopAnzeige'] == 'b2b') {
            return 1;
        }
    }

    public function getCurentUserGroupParams() {
        $row = $this->_db->cache_fetch_assoc("SELECT Rabatt, ShopAnzeige FROM " . PREFIX . "_benutzer_gruppen WHERE Id = '" . $_SESSION['user_group'] . "' LIMIT 1");
        $this->_shop_params['Rabatt'] = $row['Rabatt'];
        $this->_shop_params['ShopAnzeige'] = $row['ShopAnzeige'];
    }

    public function listAllSeenProducts($showall = 0) {
        if (!empty($_REQUEST['subaction'])) {
            switch ($_REQUEST['subaction']) {
                case 'sendfriend':
                    if (!empty($_POST['prodid']) && !empty($_POST['prod_tell_email'])) {
                        $prod_tell = '';
                        $prefix = SX::protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                        foreach ($_POST['prodid'] as $id => $key) {
                            $arr = $this->_db->cache_fetch_assoc("SELECT Titel_1 AS TitelDef, Titel_" . $this->lc . " AS Titel FROM " . PREFIX . "_shop_produkte WHERE Id = '" . intval($id) . "' " . $this->whereGroup('Gruppen') . " LIMIT 1");
                            if (!$arr['Titel']) {
                                $arr['Titel'] = $arr['TitelDef'];
                            }
                            $prod_tell .= $arr['Titel'] . "\n" . $prefix . "?area=" . AREA . "&lang=" . $_SESSION['lang'] . "&p=shop&action=showproduct&id={$id}&cid={$key}\n\n";
                        }
                        $mail_array = array(
                            '__N__'          => "\n",
                            '__NAME__'       => $this->_text->substr($_POST['prod_tell_name'], 0, 100),
                            '__ARTICLES__'   => $prod_tell,
                            '__SENDER__'     => $this->_text->substr($_POST['prod_tell_rname'], 0, 100),
                            '__SENDERMAIL__' => $this->_text->substr($_POST['prod_tell_remail'], 0, 100));
                        $text = $this->_text->replace($this->_lang['Shop_tellSeenText'], $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $_POST['prod_tell_email'],
                            'to_name'   => $_POST['prod_tell_name'],
                            'text'      => $text,
                            'subject'   => $this->_lang['Shop_tellSubject'],
                            'fromemail' => $this->settings['Email_Abs'],
                            'from'      => $this->settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 3));
                        $this->_view->assign('send', 1);
                    } else {
                        $this->_view->assign('nocheckbox', 1);
                    }
                    break;

                case 'del':
                    if (!empty($_POST['prodid'])) {
                        foreach ($_POST['prodid'] as $id => $key) {
                            unset($_SESSION['prod_seen'][$id]);
                        }
                    } else {
                        $this->_view->assign('nocheckbox', 1);
                    }
                    break;

                case 'merge':
                    if (!empty($_POST['prodid'])) {
                        foreach ($_POST['prodid'] as $id => $key) {
                            $this->mergeProduct($id, $key);
                        }
                        SX::output("<script type=\"text/javascript\">window.open('index.php?redir=1&p=misc&do=mergeproduct&prodid=$id&red=&cid=$key', 'merge_win', 'scrollbars=1,width=900,height=600,top=0,left=0');</script>");
                    } else {
                        $this->_view->assign('nocheckbox', 1);
                    }
                    break;
            }
        }
        $this->_view->assign('seen_products_array', $this->showProductsVarious($showall));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_showSeenProducts'],
            'pagetitle' => $this->_lang['Shop_showSeenProducts'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/all_seen_products.tpl'));
        $this->_view->finish($seo_array);
    }

    public function showProductsVarious($showall = 0) {
        $lim_ls = ($showall != '0') ? 300 : 3;
        $Array = (isset($_SESSION['prod_seen']) && is_array($_SESSION['prod_seen'])) ? $_SESSION['prod_seen'] : 0;

        if (is_array($Array) && !empty($Array)) {
            foreach ($Array as $val => $key) {
                $ps[] = $val;
            }
            krsort($ps);
            $seen_products = array();
            $seen_count = 1;
            foreach ($ps as $val => $key) {
                if ($seen_count <= $lim_ls) {
                    $row_products = $this->_db->cache_fetch_assoc("SELECT
                            a.*,
                            a.Titel_" . $this->lc . " AS Titel,
                            a.Beschreibung_" . $this->lc . " AS Beschreibung,
                            b.Name_1 AS KatDefault,
                            b.Name_" . $this->lc . " AS Kategorie_Name
                    FROM
                            " . PREFIX . "_shop_produkte AS a,
                            " . PREFIX . "_shop_kategorie AS b
                    WHERE
                            a.Aktiv = '1'
                    AND
                            b.Aktiv = '1'
                    AND
                            a.Id = '" . $key . "'
                    AND
                            b.Id = a.Kategorie
                    AND
                            b.Sektion = '" . $_SESSION['area'] . "'
                            " . $this->whereGroup('a.Gruppen') . "
                            " . $this->whereGroup('b.Gruppen') . "
                    LIMIT 1");

                    if (is_array($row_products)) {
                        if (!$row_products['Kategorie_Name']) {
                            $row_products['Kategorie_Name'] = $row_products['KatDefault'];
                        }
                        if (($row_products['Preis_Liste'] > $row_products['Preis']) && ($row_products['Preis_Liste'] > 0) && ($row_products['Preis_Liste_Gueltig'] == 0) || ($row_products['Preis_Liste'] > $row_products['Preis'] && $row_products['Preis_Liste_Gueltig'] != 0 && $row_products['Preis_Liste_Gueltig'] < $this->stime)) {
                            $row_products['Preis'] = $row_products['Preis_Liste'];
                        }

                        $row_products['Bild_Klein'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_small']);
                        $row_products['Beschreibung'] = Tool::cleanVideo($row_products['Beschreibung']);
                        $row_products['netto_price_orig'] = $row_products['Preis'] = $this->getPrice($row_products['Preis']);
                        $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
                        $row_products['Preis'] = ($this->justNetto($_SESSION['user_country'])) ? $row_products['netto_price'] : $row_products['Preis'];

                        if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1) {
                            $row_products['price_ust_ex'] = $row_products['netto_price_orig'] - $row_products['netto_price'];
                        }
                        $row_products['product_ust'] = $this->getUst($row_products['Kategorie']);
                        $row_products['ProdLink'] = 'index.php?p=shop&amp;action=showproduct&amp;id=' . $row_products['Id'] . '&amp;cid=' . $row_products['Kategorie'] . '&amp;pname=' . translit($row_products['Titel']);
                        $r_varcheck = $this->_db->cache_fetch_object("SELECT DISTINCT(Id) FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $row_products['Id'] . "'");
                        if (is_object($r_varcheck) && $r_varcheck->Id >= 1) {
                            $row_products['Vars'] = 1;
                        }
                        $seen_products[] = $row_products;
                        $seen_count++;
                    } else {
                        unset($_SESSION['prod_seen'][$key]);
                    }
                }
            }
        }
        $tpl_array = array(
            'price_onlynetto' => ($this->justNetto($_SESSION['user_country']) ? 1 : 0),
            'no_nettodisplay' => ($this->noNettoDisplay($_SESSION['user_country']) ? 1 : 0));
        $this->_view->assign($tpl_array);

        if (!empty($seen_products)) {
            $this->_view->assign('is_seen_products', 1);
            return $seen_products;
        }
    }

    public function shippingTime($prod_id) {
        $r = $this->_db->cache_fetch_object("SELECT Lieferzeit_" . $this->lc . " AS Lieferzeit FROM " . PREFIX . "_shop_packzeiten WHERE Id = '" . intval($prod_id) . "' LIMIT 1");
        return is_object($r) ? $r->Lieferzeit : '';
    }

    public function displayProduct($merge = 0, $id = 0, $smallimages = 0) {
        $red = base64_encode($this->__object('Redir')->link());
        $this->getShopPriceAlert($red);

        if (Arr::getPost('subaction') == 'product_request' || $this->settings['AnfrageForm'] == '1') {
            $this->getShopRequest(); // Отправка вопроса по товару
        }
        if (get_active('shop_bewertung')) {
            $this->getShopBewertung(); // Вывод отзывов о товаре
        }

        unset($_SESSION['r_land']);
        $just_netto = $this->justNetto($_SESSION['user_country']) ? true : false;
        $_SESSION['prod_seen'][intval(Arr::getRequest('id'))] = $this->stime;
        if ($id != '0') {
            $_REQUEST['id'] = $id;
        }

        $count = $count_vars = $price_ext_first = $price_ext = $vars = $vars_second = $Bilder_Klein = '';

        $q_l = $this->getSpez('a.');
        $row_products = $this->_db->cache_fetch_assoc("SELECT
                {$q_l},
                a.Zub_c AS Tuningteile,
                a.Titel_$this->lc AS Titel,
                a.Beschreibung_$this->lc AS Beschreibung,
                a.Beschreibung_lang_$this->lc AS BeschreibungLang,
                a.Beschreibung_1 AS BeschreibungDef,
                a.Beschreibung_lang_1 AS BeschreibungLangDef,
                b.Name_1 AS KategorieName
        FROM
                " . PREFIX . "_shop_produkte AS a,
                " . PREFIX . "_shop_kategorie AS b
        WHERE
                a.Aktiv = '1'
        AND
                b.Id = a.Kategorie
        AND
                b.Aktiv = '1'
        AND
                a.Id = '" . intval(Arr::getRequest('id')) . "'
                " . $this->whereGroup('a.Gruppen') . "
                " . $this->whereGroup('b.Gruppen') . "
        LIMIT 1");

        if (!is_array($row_products)) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&area=' . $_SESSION['area']);
        } else {
            if ($row_products['Sektion'] != $_SESSION['area']) {
                $_SESSION['area'] = $row_products['Sektion'];
                $this->__object('Redir')->seoRedirect('index.php?p=shop&area=' . $_SESSION['area'] . '&action=showproduct&id=' . $row_products['Id'] . '&cid=' . $row_products['Kategorie'] . '&pname=' . $_REQUEST['pname']);
            }

            if (!isset($_SESSION['product_view_'][$row_products['Id']])) {
                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Klicks=Klicks+1 WHERE Id = '" . $row_products['Id'] . "'");
                $_SESSION['product_view_'][$row_products['Id']] = 'true';
            }

            $row_products['PriceGroup'] = $row_products['Preis_Liste']; // Сохраняем начальную цену для вывода скидок для групп
            if (!$row_products['Beschreibung']) {
                $row_products['Beschreibung'] = $row_products['BeschreibungDef'];
            }
            if (!$row_products['BeschreibungLang']) {
                $row_products['BeschreibungLang'] = $row_products['BeschreibungLangDef'];
            }
            $row_products['Beschreibung'] = $this->__object('Media')->get($row_products['Beschreibung']);
            $row_products['BeschreibungLang'] = $this->__object('Media')->get($row_products['BeschreibungLang']);
            $row_products['no_vars'] = '1';
            $row_products['BeschreibungLang'] = $this->getShopArtpage($row_products); // Вывод постраничной навигации в описании товара
            $num_check = $this->_db->cache_num_rows("SELECT DISTINCT (ArtId) FROM " . PREFIX . "_shop_varianten WHERE ArtId='" . $row_products['Id'] . "'");
            if ($num_check >= 1) {
                $vars_categs = array();
                $sql_vars = $this->_db->query("SELECT Id AS Kat_Id, Name_" . $this->lc . " AS Kat_Name, Beschreibung_" . $this->lc . " AS Kat_Beschreibung FROM " . PREFIX . "_shop_varianten_kategorien WHERE Aktiv = 1 AND KatId = '" . intval(Arr::getRequest('cid')) . "'");

                while ($row_array = $sql_vars->fetch_assoc()) {
                    $vars_options = array();
                    $sql_vars_options = $this->_db->query("SELECT *, Name_" . $this->lc . " AS VarName FROM " . PREFIX . "_shop_varianten WHERE KatId='" . $row_array['Kat_Id'] . "' AND ArtId='" . intval(Arr::getRequest('id')) . "' ORDER BY Position ASC");
                    while ($row_vars_array = $sql_vars_options->fetch_assoc()) {
                        if ($row_vars_array['Bestand'] < 1) {
                            $row_vars_array['Disabled'] = 1;
                        }
                        if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1 || $this->justNetto($_SESSION['user_country'])) {
                            $row_vars_array['Price'] = $row_vars_array['Operant'] . $this->getNettoPrice($this->getPrice($row_vars_array['Wert']), $row_products['Kategorie']);
                        } else {
                            $row_vars_array['Price'] = $row_vars_array['Operant'] . $this->getPrice($row_vars_array['Wert']);
                        }
                        $vars_options[] = $row_vars_array;
                    }
                    $sql_vars_options->close();
                    $row_array['vars'] = $vars_options;
                    if ($row_array['vars']) {
                        $vars_categs[] = $row_array;
                    }
                }

                $row_products['no_vars'] = '0';
                $this->_view->assign('vars_categs', $vars_categs);
            }
            $this->_view->assign('no_vars', 1);

            if (empty($row_products['Bild'])) {
                $row_products['NoBild'] = 1;
            }
            if ($smallimages == 1) {
                $row_products['Bild'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_middle']);
            } else {
                $row_products['BildPopLink'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_big']);
                $row_products['Bild'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_norm']);
            }
            $row_products['Lieferzeit'] = $this->shippingTime($row_products['Lieferzeit']);
            $row_products['VIcon'] = $this->getAvIcon($row_products['Bestellt'], $row_products['Lagerbestand'], $row_products['Verfuegbar']);
            $row_products['VMsg'] = $this->getAvMsg($row_products['Bestellt'], $row_products['Lagerbestand'], $row_products['Verfuegbar']);
            $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']) + $price_ext + $price_ext_first;
            $row_products['netto_price_orig_list'] = $this->getPrice($row_products['Preis_Liste']) + $price_ext + $price_ext_first;
            $row_products['Preis'] = $this->getPrice($row_products['Preis']) + $price_ext + $price_ext_first;
            $row_products['Preis_Liste'] = $this->getPrice($row_products['Preis_Liste']) + $price_ext + $price_ext_first;
            $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
            $row_products['netto_price_list'] = $this->getNettoPrice($row_products['Preis_Liste'], $row_products['Kategorie']);
            $row_products['Preis_Liste'] = ($just_netto == true) ? $row_products['netto_price_list'] : $row_products['Preis_Liste'];
            if (($row_products['Preis_Liste'] > 0) && ($row_products['Preis_Liste_Gueltig'] == 0) || ($row_products['Preis_Liste_Gueltig'] != 0 && $row_products['Preis_Liste_Gueltig'] < $this->stime)) {
                $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis_Liste']) + $price_ext + $price_ext_first;
                $row_products['Preis'] = ($just_netto == true) ? $row_products['netto_price_list'] : $row_products['Preis_Liste'];
                $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
                $row_products['Preis_Liste'] = 0;
            } elseif (($row_products['Preis_Liste'] > $row_products['Preis'])) {
                $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']) + $price_ext + $price_ext_first;
                $row_products['Preis'] = ($just_netto == true) ? $row_products['netto_price'] : $row_products['Preis'];
                $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
                $row_products['Angebot'] = 1;
            } else {
                $row_products['Preis_Liste'] = ($just_netto == true) ? $row_products['netto_price_list'] : $row_products['Preis_Liste'];
                $row_products['Preis'] = ($just_netto == true) ? $row_products['netto_price'] : $row_products['Preis'];
                $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']) + $price_ext + $price_ext_first;
                $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
            }

            $this->_view->assign('Pap', number_format($row_products['Preis'] - 1.00, 2, '.', ''));

            $row_products['product_ust'] = $this->getUst($row_products['Kategorie']);
            if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1) {
                $row_products['price_ust_ex'] = $row_products['Preis'] * ($row_products['product_ust'] / 100);
            }
            $row_products['product_ust_js'] = ($row_products['product_ust'] < 10) ? '1.0' . str_replace('.', '', $row_products['product_ust']) : '1.' . str_replace('.', '', $row_products['product_ust']);
            if ($row_products['EinheitId'] > 0 && $row_products['EinheitCount'] != 0.00) {
                $EinheitArray = $this->getUnits($row_products['EinheitId']);
                $row_products['Einheit'] = $EinheitArray['Einheit'];
                $row_products['EinheitMz'] = $EinheitArray['EinheitMz'];
                $row_products['EinheitOut'] = ($row_products['EinheitCount'] > 1) ? $EinheitArray['EinheitMz'] : $EinheitArray['Einheit'];
                $row_products['EinheitPreisEinzel'] = $row_products['Preis'] / $row_products['EinheitCount'];
                $row_products['EinheitPreisEinzelNetto'] = $row_products['netto_price'] / $row_products['EinheitCount'];
            }

            $headernav = $this->__object('Navigation')->path($row_products['Kategorie'], 'shop_kategorie', 'shop', 'cid', 'Id', 'Name_' . $this->lc, '', $this->_lang['Shop']);
            $pagetitle = $row_products['Titel'];
            $row_products['Parent'] = $this->get_parent_shopcateg($row_products['Kategorie']);

            $get_parent_shopcateg = $this->get_parent_shopcateg($row_products['Parent']);
            $row_products['Navop'] = ($get_parent_shopcateg == 0) ? $row_products['Parent'] : $get_parent_shopcateg;
            $row_products['ProdLink'] = "index.php?p=shop&amp;action=showproduct&amp;id=" . $row_products['Id'] . "&amp;cid=" . $row_products['Kategorie'] . "&amp;pname=" . translit($row_products['Titel']);
            if ($count == $count_vars) {
                $this->_view->assign('to_basket', 1);
            }

            $q_l = $this->getSpez();
            $det_spez = $this->_db->cache_fetch_assoc("SELECT $q_l FROM " . PREFIX . "_shop_kategorie_spezifikation WHERE Kategorie = '" . $row_products['Kategorie'] . "' LIMIT 1");
            if (is_array($det_spez) && $det_spez['Spez_1']) {
                $this->_view->assign('det_spez', $det_spez);
            }
            if (!empty($row_products['Bilder'])) {
                $Bilder_Klein = array();
                $bilder = explode('|', $row_products['Bilder']);
                $count = 1;
                foreach ($bilder as $bild) {
                    if ($count <= 20) {
                        $arr['Bild'] = Tool::thumb('shop', $bild, $this->settings['thumb_width_small']);
                        $arr['Bild_Normal'] = Tool::thumb('shop', $bild, $this->settings['thumb_width_norm']);
                        $arr['Bild_GrossLink'] = Tool::thumb('shop', $bild, $this->settings['thumb_width_big']);
                        if (!empty($bild)) {
                            $Bilder_Klein[] = $arr;
                        }
                    }
                    $count++;
                }
            } else {
                $this->_view->assign('img_pop', 1);
            }

            if ($merge == 1) {
                $row_products['count'] = $count;
                return $row_products;
            } else {
                if (!empty($row_products['Schlagwoerter'])) {
                    $this->_view->assign('Zub_d_products_array', $this->similarProducts($row_products['Schlagwoerter']));
                    $this->_view->assign('Zub_d_products', $this->_view->fetch(THEME . '/shop/small_similar_products.tpl'));
                }

                if (!empty($row_products['Zub_a'])) {
                    $this->_view->assign('Zub_a_products_array', $this->similarProducts($row_products['Zub_a'], 1, 2));
                    $this->_view->assign('Zub_a_products', $this->_view->fetch(THEME . '/shop/small_zuba_products.tpl'));
                }

                if (!empty($row_products['Zub_b'])) {
                    $this->_view->assign('Zub_b_products_array', $this->similarProducts($row_products['Zub_b'], 1, 3));
                    $this->_view->assign('Zub_b_products', $this->_view->fetch(THEME . '/shop/small_zubb_products.tpl'));
                }

                if (!empty($row_products['Zub_c'])) {
                    $this->_view->assign('Zub_c_products_array', $this->similarProducts($row_products['Zub_c'], 1));
                    $this->_view->assign('Zub_c_products', $this->_view->fetch(THEME . '/shop/small_zubc_products.tpl'));
                }

                if ($row_products['Lagerbestand'] <= $this->settings['Lager_Gering']) {
                    $this->_view->assign('low_amount', 1);
                }
                if ($row_products['Lagerbestand'] == '0' && $row_products['Verfuegbar'] != 4) {
                    $this->_view->assign('not_on_store', 1);
                    $this->_view->assign('not_available', 1);
                }

                if ($row_products['Verfuegbar'] == 4) {
                    $this->_view->assign('order_for_you', 1);
                }
                $Tabs = $this->_db->cache_fetch_object("SELECT Teile_1_Name_{$this->lc} AS TAB1, Teile_2_Name_{$this->lc} AS TAB2, Teile_3_Name_{$this->lc} AS TAB3 FROM " . PREFIX . "_shop_kategorie_zubehoer WHERE Kategorie = '" . $row_products['Kategorie'] . "' LIMIT 1");
                if (empty($Tabs->TAB1)) {
                    $Tabs->TAB1 = $this->_lang['Shop_accessories'];
                }
                if (empty($Tabs->TAB2)) {
                    $Tabs->TAB2 = $this->_lang['Shop_parts'];
                }
                if (empty($Tabs->TAB3)) {
                    $Tabs->TAB3 = $this->_lang['Shop_tuningparts'];
                }
                $this->_view->assign('tabs', $Tabs);
                $p_free = $this->getFreeShippingByCountry();
                $p_price = $row_products['Preis'];
                $this->_view->assign('shipping_free', ($p_price >= $p_free && $p_free > 0 ? 1 : 0));
                $row_products['diff'] = $row_products['Preis_Liste'] - $row_products['Preis'];
                if ($row_products['diff'] > 0) {
                    $row_products['diffpro'] = ($row_products['diff'] * 100) / $row_products['Preis_Liste'];
                }
                $row_products['valid_till_info'] = $this->_lang['Shop_valid_to'] . ' ' . date('d.m.Y', $row_products['Preis_Liste_Gueltig']);
                $row_products['man'] = $this->getManufacturerById($row_products['Hersteller']);
                $row_products['GewichtF'] = File::filesize(($row_products['Gewicht'] / 1000), 1);
                $row_products['GewichtRaw'] = ($row_products['Gewicht_Ohne'] >= 1) ? File::filesize(($row_products['Gewicht_Ohne'] / 1000), 1) : 0;
                if ($row_products['EinheitBezug'] == 0.00) {
                    $row_products['EinheitBezug'] = '';
                }

                if (!empty($row_products['Downloads'])) {
                    $Downloads = array();
                    $files = explode('@@', $row_products['Downloads']);
                    foreach ($files as $file_content) {
                        $row = '';
                        $line_fdl = explode('||', $file_content);
                        $row->Name = $line_fdl[0];
                        $row->Text = $line_fdl[1];
                        $row->Link = $line_fdl[2];
                        $Downloads[] = $row;
                    }
                    $this->_view->assign('Downloads', $Downloads);
                }

                $tout = (!empty($row_products['Template']) && is_file(THEME . '/shop_product_custom/' . $row_products['Template'])) ? '/shop_product_custom/' . $row_products['Template'] : '/shop/' . $this->_product_detail_tpl;

                if ($this->settings['PriceGroup'] == '1') {
                    $this->getPriceGroup($row_products['PriceGroup']); // Вывод цен со скидкой для групп
                }

                $this->setCheaper($row_products['Titel'], $row_products['Preis']); // Вывод виджета Хочу Дешевле

                $tpl_array = array(
                    'whole_name'      => Tool::fullName(),
                    'CategName'       => $row_products['KategorieName'],
                    'prod_downloads'  => $this->productFiles($row_products['Id']),
                    'p_title'         => $row_products['Titel'],
                    'st_prices'       => $this->staffelPreise($row_products['Id']),
                    'red'             => $red,
                    'count'           => $count,
                    'images'          => $Bilder_Klein,
                    'vars'            => $vars,
                    'vars_second'     => $vars_second,
                    'price_onlynetto' => ($this->justNetto($_SESSION['user_country']) ? 1 : 0),
                    'p'               => $row_products,
                    'TaxValue'        => $this->getUstByCateg($row_products['Kategorie']),
                    'redir'           => $this->__object('Redir')->link());
                $this->_view->assign($tpl_array);

                $seo_array = array(
                    'headernav'        => $headernav,
                    'pagetitle'        => sanitize((!empty($row_products['SeitenTitel']) ? $row_products['SeitenTitel'] : $pagetitle) . Tool::numPage('artpage') . $this->_lang['PageSep'] . $this->_lang['Shop']),
                    'tags_keywords'    => $row_products['MetaTags'],
                    'tags_description' => $row_products['MetaDescription'],
                    'generate'         => $row_products['Titel'] . ' ' . $row_products['Beschreibung'],
                    'content'          => $this->_view->fetch(THEME . $tout));
                $this->_view->finish($seo_array);
            }
        }
    }

    /* Метод вывода постраничной навигации в описании товара */
    public function getShopPriceAlert($red) {
        if (Arr::getPost('pricealert_send') == 1) {
            $_POST['pricealert_newprice'] = number_format(str_replace(',', '.', $_POST['pricealert_newprice']), '2', '.', '');
            $error = array();
            if (!Tool::isMail($_POST['pricealert_email'])) {
                $error[] = $this->_lang['Comment_NoEmail'];
            }
            if (!is_numeric($_POST['pricealert_newprice']) || $_POST['pricealert_newprice'] < '1') {
                $error[] = $this->_lang['Shop_priceAlertError'];
            }
            $this->_view->assign('error', $error);
            if (empty($error)) {
                $id = intval(Arr::getRequest('id'));
                $mail = Arr::getPost('pricealert_email');
                $res = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_shop_preisalarm WHERE ProdId = '" . $id . "' AND Email = '" . $this->_db->escape($mail) . "' LIMIT 1");
                if (!is_object($res)) {
                    $row = $this->getArticleById($id);
                    $insert_array = array(
                        'ProdId' => $row->Id,
                        'Email'  => $mail,
                        'Datum'  => $this->stime,
                        'Ip'     => IP_USER,
                        'Preis'  => Arr::getPost('pricealert_newprice'));
                    $this->_db->insert_query('shop_preisalarm', $insert_array);

                    $mail_array = array(
                        '__IP__'       => IP_USER,
                        '__MAIL__'     => $mail,
                        '__USERNAME__' => Tool::fullName(),
                        '__DATUM__'    => date('d.m.Y', $this->stime),
                        '__PRODNAME__' => $row->Titel_1,
                        '__ID__'       => $row->Id,
                        '__LINK__'     => BASE_URL . '/index.php?p=shop&area=' . $row->Sektion . '&action=showproduct&id=' . $row->Id . '&cid=' . $row->Kategorie . '&pname=' . translit($row->Titel_1),
                        '__SUMM__'     => Arr::getPost('pricealert_newprice'),
                        '__URL__'      => BASE_URL);
                    $msg = $this->_text->replace($this->_lang['ShopPriceAlertEmail'], $mail_array);

                    $array_mail = explode(';', $this->settings['Email_Bestellung']);
                    foreach ($array_mail as $send_mail) {
                        if (!empty($send_mail)) {
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $send_mail,
                                'to_name'   => '',
                                'text'      => $msg,
                                'subject'   => $this->_lang['ShopPriceAlertEmailSubject'],
                                'fromemail' => $this->settings['Email_Abs'],
                                'from'      => $this->settings['Name_Abs'],
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 3));
                        }
                    }
                } else {
                    $this->__object('Core')->message('Shop_priceAlert', 'ShopPriceAlertDouble', base64_decode($_REQUEST['red']));
                }
                $this->__object('Core')->message('Shop_priceAlert', 'Shop_priceAlertOk', base64_decode($_REQUEST['red']));
            }
        }
        $this->_view->assign('red', $red);
        $this->_view->assign('price_alert', $this->_view->fetch(THEME . '/shop/shop_pricealert.tpl'));
    }

    /* Метод вывода постраничной навигации в описании товара */
    public function getShopArtpage($row_products) {
        $_REQUEST['artpage'] = (!empty($_REQUEST['artpage']) && $_REQUEST['artpage'] >= 1) ? intval($_REQUEST['artpage']) : 1;
        $seite_anzeigen = explode('[--NEU--]', $row_products['BeschreibungLang']);
        $anzahl_seiten = count($seite_anzeigen);
        if ($_REQUEST['artpage'] > $anzahl_seiten) {
            $_REQUEST['artpage'] = $anzahl_seiten;
            $row_products['BeschreibungLang'] = $seite_anzeigen[$anzahl_seiten - 1];
        } else {
            $row_products['BeschreibungLang'] = $seite_anzeigen[$_REQUEST['artpage'] - 1];
        }
        if ($anzahl_seiten > 1) {
            $article_pages = $this->__object('Navigation')->artpage($anzahl_seiten, $_REQUEST['artpage'], " <a class=\"page_navigation\" style=\"text-decoration:none\" href=\"index.php?p=shop&amp;action=showproduct&amp;id=" . translit($row_products['Id']) . "&amp;cid=" . $row_products['Kategorie'] . "&amp;pname=" . translit($row_products['Titel']) . "&amp;artpage={s}\">{t}</a> ");
            $this->_view->assign('article_pages', $article_pages);
        }
        return $row_products['BeschreibungLang'];
    }

    /* Метод вывода отзывов и отправка отзыва о товаре */
    public function getShopBewertung() {
        if (permission('shop_vote')) {
            if (Arr::getRequest('sub') == 'prod_vote') {
                $error = array();
                if (empty($_POST['prod_vote_text'])) {
                    $error[] = $this->_lang['Shop_prod_vote_noText'];
                }
                if ($this->__object('Captcha')->check($error, false, 'two')) {
                    $insert_array = array(
                        'Produkt'          => intval(Arr::getRequest('id')),
                        'Bewertung'        => Tool::cleanTags(Arr::getRequest('prod_vote_text'), array('codewidget')),
                        'Bewertung_Punkte' => Arr::getRequest('prod_vote_points', 0),
                        'Benutzer'         => $_SESSION['benutzer_id'],
                        'Datum'            => $this->stime,
                        'Ip'               => IP_USER,
                        'Offen'            => 0);
                    $this->_db->insert_query('shop_bewertung', $insert_array);

                    $mail_array = array(
                        '__N__'         => PE,
                        '__BENUTZER__'  => Tool::fullName(),
                        '__DATUM__'     => date('d.m.Y', $this->stime),
                        '__PRODNAME__'  => $_POST['prod_name'],
                        '__ID__'        => $_POST['id'],
                        '__ADMINLINK__' => BASE_URL . '/admin/index.php?do=shop&sub=prodvotes&id=' . $_POST['id'],
                        '__TEXT__'      => $_REQUEST['prod_vote_text']);
                    $msg = $this->_text->replace($this->_lang['Shop_prod_vote_textEmail'], $mail_array);

                    $array_mail = explode(';', $this->settings['Email_Bestellung']);
                    foreach ($array_mail as $send_mail) {
                        if (!empty($send_mail)) {
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $send_mail,
                                'to_name'   => '',
                                'text'      => $msg,
                                'subject'   => $this->_lang['Shop_prod_vote_mailsubject'],
                                'fromemail' => $this->settings['Email_Abs'],
                                'from'      => $this->settings['Name_Abs'],
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 3));
                        }
                    }
                    $this->__object('Core')->message('Shop_prod_vote_votesall', 'Shop_prod_vote_msg', base64_decode($_REQUEST['red']));
                }
            }
            $this->__object('Captcha')->start('two'); // Инициализация каптчи
        }

        $_REQUEST['id'] = Arr::getRequest('id');
        $votes = array();
        $sql_v = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_bewertung WHERE Produkt = '" . intval($_REQUEST['id']) . "' AND Offen = 1 ORDER BY Datum ASC");
        while ($row_v = $sql_v->fetch_object()) {
            $row_v->Benutzer = Tool::userName($row_v->Benutzer);
            $row_v->Bewertung = nl2br(strip_tags(sanitize($row_v->Bewertung)));
            $votes[] = $row_v;
        }
        $sql_v->close();

        $this->_view->assign(array('votes' => $votes, 'shop_bewertung' => 1));
    }

    /* Метод отправки вопроса по товару */
    protected function getShopRequest() {
        if (Arr::getPost('sub') == 'product_request') {
            $error = array();
            if (empty($_POST['product_request_email'])) {
                $error[] = $this->_lang['Comment_NoEmail'];
            }
            if (empty($_POST['product_request_name'])) {
                $error[] = $this->_lang['Comment_NoAuthor'];
            }
            if (empty($_POST['product_request_text'])) {
                $error[] = $this->_lang['No_Message'];
            }
            if ($this->__object('Captcha')->check($error)) {
                $mail_array = array(
                    '__N__'        => PE,
                    '__USER__'     => $_POST['product_request_name'],
                    '__MAIL__'     => $_POST['product_request_email'],
                    '__DATUM__'    => date('d.m.Y', $this->stime),
                    '__PRODNAME__' => $_POST['prod_name'],
                    '__ID__'       => $_POST['id'],
                    '__LINK__'     => BASE_URL . '/index.php?p=shop&action=showproduct&id=' . $_POST['id'] . '&cid=' . $_POST['cid'],
                    '__TEXT__'     => $_POST['product_request_text']);
                $msg = $this->_text->replace($this->_lang['Shop_prod_request_text'], $mail_array);

                $array_mail = explode(';', $this->settings['Email_Bestellung']);
                foreach ($array_mail as $send_mail) {
                    if (!empty($send_mail)) {
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $send_mail,
                            'to_name'   => '',
                            'text'      => $msg,
                            'subject'   => $this->_lang['Shop_prod_request_mailsubject'],
                            'fromemail' => $_POST['product_request_email'],
                            'from'      => $_POST['product_request_name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 3));
                    }
                }
                $this->_view->assign('msg_send', 1);
                $this->__object('Core')->message('Shop_prod_request_rtext', 'Shop_prod_request_thankyou', base64_decode($_REQUEST['red']));
            }
        }
        $this->__object('Captcha')->start(); // Инициализация каптчи
    }

    /*  Получаем файлы приложенные к товару */
    protected function productFiles($prodid) {
        $prodid = intval($prodid);
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS DlCount FROM " . PREFIX . "_shop_produkte_downloads WHERE ProduktId='" . $prodid . "'");
        if ($res->DlCount < 1) {
            return false;
        } else {
            $pdls = array();
            $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_produkte_downloads WHERE ProduktId='" . $prodid . "' ORDER BY DlName ASC");
            while ($row_pdls = $sql->fetch_object()) {
                $row_pdls->Size = File::filesize(filesize(UPLOADS_DIR . '/shop/product_downloads/' . $row_pdls->Datei) / 1024);
                $row_pdls->Icon = $this->fileIcon($row_pdls->Datei);
                $pdls[] = $row_pdls;
            }
            return $pdls;
        }
    }

    /* Выводим иконку типа файла */
    public function fileIcon($file) {
        $path_parts = $this->pathinfo_utf(UPLOADS_DIR . '/shop/product_downloads/' . $file);
        $fexp = strtolower($path_parts['extension']);
        $fend = (empty($fexp) || !is_file(THEME . '/images/filetypes/' . $fexp . '.png')) ? '_blank' : $fexp;
        return $fend . '.png';
    }

    public function getFreeShippingByCountry() {
        $CC = !empty($_SESSION['l_land']) && strlen($_SESSION['l_land']) == 2 ? $_SESSION['l_land'] : ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 1) && isset($_SESSION['r_land']) ? $_SESSION['r_land'] : $_SESSION['user_country']);
        if (Arr::getSession('loggedin') == 1 && isset($_SESSION['r_land']) && strlen($_SESSION['r_land']) == 2 && (isset($_SESSION['l_land']) && $_SESSION['l_land'] != $_SESSION['r_land']) && strlen($_SESSION['l_land']) == 2) {
            $CC = $_SESSION['l_land'];
        }
        $CCR = $this->_db->cache_fetch_object("SELECT VersandFreiAb FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape(strtoupper($CC)) . "' LIMIT 1");
        return $CCR->VersandFreiAb;
    }

    public function getUstByCateg($categ) {
        $row = $this->getShopWert($categ);
        return number_format($row->Wert, '2', '.', '');
    }

    public function similarProducts($words, $accessories = 0, $field = '1') {
        unset($_SESSION['r_land']);
        $id = intval(Arr::getRequest('id'));
        $fields = "
                Frei_1,
                Frei_2,
                Frei_3,
                Frei_1_Pflicht,
                Frei_2_Pflicht,
                Frei_3_Pflicht,
                Fsk18,
                Zub_a,
                Zub_b,
                Zub_c,
                MinBestellung,
                Preis_Liste,
                Preis_Liste_Gueltig,
                Preis,
                Bild,
                Id,
                Lagerbestand,
                Artikelnummer,
                Kategorie,
                Titel_" . $this->lc . " AS Titel,
                Beschreibung_" . $this->lc . " AS Beschreibung";

        $limit = $this->settings['Zubehoer_Limit'];
        if ($accessories == 0) {
            $array = explode(',', $words);
            $query = array();
            $query[] = "Schlagwoerter = '" . $this->_db->escape($words) . "'";
            foreach ($array as $value) {
                $value = $this->_db->escape($value);
                $query[] = "Schlagwoerter = '{$value}'";
                $query[] = "Schlagwoerter LIKE '{$value},%'";
                $query[] = "Schlagwoerter LIKE '%,{$value}'";
                $query[] = "Schlagwoerter LIKE '%,{$value},%'";
            }
            $query = ' AND (' . implode(' OR ', $query) . ')';

            $arr = $this->_db->query("SELECT {$fields} FROM " . PREFIX . "_shop_produkte
            WHERE
                Aktiv=1
                {$query}
            AND
                Id != '" . $id . "'
        ORDER BY " . $this->randQuery() . " LIMIT {$limit}");
        } else {
            $array = explode(',', $words);
            $query = array();
            foreach ($array as $value) {
                $query[] = "Id = '" . $this->_db->escape($value) . "'";
            }
            $query = ' AND (' . implode(' OR ', $query) . ')';

            if (Arr::getGet('tab') != $field) {
                $mpage = '1';
                $a = 0;
            } else {
                $mpage = '0';
                $a = Tool::getLimit($limit);
            }

            $arr = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS {$fields} FROM " . PREFIX . "_shop_produkte
            WHERE
                Aktiv=1
                {$query}
            AND
                Id != '" . $id . "'
            ORDER BY Titel_1 ASC LIMIT {$a},{$limit}");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);

            if ($num > $limit) {
                $page_current = 'p=shop&amp;area=' . Arr::getGet('area', 1) . '&amp;action=showproduct&amp;id=' . Arr::getGet('id') . '&amp;cid=' . Arr::getGet('cid') . '&amp;pname=' . Arr::getGet('pname') . '&amp;tab=' . $field . '#opt-' . $field;
                $this->_view->assign("pages{$field}", $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\" href=\"index.php?page={s}&amp;{$page_current}\">{t}</a> ", $mpage));
            }
        }

        $similar_products = array();
        while ($row_products = $arr->fetch_assoc()) {
            if (($row_products['Preis_Liste'] > $row_products['Preis']) && ($row_products['Preis_Liste'] > 0) && ($row_products['Preis_Liste_Gueltig'] == 0) || ($row_products['Preis_Liste'] > $row_products['Preis'] && $row_products['Preis_Liste_Gueltig'] != 0 && $row_products['Preis_Liste_Gueltig'] < $this->stime)) {
                $row_products['Preis'] = $row_products['Preis_Liste'];
            }

            $row_products['Bild_Klein'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_small']);
            $row_products['Preis'] = $this->getPrice($row_products['Preis']);
            $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
            $row_products['Preis'] = ($this->justNetto($_SESSION['user_country'])) ? $row_products['netto_price'] : $row_products['Preis'];
            if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1) {
                $row_products['price_ust_ex'] = $row_products['netto_price_orig'] - $row_products['netto_price'];
            }
            $row_products['ProdLink'] = "index.php?p=shop&amp;action=showproduct&amp;id=" . $row_products['Id'] . "&amp;cid=" . $row_products['Kategorie'] . "&amp;pname=" . translit(sanitize($row_products['Titel']));

            if ($accessories == 0) {
                $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']);
                $row_products['product_ust'] = $this->getUst($row_products['Kategorie']);
                $r_varcheck = $this->_db->cache_fetch_object("SELECT DISTINCT(Id) FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $row_products['Id'] . "' LIMIT 1");
                if (is_object($r_varcheck) && $r_varcheck->Id >= 1) {
                    $row_products['Vars'] = 1;
                }
            }
            $similar_products[] = $row_products;
        }
        $arr->close();
        return $similar_products;
    }

    public function staffelPreise($prodid) {
        $staffel = $this->_db->cache_fetch_object_all("SELECT * FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '" . intval($prodid) . "' ORDER BY Von ASC");
        return $staffel;
    }

    public function randQuery() {
        $array = array('Id', 'Kategorie', 'Artikelnummer', 'Preis', 'Preis_Liste', 'Preis_Liste_Gueltig', 'Titel_1', 'Erstellt', 'Klicks', 'Gewicht', 'Gewicht_Ohne', 'Verkauft');
        return Tool::randQuery($array);
    }

    /* Метод формирует данные для сортировки товаров */
    public function getSortable($array, $sort, &$db_sort, &$na_sort) {
        $result = array();
        foreach (array_keys($array) as $key) {
            $result[$key . '_sort'] = $key . '_asc';
            $result['img_' . $key . '_sort'] = 'sorter_none.png';
        }

        $name = explode('_', $sort);
        if (isset($name[0], $name[1], $array[$name[0]])) {
            switch ($name[1]) {
                default:
                case 'asc':
                    $result[$name[0] . '_sort'] = $name[0] . '_desc';
                    $result['img_' . $name[0] . '_sort'] = 'sorter_up.png';
                    if (isset($array[$name[0]]['convert'])) {
                        $db_sort = ' CONVERT(' . $array[$name[0]]['base'] . ', ' . $array[$name[0]]['convert'] . ') ASC ';
                    } else {
                        $db_sort = ' ' . $array[$name[0]]['base'] . ' ASC ';
                    }
                    $na_sort = '&amp;list=' . $name[0] . '_asc';
                    break;

                case 'desc':
                    $result[$name[0] . '_sort'] = $name[0] . '_asc';
                    $result['img_' . $name[0] . '_sort'] = 'sorter_down.png';
                    if (isset($array[$name[0]]['convert'])) {
                        $db_sort = ' CONVERT(' . $array[$name[0]]['base'] . ', ' . $array[$name[0]]['convert'] . ') DESC ';
                    } else {
                        $db_sort = ' ' . $array[$name[0]]['base'] . ' DESC ';
                    }
                    $na_sort = '&amp;list=' . $name[0] . '_desc';
                    break;
            }
        }
        $this->_view->assign($result);
    }

    public function listProducts($colums = 0, $shop_start = 0, $topseller = 0, $ex_limit = 0, $no_colums = 0, $recorder = 0, $angebote = 0) {
        $cat_search = $db_cid = $db_sort = $Title_S = $Manu_S = $na_sort = $is_offers = $is_topseller = $is_lowamount = $Price_s = $Price_n = $db_ss = '';
        $Products = array();

        if ($no_colums == 1) {
            $lim = $ex_limit;
            if ($recorder == 1) {
                $lim = 100;
            }
        } else {
            $lim = $shop_start == 1 ? $this->settings['Start_Limit'] : (!empty($_REQUEST['limit']) ? intval($_REQUEST['limit']) : $this->settings['Produkt_Limit_Seite']);
            $this->_colums = $colums != 0 ? $colums : 2;
            if ($ex_limit != 0) {
                $lim = $ex_limit;
            }

            if (empty($_REQUEST['shop_q'])) {
                $shop_q = 'shop_q=empty&amp;';
                $_REQUEST['shop_q'] = 'empty';
            }
            $search_s = trim(urldecode($_REQUEST['shop_q']));
            if (!empty($search_s) && $search_s != 'empty' && $this->_text->strlen($search_s) >= 2) {
                $this->__object('Core')->monitor($search_s, 'shop');
                $searcht = '"';
                $pos_start = $this->_text->strpos($search_s, $searcht);
                $pos_end = $this->_text->strpos($this->_text->strrev($search_s), $searcht);

                if (($pos_start !== false && $pos_start == '0' && $pos_end !== false && $pos_end == '0') || $this->settings['OnlyFhrase'] == 1) {
                    $trim_search = trim($search_s, $searcht);
                    $search_s = $this->_db->escape($trim_search);
                    $search_s_sys = $this->_db->escape(sanitize($trim_search));
                    $array[] = "a.Titel_$this->lc LIKE '%" . $search_s . "%'";
                    $array[] = "a.Titel_$this->lc LIKE '%" . $search_s_sys . "%'";
                    $array[] = "a.Beschreibung_$this->lc LIKE '%" . $search_s . "%'";
                    $array[] = "a.Beschreibung_$this->lc LIKE '%" . $search_s_sys . "%'";
                    $array[] = "a.Artikelnummer LIKE '%" . $search_s . "%'";
                    $array[] = "a.Artikelnummer LIKE '%" . $search_s_sys . "%'";
                    $array[] = "a.Artikelnummer LIKE '%" . str_replace(' ', '', $search_s) . "%'";
                    $array[] = "a.EAN_Nr = '" . $search_s . "'";
                    $array[] = "a.ISBN_NR = '" . $search_s . "'";
                    $array[] = "a.Schlagwoerter LIKE '%" . $search_s . "%'";
                    $array[] = "a.Schlagwoerter LIKE '%" . $search_s_sys . "%'";
                    $array[] = "a.MetaTags LIKE '%" . $search_s . "%'";
                    $array[] = "a.MetaTags LIKE '%" . $search_s_sys . "%'";
                    $array[] = "a.MetaDescription LIKE '%" . $search_s . "%'";
                    $array[] = "a.MetaDescription LIKE '%" . $search_s_sys . "%'";
                } else {
                    $pattern_or = explode(' ', $this->_text->lower($search_s));
                    $count_pattern = count($pattern_or);
                    foreach ($pattern_or as $sub_part) {
                        $trim_part = trim($sub_part);
                        $sub_part = $this->_db->escape($trim_part);
                        $sub_part_sys = $this->_db->escape(sanitize($trim_part));
                        $array[] = "a.Titel_$this->lc LIKE '%" . $sub_part . "%'";
                        $array[] = "a.Titel_$this->lc LIKE '%" . $sub_part_sys . "%' ";
                        $array[] = "a.Beschreibung_$this->lc LIKE '%" . $sub_part . "%' ";
                        $array[] = "a.Beschreibung_$this->lc LIKE '%" . $sub_part_sys . "%'";
                        $array[] = "a.Artikelnummer LIKE '%" . $sub_part . "%'";
                        $array[] = "a.Artikelnummer LIKE '%" . $sub_part_sys . "%'";
                        $array[] = "a.EAN_Nr = '" . $sub_part . "'";
                        $array[] = "a.ISBN_NR = '" . $sub_part . "'";
                        $array[] = "a.Schlagwoerter LIKE '%" . $sub_part . "%'";
                        $array[] = "a.Schlagwoerter LIKE '%" . $sub_part_sys . "%'";
                        $array[] = "a.MetaTags LIKE '%" . $sub_part . "%'";
                        $array[] = "a.MetaTags LIKE '%" . $sub_part_sys . "%'";
                        $array[] = "a.MetaDescription LIKE '%" . $sub_part . "%'";
                        $array[] = "a.MetaDescription LIKE '%" . $sub_part_sys . "%'";
                    }
                }
                if (!empty($array)) {
                    $Title_S = " (" . implode(' OR ', $array) . ") AND ";
                }
            }
            if (isset($_REQUEST['man'])) {
                $q_m = intval($_REQUEST['man']);
                $Manu_S = ($q_m != 0) ? " a.Hersteller = '$q_m' AND " : '';
            }
        }

        if ($shop_start == 1) {
            $db_ss = " AND a.Startseite = '1' ";
            $db_sort = " a.Erstellt DESC ";
        }

        if ($topseller == 1) {
            $lim = $ex_limit;
            $db_ss = " AND a.Startseite = '1' ";
            $db_sort = " a.Verkauft DESC ";
        }

        if ($angebote == 1) {
            $lim = $this->settings['Angebote_Limit'];
            $db_ss = " AND a.Startseite = '1' ";
            $db_sort = ' ' . $this->randQuery() . ' ';
            $Manu_S = " (a.Preis != a.Preis_Liste AND Preis != '0.00' AND a.Preis_Liste_Gueltig >= '" . $this->stime . "') AND ";
        }

        $_REQUEST['pf'] = $pf = number_format(intval(Arr::getRequest('pf', 0)), 2, '.', '');
        $_REQUEST['pt'] = $pt = number_format(intval(Arr::getRequest('pt', 100000000)), 2, '.', '');

        if ($pt > 0) {
            $Price_s = " AND ((a.Preis_Liste BETWEEN '" . $pf . "' AND '" . $pt . "') OR (a.Preis < a. Preis_Liste AND (a.Preis BETWEEN '" . $pf . "' AND '" . $pt . "') AND a.Preis_Liste_Gueltig >= '" . $this->stime . "')) ";
            $Price_n = '&amp;pf=' . $pf . '&amp;pt=' . $pt;
        }

        if (Arr::getRequest('offers') == 1) {
            $no_colums = 1;
            $db_ss = '';
            $db_sort = " Id DESC ";
            $Manu_S = " (a.Preis != a.Preis_Liste AND a.Preis != '0.00' AND a.Preis_Liste_Gueltig >= '" . $this->stime . "') AND ";
            $is_offers = '&amp;offers=1';
        }

        if (Arr::getRequest('topseller') == 1) {
            $no_colums = 1;
            $db_ss = '';
            $db_sort = " a.Verkauft DESC ";
            $is_topseller = '&amp;topseller=1';
        }

        if (Arr::getRequest('lowamount') == 1) {
            $no_colums = 1;
            $db_ss = '';
            $db_sort = " a.Lagerbestand ASC ";
            $Manu_S = " (a.Lagerbestand <= " . $this->settings['Lager_Gering'] . ") AND ";
            $is_lowamount = '&amp;lowamount=1';
        }

        $cid = intval(Arr::getRequest('cid'));
        $kat_array = $this->_db->cache_fetch_assoc("SELECT Name_{$this->lc} AS Name, Beschreibung_{$this->lc} AS Beschreibung FROM " . PREFIX . "_shop_kategorie WHERE Sektion = '" . $_SESSION['area'] . "' AND Id = '" . $cid . "' LIMIT 1");

        $num = $a = 0;
        $limit = $lim;

        if (!empty($cid)) {
            $search_in_child = ($this->settings['ArtikelBeiKateg'] != 1) ? $this->get_child_items($cid) : '';
            $db_cid = " AND (a.Kategorie = '" . $cid . "' OR a.Kategorie_Multi = '" . $cid . "' OR a.Kategorie_Multi LIKE '%," . $cid . ",%' OR a.Kategorie_Multi LIKE '%," . $cid . "' OR a.Kategorie_Multi LIKE '" . $cid . ",%'" . $search_in_child . ")";
            $area = $this->_db->cache_fetch_assoc("SELECT Sektion FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . $cid . "' LIMIT 1");
            if (is_array($area) && $_SESSION['area'] != $area['Sektion']) {
                $_SESSION['area'] = $area['Sektion'];
                $this->__object('Redir')->seoRedirect($this->__object('Redir')->link());
            }
        }

        $avail = $this->typeAvail();
        if (isset($_REQUEST['s']) && $_REQUEST['s'] == 1) {
            $cat_search = " AND b.Search = '1' ";
        }

        $q_s = "SELECT
	    a.*,
	    a.Titel_$this->lc AS Titel,
	    a.Beschreibung_$this->lc AS Beschreibung,
	    a.Titel_1 AS TitelDef,
	    a.Beschreibung_1 AS BeschreibungDef,
	    b.Name_{$this->lc} AS KategorieName
	FROM
	    " . PREFIX . "_shop_produkte AS a,
	    " . PREFIX . "_shop_kategorie AS b
	WHERE
	    {$Manu_S}
	    {$Title_S}
	    {$avail}
	    a.Aktiv = '1'
	AND
	    a.Sektion = '" . $_SESSION['area'] . "'
	    {$Price_s}
	    {$db_cid}
	    {$db_ss}
	    {$cat_search}
	AND
	    b.Id = a.Kategorie
	AND
	    b.Aktiv = '1'
	AND
	    b.Sektion = '" . $_SESSION['area'] . "'
            " . $this->whereGroup('a.Gruppen') . "
            " . $this->whereGroup('b.Gruppen');

        if ($no_colums == 1) {
            $db_lim = "0, $limit";
        } else {
            if ($this->no_pagenav === false) {
                $num = $this->_db->num_rows($q_s);
                $seiten = ceil($num / $limit);
                $a = Tool::getLimit($limit, $seiten);
            }
            $db_lim = "$a, $limit";

            $list = !empty($_REQUEST['list']) ? $_REQUEST['list'] : $this->settings['Sortable_Produkte'];
            $sort_array = array(
                'date'  => array('base' => 'a.Erstellt'),
                'title' => array('base' => 'a.Titel_' . $this->lc),
                'price' => array('base' => 'a.Preis_Liste'),
                'art'   => array('base' => 'a.Artikelnummer'),
                'klick' => array('base' => 'a.Klicks'),
                'kat'   => array('base' => 'a.Kategorie'));
            $this->getSortable($sort_array, $list, $db_sort, $na_sort);
        }

        $sql_products = $this->_db->query($q_s . " ORDER BY " . $db_sort . " LIMIT " . $db_lim);
        if (Arr::getRequest('s') == 1 && $shop_start != 1 && $_REQUEST['exts'] != 1) {
            if ($count_pattern < 2) {
                if ($num == 1) {
                    $res = $sql_products->fetch_assoc();
                    $this->__object('Redir')->seoRedirect('index.php?p=shop&area=' . $res['Sektion'] . '&action=showproduct&id=' . $res['Id'] . '&cid=' . $res['Kategorie'] . '&pname=' . translit($res['Titel']));
                }
            }
        }

        if ($num > $limit && $shop_start != 1) {
            $shop_q = 'shop_q=' . urlencode(Arr::getRequest('shop_q', 'empty')) . '&amp;';
            $shop_man = 'man=' . Arr::getRequest('man', 0) . '&amp;';
            $nav_limit = '&amp;limit=' . $limit;
            $nav_s = '&amp;s=' . Arr::getRequest('s', 0);
            $nav_avail = !empty($_REQUEST['avail']) ? '&amp;avail=' . Arr::getRequest('avail', 0) : '';
            $this->_view->assign('pages_inf', 'Seite ' . Tool::aktPage() . '/' . $seiten);
            $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\"  href=\"index.php?{$shop_q}{$shop_man}p=shop&amp;action=showproducts&amp;cid={$cid}&amp;page={s}{$nav_limit}{$Price_n}{$na_sort}{$is_offers}{$is_topseller}{$is_lowamount}{$nav_s}{$nav_avail}\">{t}</a>"));
        }

        $p_free = $this->getFreeShippingByCountry();
        while ($row_products = $sql_products->fetch_assoc()) {
            $row_products['Titel'] = $row_products['Titel_' . $this->lc];
            $row_products['TitelDef'] = $row_products['Titel_1'];
            $row_products['Beschreibung'] = Tool::cleanVideo(Tool::cleanTags($row_products['Beschreibung_' . $this->lc], array('screen', 'contact', 'audio', 'video')));
            $row_products['BeschreibungDef'] = Tool::cleanVideo(Tool::cleanTags($row_products['Beschreibung_1'], array('screen', 'contact', 'audio', 'video')));
            $row_products['BeschreibungLang'] = Tool::cleanTags($row_products['Beschreibung_lang_' . $this->lc], array('screen', 'contact', 'audio', 'video'));

            if (!$row_products['Titel']) {
                $row_products['Titel'] = $row_products['TitelDef'];
            }
            if (!$row_products['Beschreibung']) {
                $row_products['Beschreibung'] = $row_products['BeschreibungDef'];
            }
            if (($row_products['Preis_Liste'] > $row_products['Preis']) && ($row_products['Preis_Liste'] > 0) && ($row_products['Preis_Liste_Gueltig'] == 0) || ($row_products['Preis_Liste'] > $row_products['Preis'] && $row_products['Preis_Liste_Gueltig'] != 0 && $row_products['Preis_Liste_Gueltig'] < $this->stime)) {
                $row_products['Preis'] = $row_products['Preis_Liste'];
            }

            $r_varcheck = $this->_db->cache_fetch_object("SELECT DISTINCT(Id) FROM " . PREFIX . "_shop_varianten WHERE ArtId = '" . $row_products['Id'] . "'");
            if (is_object($r_varcheck) && $r_varcheck->Id >= 1) {
                $row_products['Vars'] = 1;
            }
            $row_products['Lieferzeit'] = $this->shippingTime($row_products['Lieferzeit']);
            $row_products['TaxValue'] = $this->getUstByCateg($row_products['Kategorie']);
            $row_products['Bild_Klein'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_small']);
            $row_products['Bild_Mittel'] = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_middle']);
            $row_products['netto_price_orig'] = $this->getPrice($row_products['Preis']);
            $row_products['Preis'] = $this->getPrice($row_products['Preis']);
            $row_products['Preis_Liste'] = $this->getPrice($row_products['Preis_Liste']);
            $row_products['netto_price'] = $this->getNettoPrice($row_products['Preis'], $row_products['Kategorie']);
            $row_products['netto_price_liste'] = $this->getNettoPrice($row_products['Preis_Liste'], $row_products['Kategorie']);
            $row_products['Preis'] = ($this->justNetto($_SESSION['user_country'])) ? $row_products['netto_price'] : $row_products['Preis'];
            $row_products['Preis_Liste'] = ($this->justNetto($_SESSION['user_country'])) ? $row_products['netto_price_liste'] : $row_products['Preis_Liste'];

            if ($this->settings['NettoPreise'] == 1 || $this->userNettoshop() == 1) {
                $row_products['price_ust_ex'] = $row_products['netto_price_orig'] - $row_products['netto_price'];
            }
            $row_products['product_ust'] = $this->getUst($row_products['Kategorie']);
            $row_products['PriceInf'] = $this->_lang['Shop_icludes'] . " " . $row_products['product_ust'] . $this->_lang['Shop_vat'];
            if ($row_products['EinheitId'] > 0) {
                $EinheitArray = $this->getUnits($row_products['EinheitId']);
                $row_products['Einheit'] = $EinheitArray['Einheit'];
                $row_products['EinheitMz'] = $EinheitArray['EinheitMz'];
                $row_products['EinheitOut'] = ($row_products['EinheitCount'] > 1) ? $EinheitArray['EinheitMz'] : $EinheitArray['Einheit'];
                if ($row_products['EinheitCount'] != 0.00) {
                    $row_products['EinheitPreisEinzel'] = $row_products['Preis'] / $row_products['EinheitCount'];
                    $row_products['EinheitPreisEinzelNetto'] = $row_products['netto_price'] / $row_products['EinheitCount'];
                }
            }
            $row_products['ProdLink'] = 'index.php?p=shop&amp;action=showproduct&amp;id=' . $row_products['Id'] . '&amp;cid=' . $row_products['Kategorie'] . '&amp;pname=' . translit($row_products['Titel']);
            $row_products['VIcon'] = $this->getAvIcon($row_products['Bestellt'], $row_products['Lagerbestand'], $row_products['Verfuegbar']);
            $row_products['Lagerbestand'] = ($row_products['Verfuegbar'] >= 5) ? 0 : $row_products['Lagerbestand'];
            $row_products['Beschreibung'] = strip_tags($row_products['Beschreibung'], '<br><br />');
            $row_products['man'] = $this->getManufacturerById($row_products['Hersteller']);
            $row_products['shipping_free'] = ($row_products['Preis'] >= $p_free && $p_free > 0 ? 1 : 0);
            $row_products['diff'] = $row_products['Preis_Liste'] - $row_products['Preis'];
            if ($row_products['diff'] > 0) {
                $row_products['diffpro'] = ($row_products['diff'] * 100) / $row_products['Preis_Liste'];
            }
            $Products[] = $row_products;
        }
        $sql_products->close();

        $tpl_array = array(
            'no_colums'              => $no_colums,
            'CategName'              => $kat_array['Name'],
            'cat_desc'               => $kat_array['Beschreibung'],
            'maxlength_prodtext'     => $this->settings['Prodtext_Laenge'],
            'colums_offers'          => $this->settings['Spalten_Angebote'],
            'colums_width_offers'    => $this->numround(100 / $this->settings['Spalten_Angebote']),
            'colums_topseller'       => $this->settings['Spalten_Topseller'],
            'colums_width_topseller' => $this->numround(100 / $this->settings['Spalten_Topseller']),
            'colums'                 => $this->_colums,
            'newest_colums'          => $this->settings['Spalten_Neueste'],
            'colums_width'           => ($shop_start == 1 ? $this->numround(100 / $this->settings['Spalten_Neueste'], 4) : $this->numround(100 / $this->_colums, 4)),
            'price_onlynetto'        => ($this->justNetto($_SESSION['user_country']) ? 1 : 0),
            'no_nettodisplay'        => ($this->noNettoDisplay($_SESSION['user_country']) ? 1 : 0));
        $this->_view->assign($tpl_array);

        if (!empty($cid)) {
            $this->_view->assign('sub_categs', $this->getSubCategs($cid));
        }
        return $Products;
    }

    /* Метод формирует часть запроса для выборки товаров по статусу наличия */
    public function typeAvail() {
        $avail = '';
        if ($this->settings['AvailType'] == '1') {
            switch (Arr::getRequest('avail')) {
                case '5': // Производится доставка на склад
                    $avail = " a.Verfuegbar = '5' AND ";
                    break;
                case '4': // Товар для предварительного заказа. Доставка 7 дней.
                    $avail = " a.Verfuegbar = '4' AND a.Lagerbestand < '1' AND ";
                    break;
                case '3': // Отсутствует на складе и не доступно для заказа
                    $avail = " a.Lagerbestand < '1' AND a.Bestellt = '0' AND ";
                    break;
                case '2': // Отсутствует на складе, но доступно для заказа
                    $avail = " a.Lagerbestand < '1' AND a.Bestellt = '1' AND ";
                    break;
                case '1': // Товар имеется на складе
                    $avail = " a.Lagerbestand > '0' AND ";
                    break;
            }
        } else {
            $avail = Arr::getRequest('avail', 0) > 0 ? ' a.Lagerbestand > 0 AND ' : '';
        }
        return $avail;
    }

    /* Метод округления с заменой разделителя c запятой на точку */
    public function numround($val, $round = 0) {
        $wert = round($val, $round);
        return str_replace(',', '.', $wert);
    }

    public function getUnits($id) {
        $units = $this->_db->cache_fetch_assoc("SELECT Titel_" . $this->lc . " AS Einheit, Mz_" . $this->lc . " AS EinheitMz FROM " . PREFIX . "_shop_einheiten WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $units;
    }

    public function mergeProduct($id, $cid, $delc = 0) {
        if (!empty($_REQUEST['delproduct'])) {
            unset($_SESSION['prod'][$_REQUEST['delproduct']]);
            $id = 0;
        } else {
            if ($id != 0) {
                $_SESSION['cid'][$cid] = $cid;
                $_SESSION['prod'][$id] = $id . '_' . $cid;
            }
        }
        if (Arr::getRequest('redir') == 1) {
            $redir = str_replace('redir=1', 'redir=0', $this->__object('Redir')->link());
            $this->__object('Redir')->redirect($redir . '&categ=' . $cid);
        }

        $cats = array();
        if (isset($_SESSION['cid'])) {
            foreach ($_SESSION['cid'] as $category) {
                $sql = $this->_db->query("SELECT Id, Name_1 AS KatDefault, Name_" . $this->lc . " AS CatName FROM " . PREFIX . "_shop_kategorie WHERE Id = '" . intval($category) . "' AND Aktiv = '1'");
                while ($row = $sql->fetch_object()) {
                    if (!$row->CatName) {
                        $row->CatName = $row->KatDefault;
                    }
                    $cats[] = $row;
                }
                $sql->close();
            }

            $productsC = array();
            $count = 1;
            foreach ($_SESSION['prod'] as $prod) {
                $prod = explode('_', $prod);
                if ($count == 1) {
                    $catid = $prod[1];
                }
                $count++;
                if (!empty($_REQUEST['categ'])) {
                    $catid = $_REQUEST['categ'];
                    $q_l = $this->getSpez();
                    $det_spez = $this->_db->cache_fetch_assoc("SELECT $q_l FROM " . PREFIX . "_shop_kategorie_spezifikation WHERE Kategorie = '" . intval($catid) . "' LIMIT 1");
                    $this->_view->assign('det_spez', $det_spez);

                    if ($catid == $prod[1]) {
                        $row = $this->displayProduct(1, $prod[0], 1);
                        $productsC[] = $row;
                    }
                } else {
                    if ($catid == $prod[1]) {
                        $row = $this->displayProduct(1, $prod[0], 0);
                        $productsC[] = $row;
                    }
                }
            }
        }
        if (empty($productsC) && $delc != 1) {
            unset($_SESSION['cid'][$_REQUEST['categ']]);
            unset($cats['CatName']);
            unset($cats['Id']);
            $this->mergeProduct(0, 0, 1);
        }
        $this->_view->assign(array('merged' => (isset($productsC) ? $productsC : ''), 'cats' => $cats));

        $seo_array = array(
            'headernav' => $this->_lang['Shop_mergeTitle'],
            'pagetitle' => $this->_lang['Shop_mergeTitle'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/product_merge_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    public function getSpez($prefix = '') {
        $value = $prefix . '*';
        if ($this->lc != 1) {
            $value .= ', ';
            for ($i = 1; $i <= 15; $i++) {
                $value .= $prefix . 'Spez_' . $i . '_' . $this->lc . ' AS Spez_' . $i . ',';
            }
        }
        return rtrim($value, ',');
    }

    public function showMyOrder($oid, $type) {
        $oid = intval($oid);
        if ($type == '1') {
            $dl_c = $this->_db->cache_fetch_object("SELECT Bestellung FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . intval($oid) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' LIMIT 1");
            $orderprint = base64_decode($dl_c->Bestellung);
        } else {
            $dl_c = $this->_db->cache_fetch_object("SELECT Order_Type FROM " . PREFIX . "_shop_bestellungen WHERE Id = '" . intval($oid) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' LIMIT 1");
            $orderprint = base64_decode($dl_c->Order_Type);
        }
        if (is_object($dl_c)) {
            $this->_view->assign('orderprint', $orderprint);
        }
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::output($this->_view->fetch(THEME . '/shop/order_print.tpl'));
    }

    public function personalDownloads($oid) {
        $oid = intval($oid);
        if (Arr::getRequest('dl') == 1) {
            $id = intval(Arr::getRequest('id'));
            $dl_c = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_shop_kundendownloads WHERE Bestellung = '$oid' AND Kunde = '" . $_SESSION['benutzer_id'] . "' AND Id = '" . $id . "' LIMIT 1");
            if (is_object($dl_c)) {
                $this->_db->query("UPDATE " . PREFIX . "_shop_kundendownloads SET Downloads=Downloads+1 WHERE Bestellung = '$oid' AND Kunde = '" . $_SESSION['benutzer_id'] . "' AND Id = '" . $id . "'");
                File::filerange(UPLOADS_DIR . '/shop/customerfiles/' . $dl_c->Datei, 'application/octet-stream');
            }
        }

        $exists = false;
        $downloads = array();
        $sql_dl = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_kundendownloads WHERE Bestellung = '$oid' AND Kunde = '" . $_SESSION['benutzer_id'] . "' ORDER BY Datum DESC");
        while ($row_dl = $sql_dl->fetch_object()) {
            $row_dl->not_exists = 0;
            if (!is_file(UPLOADS_DIR . '/shop/customerfiles/' . $row_dl->Datei)) {
                $row_dl->not_exists = 1;
            }
            $row_dl->size = is_file(UPLOADS_DIR . '/shop/customerfiles/' . $row_dl->Datei) ? File::filesize(filesize(UPLOADS_DIR . '/shop/customerfiles/' . $row_dl->Datei) / 1024) : '';
            $downloads[] = $row_dl;
            $exists = true;
        }
        $sql_dl->close();

        $this->_view->assign(array('downloads' => $downloads, 'exists' => $exists));

        $seo_array = array(
            'headernav' => $this->_lang['Shop_personalDownloads'],
            'pagetitle' => $this->_lang['Shop_personalDownloads'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/personal_downloads.tpl'));
        $this->_view->finish($seo_array);
    }

    public function browseImages($id) {
        $Bilder_Klein = '';
        $row_products = $this->_db->cache_fetch_assoc("SELECT Bild, Bilder, Titel_" . $this->lc . " AS Titel, Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_produkte WHERE Id = '" . intval($id) . "' LIMIT 1");
        $prod_image = Tool::thumb('shop', $row_products['Bild'], $this->settings['thumb_width_big']);

        if (!empty($row_products['Bilder'])) {
            $Bilder_Klein = array();
            $bilder = explode('|', $row_products['Bilder']);
            $bilder[] .= $row_products['Bild'];

            foreach ($bilder as $bild) {
                $arr['Bild'] = Tool::thumb('shop', $bild, $this->settings['thumb_width_middle']);
                $arr['Bild_Normal'] = Tool::thumb('shop', $bild, $this->settings['thumb_width_big']);
                $Bilder_Klein[] = $arr;
            }
            $this->_view->assign('images', $Bilder_Klein);
        }
        $this->_view->assign(array('prod_image' => $prod_image, 'title_html' => $row_products['Titel']));

        if (empty($row_products['Titel'])) {
            $row_products['Titel'] = $this->_lang['GlobalImage'];
        }
        $seo_array = array(
            'headernav' => $row_products['Titel'],
            'pagetitle' => $row_products['Titel'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'generate'  => $row_products['Titel'] . ' ' . $row_products['Beschreibung'],
            'content'   => $this->_view->fetch(THEME . '/shop/product_images_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    public function getRabatt($price, $val) {
        $val = number_format($val, 2, '.', '');
        $price = $price - ($price / 100 * $val);
        $price = number_format($price, 3, '.', '');
        return $price;
    }

    public function justNetto($country) {
        if (!empty($_SESSION['r_land'])) {
            $country = $_SESSION['r_land'];
        }
        $row = $this->_db->cache_fetch_object("SELECT a.Ust, b.VatByCountry FROM " . PREFIX . "_laender AS a, " . PREFIX . "_benutzer_gruppen AS b WHERE a.Code = '" . $this->_db->escape(strtoupper($country)) . "' AND b.Id = '" . $_SESSION['user_group'] . "' LIMIT 1");
        if (($row->Ust != 1 && $row->VatByCountry == '1') || ($this->settings['NettoPreise'] == 1 && $row->VatByCountry == '1') || $this->userNettoshop() == 1) {
            return true;
        }
        return false;
    }

    public function noNettoDisplay($country) {
        if (!empty($_SESSION['r_land'])) {
            $country = $_SESSION['r_land'];
        }
        if (!empty($_SESSION['l_land'])) {
            $country = $_SESSION['l_land'];
        }
        $row = $this->_db->cache_fetch_object("SELECT a.Ust, b.VatByCountry FROM " . PREFIX . "_laender AS a, " . PREFIX . "_benutzer_gruppen AS b WHERE a.Code = '" . $this->_db->escape(strtoupper($country)) . "' AND b.Id = '" . $_SESSION['user_group'] . "' LIMIT 1");
        return ($row->Ust != 1 && $row->VatByCountry == '1') ? true : false;
    }

    public function getUst($category) {
        $row = $this->getShopWert($category);
        return is_object($row) ? $row->Wert : 0;
    }

    public function getNettoPrice($price, $category = '') {
        $ust_string_f = '';
        $row = $this->getShopWert($category);
        if (!is_object($row)) {
            return 0;
        }
        if (!empty($row->Wert)) {
            $ust_string = number_format($row->Wert, 2, '.', '');
            if ($ust_string < 10) {
                $ust_string = str_replace('.', '', $ust_string);
                $ust_string_f = '1.0' . $ust_string;
            } else {
                $ust_string = str_replace('.', '', $ust_string);
                $ust_string_f = '1.' . $ust_string;
            }
            $price = $price / $ust_string_f;
        }
        $price = number_format($price, 3, '.', '');
        return $price;
    }

    public function getShopWert($categ) {
        $name = 'ShopWert_' . $categ;
        if (isset($this->_shop_params[$name])) {
            return $this->_shop_params[$name];
        }
        $this->_shop_params[$name] = $this->_db->cache_fetch_object("SELECT b.Wert FROM " . PREFIX . "_shop_kategorie AS a, " . PREFIX . "_shop_ustzone AS b WHERE a.Id = '" . intval($categ) . "' AND b.Id = a.UstId LIMIT 1");
        return $this->_shop_params[$name];
    }

    public function deleteItem($item, $mylist = 0) {
        $sess_where = ($mylist == 1) ? 'products_mylist' : 'products';
        if (!empty($_POST['mod'])) {
            $mods = implode(',', $_POST['mod']);
            $delitem = $item . '||' . $mods;
            unset($_SESSION[$sess_where][$delitem]);
        } else {
            unset($_SESSION[$sess_where][$item]);
        }

        if ($mylist == 1) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=mylist');
        } else {
            if (count($_SESSION['products']) < 1) {
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE Code = '" . $_SESSION['visitor_key'] . "'");
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb_gaeste WHERE BenutzerId = '" . $this->setBasketId() . "'");
            } else {
                $this->SaveBasketSession(serialize($_SESSION['products']));
            }
            if (!empty($_SESSION['coupon_code']) && !empty($_SESSION['coupon_val'])) {
                $this->initBasket();
                $row_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_gutscheine WHERE Code = '" . $_SESSION['coupon_code'] . "' LIMIT 1");
                if (is_object($row_c)) {
                    if ($row_c->MinBestellwert >= numf($_SESSION['price'])) {
                        $this->unsetCoupon();
                    }
                }
            }
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        }
        exit;
    }

    public function newList() {
        unset($_SESSION['products_mylist']);
        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=mylist');
    }

    public function addToCart($mylist = 0) {
        $vars = '';
        $tovar = ($mylist == 1 || Arr::getPost('mylist') == 1) ? 'products_mylist' : 'products';
        $_POST['amount'] = (!empty($_POST['amount']) && is_numeric($_POST['amount'])) ? intval($_POST['amount']) : 1;
        $product_id = intval($_POST['product_id']);
        $basket_refresh = Arr::getRequest('basket_refresh');
        $_POST['free_1'] = Arr::getPost('free_1');
        $_POST['free_2'] = Arr::getPost('free_2');
        $_POST['free_3'] = Arr::getPost('free_3');
        if ($basket_refresh != 1 && empty($_POST['free_1'])) {
            unset($_SESSION['product_' . $product_id]['free_1']);
        }
        if ($basket_refresh != 1 && empty($_POST['free_2'])) {
            unset($_SESSION['product_' . $product_id]['free_2']);
        }
        if ($basket_refresh != 1 && empty($_POST['free_3'])) {
            unset($_SESSION['product_' . $product_id]['free_3']);
        }

        $check = $this->getArticleById($product_id);
        if (!empty($_POST['free_1']) || !empty($_POST['free_2']) || !empty($_POST['free_3'])) {
            $mask = '![^\w-)(:/. ]!iu';
            if (!empty($_POST['free_1'])) {
                $_SESSION['product_' . $product_id]['free_1'] = $check->Frei_1 . ': ' . trim(preg_replace($mask, '', $_POST['free_1']));
            }
            if (!empty($_POST['free_2'])) {
                $_SESSION['product_' . $product_id]['free_2'] = $check->Frei_2 . ': ' . trim(preg_replace($mask, '', $_POST['free_2']));
            }
            if (!empty($_POST['free_3'])) {
                $_SESSION['product_' . $product_id]['free_3'] = $check->Frei_3 . ': ' . trim(preg_replace($mask, '', $_POST['free_3']));
            }
        }

        $fsk_ok = ($check->Fsk18 == '1' && Tool::userSettings('Fsk18') != '1') ? false : true;
        if ((is_object($check) && $check->Lagerbestand >= 1 || ($check->Verfuegbar == 4)) && ($fsk_ok)) {
            $amount = $_POST['amount'];
            if ($_POST['amount'] > $check->MaxBestellung && ($check->MaxBestellung != 0)) {
                $_POST['amount'] = $check->MaxBestellung;
            }
            if ($_POST['amount'] < $check->MinBestellung && ($check->MinBestellung != 0)) {
                $_POST['amount'] = $check->MinBestellung;
            }
            if ($_POST['amount'] > $check->Lagerbestand) {
                $_POST['amount'] = $check->Lagerbestand;
            }
            if ($check->Verfuegbar == 4) {
                $_POST['amount'] = $amount;
            }
            if ($check->EinzelBestellung == 1) {
                $_POST['amount'] = 1;
            }
            if (!empty($_POST['mod'])) {
                foreach ((array) $_POST['mod'] as $x) {
                    if (!empty($x)) {
                        $vars[] = $x;
                    }
                }

                if (!empty($vars)) {
                    $_SESSION[$tovar][$product_id . '||' . implode(',', $vars)] = $_POST['amount'];
                } else {
                    $_SESSION[$tovar][$product_id] = $_POST['amount'];
                }
            } else {
                $_SESSION[$tovar][$product_id] = $_POST['amount'];
            }
        }

        if (Arr::getRequest('mylist') == 1) {
            $mylist = 1;
        } else {
            $this->SaveBasketSession(serialize($_SESSION['products']));
        }

        $this->initBasket();
        if (Arr::getPost('ajax') == 1) {
            $out = $this->_view->fetch(THEME . '/shop/basket_small_raw.tpl');
            SX::output($out, true);
        } else {
            if (!empty($_SESSION['coupon_code']) && !empty($_SESSION['coupon_val'])) {
                $row_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_gutscheine WHERE Code = '" . $this->_db->escape($_SESSION['coupon_code']) . "' LIMIT 1");
                if (is_object($row_c)) {
                    if ($row_c->MinBestellwert >= numf($_SESSION['price'])) {
                        $this->unsetCoupon();
                    }
                }
            }
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        }
    }

    protected function checkFormData($what) {
        if ($what == 'guestorder' || $what == 'user') {
            $error = array();
            $prefix = $this->_lang['Shop_billingPrefix'];
            if (empty($_POST['r_email'])) {
                $error[] = $prefix . $this->_lang['Email'];
            }
            if (!Tool::isMail($_POST['r_email'])) {
                $error[] = $prefix . $this->_lang['Email'];
            }
            if ($_SESSION['ship_ok'] == 1 || SX::get('system.Reg_DataPflichtFill') == 1) {
                if (empty($_POST['r_vorname']) || (!empty($_POST['r_vorname']) && !Tool::isAllow($_POST['r_vorname']))) {
                    $error[] = $prefix . $this->_lang['GlobalName'];
                }
                if (empty($_POST['r_nachname']) || (!empty($_POST['r_nachname']) && !Tool::isAllow($_POST['r_nachname']))) {
                    $error[] = $prefix . $this->_lang['LastName'];
                }
            } else {
                if (!empty($_POST['r_vorname']) && !Tool::isAllow($_POST['r_vorname'])) {
                    $error[] = $prefix . $this->_lang['GlobalName'];
                }
                if (!empty($_POST['r_nachname']) && !Tool::isAllow($_POST['r_nachname'])) {
                    $error[] = $prefix . $this->_lang['LastName'];
                }
            }
            if (!empty($_POST['r_middlename']) && !Tool::isAllow($_POST['r_middlename'])) {
                $error[] = $prefix . $this->_lang['Profile_MiddleName'];
            }
            if ($_SESSION['ship_ok'] == 1 || SX::get('system.Reg_AddressFill') == 1) {
                if (empty($_POST['r_strasse']) || (!empty($_POST['r_strasse']) && !Tool::isAddress($_POST['r_strasse']))) {
                    $error[] = $prefix . $this->_lang['Shop_check_street'];
                }
                if (empty($_POST['r_plz']) || (!empty($_POST['r_plz']) && !Tool::isAllow($_POST['r_plz']))) {
                    $error[] = $prefix . $this->_lang['Shop_check_zip'];
                }
                if (empty($_POST['r_ort']) || (!empty($_POST['r_ort']) && !Tool::isAllow($_POST['r_ort']))) {
                    $error[] = $prefix . $this->_lang['Town'];
                }
            } else {
                if (!empty($_POST['r_strasse']) && !Tool::isAddress($_POST['r_strasse'])) {
                    $error[] = $prefix . $this->_lang['Shop_check_street'];
                }
                if (!empty($_POST['r_plz']) && !Tool::isAllow($_POST['r_plz'])) {
                    $error[] = $prefix . $this->_lang['Shop_check_zip'];
                }
                if (!empty($_POST['r_ort']) && !Tool::isAllow($_POST['r_ort'])) {
                    $error[] = $prefix . $this->_lang['Town'];
                }
            }
            if (!empty($_POST['r_firma']) && !Tool::isAllow($_POST['r_firma'])) {
                $error[] = $prefix . $this->_lang['Profile_company'];
            }
            if (!empty($_POST['r_ustid']) && !Tool::isAllow($_POST['r_ustid'])) {
                $error[] = $prefix . $this->_lang['Profile_vatnum'];
            }
            if (!empty($_POST['r_telefon']) && !Tool::isAllow($_POST['r_telefon'])) {
                $error[] = $prefix . $this->_lang['Phone'];
            }
            if (empty($_POST['r_telefon']) && $this->settings['Telefon_Pflicht'] == 1) {
                $error[] = $prefix . $this->_lang['Phone'];
            }
            if (!empty($_POST['r_fax']) && !Tool::isAllow($_POST['r_fax'])) {
                $error[] = $prefix . $this->_lang['Fax'];
            }
            if (!empty($_POST['r_plz']) && !Tool::isDigit($_POST['r_plz'])) {
                $error[] = $prefix . $this->_lang['Profile_WrongZip'];
            }
            if (!empty($_POST['diff_rl'])) {
                $_SESSION['diff_rl'] = $_POST['diff_rl'];
                if ($_POST['diff_rl'] == 'liefer_andere') {
                    $prefix = $this->_lang['Shop_shippingPrefix'];
                    if ($_SESSION['ship_ok'] == 1 || SX::get('system.Reg_DataPflichtFill') == 1) {
                        if (empty($_POST['l_vorname']) || (!empty($_POST['l_vorname']) && !Tool::isAllow($_POST['l_vorname']))) {
                            $error[] = $prefix . $this->_lang['GlobalName'];
                        }
                        if (empty($_POST['l_nachname']) || (!empty($_POST['l_nachname']) && !Tool::isAllow($_POST['l_nachname']))) {
                            $error[] = $prefix . $this->_lang['LastName'];
                        }
                    } else {
                        if (!empty($_POST['l_vorname']) && !Tool::isAllow($_POST['l_vorname'])) {
                            $error[] = $prefix . $this->_lang['GlobalName'];
                        }
                        if (!empty($_POST['l_nachname']) && !Tool::isAllow($_POST['l_nachname'])) {
                            $error[] = $prefix . $this->_lang['LastName'];
                        }
                    }
                    if (!empty($_POST['l_middlename']) && !Tool::isAllow($_POST['l_middlename'])) {
                        $error[] = $prefix . $this->_lang['Profile_MiddleName'];
                    }
                    if ($_SESSION['ship_ok'] == 1 || SX::get('system.Reg_AddressFill') == 1) {
                        if (empty($_POST['l_strasse']) || (!empty($_POST['l_strasse']) && !Tool::isAddress($_POST['l_strasse']))) {
                            $error[] = $prefix . $this->_lang['Shop_check_street'];
                        }
                        if (empty($_POST['l_plz']) || (!empty($_POST['l_plz']) && !Tool::isAllow($_POST['l_plz']))) {
                            $error[] = $prefix . $this->_lang['Shop_check_zip'];
                        }
                        if (empty($_POST['l_ort']) || (!empty($_POST['l_ort']) && !Tool::isAllow($_POST['l_ort']))) {
                            $error[] = $prefix . $this->_lang['Town'];
                        }
                    } else {
                        if (!empty($_POST['l_strasse']) && !Tool::isAddress($_POST['l_strasse'])) {
                            $error[] = $prefix . $this->_lang['Shop_check_street'];
                        }
                        if (!empty($_POST['l_plz']) && !Tool::isAllow($_POST['l_plz'])) {
                            $error[] = $prefix . $this->_lang['Shop_check_zip'];
                        }
                        if (!empty($_POST['l_ort']) && !Tool::isAllow($_POST['l_ort'])) {
                            $error[] = $prefix . $this->_lang['Town'];
                        }
                    }
                    if (!empty($_POST['l_telefon']) && !Tool::isAllow($_POST['l_telefon'])) {
                        $error[] = $prefix . $this->_lang['Phone'];
                    }
                    if (!empty($_POST['l_fax']) && !Tool::isAllow($_POST['l_fax'])) {
                        $error[] = $prefix . $this->_lang['Fax'];
                    }
                }
            }

            if (!empty($error)) {
                $this->_view->assign('r_errors', $error);
            } else {
                $_SESSION['step2_ok'] = 1;
                $_SESSION['r_email'] = Tool::cleanMail($_POST['r_email']);
                $_SESSION['r_vorname'] = Tool::cleanAllow($_POST['r_vorname']);
                $_SESSION['r_nachname'] = Tool::cleanAllow($_POST['r_nachname']);
                $_SESSION['r_strasse'] = sanitize($_POST['r_strasse']);
                $_SESSION['r_plz'] = sanitize($_POST['r_plz']);
                $_SESSION['r_ort'] = sanitize($_POST['r_ort']);
                $_SESSION['r_land'] = $_SESSION['l_land'] = sanitize($_POST['r_land']);
                $_SESSION['r_firma'] = sanitize($_POST['r_firma']);
                $_SESSION['r_ustid'] = sanitize($_POST['r_ustid']);
                $_SESSION['r_telefon'] = sanitize($_POST['r_telefon']);
                $_SESSION['r_fax'] = sanitize($_POST['r_fax']);
                $_SESSION['r_nachricht'] = sanitize($_POST['r_nachricht']);
                $_SESSION['r_middlename'] = Tool::cleanAllow($_POST['r_middlename']);
                $_SESSION['r_bankname'] = sanitize($_POST['r_bankname']);

                if (!empty($_POST['diff_rl']) && !empty($_POST['l_vorname']) && !empty($_POST['l_nachname'])) {
                    $_SESSION['l_land'] = sanitize($_POST['l_land']);
                    $_SESSION['l_vorname'] = sanitize($_POST['l_vorname']);
                    $_SESSION['l_nachname'] = sanitize($_POST['l_nachname']);
                    $_SESSION['l_strasse'] = sanitize($_POST['l_strasse']);
                    $_SESSION['l_plz'] = sanitize($_POST['l_plz']);
                    $_SESSION['l_ort'] = sanitize($_POST['l_ort']);
                    $_SESSION['l_firma'] = sanitize($_POST['l_firma']);
                    $_SESSION['l_telefon'] = sanitize($_POST['l_telefon']);
                    $_SESSION['l_fax'] = sanitize($_POST['l_fax']);
                    $_SESSION['l_middlename'] = Tool::cleanAllow($_POST['l_middlename']);
                }

                if ($_POST['diff_rl'] == 'liefer_gleich') {
                    unset($_SESSION['l_land'], $_SESSION['l_plz'], $_SESSION['l_telefon'], $_SESSION['l_plz'], $_SESSION['l_ort'], $_SESSION['l_fax']);
                    unset($_SESSION['l_firma'], $_SESSION['l_vorname'], $_SESSION['l_nachname'], $_SESSION['l_strasse'], $_SESSION['l_middlename']);
                    $_POST['l_middlename'] = $_POST['l_vorname'] = $_POST['l_nachname'] = $_POST['l_firma'] = '';
                    $_POST['l_strasse'] = $_POST['l_land'] = $_POST['l_plz'] = $_POST['l_ort'] = '';
                }
                $h2 = ($what == 'guestorder') ? '&order=guest' : '';
                $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=shoporder&subaction=step3' . $h2);
            }
        }
    }

    public function orderNumber($length) {
        $chars = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str_lng = strlen($chars) - 1;
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $chars{mt_rand(0, $str_lng)};
        }
        return $rand;
    }

    public function getCountryName($id) {
        $rl = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape($id) . "' LIMIT 1");
        return $rl->Name;
    }

    public function getShipperName($id) {
        $rl = $this->_db->cache_fetch_object("SELECT Name_" . $this->lc . " AS Name FROM " . PREFIX . "_shop_versandarten WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $rl->Name;
    }

    public function getPaymentName($id) {
        $rl = $this->_db->cache_fetch_object("SELECT Name_" . $this->lc . " AS Name FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $rl->Name;
    }

    public function listPayments($shipper_id) {
        $country_check = (Arr::getSession('diff_rl') == 'liefer_andere') ? $_SESSION['l_land'] : $_SESSION['r_land'];
        $payments = array();
        $sql = $this->_db->query("SELECT *, Beschreibung_" . $this->lc . " AS Beschreibung, Name_" . $this->lc . " AS Name FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Aktiv = 1 ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $error = false;
            if (isset($_SESSION['coupon_val']) && $_SESSION['coupon_val'] > 0 && $_SESSION['coupon_typ'] == 'wert' && $_SESSION['price_netto'] < $_SESSION['coupon_val'] && $row->KostenOperant == '-' && $row->Kosten > 0) {
                $error = true;
            }
            $Versandarten = explode(',', $row->Versandarten);
            $Laender = explode(',', strtoupper($row->Laender));
            $Gruppen = explode(',', strtoupper($row->Gruppen));
            if (in_array($shipper_id, $Versandarten) && in_array($country_check, $Laender) && in_array($_SESSION['user_group'], $Gruppen) && (!$error)) {
                $payments[] = $row;
            }
        }
        $sql->close();
        return $payments;
    }

    public function checkShipper() {
        $error = true;
        $country_check = (Arr::getSession('diff_rl') == 'liefer_andere') ? $_SESSION['l_land'] : $_SESSION['r_land'];
        $sql = $this->_db->query("SELECT SQL_CACHE Laender FROM " . PREFIX . "_shop_versandarten");
        while ($row = $sql->fetch_object()) {
            $laender = explode(',', $row->Laender);
            if (in_array($country_check, $laender)) {
                $error = false;
            }
        }
        $sql->close();
        if ($error == false) {
            return true;
        }

        return false;
    }

    public function getShipper($free = 0, $weightnull = 0) {
        $shipper = array();
        $nullsh = ($weightnull == 1) ? 'WHERE GewichtNull = 1 AND Aktiv = 1' : 'WHERE GewichtNull != 1 AND Aktiv = 1';
        $country_check = (Arr::getSession('diff_rl') == 'liefer_andere') ? $_SESSION['l_land'] : $_SESSION['r_land'];
        $sql = $this->_db->query("SELECT Icon, Gruppen, Laender, Id, Name_" . $this->lc . " AS Name, Beschreibung_" . $this->lc . " AS Beschreibung  FROM " . PREFIX . "_shop_versandarten $nullsh ORDER BY Position ASC");
        $count = 0;
        while ($row = $sql->fetch_object()) {
            $laender = explode(',', $row->Laender);
            $groups = explode(',', $row->Gruppen);
            if (in_array($country_check, $laender) && in_array($_SESSION['user_group'], $groups)) {
                $count++;
                if ($this->getShipperInf($row->Id, $free) != 'false') {
                    $shipper[] = $row;
                }
            }
        }
        $sql->close();
        if ($count < 1) {
            $this->_view->assign('shipper_found', '0');
        }
        return $shipper;
    }

    public function getShipperInf($id = 0, $free = 0) {
        $shipping_free_summ = $this->getFreeShippingByCountry();

        if ($id == 0) {
            return 'false';
        } else {
            $weight = numf($_SESSION['gewicht'] / 1000);
            $where = ($id != 0) ? "WHERE Id = '" . intval($id) . "' AND Aktiv = 1" : "WHER Aktiv = 1 ORDER BY Position ASC";
            $row = $this->_db->cache_fetch_object("SELECT Icon, Gebuehr_Pauschal, Gruppen, Laender, Id, Name_" . $this->lc . " AS Name, Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_versandarten $where LIMIT 1");
            if (is_object($row)) {
                $row_gebuehr = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_versandarten_volumen WHERE Von <= '{$weight}' AND Bis >= '{$weight}' AND VersandId = '{$row->Id}' LIMIT 1");
                if ($shipping_free_summ > 0 && $_SESSION['price'] >= $shipping_free_summ) {
                    $row_gebuehr->Gebuehr = $row->Gebuehr_Pauschal = 0.00;
                    unset($_SESSION['shipping_summ']);
                }
            }
            if (is_object($row) && is_object($row_gebuehr)) {
                if ($row->Gebuehr_Pauschal >= 0.01 || $weight < 0.01) {
                    $row->Gebuehr = ($weight < 0.01) ? 0.00 : $row->Gebuehr_Pauschal;
                    return $row;
                } else {
                    if ($free == 1) {
                        return true;
                    } else {
                        if (is_object($row_gebuehr)) {
                            $row->Gebuehr = $row_gebuehr->Gebuehr;
                            return $row;
                        } else {
                            return 'false';
                        }
                    }
                }
            } elseif (is_object($row) && !is_object($row_gebuehr)) {
                if ($weight > 0) {
                    return 'false';
                }
                return $row;
            } else {
                return 'false';
            }
        }
    }

    public function showBasket() {
        $_SESSION['shipping_summ'] = $_SESSION['payment_summ_extra'] = $_SESSION['shopstep'] = $_SESSION['step2_ok'] = $_SESSION['step3_ok'] = $_SESSION['step4_ok'] = 0;
        unset($_SESSION['shipper_id'], $_SESSION['shipping_is_free'], $_SESSION['payment_id'], $_SESSION['no_shipping'], $_SESSION['order_number']);

        if (Arr::getRequest('del') == 'coupon') {
            $this->unsetCoupon();
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        }

        $this->_view->assign('product_array', $this->initBasket(1));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_step_1'],
            'pagetitle' => $this->_lang['Shop_step_1'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/basket.tpl'));
        $this->_view->finish($seo_array);
    }

    public function loadMyLists() {
        $lists = $this->_db->cache_fetch_object_all("SELECT * FROM " . PREFIX . "_shop_merkzettel WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' ORDER BY Name ASC");
        return $lists;
    }

    public function showMyList() {
        if ($_SESSION['user_group'] != 2 && isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == 'save_list' && !empty($_SESSION['products_mylist'])) {
            $Zettel = serialize($_SESSION['products_mylist']);
            $_POST['Name_Merkzettel'] = !empty($_POST['Name_Merkzettel']) ? $_POST['Name_Merkzettel'] : 'Undefined';
            $insert_array = array(
                'Benutzer' => $_SESSION['benutzer_id'],
                'Name'     => Arr::getPost('Name_Merkzettel'),
                'Datum'    => $this->stime,
                'Inhalt'   => $Zettel);
            $this->_db->insert_query('shop_merkzettel', $insert_array);
        }

        if ($_SESSION['user_group'] != 2) {
            if (Arr::getRequest('subaction') == 'load_list' && !empty($_REQUEST['id'])) {
                $row_mz = $this->_db->cache_fetch_object("SELECT Inhalt FROM " . PREFIX . "_shop_merkzettel WHERE Id = '" . intval(Arr::getRequest('id')) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' LIMIT 1");
                if (is_object($row_mz)) {
                    $_SESSION['products_mylist'] = unserialize($row_mz->Inhalt);
                }
            }

            if (Arr::getRequest('subaction') == 'del_list' && !empty($_REQUEST['id'])) {
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_merkzettel WHERE Id = '" . intval(Arr::getRequest('id')) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "'");
            }
            $this->_view->assign('myLists', $this->loadMyLists());
        }

        $this->_view->assign('product_array', $this->initBasket(1, 'wishlist'));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_mylist'],
            'pagetitle' => $this->_lang['Shop_mylist'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/mylist.tpl'));
        $this->_view->finish($seo_array);
    }

    public function initBasket($page = 0, $mylist = '', $ajax_out = 0) {
        $weight_parts = $weight = $All = $Price = $NettoPrice = $NettoPrice2 = $Price_brutto = $Price_brutto_PB = 0;
        if (!isset($_SESSION['shipping_summ'])) {
            $_SESSION['shipping_summ'] = 0;
        }
        if (!isset($_SESSION['payment_summ_extra'])) {
            $_SESSION['payment_summ_extra'] = 0;
        }
        if (!isset($_SESSION['price_netto_coupon'])) {
            $_SESSION['price_netto_coupon'] = 0;
        }
        if (Arr::getRequest('action') == 'showbasket') {
            unset($_SESSION['payment_id'], $_SESSION['shipper_id']);
        }
        $this->_view->assign('show_vat_table', (!$this->noNettoDisplay($_SESSION['user_country']) ? 1 : 0));

        $shop_ustzone = $this->_db->cache_fetch_object_all("SELECT Wert FROM " . PREFIX . "_shop_ustzone ORDER BY Wert ASC");
        foreach ($shop_ustzone as $row) {
            unset($_SESSION[$row->Wert]);
        }

        $sess_what = $mylist == 'wishlist' ? 'products_mylist' : 'products';
        if (!empty($_SESSION[$sess_what])) {
            $arr = $_SESSION[$sess_what];
            reset($arr);
            $product_array = array();
            foreach ($arr as $key => $value) {
                $part = explode('||', $key);
                $row_p = $this->_db->cache_fetch_object("SELECT *, Titel_" . $this->lc . " AS Titel, Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_produkte WHERE Id = '" . $part[0] . "' LIMIT 1");
                if (is_object($row_p)) {
                    $var_array = array();
                    $row_p->Lieferzeit = isset($row_p->Lieferzeit) ? $this->shippingTime($row_p->Lieferzeit) : '';
                    if (isset($_SESSION['product_' . $row_p->Id])) {
                        foreach ($_SESSION['product_' . $row_p->Id] as $free => $free_item) {
                            if ($free != 'ProdId' && !empty($free_item)) {
                                $row_p->FreeFields .= Tool::cleanAllow($free_item, ' )(:\/.') . '<br />';
                            }
                        }
                    }

                    if (is_object($row_p) && (($row_p->Preis_Liste > $row_p->Preis) && ($row_p->Preis_Liste > 0) && ($row_p->Preis_Liste_Gueltig == 0) || (($row_p->Preis_Liste > $row_p->Preis) && $row_p->Preis_Liste_Gueltig != 0 && $row_p->Preis_Liste_Gueltig < $this->stime))) {
                        $row_p->Preis = $row_p->Preis_Liste;
                    }

                    if ($value > 1) {
                        $row_psta = $this->_db->cache_fetch_object("SELECT Wert, Operand FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '$part[0]' AND Von <= $value AND Bis >= $value LIMIT 1");
                        if (isset($row_psta->Operand) && $row_psta->Operand == 'pro') {
                            if (is_object($row_psta) && !empty($row_psta)) {
                                $row_p->Preis = number_format($row_p->Preis - ($row_p->Preis / 100 * $row_psta->Wert), 2, '.', '');
                            } else {
                                $row_psta = $this->_db->cache_fetch_object("SELECT Wert, Bis FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '$part[0]' ORDER BY Bis DESC LIMIT 1");
                                if (is_object($row_psta) && !empty($row_psta) && $value >= $row_psta->Bis) {
                                    $row_p->Preis = number_format($row_p->Preis - ($row_p->Preis / 100 * $row_psta->Wert), 2, '.', '');
                                }
                            }
                        } else {
                            if (is_object($row_psta) && !empty($row_psta)) {
                                $rabatt = $this->getPrice($row_psta->Wert);
                                if ($rabatt < $row_p->Preis) {
                                    $row_p->Preis = number_format($row_p->Preis - $rabatt, 2, '.', '');
                                }
                            } else {
                                $rabatt = $this->getPrice(isset($row_psta->Wert) ? $row_psta->Wert : '');
                                $row_psta = $this->_db->cache_fetch_object("SELECT Wert, Bis FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '$part[0]' ORDER BY Bis DESC LIMIT 1");
                                if (($rabatt < $row_p->Preis) && is_object($row_psta) && !empty($row_psta) && $value >= $row_psta->Bis) {
                                    $row_p->Preis = number_format($row_p->Preis - $rabatt, 2, '.', '');
                                }
                            }
                        }
                    }

                    $row_p->NPreis = number_format($this->getPrice($row_p->Preis), 3, '.', '');
                    $row_p->NPreisNetto = $this->getNettoPrice($row_p->NPreis, $row_p->Kategorie);
                    $row_p->Preis = $this->getNettoPrice($this->getPrice($row_p->Preis), $row_p->Kategorie);

                    if (!empty($part[1])) {
                        $row_p->Anzahl = $arr[$part[0] . '||' . $part[1]];
                        $row_p->Varianten = explode(',', $part[1]);
                        foreach ($row_p->Varianten as $var) {
                            $row_v = $this->_db->cache_fetch_object("SELECT
                                        a.Wert AS Wert_B,
                                        a.Gewicht,
                                        a.Name_" . $this->lc . " AS Name,
                                        a.Wert,
                                        a.Operant,
                                        b.Name_" . $this->lc . " AS KatName
                                    FROM
                                        " . PREFIX . "_shop_varianten AS a,
                                        " . PREFIX . "_shop_varianten_kategorien AS b
                                    WHERE
                                        b.Id = a.KatId
                                    AND
                                        a.Id = '" . $var . "' LIMIT 1");

                            if (is_object($row_v)) {
                                if ($row_v->GewichtOperant == '-') {
                                    $weight_parts -= ($row_v->Gewicht * $value);
                                } else {
                                    $weight_parts += ($row_v->Gewicht * $value);
                                }
                                $row_v->Wert = number_format($row_v->Wert, 2, '.', '');
                                if ($value > 1) {
                                    if (is_object($row_psta) && !empty($row_psta)) {
                                        $row_v->Wert = number_format($row_v->Wert - ($row_v->Wert / 100 * $row_psta->Wert), 2, '.', '');
                                    } else {
                                        $row_psta = $this->_db->cache_fetch_object("SELECT Wert, Bis FROM " . PREFIX . "_shop_staffelpreise WHERE ArtikelId = '$part[0]' ORDER BY Bis DESC LIMIT 1");
                                        if (is_object($row_psta) && !empty($row_psta) && $value >= $row_psta->Bis) {
                                            $row_p->Preis = number_format($row_p->Preis - ($row_p->Preis / 100 * $row_psta->Wert), 2, '.', '');
                                        }
                                    }
                                }
                                $row_v->Wert = $row_v->Operant . $this->getNettoPrice($this->getPrice($row_v->Wert), $row_p->Kategorie);
                                $row_p->NPreis = $row_p->NPreis + ($row_v->Operant . $this->getPrice($row_v->Wert_B));
                                $row_p->NPreisNetto += $row_v->Wert;
                                $row_p->Preis += $row_v->Wert;
                                $var_array[] = $row_v;
                            }
                        }
                    } else {
                        $row_p->Anzahl = $arr[$row_p->Id];
                    }
                    $row_p->Endpreis = ($row_p->Preis * $row_p->Anzahl);
                    $row_p->Bild = Tool::thumb('shop', $row_p->Bild, $this->settings['thumb_width_middle']);
                    $row_p->ProdLink = 'index.php?p=shop&amp;action=showproduct&amp;id=' . $row_p->Id . '&amp;cid=' . $row_p->Kategorie . '&amp;pname=' . translit($row_p->Titel);
                    $ustVat = $this->getUst($row_p->Kategorie);

                    if (Arr::getSession('coupon_typ') == 'pro') {
                        $IncVat = $row_p->NPreis - $row_p->NPreisNetto;
                        $IncVat = $IncVat - $this->getCouponPrice($IncVat, $row_p->Hersteller);
                    } else {
                        $IncVat = ($row_p->NPreisNetto * $ustVat) / 100;
                    }

                    $IncVat = number_format($IncVat, 3, '.', '');
                    if (!$this->noNettoDisplay($_SESSION['user_country'])) {
                        $this->_view->assign('show_vat_table', 1);
                        if (!isset($_SESSION[$ustVat])) {
                            $_SESSION[$ustVat] = 0;
                        }
                        $_SESSION[$ustVat] += ($IncVat * $value);
                    } else {
                        $IncVat = 0;
                    }

                    $row_p->UstZone = $this->getUst($row_p->Kategorie);
                    $row_p->Preis = number_format($row_p->Preis, 3, '.', '');
                    $Price_brutto += ($row_p->Preis + ($row_p->Preis / 100 * $row_p->UstZone)) * $value;
                    $Price_brutto_PB += ($row_p->Preis + ($row_p->Preis / 100 * $row_p->UstZone));
                    $row_p->Vars = $var_array;
                    $row_p->Preis_b = number_format($row_p->Preis + ($row_p->Preis / 100 * $row_p->UstZone), 3, '.', '');
                    $row_p->Preis_bs = number_format(($row_p->Preis + ($row_p->Preis / 100 * $row_p->UstZone)) * $value, 3, '.', '');
                    $product_array[] = $row_p;

                    $valico = $row_p->NPreisNetto * $value;
                    $NettoPrice += $valico;
                    $NettoPrice2 += $valico - $this->getCouponPrice($valico, $row_p->Hersteller);

                    $Price += ($row_p->Preis + $IncVat) * $value;
                    $All += $value;
                    $weight += ($row_p->Gewicht * $value);
                }
            }

            $_SESSION['PB'] = $Price_brutto_PB;
            $_SESSION['gewicht'] = $weight + $weight_parts;
            $_SESSION['gewicht_detail'] = $_SESSION['gewicht'] / 1000;
            $_SESSION['iamount_brutto'] = $Price_brutto;
            $_SESSION['price_netto'] = $NettoPrice;

            if (Arr::getSession('coupon_typ') == 'pro') {
                $_SESSION['price_netto_coupon'] = $NettoPrice - $NettoPrice2;
            } else {
                $_SESSION['price_netto_coupon'] = $this->getCouponPrice($NettoPrice, true);
            }
            $_SESSION['price_netto_zwi'] = $_SESSION['price_netto'] - $_SESSION['price_netto_coupon'];

            $configu = array();
            $Inhalt_Config = '';
            if (isset($_SESSION['products'])) {
                foreach (array_keys($_SESSION['products']) as $pid) {
                    $real_pid = explode('||', $pid);
                    $real_pid = $real_pid[0];

                    if (isset($_SESSION['product_' . $real_pid]) && is_array($_SESSION['product_' . $real_pid])) {
                        $_SESSION['product_' . $real_pid]['ProdId'] = $real_pid;
                        $configu[] = serialize($_SESSION['product_' . $real_pid]);
                    }
                }
            }

            if (is_array($configu)) {
                $Inhalt_Config = implode('|||', $configu);
            }
            $Inhalt = (isset($_SESSION['products'])) ? serialize($_SESSION['products']) : '';
            $this->SaveBasketSession($Inhalt, base64_encode($Inhalt_Config));

            $_REQUEST['payment_id'] = (isset($_SESSION['payment_id']) && empty($_REQUEST['payment_id'])) ? intval($_SESSION['payment_id']) : intval(Arr::getRequest('payment_id'));
            $Price = $this->getPaymentMethod($Price);
            $Price = $this->getShippingSum($Price);
            $_SESSION['price'] = $Price;

            $tpl_array = array(
                'ust_vals'                    => $shop_ustzone,
                'basket_products_brutto'      => $Price_brutto,
                'basket_products_weight'      => $weight,
                'basket_products_all'         => $All,
                'basket_products_price'       => $_SESSION['price'],
                'basket_products_price_netto' => $NettoPrice,
                'basket_products'             => count(Arr::getSession('products')));
            $this->_view->assign($tpl_array);

            if (!empty($_SESSION['coupon_code'])) {
                $summe_waren = number_format($_SESSION['price_netto_zwiall'], 3, '.', '');
                $gutschein_check = $this->_db->cache_fetch_object("SELECT MinBestellwert FROM " . PREFIX . "_shop_gutscheine WHERE Code = '" . $_SESSION['coupon_code'] . "' LIMIT 1");
                if ($summe_waren < $gutschein_check->MinBestellwert) {
                    $this->unsetCoupon();
                }
            }

            if ($_SESSION['price'] <= 0 && empty($_REQUEST['payment_id']) && !empty($_SESSION['coupon_code'])) {
                $this->unsetCoupon();
                $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=showbasket&couponerror=2');
            }

            $fs = $this->getFreeShippingByCountry();
            $free_shipping_summ = $this->numformat($this->getPrice($fs));

            if (($_SESSION['price'] - $_SESSION['shipping_summ']) >= $free_shipping_summ && ($free_shipping_summ > 0)) {
                $this->_view->assign('freeshipping_inf', $this->_lang['Shop_freeshipping']);
            }

            if (isset($_SESSION['payment_summ_symbol']) && $_SESSION['payment_summ_symbol'] == '-') {
                $_SESSION['price_netto_zwiall'] = $Price_brutto + $_SESSION['shipping_summ'] - $_SESSION['payment_summ_extra'];
            } else {
                $_SESSION['price_netto_zwiall'] = $Price_brutto + $_SESSION['shipping_summ'] + $_SESSION['payment_summ_extra'];
            }
        }

        $error = '';
        if ($this->settings['BestMax'] != 0.00 && Arr::getSession('price') > $this->settings['BestMax']) {
            $error = 'to_much';
        }

        if ($this->settings['BestMin'] != 0.00 && Arr::getSession('price') < $this->settings['BestMin']) {
            $error = 'not_reached';
        }
        $this->_view->assign('status_error', $error);

        if ($page == 1) {
            if (isset($product_array) && is_array($product_array)) {
                return $product_array;
            }
        } else {
            if ($ajax_out != 1) {
                return $this->_view->fetch(THEME . '/shop/basket_small.tpl');
            }
        }
    }

    public function getCouponPrice($price, $hersteller = false) {
        static $array = false;
        $result = 0;
        if (
                !empty($_SESSION['coupon_code']) &&
                !empty($_SESSION['coupon_id']) &&
                !empty($_SESSION['coupon_typ']) &&
                !empty($_SESSION['coupon_val']) &&
                !empty($_SESSION['coupon_hersteller'])
        ) {
            if ($array === false) {
                $array = explode(',', $_SESSION['coupon_hersteller']);
            }
            $_SESSION['print_coupon_price'] = 1;
            if ((!empty($array) && in_array($hersteller, $array)) || $hersteller === true) {
                if ($_SESSION['coupon_typ'] == 'pro') {
                    $result = (numf($price) / 100) * $_SESSION['coupon_val'];
                } else {
                    $result = numf($_SESSION['coupon_val']);
                }
            }
        }
        return $result;
    }

    /* Метод удаления купонных переменных сессий */
    protected function unsetCoupon() {
        unset($_SESSION['coupon_code'], $_SESSION['coupon_id'], $_SESSION['coupon_val'], $_SESSION['coupon_typ'], $_SESSION['coupon_hersteller']);
    }

    /* Метод формирования цены доставки */
    public function getPaymentMethod($price) {
        if (!empty($_REQUEST['payment_id']) && $_REQUEST['subaction'] != 'step3' && $_REQUEST['subaction'] != 'step2') {
            $p_raw = $price;
            $row_ps = $this->_db->cache_fetch_object("SELECT KostenOperant, Kosten, KostenTyp FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . intval(Arr::getRequest('payment_id')) . "' AND Aktiv = 1 LIMIT 1");
            if ($row_ps->Kosten > 0) {
                $_SESSION['payment_summ_extra'] = $row_ps->KostenTyp == 'pro' ? (($price - $_SESSION['price_netto_coupon']) / 100) * $row_ps->Kosten : $row_ps->Kosten;
                if ($row_ps->KostenOperant == '-') {
                    $_SESSION['payment_summ_mipu'] = 'abzgl';
                    $_SESSION['payment_summ_symbol'] = '-';
                    $price -= $_SESSION['payment_summ_extra'];
                } else {
                    $_SESSION['payment_summ_mipu'] = 'zzgl';
                    $_SESSION['payment_summ_symbol'] = '';
                    $price += $_SESSION['payment_summ_extra'];
                }
            }
            if ($_SESSION['price'] < 0) {
                unset($_SESSION['payment_summ_symbol']);
                $_SESSION['payment_summ_mipu'] = $_SESSION['payment_summ_extra'] = 0;
                $price = $p_raw;
            }
        }
        return $price;
    }

    /* Метод преобразования формата, пример: вход = 1234.56 выход = 1.234,56 */
    public function numformat($num) {
        return number_format($num, '2', ',', '.');
    }

    public function getShippingSum($price) {
        if (!isset($_SESSION['no_shipping']) && !empty($_SESSION['shipper_id'])) {
            $shipper = (int) $_SESSION['shipper_id'];
            $row = $this->_db->cache_fetch_object("SELECT Gebuehr_Pauschal FROM " . PREFIX . "_shop_versandarten WHERE Id = '{$shipper}' LIMIT 1");
            if (is_object($row) && $row->Gebuehr_Pauschal > 0) {
                $_SESSION['shipping_summ'] = $row->Gebuehr_Pauschal;
            } else {
                $weight = numf($_SESSION['gewicht'] / 1000);
                $row = $this->_db->cache_fetch_object("SELECT Gebuehr FROM " . PREFIX . "_shop_versandarten_volumen WHERE Von <= '{$weight}' AND Bis >= '{$weight}' AND VersandId = '{$shipper}' LIMIT 1");
                if (is_object($row)) {
                    $_SESSION['shipping_summ'] = $row->Gebuehr;
                }
            }
        }
        if (!empty($_SESSION['price']) && !empty($_SESSION['payment_summ_extra'])) {
            $shipping = $this->getFreeShippingByCountry();
            $value = numf($_SESSION['price']) - numf($_SESSION['payment_summ_extra']);
            if ($shipping > 0 && $value >= $shipping) {
                $_SESSION['shipping_summ'] = 0;
                $_SESSION['shipping_is_free'] = $_SESSION['no_shipping'] = 1;
            }
        }
        if (isset($_SESSION['shipping_summ'])) {
            if (Arr::getRequest('subaction') == 'step3' || Arr::getRequest('subaction') == 'step2') {
                $_SESSION['payment_summ_mipu'] = isset($_SESSION['payment_summ_mipu']) ? $_SESSION['payment_summ_mipu'] : '';
                if ($_SESSION['payment_summ_mipu'] != 'abzgl') {
                    $price -= ($_SESSION['payment_summ_extra'] - $_SESSION['price_netto_coupon']);
                } else {
                    $price -= $_SESSION['price_netto_coupon'];
                }
            } else {
                $price += ($_SESSION['shipping_summ'] - $_SESSION['price_netto_coupon']);
            }
        }
        return $price;
    }

    public function showSearchSmall($Source) {
        return $this->_view->fetch($Source . '/shop/search_small.tpl');
    }

    public function getAvIcon($bestellt = 0, $anz = 0, $avail = 0) {
        $Available = $lief = 1;
        $not_on_store = 0;
        if ($anz < 1 && $bestellt == 1) {
            $lief = 2;
        }
        if ($anz < 1 && $bestellt == 0) {
            $lief = 3;
        }
        if ($anz < 1 && $avail == 4) {
            $lief = 4;
        }
        if ($avail == 5) {
            $lief = 5;
            $anz = 0;
            $Available = 0;
            $not_on_store = 1;
        }

        $row_l = $this->_db->cache_fetch_object("SELECT Titel_{$this->lc} AS Titel,Text_{$this->lc} AS Text FROM " . PREFIX . "_shop_verfuegbarkeit WHERE Id='$lief' LIMIT 1");
        $sx_titel = sanitize($row_l->Titel);
        $sx_text = sanitize($row_l->Text);

        $tpl_array = array(
            'Available'      => $Available,
            'not_on_store'   => $not_on_store,
            'status'         => '<span class="shop_available_' . $lief . '">' . $sx_titel . '</span>',
            'inf'            => '<span class="shop_available_' . $lief . '">' . $sx_text . '</span>',
            'lief_id'        => $lief,
            'status_img'     => '<img class="absmiddle" src="' . BASE_PATH . 'theme/' . SX::get('options.theme') . '/images/shop/avail-' . $lief . '.png" alt="" />',
            'inf_img'        => '<strong>' . $sx_titel . '</strong><br>' . $sx_text,
            'status_inf'     => $sx_titel,
            'inf_txt_status' => '<strong>' . $sx_titel . '</strong><br>' . $sx_text,
            'inf_anz_status' => $anz);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/shop/available.tpl');
    }

    public function getAvMsg($bestellt = 0, $anz = 0, $avail = 0) {
        $lief = 0;
        if ($anz < 1 && $bestellt == 1) {
            $lief = 1;
        }
        if ($anz < 1 && $bestellt == 0) {
            $lief = 2;
        }
        if ($anz < 1 && $avail == 4) {
            $lief = 3;
        }
        if ($avail == 5) {
            $lief = 4;
        }
        return $this->vailmsg[$lief]->Name;
    }

    public function get_parent_shopcateg($param) {
        $this->getLoadShopCategs();
        $Parent_Id = is_array($param) ? $param['id'] : $param;
        return !empty($Parent_Id) && isset($this->_shop_categs[$Parent_Id]) ? $this->_shop_categs[$Parent_Id]->Parent_Id : 0;
    }

    /* Получаем все категории магазина */
    public function setShopCategs() {
        $sql = $this->_db->query("SELECT
           *,
           Id AS catid,
           Name_1 AS DefName,
           Name_1 AS DefCatName,
           Name_{$this->lc} AS Name
        FROM
           " . PREFIX . "_shop_kategorie
        WHERE
           Sektion = '" . $_SESSION['area'] . "'
        AND
           Aktiv = '1'
        ORDER BY posi ASC");
        while ($row = $sql->fetch_object()) {
            $this->_shop_categs[$row->Id] = $row;
        }
        $sql->close();
    }

    /* Загружаем все категории магазина */
    protected function getLoadShopCategs() {
        static $load = false;
        if ($load === false) {
            $load = true;
            $this->setShopCategs();
        }
    }

    /* Выводим меню навигации */
    public function MyShopNavi() {
        $this->getLoadShopCategs();
        $theme = SX::get('options.theme');
        $categs_shop = array();
        foreach ($this->_shop_categs as $item) {
            if ($item->Parent_Id == 0) {
                $get_parent_shopcateg = $this->get_parent_shopcateg($item->Parent_Id);
                $item->navop = ($get_parent_shopcateg == 0) ? $item->Parent_Id : $get_parent_shopcateg;
                $categs_2 = array();
                if (!$item->Name) {
                    $item->Name = $item->DefCatName;
                }
                $item->Icon = $this->getImageNavi($item->Bild_Navi, $theme);
                $item->Entry = $item->Name;
                foreach ($this->_shop_categs as $items_2) {
                    if ($items_2->Parent_Id == $item->Id) {
                        $items_2->Icon = $this->getImageNavi($items_2->Bild_Navi, $theme);
                        if (!$items_2->Name) {
                            $items_2->Name = $items_2->DefCatName;
                        }
                        $categs_3 = array();
                        $items_2->Entry = $items_2->Name;
                        foreach ($this->_shop_categs as $items_3) {
                            if ($items_3->Parent_Id == $items_2->Id) {
                                $items_3->Icon = $this->getImageNavi($items_3->Bild_Navi, $theme);
                                if (!$items_3->Name) {
                                    $items_3->Name = $items_3->DefCatName;
                                }
                                $categs_4 = array();
                                foreach ($this->_shop_categs as $items_4) {
                                    if ($items_4->Parent_Id == $items_3->Id) {
                                        $items_4->Icon = $this->getImageNavi($items_4->Bild_Navi, $theme);
                                        if (!$items_4->Name) {
                                            $items_4->Name = $items_4->DefCatName;
                                        }
                                        $categs_5 = array();
                                        foreach ($this->_shop_categs as $items_5) {
                                            if ($items_5->Parent_Id == $items_4->Id) {
                                                $items_5->Icon = $this->getImageNavi($items_5->Bild_Navi, $theme);
                                                if (!$items_5->Name) {
                                                    $items_5->Name = $items_5->DefCatName;
                                                }
                                                $categs_5[] = $items_5;
                                            }
                                        }
                                        $items_4->Sub4 = $categs_5;
                                        $categs_4[] = $items_4;
                                    }
                                }
                                $items_3->Sub3 = $categs_4;
                                $categs_3[] = $items_3;
                            }
                        }
                        $items_2->Sub2 = $categs_3;
                        $categs_2[] = $items_2;
                    }
                }
                $item->Sub1 = $categs_2;
                $categs_shop[] = $item;
            }
        }

        $tpl_array = array(
            'navi_current'   => $this->getStarterShopcateg(Arr::getRequest('cid')),
            'plim'           => $this->settings['Produkt_Limit_Seite'],
            'MyShopNavi'     => $categs_shop,
            'MySearchCategs' => $categs_shop);
        $this->_view->assign($tpl_array);
    }

    /* Вывод иконки меню навигации */
    public function getImageNavi($value, $theme) {
        if (!empty($value) && is_file(UPLOADS_DIR . '/shop/navi_categs/' . $value)) {
            $src = 'uploads/shop/navi_categs/' . $value;
        } else {
            $src = 'theme/' . $theme . '/images/shop/navi_no_img.png';
        }
        return '<img class="absmiddle" src="' . $src . '" border="0" alt="" />';
    }

    public function get_child_items($cid) {
        $name = 'Parent_Id_' . $cid;
        if (isset($this->_shop_params[$name])) {
            return $this->_shop_params[$name];
        }
        $this->getLoadShopCategs();

        $out = array();
        if (!empty($cid)) {
            foreach ($this->_shop_categs as $row_subs) {
                if ($row_subs->Parent_Id == $cid) {
                    $out[] = $row_subs->Id;
                    foreach ($this->_shop_categs as $row_subs2) {
                        if ($row_subs2->Parent_Id == $row_subs->Id) {
                            $out[] = $row_subs2->Id;
                            foreach ($this->_shop_categs as $row_subs3) {
                                if ($row_subs3->Parent_Id == $row_subs2->Id) {
                                    $out[] = $row_subs3->Id;
                                    foreach ($this->_shop_categs as $row_subs4) {
                                        if ($row_subs4->Parent_Id == $row_subs3->Id) {
                                            $out[] = $row_subs4->Id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->_shop_params[$name] = !empty($out) ? 'OR Kategorie = ' . implode(' OR Kategorie = ', $out) : '';
        return $this->_shop_params[$name];
    }

    /* Определяем текущее положение в меню */
    public function getStarterShopcateg($categ) {
        $name = 'Categ_Id_' . $categ;
        if (isset($this->_shop_params[$name])) {
            return $this->_shop_params[$name];
        }
        $this->getLoadShopCategs();

        $c = array();
        if (!empty($categ)) {
            if (isset($this->_shop_categs[$categ])) {
                $row = $this->_shop_categs[$categ];
                $c[] = $row->Parent_Id;
                if (isset($this->_shop_categs[$row->Parent_Id])) {
                    $row = $this->_shop_categs[$row->Parent_Id];
                    if ($row->Parent_Id != 0) {
                        $c[] = $row->Parent_Id;
                    }
                    if (isset($this->_shop_categs[$row->Parent_Id])) {
                        $row = $this->_shop_categs[$row->Parent_Id];
                        if ($row->Parent_Id != 0) {
                            $c[] = $row->Parent_Id;
                        }
                        if (isset($this->_shop_categs[$row->Parent_Id])) {
                            $row = $this->_shop_categs[$row->Parent_Id];
                            if ($row->Parent_Id != 0) {
                                $c[] = $row->Parent_Id;
                                if (isset($this->_shop_categs[$row->Parent_Id])) {
                                    $row = $this->_shop_categs[$row->Parent_Id];
                                    $c[] = $row->Parent_Id;
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->_shop_params[$name] = $c;
        return $c;
    }

    public function getManufacturer() {
        $m = $this->_db->cache_fetch_assoc_all("SELECT Id, Name FROM " . PREFIX . "_hersteller ORDER BY Name ASC");
        return $m;
    }

    public function getManufacturerById($id) {
        $manu = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Id = '" . intval($id) . "' LIMIT 1");
        return $manu;
    }

    protected function step4($accept_id = 0) {
        $_SESSION['shopstep'] = 4;
        unset($_SESSION['order_number']);

        if (!empty($this->settings['GefundenOptionen'])) {
            $GefundenDurch = explode("\r\n", $this->settings['GefundenOptionen']);
            $this->_view->assign('GefundenDurch', $GefundenDurch);
        }

        $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Aktiv = 1 AND Id = '" . intval(Arr::getRequest('payment_id')) . "' LIMIT 1");
        $Versandarten = explode(',', $row->Versandarten);
        $Gruppen = explode(',', strtoupper($row->Gruppen));
        if (in_array($_SESSION['shipper_id'], $Versandarten) && in_array($_SESSION['user_group'], $Gruppen)) {
            $error = false;
        } else {
            $error = true;
        }

        if ($_SESSION['price_netto_zwi'] == '0' && $this->_canorder_emptybasket == 1) {
            $error = false;
            $zahlungsfrei = 1;
        }

        if ($error == true) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=shoporder&subaction=step3');
        }
        if ($_SESSION['step3_ok'] != 1) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        }
        if (empty($_SESSION['products'])) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
        }
        if (empty($_REQUEST['payment_id']) || $_SESSION['step3_ok'] != 1 || ($this->settings['Gastbestellung'] != 1 && $_SESSION['user_group'] == 2) || ($_SESSION['user_group'] == 2 && $_REQUEST['order'] != 'guest')) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=shoporder&subaction=step3');
        }

        if (!empty($_REQUEST['payment_id'])) {
            $_SESSION['payment_id'] = intval($_REQUEST['payment_id']);
        }
        $zahlungsfrei = (isset($zahlungsfrei)) ? $zahlungsfrei : '';
        if ($zahlungsfrei != 1) {
            if (!isset($_SESSION['shipper_id'])) {
                $guest_wl = (Arr::getRequest('order') == 'guest') ? '&order=guest' : '';
                $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=shoporder&subaction=step3' . $guest_wl);
            }
        }

        if (!isset($_SESSION['r_land'])) {
            $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=shoporder&step=2');
        }
        $_SESSION['r_land_lang'] = $this->getCountryName($_SESSION['r_land']);
        $_SESSION['l_land_lang'] = (isset($_SESSION['l_land'])) ? $this->getCountryName($_SESSION['l_land']) : '';
        $_SESSION['shipper_name'] = $this->getShipperName($_SESSION['shipper_id']);
        $_SESSION['payment_name'] = $this->getPaymentName($_SESSION['payment_id']);

        if ($accept_id == 1) {
            $this->_view->assign('agb_error', 1);
        } elseif (!empty($_REQUEST['agb_ok'])) {
            $this->_view->assign('agb_accept_checked', 1);
        }

        $tpl_array = array(
            'INF_MSG'        => $this->_lang['Shop_agb_accept'],
            'basket_display' => 'step4',
            'product_array'  => $this->initBasket(1));
        $this->_view->assign($tpl_array);
        $_SESSION['step4_ok'] = 1;

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_step_4'],
            'pagetitle' => $this->_lang['Shop_step_4'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/order_step4.tpl'));
        $this->_view->finish($seo_array);
    }

    public function shopOrder() {
        $this->_view->assign('shop_country', $this->settings['ShopLand']);
        $this->_view->assign('disable_curreny_selector', 1);

        $_REQUEST['subaction'] = !empty($_REQUEST['subaction']) ? $_REQUEST['subaction'] : 'step1';
        switch ($_REQUEST['subaction']) {
            case 'step1':
                $_SESSION['shopstep'] = 1;
                $_SESSION['shipping_summ'] = $_SESSION['payment_summ_extra'] = $_SESSION['step2_ok'] = $_SESSION['step3_ok'] = $_SESSION['step4_ok'] = 0;
                unset($_SESSION['shipper_id'], $_SESSION['shipping_is_free'], $_SESSION['payment_id'], $_SESSION['no_shipping'], $_SESSION['order_number']);

                $seo_array = array(
                    'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_step_1'],
                    'pagetitle' => $this->_lang['Shop_step_1'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
                    'content'   => $this->_view->fetch(THEME . '/shop/order_step1.tpl'));
                $this->_view->finish($seo_array);
                break;

            case 'step2':
                $_SESSION['type_client'] = (!empty($_REQUEST['type_client'])) ? Tool::cleanDigit($_REQUEST['type_client']) : $_SESSION['type_client'];
                $_SESSION['ship_ok'] = (!empty($_REQUEST['ship_ok'])) ? Tool::cleanDigit($_REQUEST['ship_ok']) : $_SESSION['ship_ok'];
                $_SESSION['shopstep'] = 2;
                $_SESSION['shipping_summ'] = $_SESSION['payment_summ_extra'] = $_SESSION['step2_ok'] = $_SESSION['step3_ok'] = $_SESSION['step4_ok'] = 0;
                unset($_SESSION['shipper_id'], $_SESSION['shipping_is_free'], $_SESSION['payment_id'], $_SESSION['no_shipping'], $_SESSION['order_number']);

                if (!empty($_POST['coupon']) && $this->settings['Gutscheine'] == 1 && !isset($_SESSION['coupon_code'])) {
                    $coupon = Tool::cleanAllow($_POST['coupon']);
                    $where = (Arr::getSession('user_group') == 2) ? ' AND Gastbestellung=1 ' : '';
                    $row_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_gutscheine WHERE Code = '" . $this->_db->escape($coupon) . "' $where LIMIT 1");
                    if (is_object($row_c)) {
                        $c_users = (!empty($row_c->BenutzerMulti)) ? explode(',', $row_c->BenutzerMulti) : '';
                        if ((($row_c->Eingeloest == 0) && ($row_c->GueltigBis >= $this->stime) && !in_array($_SESSION['benutzer_id'], $c_users)) || (($row_c->Endlos == 1 && !in_array($_SESSION['benutzer_id'], $c_users)) && ($row_c->GueltigBis >= $this->stime))) {
                            if ($row_c->MinBestellwert <= $_SESSION['price_netto'] || $row_c->MinBestellwert < 0.01) {
                                $_SESSION['coupon_code'] = $coupon;
                                $_SESSION['coupon_id'] = $row_c->Id;
                                $_SESSION['coupon_val'] = $row_c->Wert;
                                $_SESSION['coupon_typ'] = $row_c->Typ;
                                $_SESSION['coupon_hersteller'] = $row_c->Hersteller;
                                $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=showbasket');
                            } else {
                                $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=showbasket&couponerror=2');
                            }
                        } else {
                            $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=showbasket&couponerror=1');
                        }
                    } else {
                        $this->__object('Redir')->seoRedirect('index.php?sendform=1&p=shop&action=showbasket&couponerror=1');
                    }
                }
                $login = $this->__object('Login');
                if (Arr::getRequest('register') == 'new' && $_SESSION['loggedin'] != 1) {
                    $this->_view->assign('shop', 1);
                    $login->register(1);
                } else {
                    $login_email = Arr::getRequest('s_login_email');
                    if (Arr::getRequest('s_login') == 1 && !empty($login_email) && !Arr::nilRequest('s_login_pass')) {
                        if ($login->checkLogin($login_email, Arr::getRequest('s_login_pass'), true)) {
                            $login->saveLogin();
                        } else {
                            SX::syslog('Пользователь ' . $login_email . ' неудачная авторизация в магазине', '6', '');
                            $this->_view->assign('login_false', 1);
                            $login->cleanSession();
                            $login->cleanCookie();
                        }
                    }

                    if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
                    }
                    if ($_SESSION['user_group'] == 2) {
                        if ($this->settings['Gastbestellung'] == 1) {
                            $this->_view->assign('guest_order', 1);
                        }
                        if (Arr::getRequest('order') == 'guest') {
                            if ($this->settings['Gastbestellung'] != 1) {
                                $this->__object('Redir')->seoRedirect('index.php?sendorder=1&p=shop&action=shoporder&subaction=step2');
                            }
                            if (Arr::getRequest('save') == 1) {
                                $this->checkFormData('guestorder');
                            }
                            $this->_view->assign('countries', Tool::countries());
                            $this->_view->assign('logged_options', $this->_view->fetch(THEME . '/shop/order_step2_dataform.tpl'));
                        } else {
                            $this->_view->assign('logged_options', $this->_view->fetch(THEME . '/shop/order_step2_notloggedin.tpl'));
                        }
                    } else {
                        $user = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . $_SESSION['benutzer_id'] . "' AND Kennwort = '" . $_SESSION['login_pass'] . "' LIMIT 1");
                        if (Arr::getRequest('save') == 1) {
                            $this->checkFormData('user');
                        } else {
                            if (empty($_SESSION['r_middlename'])) {
                                $_SESSION['r_middlename'] = $user->MiddleName;
                            }
                            if (empty($_SESSION['r_bankname'])) {
                                $_SESSION['r_bankname'] = $user->BankName;
                            }
                            if (empty($_SESSION['r_email'])) {
                                $_SESSION['r_email'] = $user->Email;
                            }
                            if (empty($_SESSION['r_vorname'])) {
                                $_SESSION['r_vorname'] = $user->Vorname;
                            }
                            if (empty($_SESSION['r_nachname'])) {
                                $_SESSION['r_nachname'] = $user->Nachname;
                            }
                            if (empty($_SESSION['r_strasse'])) {
                                $_SESSION['r_strasse'] = $user->Strasse_Nr;
                            }
                            if (empty($_SESSION['r_plz'])) {
                                $_SESSION['r_plz'] = $user->Postleitzahl;
                            }
                            if (empty($_SESSION['r_ort'])) {
                                $_SESSION['r_ort'] = $user->Ort;
                            }
                            if (empty($_SESSION['r_telefon'])) {
                                $_SESSION['r_telefon'] = $user->Telefon;
                            }
                            if (empty($_SESSION['r_fax'])) {
                                $_SESSION['r_fax'] = $user->Telefax;
                            }
                            if (empty($_SESSION['r_firma'])) {
                                $_SESSION['r_firma'] = $user->Firma;
                            }
                            if (empty($_SESSION['r_ustid'])) {
                                $_SESSION['r_ustid'] = $user->UStId;
                            }
                            if (empty($_SESSION['user_country'])) {
                                $_SESSION['user_country'] = $user->LandCode;
                            }
                            $this->_view->assign('user', $user);
                            $_SESSION['r_land'] = strtoupper($user->LandCode);
                        }
                        $this->_view->assign('countries', Tool::countries());
                        $this->_view->assign('logged_options', $this->_view->fetch(THEME . '/shop/order_step2_dataform.tpl'));
                    }

                    $error = '';
                    if ($this->settings['BestMax'] != 0.00 && Arr::getSession('price') > $this->settings['BestMax']) {
                        $error = 'to_much';
                        $this->_view->assign('status_error', $error);
                    }

                    if ($this->settings['BestMin'] != 0.00 && Arr::getSession('price') < $this->settings['BestMin']) {
                        $error = 'not_reached';
                        $this->_view->assign('status_error', $error);
                    }

                    $seo_array = array(
                        'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_step_2'],
                        'pagetitle' => $this->_lang['Shop_step_2'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
                        'content'   => $this->_view->fetch(THEME . '/shop/order_step2.tpl'));
                    $this->_view->finish($seo_array);
                }
                break;

            case 'step3':
                $_SESSION['shipping_summ'] = $_SESSION['payment_summ_extra'] = $_SESSION['step3_ok'] = $_SESSION['step4_ok'] = 0;
                unset($_SESSION['shipper_id'], $_SESSION['shipping_is_free'], $_SESSION['payment_id'], $_SESSION['no_shipping'], $_SESSION['order_number']);
                $error = '';
                if ($this->settings['BestMax'] != 0.00 && Arr::getSession('price') > $this->settings['BestMax']) {
                    $_SESSION['shopstep'] = 3;
                    $error = 'to_much';
                    $this->_view->assign('sx_error', $error);
                }

                if ($this->settings['BestMin'] != 0.00 && Arr::getSession('price') < $this->settings['BestMin']) {
                    $_SESSION['shopstep'] = 3;
                    $error = 'not_reached';
                    $this->_view->assign('sx_error', $error);
                }

                if (empty($error)) {
                    $error = true;
                    $shipper_id = '';

                    if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
                    }
                    if (($_SESSION['step2_ok'] != 1) || ($this->settings['Gastbestellung'] != 1 && $_SESSION['user_group'] == 2) || ($_SESSION['user_group'] == 2 && $_REQUEST['order'] != 'guest')) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=shoporder&subaction=step2');
                    }
                    $_SESSION['shopstep'] = 3;
                    $nullShipper = ($_SESSION['gewicht'] <= 1) ? 1 : 0;

                    if ($this->checkShipper()) {
                        $error = false;
                        $this->_view->assign('shipper_found', 1);
                    }

                    if ($error == false) {
                        $_SESSION['step3_ok'] = 1;
                    }
                    $versand_sess = (isset($_POST['versand_id']) && is_numeric($_POST['versand_id'])) ? $_POST['versand_id'] : ((isset($_SESSION['shipper_id'])) ? $_SESSION['shipper_id'] : '');
                    $inf_id = (!empty($versand_sess)) ? $versand_sess : ((!empty($_POST['versand_id']) && is_numeric($_POST['versand_id'])) ? intval($_POST['versand_id']) : $versand_sess);
                    $_SESSION['shipper_id'] = $inf_id;
                    $check_s = $this->getShipper(0, $nullShipper);
                    if (!empty($check_s)) {
                        $current_shipper = $this->getShipperInf($inf_id);
                        if (!is_object($current_shipper)) {
                            $start = 1;
                            foreach ($check_s as $x) {
                                if ($start == 1) {
                                    $shipper_id = $inf_id = $x->Id;
                                }
                                $start++;
                            }
                        } else {
                            $shipper_id = $current_shipper->Id;
                        }
                        $this->_view->assign('Payments', $this->listPayments($shipper_id));
                    }

                    if (!empty($shipper_id)) {
                        $_SESSION['shipper_id'] = $shipper_id;
                    }
                    $sInf = $this->getShipperInf($inf_id);
                    $allowed_shipper = array();
                    $control = $this->getShipper(0, $nullShipper);
                    foreach ($control as $key) {
                        $allowed_shipper[] = $key->Id;
                    }

                    if (!in_array($_SESSION['shipper_id'], $allowed_shipper)) {
                        $this->_view->assign('error', '1');
                        unset($_SESSION['shipper_id']);
                    }

                    if (is_object($sInf)) {
                        $sInf->Gebuehr_PauschalS = $this->GetPrice($sInf->Gebuehr_Pauschal);
                    }

                    $tpl_array = array(
                        'shipping_summ' => (is_object($sInf) ? $this->GetPrice($sInf->Gebuehr_Pauschal) : ''),
                        'shipperInf'    => $sInf,
                        'shipper'       => $this->getShipper(0, $nullShipper));
                    $this->_view->assign($tpl_array);
                }

                $seo_array = array(
                    'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_step_3'],
                    'pagetitle' => $this->_lang['Shop_step_3'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
                    'content'   => $this->_view->fetch(THEME . '/shop/order_step3.tpl'));
                $this->_view->finish($seo_array);
                break;

            case 'step4':
                $this->step4();
                break;

            case 'final':
                $order_num_billing = $att_ext = '';
                if (empty($_REQUEST['agb_ok'])) {
                    $_SESSION['step3_ok'] = 1;
                    $_REQUEST['payment_id'] = $_SESSION['payment_id'];
                    $this->step4(1);
                } else {
                    if ($_SESSION['step4_ok'] != 1) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
                    }
                    if (empty($_SESSION['products'])) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
                    }
                    if (empty($_SESSION['payment_id']) || $_SESSION['step4_ok'] != 1 || ($this->settings['Gastbestellung'] != 1 && $_SESSION['user_group'] == 2) || ($_SESSION['user_group'] == 2 && $_REQUEST['order'] != 'guest')) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=shoporder&subaction=step3');
                    }

                    $query = "SELECT DetailInfo, Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . $_SESSION['payment_id'] . "' ; ";
                    $query .= "SELECT Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_versandarten WHERE Id = '" . $_SESSION['shipper_id'] . "'";
                    if ($this->_db->multi_query($query)) {
                        if (($result = $this->_db->store_result())) {
                            $payment_data = $result->fetch_assoc();
                            $result->close();
                        }
                        if (($result = $this->_db->store_next_result())) {
                            $shipping_data = $result->fetch_assoc();
                            $result->close();
                        }
                    }

                    $ref_url = str_replace('index.php', '', $this->__object('Redir')->link());
                    $logo = $this->settings['RechnungsLogo'];
                    $order_num = $this->orderNumber(10);
                    $order_time = date('d-m-Y, H:i', $this->stime);

                    if (!empty($payment_data['DetailInfo'])) {
                        $_SESSION['DetailInfo'] = str_replace('__ORDER__', $order_num, $payment_data['DetailInfo']);
                    }

                    $tpl_array = array(
                        'product_array'    => $this->initBasket(1),
                        'link_prefix'      => $ref_url,
                        'order_num'        => (!empty($order_num_billing) ? $order_num_billing : $order_num),
                        'order_time'       => $order_time,
                        'billing_logo'     => (!empty($logo) ? '<img src="' . $logo . '" alt="" />' : ''),
                        'shop_adress_html' => $this->settings['ShopAdresse']);
                    $this->_view->assign($tpl_array);

                    $_SESSION['payment_details'] = $payment_data['Beschreibung'];
                    $_SESSION['shipping_details'] = $shipping_data['Beschreibung'];

                    foreach ($_SESSION['products'] as $product => $pid_count) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Verkauft=Verkauft+{$pid_count} WHERE Id='{$product}'");
                    }

                    if ($this->settings['Bestand_Zaehlen'] == 0) {
                        foreach ($_SESSION['products'] as $product => $pid_count) {
                            $cc = $this->getArticleById($product);
                            if ($cc->Lagerbestand >= 1) {
                                $this->_db->query("UPDATE " . PREFIX . "_shop_produkte SET Lagerbestand=Lagerbestand-{$pid_count} WHERE Id='{$product}'");
                            }
                        }
                    }

                    $_SESSION['user_order_date'] = $this->stime;
                    $Ostatus = ($_SESSION['price_netto_zwi'] == '0' && $this->_canorder_emptybasket == 1) ? 'progress' : 'wait';

                    $insert_array = array(
                        'Status'              => $Ostatus,
                        'Datum'               => $this->stime,
                        'Ip'                  => IP_USER,
                        'Benutzer'            => $_SESSION['benutzer_id'],
                        'Email'               => Arr::getSession('r_email'),
                        'Betrag'              => numf($_SESSION['price']),
                        'Artikel'             => serialize($_SESSION['products']),
                        'TransaktionsNummer'  => (!empty($order_num_billing) ? $order_num_billing : $order_num),
                        'USt'                 => '',
                        'ZahlungsId'          => Arr::getSession('payment_id'),
                        'VersandId'           => Arr::getSession('shipper_id'),
                        'Rng_Firma'           => Arr::getSession('r_firma'),
                        'Rng_Vorname'         => Arr::getSession('r_vorname'),
                        'Rng_Nachname'        => Arr::getSession('r_nachname'),
                        'Rng_Strasse'         => Arr::getSession('r_strasse'),
                        'Rng_Plz'             => Arr::getSession('r_plz'),
                        'Rng_Ort'             => Arr::getSession('r_ort'),
                        'Rng_Land'            => Arr::getSession('r_land_lang'),
                        'Rng_Fon'             => Arr::getSession('r_telefon'),
                        'Rng_Fax'             => Arr::getSession('r_fax'),
                        'Rng_Email'           => Arr::getSession('r_email'),
                        'Lief_Vorname'        => Arr::getSession('l_vorname'),
                        'Lief_Nachname'       => Arr::getSession('l_nachname'),
                        'Lief_Strasse'        => Arr::getSession('l_strasse'),
                        'Lief_Plz'            => Arr::getSession('l_plz'),
                        'Lief_Ort'            => Arr::getSession('l_ort'),
                        'Lief_Fon'            => Arr::getSession('l_telefon'),
                        'Lief_Fax'            => Arr::getSession('l_fax'),
                        'Lief_Land'           => Arr::getSession('l_land_lang'),
                        'Lief_Firma'          => Arr::getSession('l_firma'),
                        'Gewicht'             => Arr::getSession('gewicht'),
                        'GutscheinWert'       => Arr::getSession('coupon_val'),
                        'GutscheinId'         => Arr::getSession('coupon_id'),
                        'KundenNachricht'     => Arr::getSession('r_nachricht'),
                        'UStId'               => Arr::getSession('r_ustid'),
                        'WarenwertBrutto'     => number_format(round($_SESSION['iamount_brutto'], 3), 2, '.', ''),
                        'WarenwertNetto'      => number_format(round($_SESSION['price_netto'], 3), 2, '.', ''),
                        'Versandkosten'       => number_format(round($_SESSION['shipping_summ'], 3), 2, '.', ''),
                        'ZuschlagZahlungsart' => number_format(round($_SESSION['payment_summ_extra'], 3), 2, '.', ''),
                        'Rng_MiddleName'      => Arr::getSession('r_middlename'),
                        'Rng_BankName'        => Arr::getSession('r_bankname'),
                        'Lief_MiddleName'     => Arr::getSession('l_middlename'));
                    $this->_db->insert_query('shop_bestellungen', $insert_array);
                    $_SESSION['id_num_order'] = $iid = $this->_db->insert_id();

                    $inf_array = array(
                        '__NUM__'   => $order_num,
                        '__DATE__'  => date('d-m-Y', $this->stime),
                        '__ORDER__' => $iid);
                    $inf_payment = $this->_text->replace($this->_lang['Payment_Info'], $inf_array);
                    $this->_view->assign('inf_payment', $inf_payment);

                    $x = $_SESSION['products'];
                    $vars = '';
                    foreach ($x as $ord => $key) {
                        $vars = $FreeFields = '';
                        $varis = explode('||', $ord);
                        $orders = $varis[0];

                        if (isset($_SESSION['product_' . $orders])) {
                            foreach ($_SESSION['product_' . $orders] as $free_item) {
                                $FreeFields .= $this->_db->escape($free_item) . "\n";
                            }
                        }

                        $article_options = $this->getArticleById($orders);

                        if (!empty($varis[1])) {
                            $varis_all = explode(',', $varis[1]);
                            foreach ($varis_all as $vakey) {
                                $variant = $this->getArticleVariant($vakey);
                                $vars .= $variant->Name_1 . " (" . $variant->VarName . ") $variant->Op\n";
                            }
                        }
                        if (!empty($orders) && !empty($article_options->Titel_1)) {
                            $insert_array = array(
                                'Benutzer'      => $_SESSION['benutzer_id'],
                                'Vorname'       => Arr::getSession('r_vorname'),
                                'Nachname'      => Arr::getSession('r_nachname'),
                                'Firma'         => Arr::getSession('r_firma'),
                                'Datum'         => $this->stime,
                                'Datum_TS'      => date('Y-m-d H:i:s', $this->stime),
                                'Bestellnummer' => (!empty($order_num_billing) ? $order_num_billing : $order_num),
                                'Artikelnummer' => $article_options->Artikelnummer,
                                'ArtikelName'   => $article_options->Titel_1,
                                'Anzahl'        => $key,
                                'Varianten'     => $vars,
                                'Konfig_Frei'   => $FreeFields);
                            $this->_db->insert_query('shop_bestellungen_items', $insert_array);
                        }
                    }
                    $_SESSION['price_final'] = numf($_SESSION['price']);
                    $_SESSION['order_number'] = (!empty($order_num_billing)) ? $order_num_billing : $order_num;
                    $_SESSION['shopstep'] = 'final';
                    $mail_content = $this->_view->fetch(THEME . '/shop/email_order_html.tpl');
                    $mail_content = str_replace('"uploads/', '"' . BASE_URL . '/uploads/', $mail_content);

                    if ($_SESSION['payment_id'] == '1') {
                        $PaymentBank = $this->__object('PaymentBank');
                        $this->_view->assign('price_string', $PaymentBank->convert($_SESSION['price_final']));
                        $this->_view->assign('schet_time', date('d.m.Y', $this->stime));
                        $order_content = $this->_view->fetch(THEME . '/payment/payment_bn.tpl');
                    } elseif ($_SESSION['payment_id'] == '7') {
                        $order_content = $this->_view->fetch(THEME . '/payment/payment_pd4.tpl');
                    } else {
                        $order_content = '';
                    }
                    $this->__object('ShopPayment')->get($ref_url);

                    if (!defined('PAYMENT_ERROR')) {
                        if (!empty($_SESSION['coupon_id'])) {
                            $GutscheinId = $_SESSION['coupon_id'];
                            $row_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_gutscheine WHERE Id = '" . $GutscheinId . "' LIMIT 1");
                            if ($row_c->Endlos != 1) {
                                $Update_Benutzer = "Benutzer = '" . $_SESSION['benutzer_id'] . "'";
                                $Update_Eingeloest = ", Eingeloest='" . $this->stime . "'";
                                $Update_Bestellnummern = ", Bestellnummer = '" . $iid . "'";
                            } else {
                                $Update_Benutzer = "BenutzerMulti = CONCAT(BenutzerMulti, ',', '" . $_SESSION['benutzer_id'] . "') ";
                                $Update_Eingeloest = '';
                                $Update_Bestellnummern = ", Bestellnummern = CONCAT(Bestellnummern, ',', '" . $iid . "') ";
                            }
                            $q_gs = "UPDATE " . PREFIX . "_shop_gutscheine SET {$Update_Benutzer}{$Update_Eingeloest}{$Update_Bestellnummern} WHERE Id = '" . $GutscheinId . "'";
                            $this->_db->query($q_gs);
                        }
                        $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Bestellung = '" . $this->_db->escape(base64_encode($mail_content)) . "', Order_Type = '" . $this->_db->escape(base64_encode($order_content)) . "' WHERE Id = '" . $iid . "'");

                        // Отправляем письмо с данными заказа пользователю
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $_SESSION['r_email'],
                            'to_name'   => $_SESSION['r_vorname'] . ' ' . $_SESSION['r_nachname'],
                            'text'      => $mail_content,
                            'subject'   => $this->settings['Subjekt_Bestellung'],
                            'fromemail' => $this->settings['Email_Abs'],
                            'from'      => $this->settings['Name_Abs'],
                            'type'      => 'text',
                            'attach'    => $att_ext,
                            'html'      => 1,
                            'prio'      => 1));
                        // Отправляем письмо о заказе по списку адресов для уведомления о заказе
                        $array_mail = explode(';', $this->settings['Email_Bestellung']);
                        foreach ($array_mail as $send_mail) {
                            if (!empty($send_mail)) {
                                SX::setMail(array(
                                    'globs'     => '1',
                                    'to'        => $send_mail,
                                    'to_name'   => '',
                                    'text'      => $mail_content,
                                    'subject'   => $this->settings['Subjekt_Best_Kopie'],
                                    'fromemail' => $this->settings['Email_Abs'],
                                    'from'      => $this->settings['Name_Abs'],
                                    'type'      => 'text',
                                    'attach'    => $att_ext,
                                    'html'      => 1,
                                    'prio'      => 1));
                            }
                        }
                        $this->unsetShopSessions();
                        unset($_SESSION['order_number']);
                    }
                }
                break;
        }
    }

    public function paymentInfo($id) {
        $Inf = $this->_db->cache_fetch_object("SELECT Icon, Beschreibung_" . $this->lc . " AS Beschreibung, BeschreibungLang_" . $this->lc . " AS BeschreibungLang, Name_" . $this->lc . " AS Name FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '" . intval($id) . "' AND Aktiv = '1' LIMIT 1");
        if (is_object($Inf)) {
            $this->_view->assign('payment_inf', $Inf);
            $headernav = '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Shop_payment_global_inf'] . ': ' . $Inf->Name;

            $seo_array = array(
                'headernav' => $headernav,
                'pagetitle' => sanitize($Inf->Name) . $this->_lang['PageSep'] . $this->_lang['Shop_payment_methods'],
                'generate'  => $Inf->Beschreibung,
                'content'   => $this->_view->fetch(THEME . '/payment/payment_info.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    public function paymentInfoAll() {
        $payment = $this->_db->fetch_object_all("SELECT Id, Icon, Name_$this->lc AS Name FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Aktiv = '1' ORDER BY Position ASC");
        $this->_view->assign('payment', $payment);
        return $this->_view->fetch(THEME . '/payment/payment_images.tpl');
    }

    public function shopInfpage($where) {
        switch ($where) {
            case 'agb':
                $text = $this->settings['ShopAGB'];
                $headerinf = $this->_lang['Reg_agb'];
                break;

            case 'refusal':
                $text = $this->settings['Widerruf'];
                $text = str_replace('__SHOPADRESSE__', $this->settings['ShopAdresse'], $text);
                $headerinf = $this->_lang['Shop_f_rcall_inf'];
                break;

            case 'privacy':
                $text = $this->settings['ShopDatenschutz'];
                $text = str_replace('__SHOPADRESSE__', $this->settings['ShopAdresse'], $text);
                $headerinf = $this->_lang['Shop_infopageDataInf'];
                break;
        }
        $this->_view->assign(array('Text' => $text, 'InfHeader' => $headerinf));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $headerinf,
            'pagetitle' => $headerinf . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/personal_inf.tpl'));
        $this->_view->finish($seo_array);
    }

    public function unsetShopSessions() {
        $visitor_key = Arr::getSession('visitor_key');
        if (Arr::getRequest('action') == 'delbasket') {
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE Code = '" . $this->_db->escape($visitor_key) . "'");
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb_gaeste WHERE BenutzerId = '" . $this->setBasketId() . "'");
        $Inhalt = isset($_SESSION['products']) ? serialize($_SESSION['products']) : '';

        if ($_REQUEST['action'] != 'delbasket') {
            $this->BasketRememberDel($Inhalt, $visitor_key);
        }
        unset($_SESSION['visitor_key']);
        unset($_SESSION['products']);
        unset($_SESSION['gewicht']);
        unset($_SESSION['price']);
        unset($_SESSION['price_netto']);
        unset($_SESSION['shopstep']);
        unset($_SESSION['payment_summ_extra']);
        unset($_SESSION['shipping_summ']);
        unset($_SESSION['step4_ok']);
        unset($_SESSION['step3_ok']);
        unset($_SESSION['step2_ok']);
        unset($_SESSION['shipper_id']);
        unset($_SESSION['payment_id']);
        unset($_SESSION['payment_summ_mipu']);
        unset($_SESSION['r_land_lang']);
        unset($_SESSION['diff_rl']);
        unset($_SESSION['l_land_lang']);
        unset($_SESSION['shipper_name']);
        unset($_SESSION['payment_name']);
        unset($_SESSION['payment_details']);
        unset($_SESSION['price_final']);
        unset($_SESSION['order_number']);
        unset($_SESSION['payment_summ_symbol']);
        unset($_SESSION['shipping_details']);
        unset($_SESSION['order_number']);
        unset($_SESSION['print_coupon_price']);
        unset($_SESSION['price_netto_coupon']);
        unset($_SESSION['price_netto_zwi']);
        unset($_SESSION['DetailInfo']);
        unset($_SESSION['shipping_is_free']);
        unset($_SESSION['id_num_order']);
        $this->unsetCoupon();
    }

    public function shippingCosts($popup = 0) {
        $shipper = array();
        $sql_shipper = $this->_db->query("SELECT SQL_CACHE *, Name_{$this->lc} AS Name, Beschreibung_{$this->lc} AS Beschreibung FROM " . PREFIX . "_shop_versandarten WHERE Aktiv = '1' ORDER BY POSITION ASC");
        while ($row_shipper = $sql_shipper->fetch_object()) {
            $volumes = array();
            $sql_ft = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_versandarten_volumen WHERE VersandId = '" . $row_shipper->Id . "' ORDER BY VON ASC");
            while ($row_ft = $sql_ft->fetch_object()) {
                if ($row_ft->Von == 0 || $row_ft->Von < 0.2) {
                    $row_ft->Von = '';
                }
                $row_ft->Gebuehr = $this->getPrice($row_ft->Gebuehr);
                $volumes[] = $row_ft;
            }
            $sql_ft->close();
            $row_shipper->Gebuehr_Pauschal = $this->getPrice($row_shipper->Gebuehr_Pauschal);
            $row_shipper->Volumes = $volumes;
            $Laender = explode(',', $this->_text->lower($row_shipper->Laender));
            foreach ($Laender as $Land) {
                $row_shipper->Laenders .= $this->landCode($Land) . '<br />';
            }
            $shipper[] = $row_shipper;
        }
        $sql_shipper->close();

        $fs = $this->getFreeShippingByCountry();
        $shipping_free_inf = $this->_lang['Shop_shipping_cost_inf2'];
        $shipping_free_inf = str_replace('__FREESHIPPING__', $this->numformat($this->getPrice($fs)) . ' ' . SX::get('options.CurrSymbol'), $shipping_free_inf);
        $title_html = $this->_lang['Shop_shipping_cost'];

        $tpl_array = array(
            'shipper'           => $shipper,
            'freeshipping'      => $fs,
            'popup'             => $popup,
            'freeshipping_inf2' => $shipping_free_inf,
            'Inf_Footer'        => $this->settings['VersandInfo_Footer'],
            'title_html'        => $title_html);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . $title_html,
            'pagetitle' => $title_html . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/shipping_cost.tpl'));
        $this->_view->finish($seo_array);
    }

    public function landCode($code) {
        return !empty($this->_lang['Country_' . $code]) ? $this->_lang['Country_' . $code] : $this->getCountryName(strtoupper($code));
    }

    public function myDownloads() {
        if ($_SESSION['user_group'] == 2 || !get_active('shop_downloads')) {
            $this->__object('Redir')->seoRedirect('index.php?p=shop');
        }
        if (!empty($_REQUEST['sub'])) {
            switch ($_REQUEST['sub']) {
                case 'showfile':
                    $download = false;
                    $row = $this->_db->fetch_object("SELECT SQL_CALC_FOUND_ROWS a.*, b.Datei, b.Titel, b.Beschreibung FROM " . PREFIX . "_shop_downloads_user AS a, " . PREFIX . "_shop_downloads AS b WHERE a.ArtikelId = '" . intval(Arr::getRequest('FileId')) . "' AND a.Benutzer = '" . $_SESSION['benutzer_id'] . "' AND ((a.DownloadBis >= '" . $this->stime . "') OR (b.DateiTyp='other') OR (b.DateiTyp='bugfix')) AND (b.Id = '" . intval(Arr::getRequest('getId')) . "' AND b.ArtId = a.ArtikelId)");
                    if (!is_object($row)) {
                        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=mydownloads');
                    }
                    if ($this->_db->found_rows() >= 1) {
                        $row->can_download = 1;
                    }
                    $this->_view->assign('file', $row);
                    break;

                case 'getfile':
                    $error = '';
                    $download = false;
                    $q = "SELECT SQL_CALC_FOUND_ROWS a.*, a.Id AS FileLogId, b.Datei FROM " . PREFIX . "_shop_downloads_user AS a, " . PREFIX . "_shop_downloads AS b WHERE a.ArtikelId = '" . intval(Arr::getRequest('FileId')) . "' AND a.Benutzer = '" . $_SESSION['benutzer_id'] . "' AND ((a.DownloadBis >= '" . $this->stime . "') OR (b.DateiTyp='other') OR (b.DateiTyp='bugfix')) AND a.Gesperrt != '1' AND (b.Id = '" . intval(Arr::getRequest('getId')) . "' AND b.ArtId = a.ArtikelId)";
                    $row = $this->_db->fetch_object($q);
                    if ($this->_db->found_rows() >= 1) {
                        $download = true;
                    }
                    if (empty($row->UrlLizenz) && $row->UrlLizenz_Pflicht == 1 && (empty($_POST['UrlLizenz']))) {
                        $download = false;
                    }
                    if ($download == true && (Arr::getPost('agb_ok') == 1)) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_downloads_user SET UrlLizenz = '" . $this->_db->escape(Arr::getPost('UrlLizenz')) . "', KommentarBenutzer = '" . $this->_db->escape(Arr::getPost('KommentarBenutzer')) . "', Downloads=Downloads+1 WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND ArtikelId = '" . intval(Arr::getPost('FileId')) . "' AND Id = '" . $row->FileLogId . "'");

                        $insert_array = array(
                            'Benutzer'          => $_SESSION['benutzer_id'],
                            'Produkt'           => Arr::getPost('FileName'),
                            'ProduktId'         => intval(Arr::getRequest('FileId')),
                            'Datum'             => $this->stime,
                            'Ip'                => IP_USER,
                            'UrlLizenz'         => Arr::getPost('UrlLizenz'),
                            'KommentarBenutzer' => Arr::getPost('KommentarBenutzer'));
                        $this->_db->insert_query('shop_download_log', $insert_array);

                        File::filerange(UPLOADS_DIR . '/shop/files/' . $row->Datei, 'application/octet-stream');
                    } else {
                        if (!isset($_POST['agb_ok']) || $_POST['agb_ok'] != 1) {
                            $error[] = $this->_lang['Shop_DownloaAGBError'];
                        }
                        if (empty($row->UrlLizenz) && $row->UrlLizenz_Pflicht == 1 && (empty($_POST['UrlLizenz']))) {
                            $error[] = $this->_lang['Shop_DownloadURLError'];
                        }
                        $this->_view->assign('error', $error);
                    }
                    break;
            }
        }

        $downloads = array();
        $sql = $this->_db->query("SELECT a.*, b.Id AS ARTIKELNUMMER, b.Titel_{$this->lc} AS ArtName FROM " . PREFIX . "_shop_downloads_user AS a, " . PREFIX . "_shop_produkte AS b WHERE a.Benutzer = '" . $_SESSION['benutzer_id'] . "' AND a.Benutzer != '0' AND b.Id = a.ArtikelId ORDER BY a.Position ASC");
        while ($row = $sql->fetch_object()) {
            if (is_object($row)) {
                $Files = array('DataFiles' => array(), 'DataFilesUpdates' => array(), 'DataFilesOther' => array(), 'DataFilesBugfixes' => array());
                $sql_df = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_downloads WHERE ArtId = '$row->ARTIKELNUMMER' ORDER BY Position ASC, Id DESC");
                while ($row_df = $sql_df->fetch_object()) {
                    switch ($row_df->DateiTyp) {
                        case 'full':
                            if ($row->DownloadBis < $this->stime) {
                                $row_df->Abgelaufen = 1;
                            }
                            $row_df->Beschreibung = str_replace('"', '&quot;', $row_df->Beschreibung);
                            $row_df->size = $this->getShopFileSize($row_df->Datei);
                            $Files['DataFiles'][] = $row_df;
                            break;

                        case 'update':
                            if ($row->DownloadBis < $this->stime) {
                                $row_df->Abgelaufen = 1;
                            }
                            $row_df->Beschreibung = str_replace('"', '&quot;', $row_df->Beschreibung);
                            $row_df->size = $this->getShopFileSize($row_df->Datei);
                            $Files['DataFilesUpdates'][] = $row_df;
                            break;

                        case 'other':
                            $row_df->Beschreibung = str_replace('"', '&quot;', $row_df->Beschreibung);
                            $row_df->size = $this->getShopFileSize($row_df->Datei);
                            $Files['DataFilesOther'][] = $row_df;
                            break;

                        case 'bugfix':
                            $row_df->Beschreibung = str_replace('"', '&quot;', $row_df->Beschreibung);
                            $row_df->size = $this->getShopFileSize($row_df->Datei);
                            $Files['DataFilesBugfixes'][] = $row_df;
                            break;
                    }
                }
                $sql_df->close();
                $row->DataFiles = $Files['DataFiles'];
                $row->DataFilesUpdates = $Files['DataFilesUpdates'];
                $row->DataFilesOther = $Files['DataFilesOther'];
                $row->DataFilesBugfixes = $Files['DataFilesBugfixes'];
            }
            $downloads[] = $row;
        }
        $sql->close();

        $this->_view->assign('downloads', $downloads);
        $headernav = '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=userlogin">' . $this->_lang['Login'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=shop&amp;action=mydownloads">' . $this->_lang['LoginExternVd'] . '</a>';

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $this->_lang['LoginExternVd'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/my_downloads.tpl'));
        $this->_view->finish($seo_array);
    }

    public function getShopFileSize($value) {
        return is_file(UPLOADS_DIR . '/shop/files/' . $value) ? round(filesize(UPLOADS_DIR . '/shop/files/' . $value) / 1024, 2) : '';
    }

    public function getInfoPanel() {
        return $this->_view->fetch(THEME . '/shop/infopanel.tpl');
    }

    public function getArticleVariant($id) {
        $row = $this->_db->cache_fetch_object("SELECT
            a.Name_1 as Name,
            a.Operant,
            a.Wert,
            b.Name_1
        FROM
            " . PREFIX . "_shop_varianten AS a,
            " . PREFIX . "_shop_varianten_kategorien AS b
        WHERE
            a.Id = '" . intval($id) . "'
        AND
            b.Id = a.KatId LIMIT 1");
        $row->VarName = $row->Name;
        $row->Op = $row->Operant . $row->Wert;
        return $row;
    }

    public function getArticleById($id) {
        return $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_produkte WHERE Id='" . intval($id) . "' LIMIT 1");
    }

    public function CallBackMsg() {
        switch (Arr::getRequest('reply')) {
            case 'error':
                $this->_view->assign('payment_false', '1');
                $title = $this->_lang['Global_error'];
                break;

            case 'wait':
                $this->_view->assign('payment_false', '2');
                $title = $this->_lang['Global_wait'];
                break;

            case 'success':
            default:
                $this->_view->assign('payment_false', '0');
                $title = $this->_lang['Shop_status_ok'];
                break;
        }

        $seo_array = array(
            'headernav' => $title,
            'pagetitle' => $title . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_result.tpl'));
        $this->_view->finish($seo_array);
    }

    public function DatabaseBasketLoad($id) {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $id = intval($id);
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_warenkorb WHERE Id = '$id' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' AND EingeloestAm < '1' LIMIT 1");
            if (is_object($row)) {
                $_SESSION['products'] = unserialize($row->Inhalt);
                $FreeVars = explode('|||', $row->InhaltKonf);
                foreach ($FreeVars as $varcId) {
                    $newFreeVars = unserialize($varcId);
                    $_SESSION['product_' . $newFreeVars['ProdId']] = $newFreeVars;
                }
                $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE Id = '$id' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' AND EingeloestAm < '1'");
            }
        }
        $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showbasket');
    }

    public function DatabaseBasketDel($id) {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE Id = '" . intval($id) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' AND EingeloestAm < '1'");
            $this->__object('Redir')->seoRedirect('index.php?p=shop&action=showsavedbaskets');
        }
    }

    public function DatabaseBasketDelAll() {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_warenkorb WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND EingeloestAm < '1'");
            $this->__object('Redir')->seoRedirect('index.php?action=showsavedbaskets&p=shop');
        }
    }

    public function SaveBasketSession($Inhalt, $Inhalt_Config = '') {
        if ($_SESSION['loggedin'] != 1 && $this->_gast_warenkorb_erinnerung) {
            $Ablauf = $this->stime + (86400 * $this->_gast_warenkorb_erinnerung_tage);
            $this->_db->query("INSERT INTO " . PREFIX . "_shop_warenkorb_gaeste (
                    BenutzerId,
                    Ablauf,
                    Inhalt,
                    InhaltConfig
            ) VALUES (
                    '" . $this->setBasketId() . "',
                    '" . $Ablauf . "',
                    '" . $this->_db->escape($Inhalt) . "',
                    '" . $this->_db->escape($Inhalt_Config) . "'
            ) ON DUPLICATE KEY UPDATE
                    Ablauf = '" . $Ablauf . "',
                    Inhalt='" . $this->_db->escape($Inhalt) . "',
                    InhaltConfig='" . $this->_db->escape($Inhalt_Config) . "'");
        }
        $this->BasketRemember($Inhalt, Arr::getSession('visitor_key'), $Inhalt_Config);
    }

    protected function BasketRemember($Inhalt, $Code = '', $Conf = '') {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1 && !empty($Code)) {
            $ZeitBis = $this->stime + (86400 * $this->_basket_cookietime);
            $ZeitBisRaw = date('d.m.Y, H:i:s', $ZeitBis);
            $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_warenkorb WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND Code='" . $Code . "' AND EingeloestAm < '1'");
            $check = $sql->fetch_object();
            $sql->close();
            if (is_object($check)) {
                $this->_db->query("UPDATE " . PREFIX . "_shop_warenkorb SET ZeitBisRaw = '" . $ZeitBisRaw . "', ZeitBis = '" . $ZeitBis . "', Inhalt = '$Inhalt', InhaltKonf = '" . $Conf . "' WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND Code='$Code'");
            } else {
                $insert_array = array(
                    'Benutzer'   => $_SESSION['benutzer_id'],
                    'ZeitBis'    => $ZeitBis,
                    'ZeitBisRaw' => $ZeitBisRaw,
                    'Inhalt'     => $Inhalt,
                    'InhaltKonf' => $Conf,
                    'Code'       => $Code);
                $this->_db->insert_query('shop_warenkorb', $insert_array);
            }
        }
    }

    protected function BasketRememberDel($Inhalt, $Code = '') {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $this->_db->query("UPDATE  " . PREFIX . "_shop_warenkorb SET  Gesperrt = '1', EingeloestAm = '" . $this->stime . "', EingeloestAmRaw = '" . date('d.m.Y, H:i:s', $this->stime) . "' WHERE Inhalt = '" . $Inhalt . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "' AND Code = '$Code' AND Gesperrt != '1' AND EingeloestAm < '1'");
        }
    }

    public function showSavedBaskets() {
        if (isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1 && !empty($_SESSION['visitor_key'])) {
            $saved = array();
            $saved_found = false;
            $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_warenkorb WHERE Benutzer = '" . $_SESSION['benutzer_id'] . "' AND Gesperrt != '1' AND EingeloestAm = '0' AND Code != '" . $_SESSION['visitor_key'] . "' ORDER BY Id DESC");
            while ($row = $sql->fetch_object()) {
                $Items = $newFreeVars = '';
                $FreeVars = explode('|||', base64_decode($row->InhaltKonf));
                foreach ($FreeVars as $varcId) {
                    $newFreeVars = unserialize($varcId);
                    $newFreeVarsProd['product_' . $newFreeVars['ProdId']] = $newFreeVars;
                }
                $saved_found = true;
                $Items = unserialize($row->Inhalt);
                $arr = $Items;
                reset($arr);
                $product_array = array();
                foreach ($arr as $key => $value) {
                    $part = explode('||', $key);
                    $row_p = $this->_db->cache_fetch_object("SELECT *, Titel_" . $this->lc . " AS Titel, Beschreibung_" . $this->lc . " AS Beschreibung FROM " . PREFIX . "_shop_produkte WHERE Id = '$part[0]' LIMIT 1");
                    if (is_object($row_p)) {
                        $var_array = array();
                        if (isset($newFreeVarsProd['product_' . $row_p->Id])) {
                            foreach ($newFreeVarsProd['product_' . $row_p->Id] as $free => $free_item) {
                                if ($free != 'ProdId') {
                                    $row_p->FreeFields .= $free_item . '<br />';
                                }
                            }
                        }

                        if (!empty($part[1])) {
                            $var_search = explode(',', $part[1]);
                            $row_p->Anzahl = $Items[$part[0] . '||' . $part[1]];
                            $row_p->Varianten = $var_search;
                            foreach ($var_search as $var) {
                                $row_v = $this->_db->cache_fetch_object("SELECT
                                a.Wert AS Wert_B,
                                        a.Gewicht,
                                        a.Name_" . $this->lc . " AS Name,
                                        a.Wert,
                                        a.Operant,
                                        b.Name_" . $this->lc . " AS KatName
                                FROM
                                        " . PREFIX . "_shop_varianten AS a,
                                        " . PREFIX . "_shop_varianten_kategorien AS b
                                WHERE
                                        b.Id = a.KatId
                                AND
                                        a.Id = '$var' LIMIT 1");
                                if (is_object($row_v)) {
                                    $var_array[] = $row_v;
                                }
                            }
                        } else {
                            $row_p->Anzahl = $Items[$row_p->Id];
                        }

                        $row_p->Bild = Tool::thumb('shop', $row_p->Bild, $this->settings['thumb_width_middle']);
                        $row_p->ProdLink = 'index.php?p=shop&amp;action=showproduct&amp;id=' . $row_p->Id . '&amp;cid=' . $row_p->Kategorie . '&amp;pname=' . translit($row_p->Titel);
                        $row_p->Vars = $var_array;
                        $product_array[] = $row_p;
                    }
                }
                $row->Positions = $product_array;
                $row->ZeitBis = $row->ZeitBis - (86400 * $this->_basket_cookietime);
                $product_array = '';
                $saved[] = $row;
            }
            $sql->close();

            $this->_view->assign(array('saved_found' => $saved_found, 'saved' => $saved));
            $headernav = '<a href="index.php?p=shop">' . $this->_lang['Shop'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=shop&amp;action=showsavedbaskets">' . $this->_lang['Shop_savedBasketLink1'] . '</a>';

            $seo_array = array(
                'headernav' => $headernav,
                'pagetitle' => $this->_lang['Shop_savedBasketLink1'] . $this->_lang['PageSep'] . $this->_lang['Shop'],
                'content'   => $this->_view->fetch(THEME . '/shop/basket_saved.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $this->__object('Redir')->seoRedirect('index.php?p=shop');
        }
    }

    public function AjaxCouponCodeDel() {
        $this->unsetCoupon();
        $Out = $this->_view->fetch(THEME . '/shop/coupon-ajaxinsert.tpl');
        SX::output($Out, true);
    }

    public function AjaxCouponCode() {
        if (!empty($_REQUEST['coupon']) && !empty($_SESSION['coupon_code'])) {
            unset($_SESSION['coupon_code']);
        }
        if (!empty($_REQUEST['coupon']) && $this->settings['Gutscheine'] == 1 && !isset($_SESSION['coupon_code'])) {
            $coupon = Tool::cleanAllow($_REQUEST['coupon']);
            $where = (Arr::getSession('user_group') == 2) ? ' AND Gastbestellung=1 ' : '';
            $row_c = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_gutscheine WHERE Code = '{$this->_db->escape($coupon)}' $where LIMIT 1");
            if (is_object($row_c)) {
                $c_users = !empty($row_c->BenutzerMulti) ? explode(',', $row_c->BenutzerMulti) : '';
                if (($row_c->Eingeloest == 0 && $row_c->GueltigBis >= $this->stime && !in_array($_SESSION['benutzer_id'], $c_users)) || (($row_c->Endlos == 1 && !in_array($_SESSION['benutzer_id'], $c_users)) && ($row_c->GueltigBis >= $this->stime))) {
                    if ($row_c->MinBestellwert <= $_SESSION['price'] || $row_c->MinBestellwert < 0.01) {
                        $_SESSION['coupon_code'] = $coupon;
                        $_SESSION['coupon_id'] = $row_c->Id;
                        $_SESSION['coupon_val'] = $row_c->Wert;
                        $_SESSION['coupon_typ'] = $row_c->Typ;
                        $_SESSION['coupon_hersteller'] = $row_c->Hersteller;

                        $this->couponHersteller($row_c->Typ, $row_c->Hersteller);

                        $this->_view->assign('coupon_success', 1);
                        $Out = $this->_view->fetch(THEME . '/shop/coupon-ajaxdel.tpl');
                        SX::output($Out, true);
                    } else {
                        $this->_view->assign('c_error', str_replace(array('__WERT__', '__WAEHRUNG__'), array($row_c->MinBestellwert, SX::get('options.CurrSymbol')), $this->_lang['ShopCouponAjError2Detail']));
                        $Out = $this->_view->fetch(THEME . '/shop/coupon-ajaxinsert.tpl');
                        SX::output($Out, true);
                    }
                } else {
                    $this->_view->assign('c_error', $this->_lang['ShopCouponAjError3']);
                    $cout = $this->_view->fetch(THEME . '/shop/coupon-ajaxinsert.tpl');
                    SX::output($cout, true);
                }
            } else {
                $this->_view->assign('c_error', $this->_lang['ShopCouponAjError']);
                $cout = $this->_view->fetch(THEME . '/shop/coupon-ajaxinsert.tpl');
                SX::output($cout, true);
            }
        } else {
            $this->_view->assign('c_error', $this->_lang['ShopCouponAjError']);
            $cout = $this->_view->fetch(THEME . '/shop/coupon-ajaxinsert.tpl');
            SX::output($cout, true);
        }
    }

    /* Метод отбора разрешенных производителей */
    protected function couponHersteller($type, $hersteller) {
        if ($type == 'pro') {
            $hersteller = explode(',', $hersteller);
            if (!empty($hersteller)) {
                $result = array();
                foreach ($this->getManufacturer() as $value) {
                    if (isset($value['Id']) && in_array($value['Id'], $hersteller)) {
                        $result[] = $value;
                    }
                }
                $this->_view->assign('coupon_hersteller', $result);
            }
        }
    }

    public function ShopPrais() {
        $limit = 1000;
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
                    a.Id,
                    a.Kategorie,
                    a.Artikelnummer,
                    a.Titel_{$this->lc} AS Titel,
                    a.Beschreibung_{$this->lc} AS Beschreibung,
                    a.Preis_Liste
		FROM
		    " . PREFIX . "_shop_produkte as a,
                    " . PREFIX . "_shop_kategorie AS b
		WHERE
                    b.Id = a.Kategorie
                AND
		    a.Aktiv = '1'
		AND
                    b.Aktiv = '1'
                AND
		    a.Sektion = '" . $_SESSION['area'] . "'
                    " . $this->whereGroup('a.Gruppen') . "
                    " . $this->whereGroup('b.Gruppen') . "
		ORDER BY
		    b.Parent_Id DESC, a.Kategorie DESC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $array = array();
        while ($row = $sql->fetch_object()) {
            $row->Preis_Liste = $this->getPrice($row->Preis_Liste);
            $array[$row->Kategorie][] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\"  href=\"index.php?p=shop&amp;action=prais&amp;page={s}\">{t}</a> "));
        }

        if (!empty($array)) {
            $categs = $this->getCategs();
            foreach ($array as $key => $value) {
                unset($array[$key]);
                $array[$this->getCateg($categs, $key, $limit)] = $value;
            }
        }

        $tpl_array = array(
            'prais' => $array,
            'plim'  => $this->settings['Produkt_Limit_Seite']
        );
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['Shop_Preis'],
            'pagetitle' => $this->_lang['Shop_Preis'] . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Shop'],
            'content'   => $this->_view->fetch(THEME . '/shop/prais.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function getCategs() {
        $array = array();
        $query = $this->_db->query("SELECT Id, Parent_Id, Name_{$this->lc} AS Name FROM " . PREFIX . "_shop_kategorie WHERE Aktiv='1' AND Sektion='" . intval($_SESSION['area']) . "'");
        while ($row = $query->fetch_assoc()) {
            $array[$row['Id']] = $row;
        }
        $query->close();
        return $array;
    }

    protected function getCategLink($a, $limit) {
        return '<a href="index.php?p=shop&amp;action=showproducts&amp;cid=' . $a['Id'] . '&amp;page=1&amp;limit=' . $limit . '&amp;t=' . translit($a['Name']) . '">' . sanitize($a['Name']) . '</a>';
    }

    protected function getCateg($array, $id, $limit) {
        $result = '';
        if (isset($array[$id])) {
            $result .= $this->getCategLink($array[$id], $limit);
            if ($array[$id]['Parent_Id'] != $id && $array[$id]['Parent_Id'] > 0) {
                $value = $this->getCateg($array, $array[$id]['Parent_Id'], $limit);
                if (!empty($value)) {
                    $result = $value . ' / ' . $result;
                }
            }
        }
        return $result;
    }

    protected function getCategCache($array, $id, $limit) {
        static $cache = array();
        if (!isset($cache[$id])) {
            $cache[$id] = $this->getCateg($array, $id, $limit);
        }
        return $cache[$id];
    }

    protected function pathinfo_utf($path) {
        if (strpos($path, '/') !== false) {
            $basename = $this->getEnd('/', $path);
        } elseif (strpos($path, '\\') !== false) {
            $basename = $this->getEnd('\\', $path);
        } else {
            return false;
        }
        if (empty($basename)) {
            return false;
        }
        $dirname = substr($path, 0, strlen($path) - strlen($basename) - 1);

        if (strpos($basename, '.') !== false) {
            $extension = $this->getEnd('.', $path);
            $filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1);
        } else {
            $extension = '';
            $filename = $basename;
        }
        return array('dirname' => $dirname, 'basename' => $basename, 'extension' => $extension, 'filename' => $filename);
    }

    public function getEnd($mask, $path) {
        $array = explode($mask, $path);
        return end($array);
    }

    /* Формируем цену групп со скидкой */
    protected function getPriceGroup($price = 0) {
        $price_group = array();
        $multi = Arr::getSession('Multiplikator');
        if (!empty($price)) {
            $sql = $this->_db->query("SELECT Name, Rabatt FROM " . PREFIX . "_benutzer_gruppen WHERE Rabatt != '0.00' AND Id != '1' ORDER BY Id DESC");
            while ($row = $sql->fetch_object()) {
                $row->price = ((($price / 100) * (100 - $row->Rabatt)) * $multi);
                $price_group[] = $row;
            }
            $sql->close();
        }
        $this->_view->assign('price_group', $price_group);
    }

    protected function setCheaper($title, $price = 0) {
        if (permission('shop_cheaper') && $this->settings['cheaper'] == 1 && $price > 0) {
            if (isset($_POST['cheaper_send']) && $_POST['cheaper_send'] == 1) {
                $this->getCheaper($title);
            }
            $this->_view->assign('cheaper_product', $title);
            $this->_view->assign('shop_cheaper', $this->_view->fetch(THEME . '/shop/shop_cheaper.tpl'));
        }
    }

    protected function getCheaper($title) {
        $array = array(
            'cheaper_where' => NULL,
            'cheaper_email' => NULL,
            'cheaper_text'  => NULL,
            'cheaper_price' => NULL,
            'cheaper_link'  => NULL);
        $array = Arr::getRequest($array);

        if (!empty($array['cheaper_where']) && !empty($array['cheaper_email'])) {
            $mail_array = array(
                '__IP__'       => IP_USER,
                '__MAIL__'     => $array['cheaper_email'],
                '__USERNAME__' => Tool::fullName(),
                '__DATUM__'    => date('d.m.Y H:i'),
                '__PRODUCT__'  => $title,
                '__PRICE__'    => numf($array['cheaper_price']),
                '__LINK__'     => $array['cheaper_link'],
                '__WHERE__'    => $array['cheaper_where'],
                '__TEXT__'     => $array['cheaper_text'],
                '__URL__'      => BASE_URL);
            $text = $this->_text->replace($this->_lang['cheaper_send'], $mail_array);

            $array_mail = explode(';', $this->settings['Email_Bestellung']);
            foreach ($array_mail as $send_mail) {
                if (!empty($send_mail)) {
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $send_mail,
                        'to_name'   => '',
                        'text'      => $text,
                        'subject'   => $this->_lang['cheaper_subject'],
                        'fromemail' => $this->settings['Email_Abs'],
                        'from'      => $this->settings['Name_Abs'],
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => '',
                        'prio'      => 3));
                }
            }
        }
    }

}
