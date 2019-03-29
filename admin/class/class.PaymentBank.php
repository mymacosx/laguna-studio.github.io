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

class PaymentBank extends Magic {

    public function pd4($ref_url) {
        $error = true;
        if (stristr($_SERVER['HTTP_REFERER'], $ref_url)) {
            $error = false;
        }
        if ($error == true) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_sberbank.tpl'));
        $this->_view->finish($seo_array);
    }

    public function account($ref_url) {
        $error = true;
        if (stristr($_SERVER['HTTP_REFERER'], $ref_url)) {
            $error = false;
        }
        if ($error == true) {
            $this->_view->assign('payment_error', 1);
            SX::setDefine('PAYMENT_ERROR', 1);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Shop_thankyou_title'],
            'pagetitle' => $this->_lang['Shop_thankyou_title'],
            'content'   => $this->_view->fetch(THEME . '/payment/payment_bank.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function number2string($n, $rod) {
        $a = floor($n / 100);
        $b = floor(($n - $a * 100) / 10);
        $c = $n % 10;
        $s = '';
        switch ($a) {
            case 1:
                $s = 'сто';
                break;
            case 2:
                $s = 'двести';
                break;
            case 3:
                $s = 'триста';
                break;
            case 4:
                $s = 'четыреста';
                break;
            case 5:
                $s = 'пятьсот';
                break;
            case 6:
                $s = 'шестьсот';
                break;
            case 7:
                $s = 'семьсот';
                break;
            case 8:
                $s = 'восемьсот';
                break;
            case 9:
                $s = 'девятьсот';
                break;
        }
        $s .= ' ';
        if ($b != 1) {
            switch ($b) {
                case 1:
                    $s .= 'десять';
                    break;
                case 2:
                    $s .= 'двадцать';
                    break;
                case 3:
                    $s .= 'тридцать';
                    break;
                case 4:
                    $s .= 'сорок';
                    break;
                case 5:
                    $s .= 'пятьдесят';
                    break;
                case 6:
                    $s .= 'шестьдесят';
                    break;
                case 7:
                    $s .= 'семьдесят';
                    break;
                case 8:
                    $s .= 'восемьдесят';
                    break;
                case 9:
                    $s .= 'девяносто';
                    break;
            }
            $s .= ' ';
            switch ($c) {
                case 1:
                    $s .= $rod ? 'один' :
                            'одна';
                    break;
                case 2:
                    $s .= $rod ? 'два' :
                            'две';
                    break;
                case 3:
                    $s .= 'три';
                    break;
                case 4:
                    $s .= 'четыре';
                    break;
                case 5:
                    $s .= 'пять';
                    break;
                case 6:
                    $s .= 'шесть';
                    break;
                case 7:
                    $s .= 'семь';
                    break;
                case 8:
                    $s .= 'восемь';
                    break;
                case 9:
                    $s .= 'девять';
                    break;
            }
        } else {
            switch ($c) {
                case 0:
                    $s .= 'десять';
                    break;
                case 1:
                    $s .= 'одиннадцать';
                    break;
                case 2:
                    $s .= 'двенадцать';
                    break;
                case 3:
                    $s .= 'тринадцать';
                    break;
                case 4:
                    $s .= 'четырнадцать';
                    break;
                case 5:
                    $s .= 'пятьнадцать';
                    break;
                case 6:
                    $s .= 'шестьнадцать';
                    break;
                case 7:
                    $s .= 'семьнадцать';
                    break;
                case 8:
                    $s .= 'восемьнадцать';
                    break;
                case 9:
                    $s .= 'девятьнадцать';
                    break;
            }
        }
        return $s;
    }

    public function convert($n) {
        $billions = floor($n / 1000000000);
        $millions = floor(($n - $billions * 1000000000) / 1000000);
        $grands = floor(($n - $billions * 1000000000 - $millions * 1000000) / 1000);
        $roubles = floor(($n - $billions * 1000000000 - $millions * 1000000 - $grands * 1000));
        $kop = round($n * 100 - round(floor($n) * 100));
        if ($kop < 10) {
            $kop = '0' . (string) $kop;
        }
        $s = '';
        if ($billions > 0) {
            $t = 'ов';
            $temp = $billions % 10;
            if (floor(($billions % 100) / 10) != 1) {
                if ($temp == 1) {
                    $t = '';
                } elseif ($temp >= 2 && $temp <= 4) {
                    $t = 'а';
                }
            }
            $s .= $this->number2string($billions, 1) . " миллиард$t ";
        }
        if ($millions > 0) {
            $t = 'ов';
            $temp = $millions % 10;
            if (floor(($millions % 100) / 10) != 1) {
                if ($temp == 1) {
                    $t = '';
                } elseif ($temp >= 2 && $temp <= 4) {
                    $t = 'а';
                }
            }
            $s .= $this->number2string($millions, 1) . " миллион$t ";
        }
        if ($grands > 0) {
            $t = '';
            $temp = $grands % 10;
            if (floor(($grands % 100) / 10) != 1) {
                if ($temp == 1) {
                    $t = 'а';
                } elseif ($temp >= 2 && $temp <= 4) {
                    $t = 'и';
                }
            }
            $s .= $this->number2string($grands, 0) . " тысяч$t ";
        }
        if ($roubles > 0) {
            $rub = 'ей';
            $temp = $roubles % 10;
            if (floor(($roubles % 100) / 10) != 1) {
                if ($temp == 1) {
                    $rub = 'ь';
                } elseif ($temp >= 2 && $temp <= 4) {
                    $rub = 'я';
                }
            }
            $s .= $this->number2string($roubles, 1) . " рубл$rub ";
        } {
            $kp = 'ек';
            $temp = $kop % 10;
            if (floor(($kop % 100) / 10) != 1) {
                if ($temp == 1) {
                    $kp = 'йка';
                } elseif ($temp >= 2 && $temp <= 4) {
                    $kp = 'йки';
                }
            }
            $s .= "$kop копе$kp";
        }

        if ($roubles > 0 || $grands > 0 || $millions > 0 || $billions > 0) {
            $cnt = 0;
            while ($s[$cnt] == ' ') {
                $cnt++;
            }
            $s[$cnt] = chr(ord($s[$cnt]) - 32);
        }
        return $s;
    }

}
