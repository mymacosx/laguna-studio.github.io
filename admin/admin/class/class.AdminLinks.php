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

class AdminLinks extends Magic {

    public function settings() {
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Kommentare'   => intval(Arr::getPost('Kommentare')),
                'Wertung'      => intval(Arr::getPost('Wertung')),
                'LinkMelden'   => 1,
                'DefektMelden' => intval(Arr::getPost('DefektMelden')),
                'Flaggen'      => intval(Arr::getPost('Flaggen')),
                'PageLimit'    => intval(Arr::getPost('PageLimit')));
            SX::save('links', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('links');
        }
        $res = SX::get('links');
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' ' . $this->_lang['Links']);
        $this->_view->content('/links/settings.tpl');
    }

    public function show() {
        $db_sort = " ORDER BY Datum ASC";
        $nav_sort = "&amp;sort=name_asc";
        $datesort = $def_search_n = $def_search = $namesort = $hitssort = $usersort = $categsort = '';
        $activesort = $scq = $sc = $pattern = $pattern2 = $brokenq = $brokenq_n = $toplinksort = '';

        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Aktiv'] as $lid => $li) {
                $lid = intval($lid);
                $array = array(
                    'Kategorie' => $_POST['Kategorie'][$lid],
                    'Aktiv'     => $_POST['Aktiv'][$lid],
                    'Hits'      => $_POST['Hits'][$lid],
                    'Sponsor'   => $_POST['Sponsor'][$lid],
                );
                $this->_db->update_query('links', $array, "Id = '" . $lid . "'");
                if (isset($_POST['del'][$lid]) && $_POST['del'][$lid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_links WHERE Id='" . $lid . "'");
                    $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='links' AND Objekt_Id='" . $lid . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_REQUEST['categ'])) {
            $scq = "AND (Kategorie='" . $this->_db->escape(Arr::getRequest('categ')) . "') ";
            $sc = "&amp;categ=" . $_REQUEST['categ'];
        }

        if (Arr::getRequest('broken') == 1) {
            $brokenq = "AND (DefektGemeldet!='') ";
            $brokenq_n = "&amp;broken=1";
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ');
            $pattern2 = sanitize($pattern);
            $def_search_n = "&amp;q=" . urlencode($pattern) . $sc;
            $def_search = "{$scq} {$brokenq} AND ((Name_1 LIKE '%{$pattern}%' OR Beschreibung_1 LIKE '%{$pattern}%') OR (Name_1 LIKE '%{$pattern2}%' OR Beschreibung_1 LIKE '%{$pattern2}%'))";
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'date_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'name_asc':
                $db_sort = 'ORDER BY Name_1 ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Name_1 DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
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
            case 'user_desc':
                $db_sort = 'ORDER BY Autor DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'hits_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY Autor ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'hits_desc';
                break;
            case 'categ_desc':
                $db_sort = 'ORDER BY Kategorie DESC';
                $nav_sort = '&amp;sort=categ_desc';
                $categsort = 'categ_asc';
                break;
            case 'categ_asc':
                $db_sort = 'ORDER BY Kategorie ASC';
                $nav_sort = '&amp;sort=categ_asc';
                $categsort = 'categ_desc';
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
            case 'toplink_desc':
                $db_sort = 'ORDER BY Sponsor DESC';
                $nav_sort = '&amp;sort=toplink_desc';
                $toplinksort = 'toplink_asc';
                break;
            case 'toplink_asc':
                $db_sort = 'ORDER BY Sponsor ASC';
                $nav_sort = '&amp;sort=toplink_asc';
                $toplinksort = 'toplink_desc';
                break;
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
			*,
			Kategorie,
			Name_1 AS Name,
			Beschreibung_1 AS Beschreibung
		FROM
			" . PREFIX . "_links
		WHERE
			Sektion = '" . AREA . "'
		{$def_search} {$db_sort} LIMIT $a, $limit");

        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $entries = array();
        while ($row_items = $query_items->fetch_object()) {
            $row_items->User = Tool::userName($row_items->Autor);
            $row_items->Comments = $this->__object('AdminCore')->countComments('links', $row_items->Id);
            $row_items->CCount = Tool::countComments($row_items->Id, 'links');
            $row_items->Wertung = Tool::rating($row_items->Id, 'links');
            $entries[] = $row_items;
        }
        $query_items->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=links&sub=overview&categ=" . Arr::getRequest('categ') . "{$def_search_n}{$nav_sort}{$brokenq_n}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }

        $news_categ = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $news_categ, $area));
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('toplinksort', $toplinksort);
        $this->_view->assign('categsort', $categsort);
        $this->_view->assign('usersort', $usersort);
        $this->_view->assign('hitssort', $hitssort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('Entries', $entries);
        $this->_view->assign('title', $this->_lang['Links']);
        $this->_view->content('/links/links.tpl');
    }

    public function copy($id) {
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_links WHERE Id='" . intval($id) . "' LIMIT 1");
        $insert_array = array(
            'Kategorie'      => $res->Kategorie,
            'Datum'          => time(),
            'Bild'           => $res->Bild,
            'Name_1'         => $res->Name_1 . $this->_lang['DbCopy'],
            'Name_2'         => $res->Name_2 . $this->_lang['DbCopy'],
            'Name_3'         => $res->Name_3 . $this->_lang['DbCopy'],
            'Beschreibung_1' => $res->Beschreibung_1,
            'Beschreibung_2' => $res->Beschreibung_2,
            'Beschreibung_3' => $res->Beschreibung_3,
            'Url'            => $res->Url,
            'Hits'           => 0,
            'Sektion'        => $res->Sektion,
            'Autor'          => $_SESSION['benutzer_id'],
            'DDatum'         => 0,
            'Aktiv'          => $res->Aktiv,
            'Sponsor'        => $res->Sponsor);
        $this->_db->insert_query('links', $insert_array);
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал ссылку (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $datum = !empty($_POST['Datum']) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $name = str_ireplace(array('http://', 'https://'), '', Arr::getPost('Name'));
            $Kategorie = Arr::getPost('Kategorie');
            $Beschreibung = Arr::getPost('Beschreibung');

            $insert_array = array(
                'Kategorie'      => $Kategorie,
                'Datum'          => $datum,
                'Bild'           => Arr::getPost('newImg_1'),
                'Name_1'         => $name,
                'Name_2'         => $name,
                'Name_3'         => $name,
                'Beschreibung_1' => $Beschreibung,
                'Beschreibung_2' => $Beschreibung,
                'Beschreibung_3' => $Beschreibung,
                'Url'            => Arr::getPost('Url'),
                'Hits'           => 0,
                'Sektion'        => AREA,
                'Autor'          => $_SESSION['benutzer_id'],
                'DDatum'         => 0,
                'Aktiv'          => Arr::getPost('Aktiv'),
                'Sprache'        => Arr::getPost('Sprache'));
            $this->_db->insert_query('links', $insert_array);
            $new_id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $options = array(
                'name' => $name,
                'url'  => BASE_URL . '/index.php?p=links&action=showdetails&area=' . AREA . '&categ=' . $Kategorie . '&id=' . $new_id . '&name=' . translit($name),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $name,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую ссылку (' . $name . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $categs = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Links_add']);
        $this->_view->content('/links/link_new.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        $LC = $this->__object('AdminCore')->getLangcode();
        $SetAll = '';

        if (Arr::getPost('save') == 1) {
            $_POST['Datum'] = (!empty($_POST['Datum'])) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $_POST['Name'] = str_ireplace(array('http://', 'https://'), '', $_POST['Name']);
            $db_extra = '';

            if ($LC == 1) {
                $Bild = '';

                if (!empty($_POST['newImg_1'])) {
                    $Bild = "Bild='" . $this->_db->escape(Arr::getPost('newImg_1')) . "',";
                }
                $Dl = (Arr::getPost('DelDM') == 1) ? "DefektGemeldet='',DEmail='',DName='',DDatum=''," : '';
                $Bild = (Arr::getPost('NoImg') == 1 && empty($_POST['newImg_1'])) ? "Bild = ''," : $Bild;
                $db_extra = "
				   {$Bild}
				   Sprache='" . $this->_db->escape(Arr::getPost('Sprache')) . "',
				   Url='" . $this->_db->escape(Arr::getPost('Url')) . "',
				   Datum ='" . $this->_db->escape(Arr::getPost('Datum')) . "',
				   Kategorie='" . $this->_db->escape(Arr::getPost('Kategorie')) . "',
				   Aktiv='" . $this->_db->escape(Arr::getPost('Aktiv')) . "',
				   {$Dl} ";
            }

            if (Arr::getPost('saveAllLang') == 1) {
                $SetAll = "
					,Name_2 = '" . $this->_db->escape($_POST['Name']) . "'
					,Beschreibung_2 = '" . $this->_db->escape($_POST['Beschreibung']) . "'
					,Name_3 = '" . $this->_db->escape($_POST['Name']) . "'
					,Beschreibung_3 = '" . $this->_db->escape($_POST['Beschreibung']) . "'
					";
            }

            $db_in = "UPDATE " . PREFIX . "_links SET {$db_extra} Name_{$LC} = '" . $this->_db->escape(Arr::getPost('Name')) . "', Beschreibung_{$LC} = '" . $this->_db->escape($_POST['Beschreibung']) . "' {$SetAll} WHERE Id='" . $id . "'";
            $this->_db->query($db_in);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал ссылку (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT *, Beschreibung_{$LC} as Beschreibung, Name_{$LC} as Name FROM " . PREFIX . "_links WHERE Id='" . $id . "' LIMIT 1");
        $categs = array();
        $area = AREA;
        $this->_view->assign('res', $res);
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', $res->Beschreibung, 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Links_edit']);
        $this->_view->content('/links/link_edit.tpl');
    }

    public function delCateg($id) {
        if (perm('articles_category')) {
            $id = intval($id);
            $res = $this->_db->cache_fetch_object("SELECT Id, Parent_Id FROM " . PREFIX . "_links_kategorie WHERE Parent_Id='" . $id . "' LIMIT 1");
            $this->remove($id);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию ссылок (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    protected function remove($id) {
        $id = intval($id);
        $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_links_kategorie WHERE Parent_Id='" . $id . "'");
        while ($item = $query->fetch_object()) {
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_links WHERE Kategorie='" . $id . "'");
            while ($row = $sql->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='articles' AND Objekt_Id='" . $row->Id . "'");
            }
            $this->_db->query("DELETE FROM " . PREFIX . "_links WHERE Kategorie='" . $id . "'");
            $this->remove($item->Id);
        }
        $query->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_links_kategorie WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_links WHERE Kategorie='" . $id . "'");
    }

    public function showCategs() {
        $categs = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('title', $this->_lang['Links_categs']);
        $this->_view->content('/links/link_categs.tpl');
    }

    public function editCateg($id) {
        $id = intval($id);
        if (Arr::getPost('save') == 1) {
            $Name_1 = $_POST['Name_1'];
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;
            $Beschreibung_1 = $_POST['Beschreibung_1'];
            $Beschreibung_2 = !empty($_POST['Beschreibung_2']) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = !empty($_POST['Beschreibung_3']) ? $_POST['Beschreibung_3'] : $Beschreibung_1;

            $array = array(
                'Name_1'         => $Name_1,
                'Name_2'         => $Name_2,
                'Name_3'         => $Name_3,
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
            );
            $this->_db->update_query('links_kategorie', $array, "Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал категорию ссылок (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $categs = array();
        $area = AREA;
        $res = $this->_db->cache_fetch_object("SELECT *  FROM " . PREFIX . "_links_kategorie WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('title', $this->_lang['Links_categs']);
        $this->_view->content('/links/link_categ.tpl');
    }

    public function addCateg() {
        if (Arr::getPost('save') == 1) {
            $Name_1 = $_POST['Name_1'];
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;
            $Beschreibung_1 = $_POST['Beschreibung_1'];
            $Beschreibung_2 = !empty($_POST['Beschreibung_2']) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = !empty($_POST['Beschreibung_3']) ? $_POST['Beschreibung_3'] : $Beschreibung_1;

            $insert_array = array(
                'Parent_Id'      => Arr::getPost('categ'),
                'Name_1'         => $Name_1,
                'Name_2'         => $Name_2,
                'Name_3'         => $Name_3,
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
                'Sektion'        => $_SESSION['a_area']);
            $this->_db->insert_query('links_kategorie', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую категорию ссылок (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $categs = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('new', 1);
        $this->_view->assign('title', $this->_lang['Links_categs']);
        $this->_view->content('/links/link_categ.tpl');
    }

    protected function simpleCategs($id, $prefix, &$news_categ, &$area) {
        $query = $this->_db->query("SELECT Id, Parent_Id, Name_1 AS Name FROM " . PREFIX . "_links_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY Name ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . '  ' . $item->Name;
            $news_categ[] = $item;
            $this->simpleCategs($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

    public function delRating($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='links' AND Objekt_Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

}
