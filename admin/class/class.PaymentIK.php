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

class PaymentIK extends Magic {

    public function callback() {
        if (Arr::getPost('ik_payment_state') == 'success') {
            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '8' LIMIT 1");
            $common = strtoupper(md5($payment_data['Install_Id'] . ':' . trim(Arr::getPost('ik_payment_amount')) . ':' . trim(Arr::getPost('ik_payment_id')) . ':' . trim(Arr::getPost('ik_paysystem_alias')) . ':' . trim(Arr::getPost('ik_baggage_fields')) . ':' . trim(Arr::getPost('ik_payment_state')) . ':' . trim(Arr::getPost('ik_trans_id')) . ':' . trim(Arr::getPost('ik_currency_exch')) . ':' . trim(Arr::getPost('ik_fees_payer')) . ':' . $payment_data['Betreff']));
            if ($common == trim(Arr::getPost('ik_sign_hash'))) {
                SX::syslog('Ошибка оплаты заказа в системе INTERKASSA, несоответствие хеша', '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
            $hash = Tool::cleanAllow($_POST['ik_baggage_fields']);
            $post_amount = trim(Arr::getPost('ik_payment_amount'));
            $check_call = md5($hash . $payment_data['Betreff'] . $payment_data['Install_Id']);
            $res = $this->_db->cache_fetch_assoc("SELECT price_order, order_number FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape($hash) . "' AND system='interkassa' LIMIT 1");
            if ($res['price_order'] == $post_amount) {
                $res_check = $this->_db->cache_fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($res['order_number']) . "' LIMIT 1");
                if ($res_check['Betrag'] == $post_amount) {
                    $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($res['order_number']) . "'");
                    SX::syslog('Успешно оплачен заказ по системе INTERKASSA, идентификатор заказа ' . $res['order_number'], '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                } else {
                    SX::syslog('Ошибка оплаты заказа в системе INTERKASSA, несоответствие суммы оплаты, идентификатор заказа ' . $res['order_number'], '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
            } else {
                SX::syslog('Попытка оплатить в системе INTERKASSA, несуществующий заказ c идентификатором ' . $res['order_number'], '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему INTERKASSA или предпринята попытка несанкционированного доступа', '2', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect();
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='8' LIMIT 1");

            $data = array($payment_data['Betreff'], numf($_SESSION['price_final']), $payment_data['Betreff'], $payment_data['Install_Id'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);
            $check_call = md5($hash . $payment_data['Betreff'] . $payment_data['Install_Id']);
            $ik_hash = md5($payment_data['Install_Id'] . ':' . $_SESSION['price_final'] . ':' . $_SESSION['id_num_order'] . '::' . $hash . ':' . $payment_data['Betreff']);
            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => $check_call,
                'system'          => 'interkassa');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'ik_hash'      => $ik_hash,
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_interkassa.tpl'));
        $this->_view->finish($seo_array);
    }

}
