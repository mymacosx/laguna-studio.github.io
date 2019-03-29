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

class PaymentW1 extends Magic {

    public function callback() {
        if (!empty($_POST['payment_hash'])) {
            if (empty($_POST['WMI_SIGNATURE'])) {
                $this->answer('Retry', 'Отсутствует параметр WMI_SIGNATURE');
            } elseif (empty($_POST['WMI_PAYMENT_NO'])) {
                $this->answer('Retry', 'Отсутствует параметр WMI_PAYMENT_NO');
            } elseif (empty($_POST['WMI_ORDER_STATE'])) {
                $this->answer('Retry', 'Отсутствует параметр WMI_ORDER_STATE');
            } else {
                $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '19' LIMIT 1");
                $params = array();
                foreach ($_POST as $key => $value) {
                    if ($key !== 'WMI_SIGNATURE') {
                        $params[$key] = $value;
                    }
                }
                $_POST['WMI_DESCRIPTION'] = urldecode($_POST['WMI_DESCRIPTION']);
                uksort($params, 'strcasecmp');
                $values = implode('', $params);
                $signature = base64_encode(pack("H*", md5($values . $payment_data['Install_Id'])));
                if ($signature == $_POST['WMI_SIGNATURE']) {
                    $check_call = md5(Arr::getPost('WMI_PAYMENT_NO') . $payment_data['Betreff'] . Arr::getPost('WMI_MERCHANT_ID') . Arr::getPost('WMI_CURRENCY_ID'));
                    $res = $this->_db->fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='" . $check_call . "' AND hashcode='" . $this->_db->escape($_POST['payment_hash']) . "' AND system='w1' LIMIT 1");
                    $post_amount = trim(Arr::getPost('WMI_PAYMENT_AMOUNT'));
                    if (is_array($res) && $res['price_order'] == $post_amount) {
                        $reason = $_POST['WMI_PAYMENT_NO'];
                        $res_check = $this->_db->fetch_assoc("SELECT Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "' LIMIT 1");
                        if ($res_check['Betrag'] == $post_amount) {
                            $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "'");
                            SX::syslog('Успешно оплачен заказ по системе Единый кошелек, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                            $this->answer('Ok', 'Заказ #' . $reason . ' оплачен!');
                        } else {
                            SX::syslog('Ошибка оплаты заказа ' . $reason . ' через систему Единый кошелек, несоответствие цены', '2', $_SESSION['benutzer_id']);
                            $this->answer('Retry', 'Изменена стоимость заказа');
                        }
                    }
                } else {
                    $this->answer('Retry', 'Неверная подпись ' . $_POST['WMI_SIGNATURE']);
                }
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему Единый кошелек, не передан обязательный параметр payment_hash', '2', $_SESSION['benutzer_id']);
        $this->answer('Retry', 'Не передан обязательный параметр payment_hash');
        sleep(2);
        $this->__object('Redir')->redirect();
    }

    protected function answer($result, $description) {
        $value = 'WMI_RESULT=' . strtoupper($result) . '&' . 'WMI_DESCRIPTION=' . urlencode($description);
        SX::output($value, true);
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='19' LIMIT 1");

            $data = array($payment_data['Install_Id'], numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $payment_hash = sha1($data_implode);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $payment_hash,
                'check_call'      => md5($_SESSION['order_number'] . $payment_data['Betreff'] . $payment_data['Install_Id'] . $payment_data['Testmodus']),
                'system'          => 'w1');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $payment_array = array(
                'WMI_MERCHANT_ID'    => $payment_data['Install_Id'],
                'WMI_PAYMENT_AMOUNT' => $_SESSION['price_final'],
                'WMI_CURRENCY_ID'    => $payment_data['Testmodus'],
                'WMI_PAYMENT_NO'     => $_SESSION['order_number'],
                'WMI_DESCRIPTION'    => $this->_view->getTemplateVars('inf_payment'),
                'WMI_SUCCESS_URL'    => BASE_URL . '/index.php?payment=w1&p=shop&action=callback&reply=success',
                'WMI_FAIL_URL'       => BASE_URL . '/index.php?payment=w1&p=shop&action=callback&reply=error',
                'WMI_CULTURE_ID'     => $_SESSION['lang'],
                'payment_hash'       => $payment_hash);

            uksort($payment_array, 'strcasecmp');
            $fields = implode('', $payment_array);
            $payment_array['WMI_SIGNATURE'] = base64_encode(pack("H*", md5($fields . $payment_data['Betreff'])));
            $payment_array['WMI_DESCRIPTION'] = $payment_array['WMI_DESCRIPTION'];

            $tpl_array = array(
                'payment_array' => $payment_array,
                'payment_data'  => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_w1.tpl'));
        $this->_view->finish($seo_array);
    }

}
