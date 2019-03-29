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

class Cache {

    protected $_dir;
    protected $_ext = '.txt'; // Расширение файлов с кешем
    protected $_life = 86400; // Время хранения по умолчанию

    /* Метод конструктор класса */
    public function __construct() {
        $this->_dir = TEMP_DIR . '/private/';
    }

    /* Метод получения кеша по ключу */
    public function get($key) {
        $file = $this->file($key);
        if (is_file($file) && filemtime($file) > time()) {
            return unserialize(file_get_contents($file));
        }
        return false;
    }

    /* Метод сохранения кеша по ключу */
    public function set($key, $value, $life = 0) {
        $file = $this->file($key);
        if (file_put_contents($file, serialize($value), LOCK_EX) !== false) {
            chmod($file, 0777);
            return touch($file, $this->life($life, true));
        }
        return false;
    }

    /* Метод удаления кеша по ключу */
    public function del($key) {
        $file = $this->file($key);
        return is_file($file) ? unlink($file) : false;
    }

    /* Метод удаления всего кеша */
    public function clear() {
        $files = glob($this->_dir . '*' . $this->_ext);
        foreach ($files as &$file) {
            unlink($file);
        }
        return true;
    }

    /* Метод получения имени файла */
    protected function file($key) {
        return $this->_dir . md5($key) . $this->_ext;
    }

    /* Метод установки срока хранения кеша */
    protected function life($life = 0, $time = false) {
        if ($life <= 0) {
            $life = $this->_life;
        }
        return $time === true ? $life + time() : $life;
    }

}
