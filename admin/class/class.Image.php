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

class Image {

    protected $_mime;
    protected $_image;
    protected $_limit = 1280; // Ограничение ширины и высоты в px
    protected $_mimes = array(
        'image/pjpeg' => 'jpg',
        'image/jpeg'  => 'jpg',
        'image/jpg'   => 'jpg',
        'image/gif'   => 'gif',
        'image/x-png' => 'png',
        'image/png'   => 'png'
    );

    /* Метод конструктор класса */
    public function __sx_construct() {
        if (!function_exists('gd_info')) {
            throw new Exception('Не найдена библиотека GD');
        }
    }

    /* Метод работы с объектами */
    protected function resource($image, $mime = NULL) {
        $object = is_resource($this->_image) ? clone $this : $this;
        $object->_mime = $mime;
        $object->_image = $image;
        return $object;
    }

    /* Метод создания изображения из строки */
    public function string($string, $resource = false) {
        $image = imagecreatefromstring($string);
        return $resource === true ? $image : $this->resource($image);
    }

    /* Метод создания изображения */
    public function create($width, $height, $resource = false) {
        $image = imagecreatetruecolor($width, $height);
        return $resource === true ? $image : $this->resource($image);
    }

    /* Метод чтения изображения */
    public function open($file, $resource = false) {
        $result = false;
        if (($mime = $this->mime($file)) !== false) {
            $image = false;
            switch ($mime) {
                case 'image/jpg' :
                case 'image/jpeg':
                case 'image/pjpeg':
                    $image = imagecreatefromjpeg($file);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file);
                    break;
                case 'image/png':
                case 'image/x-png':
                    $image = imagecreatefrompng($file);
                    imagealphablending($image, false);
                    break;
            }
            if ($image !== false) {
                $result = $resource === true ? $image : $this->resource($image, $mime);
            }
        }
        return $result;
    }

    /* Метод сохранения изображения */
    public function save($file, $quality = false) {
        $result = false;
        if (is_resource($this->_image)) {
            switch ($this->extension($file)) {
                case 'jpg' :
                case 'jpe' :
                case 'jpeg':
                    $quality = $this->quality('jpg', $quality);
                    $result = imagejpeg($this->_image, $file, $quality);
                    break;
                case 'gif':
                    $result = imagegif($this->_image, $file);
                    break;
                case 'png':
                    imagesavealpha($this->_image, true);
                    $quality = $this->quality('png', $quality);
                    $result = imagepng($this->_image, $file, $quality);
                    break;
            }
        }
        return $result;
    }

    /* Метод вывода изображения */
    public function output($type = 'jpg', $quality = false) {
        $result = false;
        if (is_resource($this->_image)) {
            switch ($this->extension($type)) {
                case 'jpg' :
                case 'jpe' :
                case 'jpeg':
                    header('Content-type: image/jpeg');
                    $quality = $this->quality('jpg', $quality);
                    $result = imagejpeg($this->_image, NULL, $quality);
                    break;
                case 'gif':
                    header('Content-type: image/gif');
                    $result = imagegif($this->_image, NULL);
                    break;
                case 'png':
                    header('Content-type: image/png');
                    imagesavealpha($this->_image, true);
                    $quality = $this->quality('png', $quality);
                    $result = imagepng($this->_image, NULL, $quality);
                    break;
            }
        }
        return $result;
    }

    /* Метод скачивания изображения */
    public function download($file, $quality = false, $cache = false) {
        $type = $cache === true ? 'private' : 'public';
        ob_start();
        if ($this->output($file, $quality)) {
            $content = ob_get_contents();
            header('Cache-control: ' . $type);
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"; size=' . strlen($content) . ';');
            echo $content;
            exit;
        }
        ob_end_clean();
        return false;
    }

    /* Метод чтения и вывода изображения */
    public function read($file) {
        if (($mime = $this->mime($file)) !== false) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-type: ' . $mime);
            readfile($file);
            exit;
        }
        return false;
    }

    /* Метод получения миме типа или расширения изображения */
    public function mime($file, $ext = false) {
        $result = false;
        if (is_file($file)) {
            $info = getimagesize($file);
            if ($info !== false && isset($this->_mimes[$info['mime']])) {
                $result = $ext === true ? $this->_mimes[$info['mime']] : $info['mime'];
            }
        }
        return $result;
    }

    /* Метод исправления расширения по содержимому */
    public function fix($file, $rename = false) {
        $result = false;
        if (($mime = $this->mime($file, true)) !== false) {
            $result = $file;
            $array = explode('.', $file);
            if ($mime != strtolower(array_pop($array))) {
                $result = implode('.', $array) . '.' . $mime;
                if ($rename === true) {
                    rename($file, $result);
                }
            }
        }
        return $result;
    }

    /* Метод проверки файла на изображение */
    public function has($file) {
        $result = false;
        if ($this->mime($file) !== false) {
            $result = true;
        }
        return $result;
    }

    /* Метод корректировки разных типов изображений */
    public function correction($type, $new, $image) {
        switch ($type) {
            case 'gif':
            case 'image/gif':
                $color = imagecolortransparent($image);
                if ($color >= 0 && $color < imagecolorstotal($image)) {
                    $rgb = imagecolorsforindex($image, $color);
                    $color = imagecolorallocate($new, $rgb ['red'], $rgb ['green'], $rgb ['blue']);
                    imagecolortransparent($new, $color);
                    imagefill($new, 0, 0, $color);
                }
                break;
            case 'png':
            case 'image/png':
            case 'image/x-png':
                imagealphablending($new, false);
                imagesavealpha($new, true);
                break;
        }
        return $new;
    }

    /* Метод изменения размеров изображения */
    public function resize($value, $type = 'auto') {
        $result = false;
        list($w, $h) = $this->_resize($value, $type);
        list($w, $h) = $this->limit($w, $h);
        list($width, $height) = $this->sizes();
        if ($w > 0 && $h > 0 && ($w < $width || $h < $height)) {
            $image = $this->create($w, $h, true);
            $image = $this->correction($this->_mime, $image, $this->_image);
            $result = imagecopyresampled($image, $this->_image, 0, 0, 0, 0, $w, $h, $width, $height);
            $this->_image = $image;
        }
        return $result;
    }

    /* Метод определения типов ресайза и расчета размеров */
    protected function _resize($value = 0, $type = 'auto') {
        $result = array(0, 0);
        if ($value > 0) {
            if ($type > 0) {
                $result = array($value, $type);
            } else {
                list($width, $height) = $this->sizes();
                switch (strtolower($type)) {
                    default:
                    case 'auto':
                        if ($width > $height) {
                            $result = array($value, $height * ($value / $width));
                        } else {
                            $result = array($width * ($value / $height), $value);
                        }
                        break;
                    case 'x':
                    case 'width':
                        $result = array($value, $height * ($value / $width));
                        break;
                    case 'y':
                    case 'height':
                        $result = array($width * ($value / $height), $value);
                        break;
                    case '%':
                    case 'percent':
                        $result = array(($width / 100) * (100 + $value), ($height / 100) * (100 + $value));
                        break;
                }
            }
        }
        return array_map('intval', $result);
    }

    /* Метод наложения водяного знака */
    public function watermark($file, $position, $opacity = 100) {
        $result = false;
        if (($image = $this->open($file, true)) !== false) {
            list($w, $h) = array(imagesx($image), imagesy($image));
            list($width, $height) = $this->sizes();
            if ($width > ($w * 1.5) && $height > ($h * 1.5)) {
                $x = $width - $w;
                $y = $height - $h;
                list($x, $y) = $this->_watermark($position, $x, $y);
                $result = imagecopymerge($this->_image, $image, $x, $y, 0, 0, $w, $h, min($opacity, 100));
            }
            imagedestroy($image);
        }

        return $result;
    }

    /* Метод определения месторасположения водяного знака */
    protected function _watermark($position, $x, $y, $margin = 2) {
        switch (strtolower($position)) {
            default:
            case 'bottom_right':
                $result = array($x - $margin, $y - $margin);
                break;
            case 'bottom_left':
                $result = array($margin, $y - $margin);
                break;
            case 'bottom_center':
                $result = array($x / 2, $y - $margin);
                break;
            case 'top_right':
                $result = array($x - $margin, $margin);
                break;
            case 'top_left':
                $result = array($margin, $margin);
                break;
            case 'top_center':
                $result = array($x / 2, $y + $margin);
                break;
            case 'center_right':
                $result = array($x - $margin, $y / 2);
                break;
            case 'center_left':
                $result = array($margin, $y / 2);
                break;
            case 'center':
                $result = array($x / 2, $y / 2);
                break;
        }
        return $result;
    }

    /* Метод вырезки части изображения */
    public function crop($w, $h, $x = 0, $y = 0) {
        list($width, $height) = $this->sizes();
        if ($w > ($width - $x)) {
            $w = $width;
        }
        if ($h > ($height - $y)) {
            $h = $height;
        }
        $result = false;
        if ($w > 0 && $h > 0) {
            $image = $this->create($w, $h, true);
            $image = $this->correction($this->_mime, $image, $this->_image);
            $result = imagecopyresampled($image, $this->_image, 0, 0, $x, $y, $w, $h, $w, $h);
            $this->_image = $image;
        }
        return $result;
    }

    /* Метод создания круглых углов */
    public function corners($radius) {

    }

    /* Метод отражения изображения по горизонтали */
    public function flip() {
        list($width, $height) = $this->sizes();
        $image = $this->create($width, $height, true);
        $image = $this->correction($this->_mime, $image, $this->_image);
        for ($y = 0; $y < $height; $y++) {
            imagecopy($image, $this->_image, 0, $y, 0, $height - $y - 1, $width, 1);
        }
        $this->_image = $image;
    }

    /* Метод отражения изображения по вертикали */
    public function flop() {
        list($width, $height) = $this->sizes();
        $image = $this->create($width, $height, true);
        $image = $this->correction($this->_mime, $image, $this->_image);
        for ($x = 0; $x < $width; $x++) {
            imagecopy($image, $this->_image, $x, 0, $width - $x - 1, 0, 1, $height);
        }
        $this->_image = $image;
    }

    /* Метод создания рамки для изображения */
    public function border($color = '#000', $depth = 5) {
        list($width, $height) = $this->sizes();
        $color = $this->color($color);
        for ($i = 0; $i < $depth; $i++) {
            if ($i < 0) {
                $x = $width++;
                $y = $height++;
            } else {
                $x = $width--;
                $y = $height--;
            }
            imagerectangle($this->_image, $i, $i, $x, $y, $color);
        }
    }

    /* Метод вращения изображения */
    public function rotate($angle, $color, $transparent = 0) {
        $result = imagerotate($this->_image, $angle, $this->color($color), $transparent);
        if ($result !== false) {
            $this->_image = $result;
            return true;
        }
        return false;
    }

    /* Метод получения ресурса изображения */
    public function get() {
        return $this->_image;
    }

    /* Метод сохранения ресурса изображения */
    public function set($image) {
        $this->_image = $image;
    }

    /* Метод получения ширины изображения */
    public function width() {
        return imagesx($this->_image);
    }

    /* Метод получения высоты изображения */
    public function height() {
        return imagesy($this->_image);
    }

    /* Метод получения ширины и высоты */
    public function sizes() {
        return array(imagesx($this->_image), imagesy($this->_image));
    }

    /* Метод рисования линии на изображении */
    public function line($x1, $y1, $x2, $y2, $color) {
        return imageline($this->_image, $x1, $y1, $x2, $y2, $this->color($color));
    }

    /* Метод выполняет заливку цветом изображения */
    public function fill($color, $x = 0, $y = 0) {
        return imagefill($this->_image, $x, $y, $this->color($color));
    }

    /* Метод добавляет рельеф на изображение */
    public function emboss() {
        return imagefilter($this->_image, IMG_FILTER_EMBOSS);
    }

    /* Метод инвертирует все цвета изображения */
    public function negate() {
        return imagefilter($this->_image, IMG_FILTER_NEGATE);
    }

    /* Метод изменяет яркость изображения */
    public function brightness($arg) {
        return imagefilter($this->_image, IMG_FILTER_BRIGHTNESS, $arg);
    }

    /* Метод изменяет контрастность изображения */
    public function contrast($arg) {
        return imagefilter($this->_image, IMG_FILTER_CONTRAST, $arg);
    }

    /* Метод преобразует цвета изображения в градации серого */
    public function grayscale() {
        return imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
    }

    /* Метод добавляет размытие изображения */
    public function blur($type = 'gaussian') {
        $filter = $type == 'gaussian' ? IMG_FILTER_GAUSSIAN_BLUR : IMG_FILTER_SELECTIVE_BLUR;
        return imagefilter($this->_image, $filter);
    }

    /* Метод добавляет эфффект рисунка */
    public function picture() {
        imagefilter($this->_image, IMG_FILTER_MEAN_REMOVAL);
        imagefilter($this->_image, IMG_FILTER_GAUSSIAN_BLUR);
    }

    /* Метод добавляет эфффект сепии */
    public function sepia() {
        imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->_image, IMG_FILTER_COLORIZE, 110, 70, 20);
    }

    /* Метод преобразует цвета изображения в указанные цвета */
    public function colorize($r, $g, $b, $a) {
        return imagefilter($this->_image, IMG_FILTER_COLORIZE, $r, $g, $b, $a);
    }

    /* Метод выполняет определение границ изображения для подсветки */
    public function edgedetect() {
        return imagefilter($this->_image, IMG_FILTER_EDGEDETECT);
    }

    /* Метод выполняет усреднение изображения для достижения эффекта эскиза */
    public function meanremoval() {
        return imagefilter($this->_image, IMG_FILTER_MEAN_REMOVAL);
    }

    /* Метод делает границы изображения более плавными, а изображение менее четким */
    public function smooth($arg) {
        return imagefilter($this->_image, IMG_FILTER_SMOOTH, $arg);
    }

    /* Метод наложения текста TrueType шрифтом */
    public function fttext($text, $fontfile, $color, $size, $x = 0, $y = 0, $angle = 0) {
        return imagefttext($this->_image, $size, $angle, $x, $y, $this->color($color), $fontfile, $text);
    }

    /* Метод наложения текста ttf шрифтом */
    public function ttftext($text, $fontfile, $color, $size, $x = 0, $y = 0, $angle = 0) {
        return imagettftext($this->_image, $size, $angle, $x, $y, $this->color($color), $fontfile, $text);
    }

    /* Метод наложения текста на изображение */
    public function text($text, $color = '#000', $font = 6, $x = 5, $y = 5) {
        return imagestring($this->_image, $font, $x, $y, $text, $this->color($color));
    }

    /* Метод устанавливает идентификатор цвета */
    public function color() {
        list($r, $g, $b) = func_num_args() == 3 ? func_get_args() : $this->rgb(func_get_arg(0));
        return imagecolorallocate($this->_image, $r, $g, $b);
    }

    /* Метод конвертации из hex в RGB */
    public function rgb($c) {
        $c = ltrim($c, '#');
        if (strlen($c) == 6) {
            $result = array($c[0] . $c[1], $c[2] . $c[3], $c[4] . $c[5]);
        } else {
            $result = array($c[0] . $c[0], $c[1] . $c[1], $c[2] . $c[2]);
        }
        return array_map('hexdec', $result);
    }

    /* Метод опредения качества изображений */
    public function quality($type, $input = false) {
        $output = abs(intval($input));
        if ($type == 'png') {
            $output = $input === false ? 0 : min(round(($output / 100) * 9), 9);
        } elseif ($type == 'jpg') {
            $output = $input === false ? 75 : min($output, 100);
        }
        return $output;
    }

    /* Метод проверки превышения лимита */
    public function limit($width, $height) {
        if ($width > $this->_limit) {
            $height /= ($width / $this->_limit);
            $width = $this->_limit;
        }
        if ($height > $this->_limit) {
            $width /= ($height / $this->_limit);
            $height = $this->_limit;
        }
        return array(floor($width), floor($height));
    }

    /* Метод генерации рандомного имени файла */
    public function filename() {
        $array = func_get_args();
        if (isset($array[0])) {
            $name = md5(implode('_', $array));
            return $name . $this->extension($array[0], true);
        }
        return false;
    }

    /* Метод получения расширения файла */
    public function extension($file, $point = false) {
        $value = pathinfo($file, PATHINFO_EXTENSION);
        if ($point === true && !empty($value)) {
            $value = '.' . $value;
        }
        return $value;
    }

    /* Метод уничтожения ресурса изображения */
    public function close() {
        if (is_resource($this->_image)) {
            imagedestroy($this->_image);
            $this->_image = NULL;
            $this->_mime = NULL;
        }
    }

    /* Метод деструктор класса */
    public function __destruct() {
        $this->close();
    }

}
