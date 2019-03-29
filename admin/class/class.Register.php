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

class Register extends Magic {

    /* Метод обработки виджетов в шаблонах */
    public function widget($params = array()) {
        return !empty($params['name']) ? $this->__object('Widget')->get($params) : NULL;
    }

    public function autowords($text) {
        return $this->__object('Glossar')->get($text);
    }

    /* Подключаем модуль статических страниц */
    public function content($params = array()) {
        return get_active('content') && !empty($params['id']) ? $this->__object('Content')->load($params['id']) : NULL;
    }

    /* Подключаем модуль контактных форм */
    public function contact($params = array()) {
        $result = NULL;
        if (!empty($params['id'])) {
            $result = $this->__object('Contactform')->load(array(
                1 => $params['id'],
                2 => !empty($params['tpl']) ? $params['tpl'] : null
            ));
        }
        return $result;
    }

    public function useronline($params = array()) {
        return get_active('whosonline') ? $this->__object('Counter')->online($params) : NULL;
    }

    public function forumstats($params = array()) {
        return $this->__object('Forum')->forumStats($params);
    }

    public function birthdays($params = array()) {
        return $this->__object('Forum')->birthdays($params);
    }

    public function onlinestatus($params = array()) {
        return $this->__object('Forum')->onlineStatus($params);
    }

    /* Получаем количество новых личных сообщений и количество непрочитаных личных сообщений */
    public function newpn() {
        static $array = array();
        $result = '';
        if (get_active('pn')) {
            if (empty($array)) {
                $array = $this->_db->fetch_assoc_all(
                        "SELECT COUNT(pnid) AS num FROM " . PREFIX . "_pn WHERE to_uid='" . $_SESSION['benutzer_id'] . "' AND typ='inbox' AND is_readed='no'
	            UNION ALL
		    SELECT COUNT(pnid) AS num FROM " . PREFIX . "_pn WHERE to_uid='" . $_SESSION['benutzer_id'] . "' AND typ='inbox'");
            }
            if ($array[0]['num'] >= 1 && $_REQUEST['p'] != 'pn' && Tool::userSettings('PnPopup') == 1) {
                $result .= '<script type="text/javascript">newWindow(\'' . BASE_URL . '/index.php?p=misc&do=pnpop\', 550, 340);</script>';
            }
            $result .= ' (' . $array[0]['num'] . '|' . $array[1]['num'] . ')';
        }
        return $result;
    }

    public function bookmarks($params = array()) {
        return get_active('social_bookmarks') ? $this->__object('Bookmark')->get($params) : NULL;
    }

    public function flashtag($params = array()) {
        return get_active('flashtag') ? $this->__object('Flashtag')->get($params) : NULL;
    }

    public function banner($params = array()) {
        return $this->__object('Banner')->get($params);
    }

    public function phrases($params = array()) {
        return get_active('phrases') ? $this->__object('Phrases')->get($params) : NULL;
    }

    public function navigation($params = array()) {
        return $this->__object('Navigation')->panel($params);
    }

    public function page_link() {
        return $this->__object('Redir')->link();
    }

    /*  Вывод версии системы */
    public function version() {
        return str_replace('SX CMS', '<a href="http://www.status-x.ru">SX CMS</a>', 'Powered by SX CMS ' . SX::get('system.Version'));
    }

    /* Метод обработки текста всплывающих подсказок */
    public function tooltip($text, $limit = NULL) {
        if (!empty($text)) {
            $text = str_replace('&nbsp;', ' ', $text);
            $text = strip_tags($text, '<br><br/>');
            $text = str_replace(array("\r\n", "\n\r", "\n", "\r", '<br />', '<br/>', '<br>'), "\n", $text);
            $text = explode("\n", $text);
            $text = array_map('trim', $text);
            $text = array_diff($text, array(NULL));
            $text = array_map(array('Tool', 'cleanSpace'), $text);
            $text = implode("\n", $text);
            if (!empty($limit)) {
                $text = $this->_text->chars($text, $limit);
            }
            $text = str_replace("\n", '<br />', $text);
            return sanitize($text);
        }
        return NULL;
    }

}