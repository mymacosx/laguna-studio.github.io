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

class Glossar extends Magic {

    protected $_cache = array();
    protected $_param = array();

    /* Метод проверки кеширования */
    protected function load() {
        static $load = false;
        if ($load === false) {
            $load = true;
            $this->cache();
        }
    }

    /* Метод кеширования в память значений энциклопедии */
    protected function cache() {
        $this->_cache = $this->_db->fetch_assoc_all("SELECT Id, Wort, Typ, Beschreibung FROM " . PREFIX . "_glossar WHERE Aktiv='1'");
    }

    /* Метод замены фраз и скрытых ссылок из модуля энциклопедии */
    public function get($text) {
        $this->load();
        if (!empty($this->_cache)) {
            foreach ($this->_cache as $arr) {
                $this->_param = $arr;
                $mask = '/(^|[^a-zа-яё0-9-_\/])(' . $this->words($arr['Wort']) . ')(?![^<]+>|[^<a]*<\/a>)([^a-zа-яё0-9-_\/]|$)/Uisu';
                $text = preg_replace_callback($mask, array($this, 'callback'), $text);
            }
        }
        return $text;
    }

    /* Метод состовления списка фраз одного значения для поиска */
    protected function words($words) {
        $array = explode('||', $words);
        $array = array_map('sanitize', $array);
        $words = implode('|', $array);
        return $words;
    }

    protected function callback($match) {
        switch ($this->_param['Typ']) {
            case 1:
                $word = '<a title="" href="' . $this->_param['Beschreibung'] . '" style="color:windowtext;text-decoration:none;text-underline:none;cursor:text">' . $match[2] . '</a>';
                break;
            case 2:
                $word = '<a title="' . $match[2] . '" href="' . $this->_param['Beschreibung'] . '" rel="nofollow" target="_blank">' . $match[2] . '</a>';
                break;
            case 0:
            default:
                $word = '<a class="autowords colorbox_small" href="index.php?p=misc&amp;do=autowords&amp;id=' . $this->_param['Id'] . '">' . $match[2] . '</a>';
                break;
        }
        return $match[1] . $word . $match[3];
    }

    public function autowords($id) {
        $id = intval($id);
        $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_glossar WHERE Id = '" . $id . "' LIMIT 1");
        $this->_db->query("UPDATE " . PREFIX . "_glossar SET Hits = Hits+1 WHERE Id = '" . $id . "'");
        $this->_view->assign('text', $this->__object('Glossar')->get($row->Beschreibung));
        $array = explode('||', $this->_text->lower($row->Wort));
        $array = array_unique($array);
        $title = implode(', ', $array);
        $title = $this->_text->upper($title);
        $this->_view->assign('title_html', $this->_lang['Glossar_Title'] . ': ' . $title);

        $seo_array = array(
            'headernav' => $array,
            'pagetitle' => sanitize($title . $this->_lang['PageSep'] . $this->_lang['Glossar_Title']),
            'generate'  => $row->Wort . ' ' . $row->Beschreibung,
            'content'   => $this->_view->fetch(THEME . '/other/autowords.tpl'));
        $this->_view->finish($seo_array);
    }

}
