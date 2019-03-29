<?php
/*
  Класс для оплаты заказов через систему EasyPay
  Разработчик: EasyPay
  Изменения внес: Александр Волошин
*/
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

class PaymentEP extends Magic {

    public function callback() {
        $array = array(
            'order_mer_code'   => '',
            'sum'              => '',
            'mer_no'           => '',
            'card'             => '',
            'notify_signature' => '',
            'purch_date'       => '',
        );
        $array = Arr::getPost($array);
        extract($array);

        $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '20' LIMIT 1"); // достаём данные из настроек метода оплаты EasyPay
        list($EP_MerNo, $web_key) = explode(',', $payment_data['Install_Id']);

        $check_call = md5($order_mer_code . $payment_data['Install_Id']);
        $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND system='EasyPay' LIMIT 1"); //получаем цену заказа

        $sign = md5($order_mer_code . $sum . $mer_no . $card . $purch_date . $web_key);

        if ($notify_signature == $sign) {                  // проверка правильности подписи
            if (ceil($res['price_order']) == ceil($sum)) { // проверка соответствия суммы заказа и оплаченной
                // записываем в базу информацию об успешной оплате и меняем статус
                $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($order_mer_code) . "'");

                SX::syslog('Успешно оплачен заказ по системе EasyPay, заказ ' . $order_mer_code, '2', $_SESSION['benutzer_id']);
                $this->__object('Response')->get(200);
                SX::output('OK | payment received', true);
            } else { // суммы не совпадают
                SX::syslog('Ошибка оплаты заказа по системе EasyPay, суммы не совпадают, заказ ' . $order_mer_code, '2', $_SESSION['benutzer_id']);
                $this->__object('Response')->get(400);
                SX::output('FAILED | amounts of money is not equival', true);
            }
        } else { // подписи не совпадают
            SX::syslog('Ошибка оплаты заказа по системе EasyPay, подписи не совпадают, заказ ' . $order_mer_code, '2', $_SESSION['benutzer_id']);
            $this->__object('Response')->get(400);
            SX::output('FAILED | wrong notify signature', true);
        }
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_CS->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '20' LIMIT 1");

            $account = explode(',', $payment_data['Install_Id']);

            $EP_MerNo = $account[0];                  // Номер Поставщика
            $web_key = $account[1];                   // Ключ, участвующий в электронной подписи
            $EP_Expires = $payment_data['Betreff'];   // Время действительности счета в днях
            $EP_Debug = $payment_data['Testmodus'];   // Режим отладки
            $EP_OrderNo = $_SESSION['order_number'];  // Номер счета
            $EP_Sum = ceil($_SESSION['price_final']); // Сумма
            $EP_OrderInfo = 'Oплата заказа №' . $_SESSION['order_number']; //Комментарий счета

            $data = array($account[0], numf($_SESSION['price_final']), $_SESSION['order_number'], $payment_data['Betreff'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $hash         = sha1($data_implode);
            $check_call   = md5($EP_OrderNo . $payment_data['Install_Id']);

            $EP_Hash = md5($EP_MerNo . $web_key . $EP_OrderNo . $EP_Sum); //Электронная подпись

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => $check_call,
                'system'          => 'EasyPay');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'EP_MerNo'     => $EP_MerNo,
                'web_key'      => $web_key,
                'EP_OrderNo'   => $EP_OrderNo,
                'EP_Sum'       => $EP_Sum,
                'EP_Hash'      => $EP_Hash,
                'EP_Expires'   => $EP_Expires,
                'EP_Debug'     => $EP_Debug,
                'EP_OrderInfo' => $EP_OrderInfo,
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_easypay.tpl'));
        $this->_view->finish($seo_array);
    }

}
