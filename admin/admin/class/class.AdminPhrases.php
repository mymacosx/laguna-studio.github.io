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

class AdminPhrases extends Magic {

    public function edit($id, $name, $text) {
        $name = Tool::cleanSpace(Tool::cleanAllow($name));
        $text = Tool::cleanSpace($text);
        if (!empty($name) && !empty($text)) {
            $this->_db->query("UPDATE " . PREFIX . "_phrases SET name='" . $this->_db->escape($name) . "', phrase='" . $this->_db->escape($text) . "' WHERE id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('vkl', 1);
            $this->_view->assign('error', 1);
            SX::output("<script type=\"text/javascript\">alert('" . $this->_lang['Validate_required'] . "')</script>");
        }
        $this->show();
    }

    public function add($name, $text) {
        $name = Tool::cleanSpace(Tool::cleanAllow($name));
        $text = Tool::cleanSpace($text);
        if (!empty($name) && !empty($text)) {
            $this->_db->insert_query('phrases', array('active' => 1, 'name' => $this->_db->escape($name), 'phrase' => $this->_db->escape($text)));
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('error', 1);
            SX::output("<script type=\"text/javascript\">alert('" . $this->_lang['Validate_required'] . "')</script>");
        }
        $this->show();
    }

    public function delete($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_phrases WHERE id='" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    public function aktive($type, $id) {
        if (isset($type) && !empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_phrases SET active='" . intval($type) . "' WHERE id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->show();
    }

    public function get($id) {
        if (!empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_phrases WHERE id = '" . intval($id) . "' LIMIT 1");
            $this->_view->assign('vkl', 1);
            $this->_view->assign('row', $row);
        }
        $this->show();
    }

    public function show() {
        $db_sort = " ORDER BY name ASC";
        $nav_sort = "&amp;sort=name_asc";
        $namesort = $phrasesort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY name DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'phrase_asc':
                $db_sort = 'ORDER BY phrase ASC';
                $nav_sort = '&amp;sort=phrase_asc';
                $phrasesort = 'phrase_desc';
                break;
            case 'phrase_desc':
                $db_sort = 'ORDER BY phrase DESC';
                $nav_sort = '&amp;sort=phrase_desc';
                $phrasesort = 'phrase_asc';
                break;
        }
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('phrasesort', $phrasesort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $def_search = " WHERE (name LIKE '%{$this->_db->escape($pattern)}%' OR phrase LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $limit = $this->__object('AdminCore')->limit(10);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_phrases {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=phrases{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->content('/other/phrases.tpl');
    }

}
