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

class AdminInsert extends Magic {

    public function show() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $lid => $li) {
                $name = $this->filter($_POST['Name'][$lid]);
                if (!empty($name)) {
                    $this->_db->query("UPDATE " . PREFIX . "_collection SET
                        Name = '" . $this->_db->escape($name) . "',
                        Marker = '" . $this->_db->escape($_POST['Marker'][$lid]) . "',
                        Active = '" . $this->_db->escape($_POST['Active'][$lid]) . "'
                    WHERE Id = '" . intval($lid) . "'");
                }

                if (!empty($_POST['del'][$lid])) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_collection WHERE Id = '" . intval($lid) . "'");
                }
            }
            $this->clear();
            $this->__object('AdminCore')->script('save');
        }

        $db_sort = " ORDER BY Id ASC";
        $nav_sort = "&amp;sort=id_asc";
        $idsort = $markersort = $activesort = $def_search_n = $def_search = $namesort = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'id_asc':
            default:
                $db_sort = 'ORDER BY Id ASC';
                $nav_sort = '&amp;sort=id_asc';
                $idsort = 'id_desc';
                break;
            case 'id_desc':
                $db_sort = 'ORDER BY Id DESC';
                $nav_sort = '&amp;sort=id_desc';
                $idsort = 'id_asc';
                break;
            case 'name_asc':
                $db_sort = 'ORDER BY Name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'marker_asc':
                $db_sort = 'ORDER BY Marker ASC';
                $nav_sort = '&amp;sort=marker_asc';
                $markersort = 'marker_desc';
                break;
            case 'marker_desc':
                $db_sort = 'ORDER BY Marker DESC';
                $nav_sort = '&amp;sort=marker_desc';
                $markersort = 'marker_asc';
                break;
            case 'active_asc':
                $db_sort = 'ORDER BY Active ASC';
                $nav_sort = '&amp;sort=active_asc';
                $activesort = 'active_desc';
                break;
            case 'active_desc':
                $db_sort = 'ORDER BY Active DESC';
                $nav_sort = '&amp;sort=active_desc';
                $activesort = 'active_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = "WHERE (Name LIKE '%{$pattern}%' OR Marker LIKE '%{$pattern}%')";
        }

        $this->_view->assign('idsort', $idsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('markersort', $markersort);

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_collection {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $array = array();
        while ($row = $sql->fetch_object()) {
            $array[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=insert{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('collections', $array);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('title', $this->_lang['InsertContent']);
        $this->_view->content('/insert/overview.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $this->_db->query("UPDATE " . PREFIX . "_collection SET
                Name = '" . $this->_db->escape($this->filter(Arr::getPost('Name'))) . "',
                Text1 = '" . $this->_db->escape(Arr::getPost('Text1')) . "',
                Text2 = '" . $this->_db->escape(Arr::getPost('Text2')) . "',
                Text3 = '" . $this->_db->escape(Arr::getPost('Text3')) . "',
                Marker = '" . $this->_db->escape($this->marker()) . "',
                Active = '" . $this->_db->escape(Arr::getPost('Active')) . "'
            WHERE Id = '" . $id . "'");
            $this->clear();
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_collection WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->editor($res->Text1, $res->Text2, $res->Text3);
        $this->_view->assign('title', $this->_lang['InsertContent']);
        $this->_view->content('/insert/edit.tpl');
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $insert_array = array(
                'Name'   => $this->filter(Arr::getPost('Name')),
                'Text1'  => Arr::getPost('Text1'),
                'Text2'  => Arr::getPost('Text2'),
                'Text3'  => Arr::getPost('Text3'),
                'Marker' => $this->marker(),
                'Active' => Arr::getPost('Active')
            );
            $this->_db->insert_query('collection', $insert_array);
            $this->clear();
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый контент с ключем: ' . Arr::getPost('Name'), '0', $_SESSION['benutzer_id']);
            SX::output("<script type=\"text/javascript\">parent.location.href='?do=insert';</script>");
        }

        $this->editor();
        $this->_view->assign('title', $this->_lang['InsertContent']);
        $this->_view->content('/insert/new.tpl');
    }

    protected function editor($text1 = '', $text2 = '', $text3 = '') {
        $type = '';
        $size = 250;
        if (Arr::getRequest('html') == 1) {
            $type = 'admin';
            $size = 350;
        }
        $object = $this->__object('Editor');
        $this->_view->assign('text1', $object->load($type, $text1, 'Text1', 350, 'Content'));
        $this->_view->assign('text2', $object->load($type, $text2, 'Text2', 350, 'Content'));
        $this->_view->assign('text3', $object->load($type, $text3, 'Text3', 350, 'Content'));
    }

    protected function filter($string) {
        $string = translit($string, '_');
        return trim($string);
    }

    protected function clear() {
        $sql = $this->_db->query("SELECT Sprachcode FROM " . PREFIX . "_sprachen WHERE Aktiv = 1");
        while ($row = $sql->fetch_object()) {
            $this->__object('Cache')->del($row->Sprachcode . 'insert');
        }
        $sql->close();
    }

    protected function marker() {
        $marker = Arr::getPost('Marker');
        if (empty($marker)) {
            $marker = Arr::getPost('Text1');
            if (!empty($marker)) {
                $marker = strip_tags($marker);
                $marker = Tool::cleanSpace($marker);
                $marker = $this->_text->chars($marker, 147, '...', false);
            }
        }
        return trim($marker);
    }

    public function load() {
        return $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_collection ORDER BY Id DESC");
    }

}