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

class PaymentRBK extends Magic {

    public function callback() {
        if (!empty($_POST['paymentStatus'])) {
            $reason = Tool::cleanAllow($_POST['userField_1']);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff, Testmodus FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '15' LIMIT 1");
            $account = explode(',', $payment_data['Install_Id']);
            $payment_data['IdSeller'] = $account[0];
            $payment_data['IdPayer'] = $account[1];
            $common = md5($payment_data['IdSeller'] . '::' . trim(Arr::getPost('orderId')) . '::' . trim(Arr::getPost('serviceName')) . '::' . $payment_data['IdPayer'] . '::' . trim(Arr::getPost('recipientAmount')) . '::' . $payment_data['Testmodus'] . '::' . trim(Arr::getPost('paymentStatus')) . '::' . trim(Arr::getPost('userName')) . '::' . trim(Arr::getPost('userEmail')) . '::' . trim(Arr::getPost('paymentData')) . '::' . $payment_data['Betreff']);
            if ($common != trim(Arr::getPost('hash'))) {
                SX::syslog('Ошибка оплаты заказа в системе RBK Money, несоответствие хеша, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
            $check_call = md5($reason . $payment_data['Betreff'] . $payment_data['IdSeller'] . $payment_data['IdPayer'] . $payment_data['Testmodus']);
            $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape(Tool::cleanAllow($_POST['userField_2'])) . "' AND system='rbkmoney' LIMIT 1");
            $post_amount = trim(Arr::getPost('recipientAmount'));
            if ($res['price_order'] == $post_amount) {
                $res_check = $this->_db->cache_fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "' LIMIT 1");
                if ($res_check['Betrag'] == $post_amount) {
                    if ($_POST['paymentStatus'] == 5) {
                        $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "'");
                        SX::syslog('Успешно оплачен заказ по системе RBK Money, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        SX::output('OK', true);
                    } elseif ($_POST['paymentStatus'] == 3) {
                        SX::syslog('Заказ принят на обработку в системе RBK Money, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                        SX::output('OK', true);
                    }
                } else {
                    SX::syslog('Ошибка оплаты заказа в системе RBK Money, несоответствие суммы оплаты, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
            } else {
                SX::syslog('Попытка оплатить в системе RBK Money, несуществующий заказ c идентификатором ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему RBK Money или предпринята попытка несанкционированного доступа', '2', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect();
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='15' LIMIT 1");
            $account = explode(',', $payment_data['Install_Id']);
            $payment_data['IdSeller'] = $account[0];
            $payment_data['IdPayer'] = $account[1];

            $data = array($payment_data['IdSeller'], numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']), $payment_data['IdPayer']);
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);
            $check_call = md5($_SESSION['order_number'] . $payment_data['Betreff'] . $payment_data['IdSeller'] . $payment_data['IdPayer'] . $payment_data['Testmodus']);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => $check_call,
                'system'          => 'rbkmoney');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_rbk.tpl'));
        $this->_view->finish($seo_array);
    }

}
