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

class Session {

    protected $_db;

    public function __construct() {
        $this->_db = DB::get();
        session_set_save_handler(
                array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc')
        );
    }

    /* Инициализация сессии */
    public function open($path, $name) {
        return true;
    }

    public function close() {
        if (mt_rand(1, 150) <= 3) {
            $this->gc();
        }
        return true;
    }

    /* Читаем данные сессии */
    public function read($key) {
        $sql = $this->_db->query("SELECT Wert FROM " . PREFIX . "_sessions WHERE Schluessel = '" . $this->_db->escape($key) . "' AND Ip = '" . IP_USER . "' AND Ablauf > " . time());
        if ((list($session) = $sql->fetch_row())) {
            return $session;
        }
        return '';
    }

    /* Записываем или обновляем сессию */
    public function write($key, $val) {
        $expire = time() + SX::get('database.dbsesslife');
        $val = $this->_db->escape($val);
        $sql = $this->_db->query("INSERT INTO " . PREFIX . "_sessions (
                Schluessel,
                Ablauf,
                Wert,
                Ip
        ) VALUES (
                '" . $this->_db->escape($key) . "',
                '" . $expire . "',
                '" . $val . "',
                '" . IP_USER . "'
        ) ON DUPLICATE KEY UPDATE
                Ablauf = '" . $expire . "',
                Wert = '" . $val . "',
                Ip = '" . IP_USER . "'");
        return $sql ? true : false;
    }

    /* Уничтожаем сессию */
    public function destroy($key) {
        $sql = $this->_db->query("DELETE FROM " . PREFIX . "_sessions WHERE Schluessel = '" . $this->_db->escape($key) . "'");
        return $sql ? true : false;
    }

    /* Чистка истекших сессий */
    public function gc() {
        $this->_db->query("DELETE FROM " . PREFIX . "_sessions WHERE Ablauf < " . time());
        return true;
    }

}