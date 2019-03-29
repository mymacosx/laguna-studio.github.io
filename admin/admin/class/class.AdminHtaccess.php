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

class AdminHtaccess extends Magic {

    protected $_file;
    protected $_error = false;

    public function __construct() {
        $this->_file = SX_DIR . '/.htaccess';
    }

    /* Метод вывода шаблона и сохранеия данных */
    public function get() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $auto = 0;
        $error = $this->check();
        if (Arr::getPost('save') == 1) {
            $auto = Arr::getPost('auto');
            $array = array(
                'auto'    => $auto,
                'expires' => Arr::getPost('expires'),
                'headers' => Arr::getPost('headers'),
                'rewrite' => Arr::getPost('rewrite'),
                'www'     => Arr::getPost('www'),
                'lich'    => Arr::getPost('lich'),
                'exts'    => $this->exts(Arr::getPost('exts')),
            );
            SX::save('htaccess', $array);

            if ($auto == 1) {
                $this->auto();
            } else {
                $this->write(Arr::getPost('htaccess'));
            }

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки htaccess', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
            SX::load('htaccess');
        }
        $row = SX::get('htaccess');

        $row['mod_rewrite'] = Tool::apacheModul('mod_rewrite');
        $row['mod_headers'] = Tool::apacheModul('mod_headers');
        $row['mod_expires'] = Tool::apacheModul('mod_expires');

        $row['exts'] = str_replace('|', PE, $row['exts']);
        $this->_view->assign('row', $row);
        $this->_view->assign('save', $auto);
        $this->_view->assign('host', str_replace(array('http://', 'https://', 'www.'), '', BASE_URL));
        $this->_view->assign('error', $error);
        $this->_view->assign('htaccess', $this->read());
        $this->_view->assign('title', $this->_lang['HtaccessSettings']);
        $this->_view->content('/settings/htaccess_settings.tpl');
    }

    protected function exts($value) {
        if (!empty($value)) {
            $value = str_replace(array("\r\n", "\n\r", "\n", "\r"), '|', $value);
            $array = explode('|', $value);
            $array = array_unique($array);
            $result = array();
            foreach ($array as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $result[] = $value;
                }
            }
            return implode('|', $result);
        }
        return '';
    }

    /* Метод проверки на ошибки */
    protected function check() {
        if (!is_file($this->_file)) {
            $this->_error = true;
            return $this->_lang['RwEHtacces_ne'];
        }
        if (!is_writable($this->_file)) {
            chmod($this->_file, 0777);
        }
        if (!is_writable($this->_file)) {
            $this->_error = true;
            return $this->_lang['RwEHtacces_nw'];
        }
        return NULL;
    }

    /* Метод чтения файла */
    protected function read() {
        if ($this->_error === false) {
            return File::get($this->_file);
        }
        return NULL;
    }

    /* Метод записи в файл */
    protected function write($content) {
        if ($this->_error === false) {
            File::set($this->_file, $content);
        }
    }

    protected function auto() {
        if ($this->_error === false) {
            SX::save('system', array('Seo_Sprachen' => ''));
        }
    }

}
