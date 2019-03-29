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

class PaymentRK extends Magic {

    public function callback() {
        if (!empty($_POST['Shp_hash'])) {
            $reason = Tool::cleanAllow($_POST['Shp_order']);
            $hash = Tool::cleanAllow($_POST['Shp_hash']);
            $post_amount = trim(Arr::getPost('OutSum'));
            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff, Testmodus FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '16' LIMIT 1");
            $common = strtoupper(md5($post_amount . ':' . trim(Arr::getPost('InvId')) . ':' . $payment_data['Testmodus'] . ':Shp_hash=' . $hash . ':Shp_order=' . $reason));
            if ($common != trim(Arr::getPost('SignatureValue'))) {
                SX::syslog('Ошибка оплаты заказа в системе ROBOKASSA, несоответствие хеша, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
            $check_call = md5($reason . $payment_data['Betreff'] . $payment_data['Install_Id'] . $payment_data['Testmodus']);
            $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND hashcode='" . $this->_db->escape($hash) . "' AND system='robokassa' LIMIT 1");
            if ($res['price_order'] == $post_amount) {
                $res_check = $this->_db->cache_fetch_assoc("SELECT Id, Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "' LIMIT 1");
                if ($res_check['Betrag'] == $post_amount) {
                    $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($reason) . "'");
                    SX::syslog('Успешно оплачен заказ по системе ROBOKASSA, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                    SX::output('OK' . $res_check['Id'], true);
                } else {
                    SX::syslog('Ошибка оплаты заказа в системе ROBOKASSA, несоответствие суммы оплаты, идентификатор заказа ' . $reason, '2', $_SESSION['benutzer_id']);
                    $this->__object('Redir')->redirect();
                }
            } else {
                SX::syslog('Попытка оплатить в системе ROBOKASSA, несуществующий заказ c идентификатором ' . $reason, '2', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect();
            }
        }
        SX::syslog('Глобальная ошибка при попытке оплаты через систему ROBOKASSA или предпринята попытка несанкционированного доступа', '2', $_SESSION['benutzer_id']);
        $this->__object('Redir')->redirect();
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='16' LIMIT 1");

            $data = array($payment_data['Install_Id'], numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $payment_data['Testmodus'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);
            $crc = md5($payment_data['Install_Id'] . ':' . numf($_SESSION['price_final']) . ':' . $_SESSION['id_num_order'] . ':' . $payment_data['Betreff'] . ':Shp_hash=' . $hash . ':Shp_order=' . $_SESSION['order_number']);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => md5($_SESSION['order_number'] . $payment_data['Betreff'] . $payment_data['Install_Id'] . $payment_data['Testmodus']),
                'system'          => 'robokassa');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'crc'          => $crc,
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_robokassa.tpl'));
        $this->_view->finish($seo_array);
    }

}
