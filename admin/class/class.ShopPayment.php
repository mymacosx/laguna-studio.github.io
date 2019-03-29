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

class ShopPayment extends Magic {

    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    public function get($ref_url) {
        switch ($_SESSION['payment_id']) {
            case '1':
                $this->__object('PaymentBank')->account($ref_url);
                break;

            case '7':
                $this->__object('PaymentBank')->pd4($ref_url);
                break;

            case '8':
                $this->__object('PaymentIK')->get($ref_url);
                break;

            case '9':
                $this->__object('PaymentWM')->get($ref_url);
                break;

            case '10':
                $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $this->Lc . " AS Text, BeschreibungLang_" . $this->Lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '10' LIMIT 1");
                $_SESSION['back_url'] = $ref_url . 'index.php?p=shop&area=' . AREA;
                $_SESSION['return_url'] = $ref_url . 'index.php?p=shop';
                $this->_view->assign('payment_data', $payment_data);

                $seo_array = array(
                    'headernav' => $this->_lang['Shop_thankyou_title'],
                    'pagetitle' => $this->_lang['Shop_thankyou_title'],
                    'content'   => $this->_view->fetch(THEME . '/payment/payment_paypal.tpl'));
                $this->_view->finish($seo_array);
                break;

            case '11':
                $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $this->Lc . " AS Text, BeschreibungLang_" . $this->Lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '11' LIMIT 1");
                $_SESSION['back_url'] = $ref_url . 'index.php?p=shop&area=' . AREA;
                $_SESSION['return_url'] = $ref_url . 'index.php?p=shop';
                $this->_view->assign('payment_data', $payment_data);

                $seo_array = array(
                    'headernav' => $this->_lang['Shop_thankyou_title'],
                    'pagetitle' => $this->_lang['Shop_thankyou_title'],
                    'content'   => $this->_view->fetch(THEME . '/payment/payment_moneybookers.tpl'));
                $this->_view->finish($seo_array);
                break;

            case '12':
                $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $this->Lc . " AS Text, BeschreibungLang_" . $this->Lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '12' LIMIT 1");
                $this->_view->assign('payment_data', $payment_data);

                $seo_array = array(
                    'headernav' => $this->_lang['Shop_thankyou_title'],
                    'pagetitle' => $this->_lang['Shop_thankyou_title'],
                    'content'   => $this->_view->fetch(THEME . '/payment/payment_worldpay.tpl'));
                $this->_view->finish($seo_array);
                break;

            case '13':
                $this->__object('PaymentZP')->get($ref_url);
                break;

            case '14':
                $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $this->Lc . " AS Text, BeschreibungLang_" . $this->Lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '14' LIMIT 1");
                $this->_view->assign('payment_data', $payment_data);

                $seo_array = array(
                    'headernav' => $this->_lang['Shop_thankyou_title'],
                    'pagetitle' => $this->_lang['Shop_thankyou_title'],
                    'content'   => $this->_view->fetch(THEME . '/payment/payment_assist.tpl'));
                $this->_view->finish($seo_array);
                break;

            case '15':
                $this->__object('PaymentRBK')->get($ref_url);
                break;

            case '16':
                $this->__object('PaymentRK')->get($ref_url);
                break;

            case '17':
                $this->__object('PaymentLP')->get($ref_url);
                break;

            case '18':
                $this->__object('PaymentP2P')->get($ref_url);
                break;

            case '19':
                $this->__object('PaymentW1')->get($ref_url);
                break;

            case '20':
                $this->__object('PaymentEP')->get($ref_url);
                break;

            default:
                $seo_array = array(
                    'headernav' => $this->_lang['Shop_thankyou_title'],
                    'pagetitle' => $this->_lang['Shop_thankyou_title'],
                    'content'   => $this->_view->fetch(THEME . '/payment/payment_thankyou.tpl'));
                $this->_view->finish($seo_array);
                break;
        }
    }

}
