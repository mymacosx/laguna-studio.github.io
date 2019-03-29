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

class AdminDownloads extends Magic {

    public function delRating($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='downloads' AND Objekt_Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    protected function os() {
        return array(
            'Windows 98',
            'Windows 2000',
            'Windows XP',
            'Windows XP (32 bit)',
            'Windows XP (64 bit)',
            'Windows Vista',
            'Windows Vista (32 bit)',
            'Windows Vista (64 bit)',
            'Windows 7',
            'Windows 7 (32 bit)',
            'Windows 7 (64 bit)',
            'windows 8',
            'windows 8 (32 bit)',
            'windows 8 (64 bit)',
            'windows 10',
            'windows 10 (32 bit)',
            'windows 10 (64 bit)',
            'Windows CE',
            'Windows CE/PocketPC',
            'Windows Mobile',
            'Windows Server 2003',
            'Windows Server 2008',
            'MAC OS',
            'Mac OS X',
            'Unix',
            'Linux',
            'SunOS',
            'EPOC',
            'Palm OS',
            'Symbian OS',
            'OS X');
    }

    public function settings() {
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Kommentare'   => Arr::getPost('Kommentare'),
                'Wertung'      => Arr::getPost('Wertung'),
                'LinkMelden'   => 1,
                'DefektMelden' => Arr::getPost('DefektMelden'),
                'Flaggen'      => Arr::getPost('Flaggen'),
                'PageLimit'    => Arr::getPost('PageLimit'));
            SX::save('downloads', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('downloads');
        }
        $res = SX::get('downloads');
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' ' . $this->_lang['Downloads']);
        $this->_view->content('/downloads/settings.tpl');
    }

    public function show() {
        $db_sort = " ORDER BY Datum ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $def_search_n = $def_search = $namesort = $hitssort = $usersort = $categsort = '';
        $activesort = $scq = $sc = $pattern = $pattern2 = $brokenq = $brokenq_n = $toplinksort = '';

        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $lid) {
                $array = array(
                    'Kategorie' => $_POST['Kategorie'][$lid],
                    'Aktiv'     => $_POST['Aktiv'][$lid],
                    'Hits'      => $_POST['Hits'][$lid],
                    'Sponsor'   => $_POST['Sponsor'][$lid],
                );
                $this->_db->update_query('downloads', $array, "Id = '" . intval($lid) . "'");
                if (isset($_POST['del'][$lid]) && $_POST['del'][$lid] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_downloads WHERE Id='" . intval($lid) . "'");
                    $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='downloads' AND Objekt_Id='" . intval($lid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_REQUEST['categ'])) {
            $scq = "AND (Kategorie='" . intval(Arr::getRequest('categ')) . "') ";
            $sc = "&amp;categ=" . Arr::getRequest('categ');
        }

        if (Arr::getRequest('broken') == 1) {
            $brokenq = "AND (DefektGemeldet!='') ";
            $brokenq_n = '&amp;broken=1';
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ');
            $pattern2 = sanitize($pattern);
            $def_search_n = '&amp;q=' . urlencode($pattern) . $sc;
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
        $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Kategorie, Name_1 AS Name, Beschreibung_1 AS Beschreibung FROM " . PREFIX . "_downloads WHERE Sektion = '" . AREA . "' {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $entries = array();
        while ($row_items = $query_items->fetch_object()) {
            $row_items->User = Tool::userName($row_items->Autor);
            $row_items->Comments = $this->__object('AdminCore')->countComments('downloads', $row_items->Id);
            $row_items->CCount = Tool::countComments($row_items->Id, 'downloads');
            $row_items->Wertung = Tool::rating($row_items->Id, 'downloads');
            $entries[] = $row_items;
        }
        $query_items->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=downloads&sub=overview&categ=" . Arr::getRequest('categ') . "{$def_search_n}{$nav_sort}{$brokenq_n}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
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
        $this->_view->assign('title', $this->_lang['Downloads']);
        $this->_view->content('/downloads/downloads.tpl');
    }

    public function copy($id) {
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_downloads WHERE Id='" . intval($id) . "' LIMIT 1");

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
            'Url_Direct'     => $res->Url_Direct,
            'Size_Direct'    => $res->Size_Direct,
            'Mirrors'        => $res->Mirrors,
            'Hits'           => 0,
            'Sektion'        => $res->Sektion,
            'Autor'          => $_SESSION['benutzer_id'],
            'DDatum'         => 0,
            'Aktiv'          => $res->Aktiv,
            'Sponsor'        => $res->Sponsor,
            'BetriebsOs'     => $res->BetriebsOs,
            'SoftwareTyp'    => $res->SoftwareTyp,
            'Version'        => $res->Version);
        $this->_db->insert_query('downloads', $insert_array);
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал закачку (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function add() {
        if (Arr::getPost('save') == 1) {
            $name = Arr::getPost('Name');
            $_POST['FileManual'] = trim(Arr::getPost('q'));
            $_POST['Datum'] = !empty($_POST['Datum']) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $Url = !empty($_POST['newImg_2']) ? $_POST['newImg_2'] : '';
            $Url = (!empty($_POST['FileManual']) && $_POST['FileManual'] != '.htaccess' && $_POST['FileManual'] != 'index.php' && is_file(UPLOADS_DIR . '/downloads_files/' . $_POST['FileManual'])) ? $_POST['FileManual'] : $Url;
            $bos = isset($_POST['BetriebsOs']) ? implode('|', $_POST['BetriebsOs']) : '';

            $insert_array = array(
                'Kategorie'      => Arr::getPost('Kategorie'),
                'Datum'          => Arr::getPost('Datum'),
                'Bild'           => Arr::getPost('newImg_1'),
                'Name_1'         => $name,
                'Name_2'         => $name,
                'Name_3'         => $name,
                'Beschreibung_1' => $_POST['Beschreibung'],
                'Beschreibung_2' => $_POST['Beschreibung'],
                'Beschreibung_3' => $_POST['Beschreibung'],
                'Url'            => $Url,
                'Url_Direct'     => Arr::getPost('Url_Direct'),
                'Size_Direct'    => (!empty($_POST['Size_Direct']) ? $_POST['Size_Direct'] : '0.00'),
                'Mirrors'        => Arr::getPost('Mirrors'),
                'Hits'           => 0,
                'Sektion'        => AREA,
                'Autor'          => $_SESSION['benutzer_id'],
                'DDatum'         => 0,
                'Aktiv'          => Arr::getPost('Aktiv'),
                'Sprache'        => Arr::getPost('Sprache'),
                'BetriebsOs'     => $bos,
                'SoftwareTyp'    => Arr::getPost('SoftwareTyp'),
                'Version'        => Arr::getPost('Version'));
            $this->_db->insert_query('downloads', $insert_array);
            $new_id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $url_ping = BASE_URL . '/index.php?p=downloads&action=showdetails&area=' . AREA . '&categ=' . $_POST['Kategorie'] . '&id=' . $new_id . '&name=' . translit($name);
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

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую закачку (' . $name . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $categs = array();
        $area = AREA;
        $this->_view->assign('BetriebsOs', $this->os());
        $this->_view->assign('post_maxMb', $this->__object('AdminCore')->postMaxsizeMb());
        $this->_view->assign('wrietable_img', ((is_writable(UPLOADS_DIR . '/downloads/')) ? 1 : 0));
        $this->_view->assign('wrietable', ((is_writable(UPLOADS_DIR . '/downloads_files/')) ? 1 : 0));
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', ' ', 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Download_add']);
        $this->_view->content('/downloads/download_new.tpl');
    }

    public function search($q) {
        $value = NULL;
        if (perm('forum_attachments')) {
            $q = urldecode($q);
            if (!empty($q) && $this->_text->strlen($q) >= 2) {
                $d = UPLOADS_DIR . '/downloads_files/';
                $handle = opendir($d);
                while (false !== ($file = readdir($handle))) {
                    if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_file($d . $file)) {
                        if ($this->_text->stripos($file, $q) !== false) {
                            $value .= $file . PE;
                        }
                    }
                }
                closedir($handle);
            }
        }
        SX::output($value);
    }

    public function edit($id) {
        $id = intval($id);
        $LC = $this->__object('AdminCore')->getLangcode();
        $SetAll = '';

        if (Arr::getPost('save') == 1) {
            $_POST['Datum'] = !empty($_POST['Datum']) ? $this->__object('AdminCore')->mktime($_POST['Datum']) : time();
            $db_extra = '';

            if ($LC == 1) {
                $Bild = '';
                if (!empty($_POST['newImg_1'])) {
                    $Bild = "Bild='" . $this->_db->escape(Arr::getPost('newImg_1')) . "',";
                }
                $_POST['FileManual'] = trim(Arr::getPost('q'));
                $Url = !empty($_POST['newImg_2']) ? "Url = '" . $this->_db->escape(Arr::getPost('newImg_2')) . "'," : '';
                $Url = (!empty($_POST['FileManual']) && is_file(UPLOADS_DIR . '/downloads_files/' . $_POST['FileManual']) && $_POST['FileManual'] != '.htaccess' && $_POST['FileManual'] != 'index.php') ? "Url = '" . $this->_db->escape(Arr::getPost('FileManual')) . "'," : $Url;
                $Dl = Arr::getPost('DelDM') == 1 ? "DefektGemeldet='',DEmail='',DName='',DDatum=''," : '';
                $Bild = Arr::getPost('NoImg') == 1 && empty($_POST['newImg_1']) ? "Bild = ''," : $Bild;
                $bos = isset($_POST['BetriebsOs']) ? implode('|', $_POST['BetriebsOs']) : '';
                $db_extra = "
                {$Url}
                {$Bild}
                Mirrors = '" . $this->_db->escape(Arr::getPost('Mirrors')) . "',
                BetriebsOs = '" . $this->_db->escape(Arr::getPost('BetriebsOs')) . "',
                SoftwareTyp = '" . $this->_db->escape(Arr::getPost('SoftwareTyp')) . "',
                Version = '" . $this->_db->escape(Arr::getPost('Version')) . "',
                Url_Direct = '" . $this->_db->escape(Arr::getPost('Url_Direct')) . "',
                Size_Direct = '" . $this->_db->escape(Arr::getPost('Size_Direct')) . "',
                Sprache ='" . $this->_db->escape(Arr::getPost('Sprache')) . "',
                Datum ='" . $this->_db->escape(Arr::getPost('Datum')) . "',
                Kategorie ='" . $this->_db->escape(Arr::getPost('Kategorie')) . "',
                BetriebsOs = '" . $this->_db->escape($bos) . "',
                Aktiv='" . $this->_db->escape(Arr::getPost('Aktiv')) . "', {$Dl} ";
            }

            if (Arr::getPost('saveAllLang') == 1) {
                $SetAll = "
                ,Name_2 = '" . $this->_db->escape($_POST['Name']) . "'
                ,Beschreibung_2 = '" . $this->_db->escape($_POST['Beschreibung']) . "'
                ,Name_3 = '" . $this->_db->escape($_POST['Name']) . "'
                ,Beschreibung_3 = '" . $this->_db->escape($_POST['Beschreibung']) . "'
                ";
            }

            $db_in = "UPDATE " . PREFIX . "_downloads SET
                {$db_extra}
                Name_{$LC} = '" . $this->_db->escape(Arr::getPost('Name')) . "',
                Beschreibung_{$LC} = '" . $this->_db->escape($_POST['Beschreibung']) . "'
                {$SetAll}
            WHERE Id='" . $id . "'";

            $this->_db->query($db_in);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал закачку (' . Arr::getPost('Name') . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT *, Beschreibung_{$LC} as Beschreibung, Name_{$LC} as Name FROM " . PREFIX . "_downloads WHERE Id='" . $id . "' LIMIT 1");
        $categs = array();
        $area = AREA;
        $this->_view->assign('BetriebsOs', $this->os());
        $this->_view->assign('BetriebsOsIn', explode('|', $res->BetriebsOs));
        $this->_view->assign('SoftwareTypIn', explode('|', $res->SoftwareTyp));
        $this->_view->assign('post_maxMb', $this->__object('AdminCore')->postMaxsizeMb());
        $this->_view->assign('wrietable_img', ((is_writable(UPLOADS_DIR . '/downloads/')) ? 1 : 0));
        $this->_view->assign('wrietable', ((is_writable(UPLOADS_DIR . '/downloads_files/')) ? 1 : 0));
        $this->_view->assign('res', $res);
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('Beschreibung', $this->__object('Editor')->load('admin', $res->Beschreibung, 'Beschreibung', 350, 'Content'));
        $this->_view->assign('title', $this->_lang['Downloads_edit']);
        $this->_view->content('/downloads/download_edit.tpl');
    }

    public function delCateg($id) {
        if (perm('articles_category')) {
            $res = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_downloads_kategorie WHERE Parent_Id='" . intval($id) . "'");
            $this->remove($id);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию закачек (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    protected function remove($id) {
        $id = intval($id);
        $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_downloads_kategorie WHERE Parent_Id='" . $id . "'");
        while ($item = $query->fetch_object()) {
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_downloads WHERE Kategorie='" . $id . "'");
            while ($row = $sql->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='downloads' AND Objekt_Id='" . $row->Id . "'");
            }
            $this->_db->query("DELETE FROM " . PREFIX . "_downloads WHERE Kategorie='" . $id . "'");
            $this->remove($item->Id);
        }
        $query->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_downloads_kategorie WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_downloads WHERE Kategorie='" . $id . "'");
    }

    public function showCategs() {
        $categs = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('title', $this->_lang['Download_categs']);
        $this->_view->content('/downloads/download_categs.tpl');
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
            $this->_db->update_query('downloads_kategorie', $array, "Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал категорию закачек (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $categs = array();
        $area = AREA;
        $res = $this->_db->cache_fetch_object("SELECT *  FROM " . PREFIX . "_downloads_kategorie WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('title', $this->_lang['Download_categs']);
        $this->_view->content('/downloads/download_categ.tpl');
    }

    public function addCateg() {
        if (Arr::getPost('save') == 1) {
            $Name_1 = Arr::getPost('Name_1');
            $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
            $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;
            $Beschreibung_1 = Arr::getPost('Beschreibung_1');
            $Beschreibung_2 = !empty($_POST['Beschreibung_2']) ? $_POST['Beschreibung_2'] : $Beschreibung_1;
            $Beschreibung_3 = !empty($_POST['Beschreibung_3']) ? $_POST['Beschreibung_3'] : $Beschreibung_1;

            $insert_array = array(
                'Parent_Id'      => intval(Arr::getPost('categ')),
                'Name_1'         => $Name_1,
                'Name_2'         => $Name_2,
                'Name_3'         => $Name_3,
                'Beschreibung_1' => $Beschreibung_1,
                'Beschreibung_2' => $Beschreibung_2,
                'Beschreibung_3' => $Beschreibung_3,
                'Sektion'        => $_SESSION['a_area']);
            $this->_db->insert_query('downloads_kategorie', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую категорию закачек (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $categs = array();
        $area = AREA;
        $this->_view->assign('Categs', $this->simpleCategs('', '', $categs, $area));
        $this->_view->assign('new', 1);
        $this->_view->assign('title', $this->_lang['Download_categs']);
        $this->_view->content('/downloads/download_categ.tpl');
    }

    protected function simpleCategs($id, $prefix, &$news_categ, &$area) {
        $query = $this->_db->query("SELECT Id, Parent_Id, Name_1 AS Name FROM " . PREFIX . "_downloads_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY Name ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . '  ' . $item->Name;
            $news_categ[] = $item;
            $this->simpleCategs($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

}
