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

class Captcha extends Magic {

    protected $options = array(
        'max_calc1' => 9, // Максимальное число первого значения в примере
        'max_calc2' => 9, // Максимальное число второго значения в примере
        'min_text'  => 3, // Минимальное количество символов, текстовой капчи
        'max_text'  => 4, // Максимальное количество символов, текстовой капчи
        'type'      => 'auto', // auto - попеременно случайные буквы и математика, text - случайные буквы, calc - математика
        'text'      => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', // Набор символов для текстовой капчи
        'fonts'     => array('alien', 'army', 'davinci', 'dustismo', 'linear', 'simpler', 'jasper', 'plakat', 'titul', 'demish', 'actionj', 'lexo'),
        'ignore'    => array('=' => 'army', '+' => 'army', '-' => 'army'), // Символы не участвуют в случайном выборе шрифта
        'uniqid'    => null,
    );

    public function __construct() {
        $this->options = (array) SX::get('secure') + $this->options;
        if (empty($this->options['uniqid'])) {
            $this->options['uniqid'] = $this->generate(null, 6);
        }
    }

    /* Метод получения имени поля */
    public function input() {
        $result = '';
        if ($this->active() && !empty($this->options['uniqid'])) {
            $secure = Arr::getSession($this->secure($this->options['uniqid']));
            if (!empty($secure['input'])) {
                $result = $secure['input'];
            }
        }
        return $result;
    }

    /* Метод инициализации капчи */
    public function start($uniqid = null) {
        if ($this->active()) {
            $uniqid = $this->uniqid($uniqid);
            $create = $this->create($uniqid);
            $this->_view->assign(array(
                'secure_active'           => 1,
                'secure_default'          => $this->options['uniqid'],
                'secure_input_' . $uniqid => $create['input'],
                'secure_image_' . $uniqid => $this->choice($uniqid, $create['question']),
            ));
        } else {
            $this->_view->assign('secure_active', 0);
        }
    }

    /* Метод вывода капчи ajax */
    public function ajax() {
        $result = null;
        if ($this->active()) {
            $uniqid = Arr::getRequest('secure_uniqid');
            $create = $this->create($uniqid, false);
            $result = $this->choice($uniqid, $create['question']);
        }
        SX::output($result, true);
    }

    /* Метод проверки правильности ввода капчи через ajax */
    public function check($error = array(), $spam = false, $uniqid = null) {
        if ($this->active() && !$this->control()) {
            $error[] = $this->_lang['Reg_SecurecodeWrong'];
        }
        return $this->error($error, $spam, $uniqid);
    }

    /* Метод проверки правильности ввода капчи */
    public function validate() {
        $result = 'false';
        if ($this->active() && $this->control()) {
            $result = 'true';
        }
        SX::output($result, true);
    }

    /* Метод запуска генерации капчи */
    public function load() {
        if (!extension_loaded('gd')) {
            SX::output('Не установлена библиотека для работы с изображениями', true);
        }
        $string = $this->receive();
        $width = strlen($string) * 30;
        $image = imagecreate($width, 44);
        if ($image === false) {
            SX::output('Ошибка генерации изображения', true);
        }
        imagecolorallocate($image, 0xff, 0xff, 0xff);
        if ($string == 'ERROR' || $this->options['ttf_font'] != 1 || $this->ttf($image, $string) === false) {
            $this->string($image, $string);
        }
        $this->send($image);
    }

    /* Метод проверки активности капчи */
    protected function active() {
        return $this->options['active'] == 1 || ($this->options['active'] == 2 && Arr::getSession('loggedin') != 1);
    }

    /* Метод получения идентификатора */
    protected function uniqid($value) {
        if (empty($value)) {
            $value = $this->options['uniqid'];
        }
        return $value;
    }

    /* Метод создания идентификатора */
    protected function generate($value = null, $limit = 0) {
        if (empty($value)) {
            $value = date('z') . IP_USER . SX::object('Agent')->agent;
        }
        $value = ltrim(md5($value), '0123456789');
        if ($limit > 0) {
            $value = substr($value, 0, $limit);
        }
        return $value;
    }

    /* Метод создания значения капчи */
    protected function create($uniqid, $reload = true) {
        $secure = $this->secure($uniqid);
        $method = $this->options['type'];
        if (!is_callable(array($this, $method))) {
            $method = 'auto';
        }
        if ($reload === false) {
            $array = Arr::getSession($secure);
            if (!empty($array['input'])) {
                $input = $array['input'];
            }
        }
        if (empty($input)) {
            $input = $this->generate(uniqid(mt_rand(), true));
        }
        unset($_SESSION[$secure]);
        list($result, $question) = $this->$method();
        $array = compact('input', 'result', 'question');
        Arr::setSession($secure, $array);
        return $array;
    }

    /* Метод получения данных сессии */
    protected function secure($uniqid) {
        $result = 'secure';
        if (!empty($uniqid)) {
            $result .= '-' . $uniqid;
        }
        return $result;
    }

    /* Метод проверки данных капчи */
    protected function control() {
        $result = false;
        $verify = Arr::getRequest('scode');
        if (empty($verify)) {
            $uniqid = Arr::getRequest('secure_uniqid');
            if (!empty($uniqid)) {
                $secure = Arr::getSession($this->secure($uniqid));
                if (!empty($secure['result']) && !empty($secure['input'])) {
                    $input = (string) Arr::getRequest($secure['input']);
                    if (!empty($input) && strtolower($input) === $secure['result']) {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }

    /* Метод выбора типа капчи */
    protected function choice($uniqid, $question) {
        $result = $question;
        if ($this->options['gd'] == '1') {
            $result = '<img class="absmiddle" src="' . BASE_URL . '/lib/secure.php?secure_uniqid=' . $uniqid . '&' . time() . '" alt="" />';
        }
        return $result;
    }

    /* Метод получения строки с капчей */
    protected function receive() {
        $result = 'ERROR';
        if ($this->active()) {
            $uniqid = Arr::getRequest('secure_uniqid');
            if (!empty($uniqid)) {
                $secure = Arr::getSession($this->secure($uniqid));
                if (!empty($secure['question'])) {
                    $result = $secure['question'];
                }
            }
        }
        return $result;
    }

    /* Метод работы с ошибками */
    protected function error($error, $spam, $uniqid) {
        if ($spam === true) {
            foreach ($_POST as $key) {
                if ($this->_text->strlen($key) >= 2 && Tool::checkSpam($key) === false) {
                    $error[] = $this->_lang['SpamUsed'];
                    break;
                }
            }
        }
        $uniqid = $this->uniqid($uniqid);
        $assign = 'error';
        if ($uniqid !== $this->options['uniqid']) {
            $assign .= $uniqid;
        }
        $this->_view->assign($assign, $error);
        return !empty($error) ? false : true;
    }

    /* Метод автоматического выбора типа капчи */
    protected function auto() {
        $method = Arr::rand(array('calc', 'text'));
        return $this->$method();
    }

    /* Метод генерации значения математической капчи */
    protected function calc() {
        $operator = Arr::rand(array('+', '-'));
        list($val1, $val2) = $this->rand($operator);
        $result = $operator == '+' ? $val1 + $val2 : $val1 - $val2;
        $question = $val1 . $operator . $val2 . '=';
        return array((string) $result, $question);
    }

    /* Метод генерации двух чисел для математической капчи */
    protected function rand($operator) {
        do {
            $array = array(mt_rand(1, $this->options['max_calc1']), mt_rand(1, $this->options['max_calc2']));
        } while ($array[0] == $array[1]);
        if ($operator == '-' && $array[0] < $array[1]) {
            $array = array_reverse($array);
        }
        return $array;
    }

    /* Метод генерации значения текстовой капчи */
    protected function text() {
        $array = str_split($this->options['text']);
        $array = Arr::rand($array, mt_rand($this->options['min_text'], $this->options['max_text']));
        $string = implode('', $array);
        return array(strtolower($string), $string);
    }

    /* Метод генерации цвета шрифта */
    protected function color($image) {
        return imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 250), mt_rand(0, 250));
    }

    /* Метод генерации размера шрифта */
    protected function size() {
        return mt_rand(18, 28);
    }

    /* Метод генерации размера шрифта */
    protected function angle() {
        return mt_rand(-25, 25);
    }

    /* Метод загрузки шрифта */
    protected function font($font = NULL) {
        if (empty($font)) {
            $font = Arr::rand($this->options['fonts']);
        }
        return SX_DIR . '/lib/fonts/' . $font . '.ttf';
    }

    /* Метод генерации примера */
    protected function ttf($image, $string) {
        if (function_exists('imagettftext')) {
            $next = 10;
            foreach (str_split($string) as $value) {
                $size = $this->size();
                $font = isset($this->options['ignore'][$value]) ? $this->font($this->options['ignore'][$value]) : $this->font();
                if (!imagettftext($image, $size, $this->angle(), $next, 32, $this->color($image), $font, $value)) {
                    return false;
                }
                $next += $size;
            }
            return true;
        }
        return false;
    }

    /* Метод создания капчи без использования ttf */
    protected function string($image, $string) {
        $y_old = '';
        $font = 5;
        $x = mt_rand(2, 35);
        for ($i = 0; $i < strlen($string); $i++) {
            if (($y1 = $y_old - 7) < 2) {
                $y1 = 2;
            }
            if (($y2 = $y_old + 7) > 15) {
                $y2 = 15;
            }
            $y = mt_rand($y1, $y2);
            imagestring($image, $font, $x, $y, $string{$i}, $this->color($image));
            $x += 11;
            $y_old = $y;
        }
    }

    /* Метод вывода капчи */
    protected function send($image) {
        header('Expires: Mon, 1 Jan 2006 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        if (function_exists('imagepng')) {
            header('Content-type: image/png');
            imagealphablending($image, false);
            $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $color);
            imagesavealpha($image, true);
            imagepng($image);
        } elseif (function_exists('imagegif')) {
            header('Content-type: image/gif');
            imagegif($image);
        } elseif (function_exists('imagejpeg')) {
            header('Content-type: image/jpeg');
            imagejpeg($image, NULL, 50);
        } else {
            SX::output('Не удалось создать изображение...', true);
        }
        imagedestroy($image);
    }

}
