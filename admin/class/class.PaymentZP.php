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

class PaymentZP extends Magic {

    public function callback() {
        if (Arr::getPost('LMI_PREREQUEST') == 1) {
            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '13' LIMIT 1");
            if ($payment_data['Install_Id'] != trim(Arr::getPost('LMI_PAYEE_PURSE'))) {
                $this->__object('Redir')->redirect();
            }
            $check_call = md5(trim(Arr::getPost('FIELD_1')) . $payment_data['Betreff'] . $payment_data['Install_Id']);
            $post_amount = trim(Arr::getPost('LMI_PAYMENT_AMOUNT'));
            $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape(Tool::cleanAllow($_POST['FIELD_2'])) . "' AND system='zpayment' LIMIT 1");
            if ($res['price_order'] == $post_amount) {
                $res_check = $this->_db->cache_fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape(Tool::cleanAllow($_POST['FIELD_1'])) . "' LIMIT 1");
                if ($res_check['Betrag'] == $post_amount) {
                    SX::output('YES', true);
                }
            }
        } else {
            if (!empty($_POST['LMI_HASH'])) {
                $reason = Tool::cleanAllow($_POST['FIELD_1']);
                $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '13' LIMIT 1");
                $common = strtoupper(md5($_POST['LMI_PAYEE_PURSE'] . $_POST['LMI_PAYMENT_AMOUNT'] . $_POST['LMI_PAYMENT_NO'] . $_POST['LMI_MODE'] . $_POST['LMI_SYS_INVS_NO'] . $_POST['LMI_SYS_TRANS_NO'] . $_POST['LMI_SYS_TRANS_DATE'] . $payment_data['Betreff'] . $_POST['LMI_PAYER_PURSE'] . $_POST['LMI_PAYER_WM']));
                if ($common != trim(Arr::getPost('LMI_HASH'))) {
                    SX::syslog('Ошибка оплаты заказа в системе Z-Payment, несоответствие хеша, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
                $check_call = md5(trim(Arr::getPost('FIELD_1')) . $payment_data['Betreff'] . $payment_data['Install_Id']);
                $post_amount = trim(Arr::getPost('LMI_PAYMENT_AMOUNT'));
                $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape(Tool::cleanAllow($_POST['FIELD_2'])) . "' AND system='zpayment' LIMIT 1");
                if ($res['price_order'] == $post_amount) {
                    $res_check = $this->_db->cache_fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "' LIMIT 1");
                    if ($res_check['Betrag'] == $post_amount) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "'");
                        SX::syslog('Успешно оплачен заказ по системе Z-Payment, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        $this->__object('Redir')->redirect();
                    } else {
                        SX::syslog('Ошибка оплаты заказа в системе Z-Payment, несоответствие суммы оплаты, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        $this->__object('Redir')->redirect();
                    }
                } else {
                    SX::syslog('Попытка оплатить в системе Z-Payment, несуществующий заказ c идентификатором ' . $reason, '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему Z-Payment или предпринята попытка несанкционированного доступа', '2', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect();
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='13' LIMIT 1");

            $data = array($payment_data['Install_Id'], numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);
            $z_hash = md5($payment_data['Install_Id'] . $_SESSION['id_num_order'] . numf($_SESSION['price_final']) . $payment_data['Betreff']);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => md5($_SESSION['order_number'] . $payment_data['Betreff'] . $payment_data['Install_Id']),
                'system'          => 'zpayment');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'payment_hash'   => $hash,
                'payment_z_hash' => $z_hash,
                'payment_data'   => $payment_data);
            $this->_view->assign($tpl_array);
        }
        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_zpayment.tpl'));
        $this->_view->finish($seo_array);
    }

}
