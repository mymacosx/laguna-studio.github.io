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

class PaymentLP extends Magic {

    public function reset() {
        if (!empty($_POST['status'])) {
            if ($_POST['status'] == 'success') {
                $this->__object('Redir')->redirect('index.php?payment=lp&p=shop&action=callback&reply=success');
            } elseif ($_POST['status'] == 'wait_secure') {
                $this->__object('Redir')->redirect('index.php?payment=lp&p=shop&action=callback&reply=wait');
            } else {
                $this->__object('Redir')->redirect('index.php?payment=lp&p=shop&action=callback&reply=error');
            }
        }
        $this->__object('Redir')->redirect();
    }

    public function callback() {
        if (!empty($_POST['status'])) {
            $reason = Tool::cleanAllow($_POST['order_number']);
            $post_amount = trim(Arr::getPost('amount'));
            $status = trim(Arr::getPost('status'));
            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff, Testmodus FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '17' LIMIT 1");
            $sum = '|' . trim(Arr::getPost('version')) . '|' . $payment_data['Betreff'] . '|' . trim(Arr::getPost('action_name')) . '|' . trim(Arr::getPost('sender_phone')) . '|' . $payment_data['Install_Id'] . '|' . $post_amount . '|' . $payment_data['Testmodus'] . '|' . trim(Arr::getPost('order_id')) . '|' . trim(Arr::getPost('transaction_id')) . '|' . $status . '|' . trim(Arr::getPost('code')) . '|';
            $common = base64_encode(sha1($sum, true));
            if ($common != trim(Arr::getPost('signature'))) {
                SX::syslog('Ошибка оплаты заказа в системе LiqPAY, несоответствие хеша, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
            $check_call = md5($reason . $payment_data['Betreff'] . $payment_data['Install_Id'] . $payment_data['Testmodus']);
            $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape(Tool::cleanAllow($_POST['hash'])) . "' AND system='liqpay' LIMIT 1");
            if ($res['price_order'] == $post_amount) {
                $res_check = $this->_db->cache_fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "' LIMIT 1");
                if ($res_check['Betrag'] == $post_amount) {
                    if ($status == 'success') {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "'");
                        SX::syslog('Успешно оплачен заказ по системе LiqPAY, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        exit;
                    } elseif ($status == 'wait_secure') {
                        SX::syslog('Заказ на оплату принят в системе LiqPAY и ожидает обработки, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        exit;
                    } else {
                        SX::syslog('Ошибка обработки заказа в системе LiqPAY, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        exit;
                    }
                } else {
                    SX::syslog('Ошибка оплаты заказа в системе LiqPAY, несоответствие суммы оплаты, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
            } else {
                SX::syslog('Попытка оплатить в системе LiqPAY, несуществующий заказ c идентификатором ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему LiqPAY или предпринята попытка несанкционированного доступа', '2', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect();
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='17' LIMIT 1");

            $data = array(numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']), $payment_data['Install_Id']);
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => md5($_SESSION['order_number'] . $payment_data['Betreff'] . $payment_data['Install_Id'] . $payment_data['Testmodus']),
                'system'          => 'liqpay');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }
        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_liqpay.tpl'));
        $this->_view->finish($seo_array);
    }

}
