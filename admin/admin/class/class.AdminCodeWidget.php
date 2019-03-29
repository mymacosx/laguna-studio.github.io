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

class AdminCodeWidget extends Magic {

    public function show() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $lid => $li) {
                $Name = (!empty($_POST['Name'][$lid])) ? $_POST['Name'][$lid] : '';
                if (!empty($Name)) {
                    $this->_db->query("UPDATE " . PREFIX . "_codewidget SET Aktiv = '" . $this->_db->escape($_POST['Aktiv'][$lid]) . "', Name = '" . $this->_db->escape(trim($_POST['Name'][$lid])) . "' WHERE Id = '" . intval($lid) . "'");
                }

                if (!empty($_POST['del'][$lid])) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_codewidget WHERE Id = '" . intval($lid) . "'");
                }
                $this->__object('AdminCore')->script('save');
            }
        }

        $db_sort = " ORDER BY Name ASC";
        $nav_sort = "&amp;sort=name_asc";
        $datesort = $activesort = $imgsort = $usersort = $def_search_n = $def_search = $namesort = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'name_asc':
            default:
                $db_sort = 'ORDER BY Name ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'date_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY Benutzer ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY Benutzer DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
            case 'active_asc':
                $db_sort = 'ORDER BY Aktiv ASC';
                $nav_sort = '&amp;sort=active_asc';
                $activesort = 'active_desc';
                break;
            case 'active_desc':
                $db_sort = 'ORDER BY Aktiv DESC';
                $nav_sort = '&amp;sort=active_desc';
                $activesort = 'active_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " AND (Name LIKE '%{$pattern}%') ";
        }

        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('imgsort', $imgsort);
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('usersort', $usersort);

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_codewidget WHERE Name != '' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $widgets = array();
        while ($row = $sql->fetch_object()) {
            $row->BenutzerName = Tool::userName($row->Benutzer);
            $widgets[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=codewidgets{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('widgets', $widgets);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('title', $this->_lang['CodeWidgets']);
        $this->_view->content('/codewidgets/overview.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $groups = (Arr::getPost('AlleGruppen') == 1) ? "Gruppen = '', " : "Gruppen = '" . $this->_db->escape(implode(',', $_POST['Gruppen'])) . "',";
            $this->_db->query("UPDATE " . PREFIX . "_codewidget SET {$groups} Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "', Name = '" . $this->_db->escape(trim(Arr::getPost('Name'))) . "', Inhalt = '" . $this->_db->escape(Arr::getPost('Inhalt')) . "' WHERE Id = '" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_codewidget WHERE Id = '" . $id . "' LIMIT 1");
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('groups', explode(',', $res->Gruppen));
        $this->_view->assign('res', $res);
        $this->_view->assign('text', $this->editor($res->Inhalt));
        $this->_view->assign('title', $this->_lang['CodeWidgets']);
        $this->_view->content('/codewidgets/edit.tpl');
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $insert_array = array(
                'Name'     => Arr::getPost('Name'),
                'Inhalt'   => Arr::getPost('Inhalt'),
                'Benutzer' => $_SESSION['benutzer_id'],
                'Datum'    => time(),
                'Gruppen'  => (Arr::getPost('AlleGruppen') == 1) ? '' : implode(',', $_POST['Gruppen']));
            $this->_db->insert_query('codewidget', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новый виджет-кода: ' . Arr::getPost('Name'), '0', $_SESSION['benutzer_id']);
            SX::output("<script type=\"text/javascript\">parent.location.href='?do=codewidgets';</script>");
        }

        $nullarray = array();
        $this->_view->assign('groups', $nullarray);
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('text', $this->editor());
        $this->_view->assign('title', $this->_lang['CodeWidgets']);
        $this->_view->content('/codewidgets/new.tpl');
    }

    protected function editor($text = '') {
        if (isset($_REQUEST['html']) && $_REQUEST['html'] == 1) {
            return $this->__object('Editor')->load('admin', $text, 'Inhalt', 350, 'Content');
        }
        return $this->__object('Editor')->load('', $text, 'Inhalt', 250, 'Content');
    }

    public function load() {
        $widgets = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_codewidget ORDER BY Id DESC");
        return $widgets;
    }

}
