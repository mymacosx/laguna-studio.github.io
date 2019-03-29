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

abstract class File {

    /* Метод скачивания файла с возможностью докачивания */
    public static function filerange($file, $mimetype = NULL, $type = 'attachment') {
        if (ob_get_level()) {
            ob_end_clean();
        }
        if (!is_file($file)) {
            return SX::object('Response')->get(404);
        }
        if (($fd = fopen($file, 'rb')) === false) {
            return SX::object('Response')->get(505);
        }
        if (empty($mimetype)) {
            $mimetype = SX::object('Mimes')->get($file);
        }
        set_time_limit(600);
        $range = 0;
        $filesize = $end = filesize($file);
        $filemtime = filemtime($file);
        if (isset($_SERVER['HTTP_RANGE'])) {
            $matches = array();
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/iu', $_SERVER['HTTP_RANGE'], $matches)) {
                if (!empty($matches[1])) {
                    $range = intval($matches[1]);
                }
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }
        SX::object('Response')->get(($range > 0 || $end < $filesize) ? 206 : 200);
        header('Content-Type: ' . $mimetype);
        header('Cache-control: private');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . ($end - $range));
        header('Content-Range: bytes ' . $range . '-' . $end . '/' . $filesize);
        header('Content-Disposition: ' . $type . '; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $filemtime));
        header('Etag: "' . md5($file . fileinode($file) . $filemtime . $filesize) . '"');
        header('Connection: close');
        $download = $range;
        fseek($fd, $range, 0);
        while (!feof($fd) && !connection_status() && $download < $end) {
            $block = min(1024 * 16, $end - $download);
            echo fread($fd, $block);
            $download += $block;
        }
        fclose($fd);
        exit;
    }

    /* Метод вывода файла */
    public static function read($file, $name = NULL, $mimetype = NULL, $type = 'attachment') {
        if (ob_get_level()) {
            ob_end_clean();
        }
        if (!is_file($file)) {
            return SX::object('Response')->get(404);
        }
        if (empty($name)) {
            $name = basename($file);
        }
        if (empty($mimetype)) {
            $mimetype = SX::object('Mimes')->get($name);
        }
        header('Cache-control: private');
        header('Content-type: image/' . $mimetype);
        header('Content-disposition:' . $type . '; filename=' . basename($name));
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    /* Метод скачивания файла */
    public static function download($data, $name = NULL, $mimetype = NULL, $type = 'attachment') {
        if (ob_get_level()) {
            ob_end_clean();
        }
        if (empty($mimetype)) {
            $mimetype = SX::object('Mimes')->get($name);
        }
        header('Content-Type: ' . $mimetype);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Disposition: ' . $type . '; filename="' . basename($name) . '"');
        header('Content-Length: ' . strlen($data));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $data;
        exit;
    }

    /* Добавление в файл */
    public static function add($file, $data) {
        if (file_put_contents($file, $data, FILE_APPEND)) {
            return true;
        }
        SX::syslog('Не удалось добавить в файл ' . $file, '5', $_SESSION['benutzer_id']);
        return false;
    }

    /* Запись в файл */
    public static function set($file, $data) {
        if (file_put_contents($file, $data)) {
            return true;
        }
        SX::syslog('Не удалось создать файл ' . $file, '5', $_SESSION['benutzer_id']);
        return false;
    }

    /* Чтение файла */
    public static function get($file) {
        if (is_file($file) && ($content = file_get_contents($file))) {
            return $content;
        }
        SX::syslog('Не удалось открыть файл ' . $file, '5', $_SESSION['benutzer_id']);
        return false;
    }

    /* Чтение файла */
    public static function arr($file) {
        if (is_file($file) && ($content = file($file))) {
            return $content;
        }
        SX::syslog('Не удалось открыть файл ' . $file, '5', $_SESSION['benutzer_id']);
        return false;
    }

    /* Удаление файла */
    public static function delete($file) {
        if (is_file($file) && !unlink($file)) {
            SX::syslog('Не удалось удалить файл ' . $file, '5', $_SESSION['benutzer_id']);
            return false;
        }
        return true;
    }

    /* Получаем данные конфиг файла */
    public static function parse($file) {
        if (phpversion() >= '5.3.1') {
            $contents = file_get_contents($file);
            $array = parse_ini_string($contents);
        } else {
            $array = parse_ini_file($file);
        }
        return $array;
    }

    /* Метод вывода единиц измерения */
    public static function filesize($size, $shop = 0) {
        if ($shop == 1) {
            $unit = 'UnitWeight';
            $num = 1000;
        } else {
            $unit = 'UnitByte';
            $num = 1024;
        }
        $sizes = explode(',', SX::$lang[$unit]);
        $size = $size * $num;
        $ext = $sizes[0];
        for ($i = 1, $count = count($sizes); ($i < $count && $size >= $num); $i++) {
            $size = $size / $num;
            $ext = $sizes[$i];
        }
        return round($size, 1) . ' ' . $ext;
    }

}
