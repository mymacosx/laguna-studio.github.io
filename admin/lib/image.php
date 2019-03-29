<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
error_reporting(0);

if (!defined('SX_DIR')) {
    define('SX_DIR', realpath(dirname(dirname(__FILE__))));
    require_once SX_DIR . '/class/class.SX.php'; // Подключаем основной класс системы
    SX::preload('user');                         // Инициализируем систему
}

/* Метод установки дефолтного изображения в случае ошибки */
function noimage($image, $width) {
    $object = SX::object('Image');
    $file = $object->filename($image, $width);
    $file = TEMP_DIR . '/cache/' . $file;

    if ($object->read($file) === false) {
        if ($object->open($image)) {
            $object->resize($width, 'width');
            $object->save($file);
            $object->output($file);
            $object->close();
        }
    }
    exit;
}

switch (Arr::getRequest('action')) {
    case 'ugallery';
        $params = Arr::getRequest(array('width' => 200, 'image' => 'error'));
        $image = Tool::cleanString($params['image'], '._-');
        $width = $params['width'] > 400 ? 400 : intval($params['width']);

        $object = SX::object('Image');
        $file = $object->filename($image, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/user/gallery/' . $image;
            if ($object->open($load)) {
                $quality = SX::get('users.ImageCompres');
                $object->resize($width, 'width');
                $object->save($file, $quality);
                $object->output($file, $quality);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;

    case 'avatar';
        $params = Arr::getRequest(array('image' => 'user.png', 'width' => 60));
        $image = Tool::cleanString($params['image'], '._-');
        if (empty($width)) {
            $width = SX::get('users.AvatarWidth');
        }
        $width = $params['width'] > 160 ? 160 : intval($params['width']);

        $object = SX::object('Image');
        $file = $object->filename($image, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/avatars/' . $image;
            if ($object->open($load)) {
                $quality = SX::get('users.AvatarCompres');
                $object->resize($width, 'width');
                $object->save($file, $quality);
                $object->output($file, $quality);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/avatars/no_avatar.png', $width);
            }
        }
        break;

    case 'shop';
        $params = Arr::getRequest(array('width' => 140, 'image' => 'error'));
        $image = Tool::cleanString($params['image'], '._-');
        $width = Tool::cleanDigit($params['width']);

        $config = SX::get('shop');
        $array = Arr::get($config, array('thumb_width_norm', 'thumb_width_big', 'thumb_width_small', 'thumb_width_middle'));
        $maxwidth = max($array);
        if ($width > $maxwidth) {
            $width = $maxwidth;
        }

        $object = SX::object('Image');
        $file = $object->filename($image, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/shop/icons/' . $image;
            if ($object->open($load)) {
                $object->resize($width, 'width');
                if ($config['Wasserzeichen'] == 1) {
                    $watemark = UPLOADS_DIR . '/watermarks/' . $config['Wasserzeichen_Bild'];
                    $object->watermark($watemark, $config['Wasserzeichen_Position'], $config['WasserzeichenKomp']);
                }
                $object->save($file, $config['thumb_quality']);
                $object->output($file, $config['thumb_quality']);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;

    case 'forum';
        $params = Arr::getRequest(array('width' => 100, 'image' => 'error'));
        $image = Tool::cleanDigit($params['image']);
        $sql = DB::get()->fetch_object("SELECT filename, orig_name FROM " . PREFIX . "_f_attachment WHERE id = '" . DB::get()->escape($image) . "' LIMIT 1");
        $width = $params['width'] > 400 ? 400 : intval($params['width']);
        if (empty($width)) {
            $width = SX::get('forum.size');
        }
        if (empty($sql->orig_name) || empty($sql->filename)) {
            noimage(UPLOADS_DIR . '/other/noimage.png', $width);
        }

        $object = SX::object('Image');
        $file = $object->filename($sql->orig_name, $sql->filename, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/forum/' . $sql->filename;
            if ($object->open($load)) {
                $quality = SX::get('forum.compres');
                $object->resize($width, 'width');
                $object->save($file, $quality);
                $object->output($file, $quality);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;

    case 'news';
        $params = Arr::getRequest(array('width' => 100, 'image' => 'error'));
        $image = Tool::cleanString($params['image'], '._-');
        $width = $params['width'] > 400 ? 400 : intval($params['width']);
        if (empty($width)) {
            $width = SX::get('news.size');
        }

        $object = SX::object('Image');
        $file = $object->filename($image, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/news/' . $image;
            if ($object->open($load)) {
                $quality = SX::get('news.compres');
                $object->resize($width, 'width');
                $object->save($file, $quality);
                $object->output($file, $quality);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;

    case 'gallery';
        $params = Arr::getRequest(array('width' => 140, 'image' => 'error'));
        $image = Tool::cleanDigit($params['image']);
        $width = Tool::cleanDigit($params['width']);
        $row = DB::get()->fetch_assoc("SELECT Bildname FROM " . PREFIX . "_galerie_bilder WHERE Id='" . DB::get()->escape($image) . "' LIMIT 1");
        $config = SX::get('galerie');
        $array = Arr::get($config, array('Bilder_Klein', 'Bilder_Mittel', 'Bilder_Gross'));
        $maxwidth = max($array);
        if ($width > $maxwidth) {
            $width = $maxwidth;
        }
        if (empty($row['Bildname'])) {
            noimage(UPLOADS_DIR . '/other/noimage.png', $width);
        }
        $object = SX::object('Image');
        $file = $object->filename($row['Bildname'], $image, $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/galerie/' . $row['Bildname'];
            if ($object->open($load)) {
                $object->resize($width, 'width');
                if ($config['Wasserzeichen_Vorschau'] == 1) {
                    $watemark = UPLOADS_DIR . '/watermarks/' . $config['Watermark_File'];
                    $object->watermark($watemark, $config['Watermark_Position'], $config['Transparenz']);
                }
                $object->save($file, $config['Quali_Gross']);
                $object->output($file, $config['Quali_Gross']);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;

    default:
        $action = Arr::getRequest('action');
        $allowed = array(// Соответствие action => папка в uploads
            'links'        => 'links',
            'cheats'       => 'cheats',
            'partner'      => 'partner',
            'content'      => 'content',
            'products'     => 'products',
            'articles'     => 'articles',
            'downloads'    => 'downloads',
            'screenshots'  => 'screenshots',
            'manufacturer' => 'manufacturer',
            'shopicons'    => 'shop/icons_categs',
            'shopnavi'     => 'shop/navi_categs',
            'payment'      => 'shop/payment_icons',
            'shipper'      => 'shop/shipper_icons',
        );

        $params = Arr::getRequest(array('width' => 50, 'image' => 'error'));
        $image = Tool::cleanString($params['image'], '._-');
        $width = $params['width'] > 600 ? 600 : intval($params['width']);

        if (!isset($allowed[$action])) {
            noimage(UPLOADS_DIR . '/other/noimage.png', $width);
        }

        $object = SX::object('Image');
        $file = $object->filename($image, $allowed[$action], $width);
        $file = TEMP_DIR . '/cache/' . $file;

        if ($object->read($file) === false) {
            $load = UPLOADS_DIR . '/' . $allowed[$action] . '/' . $image;
            if ($object->open($load)) {
                $object->resize($width, 'width');
                $object->save($file);
                $object->output($file);
                $object->close();
            } else {
                noimage(UPLOADS_DIR . '/other/noimage.png', $width);
            }
        }
        break;
}
exit;
