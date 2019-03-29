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

class AdminGlossar extends Magic {

    public function edit($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Wort'         => Arr::getPost('Wort'),
                'Beschreibung' => $_POST['Beschreibung'],
                'Typ'          => Arr::getPost('Typ'),
            );
            $this->_db->update_query('glossar', $array, "Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил энциклопедию (' . $_POST['Wort'] . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_glossar WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', $res->Beschreibung, 'Beschreibung', 400, 'Settings'));
        $this->_view->assign('title', $this->_lang['Glossar']);
        $this->_view->content('/glossar/edit.tpl');
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $insert_array = array(
                'Wort'         => Arr::getPost('Wort'),
                'Beschreibung' => Arr::getPost('Beschreibung'),
                'Aktiv'        => 1,
                'Typ'          => Arr::getPost('Typ'));
            $this->_db->insert_query('glossar', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил пункт в энциклопедию (' . $_POST['Wort'] . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 400, 'Settings'));
        $this->_view->assign('title', $this->_lang['Glossar']);
        $this->_view->content('/glossar/edit.tpl');
    }

    public function show() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Aktiv'] as $lid => $glos) {
                $array = array(
                    'Wort'  => $_POST['Wort'][$lid],
                    'Aktiv' => $_POST['Aktiv'][$lid],
                    'Typ'   => $_POST['Typ'][$lid],
                );
                $this->_db->update_query('glossar', $array, "Id='" . intval($lid) . "'");
                if (isset($_POST['del'][$lid]) && $_POST['del'][$lid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_glossar WHERE Id='" . intval($lid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $db_sort = " ORDER BY Name ASC";
        $nav_sort = '&amp;sort=name_asc';
        $def_search_n = $def_search = $namesort = $typesort = $hitssort = $activesort = $pattern2 = '';

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ');
            $pattern2 = sanitize($pattern);
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " AND ((Wort LIKE '%{$this->_db->escape($pattern)}%') OR (Wort LIKE '%{$this->_db->escape($pattern2)}%'))";
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'name_asc':
                $db_sort = 'ORDER BY Wort ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Wort DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'type_asc':
                $db_sort = 'ORDER BY Typ ASC';
                $nav_sort = '&amp;sort=type_asc';
                $typesort = 'type_desc';
                break;
            case 'type_desc':
                $db_sort = 'ORDER BY Typ DESC';
                $nav_sort = '&amp;sort=type_desc';
                $typesort = 'type_asc';
                break;
            case 'hits_desc':
                $db_sort = 'ORDER BY Hits DESC';
                $nav_sort = '&amp;sort=hits_desc';
                $hitssort = 'hits_asc';
                break;
            case 'hits_asc':
                $db_sort = 'ORDER BY Hits ASC';
                $nav_sort = '&amp;sort=hits_asc';
                $hitssort = 'hits_desc';
                break;
            case 'active_desc':
                $db_sort = 'ORDER BY Aktiv DESC';
                $nav_sort = '&amp;sort=active_desc';
                $activesort = 'active_asc';
                break;
            case 'active_asc':
                $db_sort = 'ORDER BY Aktiv ASC';
                $nav_sort = '&amp;sort=active_asc';
                $activesort = 'active_desc';
                break;
        }

        $limit = $this->__object('AdminCore')->limit(25);
        $a = Tool::getLimit($limit);
        $query = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_glossar WHERE Id!='0' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $query->fetch_object()) {
            $items[] = $row;
        }
        $query->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=glossar{$def_search_n }{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('hitssort', $hitssort);
        $this->_view->assign('typesort', $typesort);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Glossar']);
        $this->_view->content('/glossar/overview.tpl');
    }

}
