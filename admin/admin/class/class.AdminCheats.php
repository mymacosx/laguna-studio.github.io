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

class AdminCheats extends Magic {

    public function delRating($id) {
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='cheats' AND Objekt_Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function showPlattforms() {
        if (!perm('plattforms')) {
            SX::object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1) {
            foreach ($_POST['Name'] as $nn => $nid) {
                if (!empty($_POST['Name'][$nn])) {
                    $this->_db->insert_query('plattformen', array('Name' => $_POST['Name'][$nn], 'Sektion' => AREA));
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $cat => $nid) {
                if (!empty($_POST['Name'][$cat])) {
                    $this->_db->query("UPDATE " . PREFIX . "_plattformen SET Name='" . $this->_db->escape($_POST['Name'][$cat]) . "' WHERE Id='" . intval($cat) . "'");
                }
                if (isset($_POST['del'][$cat]) && $_POST['del'][$cat] == 1 && perm('plattforms')) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_plattformen WHERE Id='" . intval($cat) . "'");
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил платформу (' . $_POST['Name'][$cat] . ')', '0', $_SESSION['benutzer_id']);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $pf = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_plattformen WHERE Sektion='" . AREA . "' ORDER BY Name ASC");

        $this->_view->assign('plattforms', $pf);
        $this->_view->assign('title', $this->_lang['Gaming_plattforms']);
        $this->_view->content('/cheats/plattforms.tpl');
    }

    public function settings() {
        if (!perm('cheats')) {
            SX::object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Kommentare'   => intval(Arr::getPost('Kommentare')),
                'Wertung'      => intval(Arr::getPost('Wertung')),
                'LinkMelden'   => 1,
                'DefektMelden' => intval(Arr::getPost('DefektMelden')),
                'Flaggen'      => intval(Arr::getPost('Flaggen')),
                'PageLimit'    => intval(Arr::getPost('PageLimit')));
            SX::save('cheats', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('cheats');
        }
        $res = SX::get('cheats');
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' ' . $this->_lang['Gaming_cheats']);
        $this->_view->content('/cheats/settings.tpl');
    }

    public function edit($id) {
        if (!perm('cheats_edit')) {
            SX::object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $LC = $this->__object('AdminCore')->getLangcode();
        $SetAll = '';

        if (Arr::getPost('save') == 1) {
            $_POST['DatumUpdate'] = (!empty($_POST['DatumUpdate'])) ? $this->__object('AdminCore')->mktime($_POST['DatumUpdate']) : time();
            $db_extra = '';

            if ($LC == 1) {
                $Bild = '';

                if (!empty($_POST['newImg_1'])) {
                    $Bild = "Bild='" . $this->_db->escape(Arr::getPost('newImg_1')) . "',";
                }

                $_POST['FileManual'] = trim(Arr::getPost('q'));
                $Dl = (Arr::getPost('DelDM') == 1) ? "DefektGemeldet='',DEmail='',DName='',DDatum=''," : '';
                $Url = !empty($_POST['newImg_2']) ? "Download = '" . $this->_db->escape(Arr::getPost('newImg_2')) . "'," : '';
                $Url = (!empty($_POST['FileManual']) && is_file(UPLOADS_DIR . '/cheats_files/' . $_POST['FileManual']) && $_POST['FileManual'] != '.htaccess' && $_POST['FileManual'] != 'index.php') ? "Download = '" . $this->_db->escape(Arr::getPost('FileManual')) . "'," : $Url;
                $Url = (Arr::getPost('deldl') == 1) ? "Download = ''," : $Url;
                $Bild = (Arr::getPost('NoImg') == 1 && empty($_POST['newImg_1'])) ? "Bild = ''," : $Bild;
                $glr = (isset($_POST['Galerien'])) ? implode(',', $_POST['Galerien']) : '';
                $db_extra = "
                {$Url}
                {$Bild}
                Galerien = '" . $this->_db->escape($glr) . "',
                CheatProdukt = '" . $this->_db->escape(Arr::getPost('CheatProdukt')) . "',
                Webseite = '" . $this->_db->escape(Arr::getPost('Webseite')) . "',
                Hersteller = '" . $this->_db->escape(Arr::getPost('Hersteller')) . "',
                CheatLinks = '" . $this->_db->escape(Arr::getPost('CheatLinks')) . "',
                DownloadLink = '" . $this->_db->escape(Arr::getPost('Url_Direct')) . "',
                Sprache ='" . $this->_db->escape(Arr::getPost('Sprache')) . "',
                DatumUpdate ='" . $this->_db->escape(Arr::getPost('DatumUpdate')) . "',
                Plattform ='" . $this->_db->escape(Arr::getPost('Plattform')) . "',
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

            $db_in = "UPDATE " . PREFIX . "_cheats SET
                    {$db_extra}
                    Name_{$LC} = '" . $this->_db->escape(Arr::getPost('Name')) . "',
                    Beschreibung_{$LC} = '" . $this->_db->escape($_POST['Beschreibung']) . "'
                    {$SetAll}
            WHERE Id='" . $id . "'";

            $this->_db->query($db_in);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал программу(' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT *, Beschreibung_{$LC} as Beschreibung, Name_{$LC} as Name FROM " . PREFIX . "_cheats WHERE Id='" . $id . "' LIMIT 1");
        $categs = array();
        $area = AREA;
        $res->Galerien = explode(',', $res->Galerien);
        $this->_view->assign('post_maxMb', $this->__object('AdminCore')->postMaxsizeMb());
        $this->_view->assign('wrietable_img', ((is_writable(UPLOADS_DIR . '/cheats/')) ? 1 : 0));
        $this->_view->assign('wrietable', ((is_writable(UPLOADS_DIR . '/cheats_files/')) ? 1 : 0));
        $this->_view->assign('res', $res);
        $this->_view->assign('manuf', $this->manufacturer());
        $this->_view->assign('products', $this->products());
        $this->_view->assign('Categs', $this->categs($categs, $area));
        $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', $res->Beschreibung, 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Gaming_cheats_edit']);
        $this->_view->content('/cheats/edit.tpl');
    }

    public function add() {
        if (!perm('cheats_new')) {
            SX::object('AdminCore')->noAccess();
        }
        $categs = array();
        $area = AREA;

        if (Arr::getPost('save') == 1) {
            $_POST['FileManual'] = trim(Arr::getPost('q'));
            $_POST['DatumUpdate'] = (!empty($_POST['DatumUpdate'])) ? $this->__object('AdminCore')->mktime($_POST['DatumUpdate']) : time();
            $Url = !empty($_POST['newImg_2']) ? $_POST['newImg_2'] : '';
            $Url = (!empty($_POST['FileManual']) && is_file(UPLOADS_DIR . '/cheats_files/' . $_POST['FileManual']) && $_POST['FileManual'] != '.htaccess' && $_POST['FileManual'] != 'index.php') ? $_POST['FileManual'] : $Url;
            $glr = isset($_POST['Galerien']) ? implode(',', $_POST['Galerien']) : '';
            $name = Arr::getPost('Name');

            $insert_array = array(
                'Plattform'      => intval(Arr::getPost('Plattform')),
                'Sprache'        => Arr::getPost('Sprache'),
                'Typ'            => 'cheat',
                'Benutzer'       => $_SESSION['benutzer_id'],
                'Hits'           => 0,
                'DatumUpdate'    => Arr::getPost('DatumUpdate'),
                'Name_1'         => $name,
                'Name_2'         => $name,
                'Name_3'         => $name,
                'Beschreibung_1' => $_POST['Beschreibung'],
                'Beschreibung_2' => $_POST['Beschreibung'],
                'Beschreibung_3' => $_POST['Beschreibung'],
                'Bild'           => Arr::getPost('newImg_1'),
                'Download'       => $Url,
                'DownloadHits'   => 0,
                'DownloadLink'   => Arr::getPost('Url_Direct'),
                'Hersteller'     => intval(Arr::getPost('Hersteller')),
                'Webseite'       => Arr::getPost('Webseite'),
                'Galerien'       => $glr,
                'CheatLinks'     => Arr::getPost('CheatLinks'),
                'CheatProdukt'   => intval(Arr::getPost('CheatProdukt')),
                'Sektion'        => AREA,
                'Aktiv'          => 1,
                'DDatum'         => 0);
            $this->_db->insert_query('cheats', $insert_array);
            $new_id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $url_ping = BASE_URL . '/index.php?p=cheats&action=showcheat&area=' . AREA . '&plattform=' . $_POST['Plattform'] . '&id=' . $new_id . '&name=' . translit($name);
            $options = array(
                'name' => $name,
                'url'  => $url_ping,
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $name,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую программу (' . $name . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $this->_view->assign('post_maxMb', $this->__object('AdminCore')->postMaxsizeMb());
        $this->_view->assign('wrietable_img', ((is_writable(UPLOADS_DIR . '/cheats/')) ? 1 : 0));
        $this->_view->assign('wrietable', ((is_writable(UPLOADS_DIR . '/cheats_files/')) ? 1 : 0));
        $this->_view->assign('manuf', $this->manufacturer());
        $this->_view->assign('products', $this->products());
        $this->_view->assign('Categs', $this->categs($categs, $area));
        $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Gaming_cheats_new']);
        $this->_view->content('/cheats/new.tpl');
    }

    public function copy($id) {
        if (!perm('cheats_new')) {
            SX::object('AdminCore')->noAccess();
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_cheats WHERE Id='" . intval($id) . "' LIMIT 1");
        if (is_object($res)) {
            $insert_array = array(
                'Plattform'      => $res->Plattform,
                'Sprache'        => $res->Sprache,
                'Typ'            => $res->Typ,
                'Benutzer'       => $_SESSION['benutzer_id'],
                'Hits'           => 0,
                'DatumUpdate'    => time(),
                'Name_1'         => $res->Name_1 . $this->_lang['DbCopy'],
                'Name_2'         => $res->Name_2 . $this->_lang['DbCopy'],
                'Name_3'         => $res->Name_3 . $this->_lang['DbCopy'],
                'Beschreibung_1' => $res->Beschreibung_1,
                'Beschreibung_2' => $res->Beschreibung_2,
                'Beschreibung_3' => $res->Beschreibung_3,
                'Bild'           => $res->Bild,
                'Download'       => $res->Download,
                'DownloadHits'   => $res->DownloadHits,
                'DownloadLink'   => $res->DownloadLink,
                'Hersteller'     => $res->Hersteller,
                'Webseite'       => $res->Webseite,
                'Galerien'       => $res->Galerien,
                'CheatLinks'     => $res->CheatLinks,
                'CheatProdukt'   => $res->CheatProdukt,
                'Sektion'        => $res->Sektion,
                'Aktiv'          => 0,
                'DDatum'         => 0);
            $this->_db->insert_query('cheats', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал программу (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->backurl();
        }
    }

    public function show() {
        if (!perm('cheats')) {
            SX::object('AdminCore')->noAccess();
        }
        $db_sort = " ORDER BY Name ASC";
        $nav_sort = "&amp;sort=name_asc";
        $datesort = $def_search_n = $def_search = $namesort = $hitssort = $usersort = '';
        $categsort = $activesort = $scq = $sc = $pattern = $pattern2 = $brokenq = $brokenq_n = '';

        if (Arr::getPost('save') == 1 && isset($_POST['Aktiv']) && perm('cheats_edit')) {
            foreach (array_keys($_POST['Aktiv']) as $lid) {
                $lid = intval($lid);
                $array = array(
                    'Plattform' => $_POST['Plattform'][$lid],
                    'Aktiv'     => $_POST['Aktiv'][$lid],
                    'Hits'      => $_POST['Hits'][$lid],
                );
                $this->_db->update_query('cheats', $array, "Id = '" . $lid . "'");
                if (isset($_POST['del'][$lid]) && $_POST['del'][$lid] == 1 && perm('cheats_delete')) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_cheats WHERE Id='" . $lid . "'");
                    $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='cheats' AND Objekt_Id='" . $lid . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_REQUEST['categ'])) {
            $scq = "AND (Plattform='" . intval(Arr::getRequest('categ')) . "') ";
            $sc = "&amp;categ=" . Arr::getRequest('categ');
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
                $db_sort = 'ORDER BY DatumUpdate DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY DatumUpdate ASC';
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
                $db_sort = 'ORDER BY Benutzer DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'hits_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY Benutzer ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'hits_desc';
                break;
            case 'categ_desc':
                $db_sort = 'ORDER BY Plattform DESC';
                $nav_sort = '&amp;sort=categ_desc';
                $categsort = 'categ_asc';
                break;
            case 'categ_asc':
                $db_sort = 'ORDER BY Plattform ASC';
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
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
                *,
                Plattform,
                Name_1 AS Name,
                Beschreibung_1 AS Beschreibung
        FROM
                " . PREFIX . "_cheats
        WHERE
                Sektion = '" . AREA . "'
                {$def_search} {$db_sort} LIMIT $a, $limit");

        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $entries = array();
        while ($row_items = $query_items->fetch_object()) {
            $row_items->User = Tool::userName($row_items->Benutzer);
            $row_items->Comments = $this->__object('AdminCore')->countComments('cheats', $row_items->Id);
            $row_items->CCount = Tool::countComments($row_items->Id, 'cheats');
            $row_items->Wertung = Tool::rating($row_items->Id, 'cheats');
            $entries[] = $row_items;
        }
        $query_items->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=cheats&sub=show&categ=" . Arr::getRequest('categ') . "{$def_search_n}{$nav_sort}{$brokenq_n}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }

        $news_categ = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->categs($news_categ, $area));
        $this->_view->assign('activesort', $activesort);
        $this->_view->assign('categsort', $categsort);
        $this->_view->assign('usersort', $usersort);
        $this->_view->assign('hitssort', $hitssort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('Entries', $entries);
        $this->_view->assign('title', $this->_lang['Gaming_cheats']);
        $this->_view->content('/cheats/show.tpl');
    }

    protected function categs(&$news_categ, &$area) {
        $query = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Sektion = '" . intval($area) . "' ORDER BY Name ASC");
        while ($item = $query->fetch_object()) {
            $news_categ[] = $item;
        }
        $query->close();
        return $news_categ;
    }

    protected function manufacturer() {
        $query = $this->_db->fetch_object_all("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Sektion = '" . AREA . "' ORDER BY Name ASC");
        return $query;
    }

    protected function products() {
        $query = $this->_db->fetch_object_all("SELECT Id, Name1 AS Name FROM " . PREFIX . "_produkte WHERE Sektion = '" . AREA . "' ORDER BY Name1 ASC");
        return $query;
    }

}
