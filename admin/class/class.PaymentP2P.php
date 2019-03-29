<?php
/*
  Класс для оплаты заказов через систему Pay2Pay (http://pay2pay.com)
  Разработчик: Федоров Олег (http://free-lance.ru/users/olegf13)
  Изменения внес: Александр Волошин */
if (!defined('SX_DIR')) {
    exit('Доступ запрещен');
}

class PaymentP2P extends Magic {

    public function callback() {
        if (!empty($_POST['xml']) && !empty($_POST['sign'])) {
            $xml_decoded = base64_decode(str_replace(' ', '+', $_POST['xml'])); // декодируем входные параметры
            $xml_object = simplexml_load_string($xml_decoded); // преобразуем входной xml в удобный для использования формат
            $in_order_id = $xml_object->order_id; // Номер заказа, переданный системой после оплаты
            $in_amount = $xml_object->amount; // Сумма заказа, переданная системой после оплаты

            $payment_data = $this->_db->cache_fetch_assoc("SELECT Install_Id, Betreff, Testmodus FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id = '18' LIMIT 1"); // достаём данные из настроек метода оплаты pay2pay в массив
            list($secret_key, $hidden_key) = explode(',', $payment_data['Betreff']);

            $xml_check_sign = base64_encode(md5($hidden_key . $xml_decoded . $hidden_key)); // строка для проверки
            $sign = str_replace(' ', '+', $_POST['sign']); // некоторые сервера заменяют символы «+» на пробелы во всех входящих параметрах, поэтому заменяем
            if ($sign != $xml_check_sign) {
                // если ключи не совпадают, то описываем ошибку
                $p2p_error = "Security check failed";
            } else {
                $check_call = md5($in_order_id . $secret_key . $payment_data['Install_Id'] . $hidden_key); // переменная для проверки совпадения данных об исходящем платеже и пришедшей информации
                $res = $this->_db->cache_fetch_assoc("SELECT price_order FROM " . PREFIX . "_shop_webpayment WHERE check_call='$check_call' AND system='pay2pay' LIMIT 1");
                // проверка на сумму заказа
                if ($res['price_order'] <= $in_amount) {
                    $res_check = $this->_db->cache_fetch_assoc("SELECT Id, Betrag FROM " . PREFIX . "_shop_bestellungen WHERE TransaktionsNummer = '" . $this->_db->escape($in_order_id) . "' LIMIT 1");
                    // ещё проверка на сумму заказа
                    if ($res_check['Betrag'] <= $in_amount) {
                        // записываем в базу информацию об успешной оплате
                        $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Payment = 1 WHERE TransaktionsNummer = '" . $this->_db->escape($in_order_id) . "'");
                        SX::syslog('Успешно оплачен заказ по системе Pay2Pay, идентификатор заказа ' . $in_order_id, '2', $_SESSION['benutzer_id']);
                        //SX::output('OK' . $res_check['Id'], true);
                    } else {
                        $p2p_error = "Amount check failed";
                    }
                } else {
                    $p2p_error = "Amount check failed";
                }
            }
            // Отвечаем серверу Pay2Pay
            if ($p2p_error == '') {
                $ret = "
                    <?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <response>
                            <status>yes</status>
                            <err_msg></err_msg>
                    </response>
                    ";
            } else {
                $ret = "
                    <?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <response>
                            <status>no</status>
                            <err_msg>$p2p_error</err_msg>
                    </response>
                    ";
            }
            exit($ret);
        }
    }

    public function get($ref_url) {
        if (stripos($_SERVER['HTTP_REFERER'], $ref_url) === false) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        } else {
            $lc = Arr::getSession('Langcode', 1);
            $payment_data = $this->_db->cache_fetch_assoc("SELECT *, Beschreibung_" . $lc . " AS Text, BeschreibungLang_" . $lc . " AS TextLang FROM " . PREFIX . "_shop_zahlungsmethoden WHERE Id='18' LIMIT 1");

            list($secret_key, $hidden_key) = explode(',', $payment_data['Betreff']);
            $data = array($payment_data['Install_Id'], $secret_key, $hidden_key, numf($_SESSION['price_final']), $_SESSION['order_number'], $_SESSION['order_number'], $_SESSION['user_order_date'], numf($_SESSION['price_final']));
            $data_implode = implode('|', $data);
            $hash = sha1($data_implode);

            $shop_currency = strtoupper($_SESSION['currency_registered']);
            $p2p_currencies = array('BYR', 'CNY', 'EUR', 'KZT', 'RUB', 'TJS', 'UAH ', 'USD', 'UZS');
            switch ($shop_currency) {
                case 'ГРН':
                    $currency_registered = 'UAH';
                    break;
                default:
                    if (in_array($shop_currency, $p2p_currencies)) {
                        $currency_registered = $shop_currency;
                    } else {
                        $currency_registered = 'RUB';
                    }
                    break;
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <request>
                        <version>1.3</version>
                        <merchant_id>' . $payment_data['Install_Id'] . '</merchant_id>
                        <language>' . $_SESSION['lang'] . '</language>
                        <order_id>' . $_SESSION['order_number'] . '</order_id>
                        <amount>' . $_SESSION['price_final'] . '</amount>
                        <currency>' . $currency_registered . '</currency>
                        <description>Оплата заказа №' . $_SESSION['order_number'] . '</description>
                        <test_mode>' . $payment_data['Testmodus'] . '</test_mode>
                </request>
			';
            $xml_encoded = base64_encode($xml);
            $sign = md5($secret_key . $xml . $secret_key);
            $sign_encoded = base64_encode($sign);

            $insert_array = array(
                'order_number'    => $_SESSION['order_number'],
                'price_order'     => $_SESSION['price_final'],
                'user_order_date' => $_SESSION['user_order_date'],
                'hashcode'        => $hash,
                'check_call'      => md5($_SESSION['order_number'] . $secret_key . $payment_data['Install_Id'] . $hidden_key),
                'system'          => 'pay2pay');
            $this->_db->insert_query('shop_webpayment', $insert_array);

            $tpl_array = array(
                'xml_encoded'  => $xml_encoded,
                'sign_encoded' => $sign_encoded,
                'payment_hash' => $hash,
                'payment_data' => $payment_data);
            $this->_view->assign($tpl_array);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_pay2pay.tpl'));
        $this->_view->finish($seo_array);
    }

}
