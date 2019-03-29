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

abstract class Folder {

    /* Создание папок указанных в пути к конечной папке */
    public static function fullpath($dirs, $chmod) {
        $item = '';
        $dirs = explode('/', $dirs);
        foreach ($dirs as $dir) {
            $item .= $dir . '/';
            self::create($item, $chmod);
        }
    }

    /* Создание папки */
    public static function create($folder, $chmod = 0777) {
        if (!is_dir($folder)) {
            if (!mkdir($folder)) {
                SX::syslog('Произошла ошибка! Не удалось создать директорию ' . $folder, '5', $_SESSION['benutzer_id']);
                return false;
            } else {
                chmod($folder, $chmod);
            }
        }
        return true;
    }

    /* Удаление папки */
    public static function delete($folder) {
        if (!is_dir($folder)) {
            chmod($folder, 0777);
            if (!mkdir($folder)) {
                SX::syslog('Произошла ошибка! Не удалось удалить директорию ' . $folder, '5', $_SESSION['benutzer_id']);
                return false;
            }
        }
    }

    /* Создание защищенной папки со стандартным набором файлов */
    public static function protection($file, $chmod = 0777) {
        self::create($file, $chmod);
        File::set($file . '.htaccess', 'deny from all');
        File::set($file . 'index.php', "<?php\r\nheader('Refresh: 0; url=/index.php?p=notfound', true, 404);\r\nexit;");
    }

    /* Метод удаления содержимого папки */
    public static function clean($folder) {
        if (!empty($folder)) {
            set_time_limit(60);
            $handle = opendir($folder);
            while (false !== ($datei = readdir($handle))) {
                if (!in_array($datei, array('.', '..', '.htaccess', 'index.php'))) {
                    File::delete($folder . $datei);
                }
            }
            closedir($handle);
        }
    }

}
