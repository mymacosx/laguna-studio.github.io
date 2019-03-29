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

class AdminNews extends Magic {

    public function settings() {
        if (!perm('news')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'compres'  => Arr::getPost('compres'),
                'size'     => Arr::getPost('size'),
            );
            SX::save('news', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки новостей', '0', $this->UserId);
            $this->__object('AdminCore')->script('save');
            SX::load('news');
        }
        $row = SX::get('news');
        $this->_view->assign('row', $row);
        $this->_view->assign('title', $this->_lang['SettingsModule']);
        $this->_view->content('/news/settings.tpl');
    }

    public function delRating($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='news' AND Objekt_Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function delCateg($id) {
        if (perm('news_category')) {
            $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_news_kategorie WHERE Id='" . intval($id) . "' LIMIT 1");
            $this->remove($id);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию новостей (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    public function editCateg($id) {
        if (!perm('news_category')) {
            $this->__object('AdminCore')->noAccess();
        }
            $id = intval($id);
            if (Arr::getPost('save') == 1) {
                $Name_1 = $_POST['Name_1'];
                $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
                $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;
                $array = array(
                    'Name_1' => $Name_1,
                    'Name_2' => $Name_2,
                    'Name_3' => $Name_3,
                );
                $this->_db->update_query('news_kategorie', $array, "Id='" . $id . "'");
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал категорию новостей (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }
            $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_news_kategorie WHERE Id='" . $id . "' LIMIT 1");
            $this->_view->assign('res', $res);
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['Global_CategEdit']);
            $this->_view->content('/news/news_categ.tpl');
    }

    public function addCateg() {
        if (!perm('news_category')) {
            $this->__object('AdminCore')->noAccess();
        }
            if (Arr::getPost('save') == 1) {
                $Name_1 = Arr::getPost('Name_1');
                $Name_2 = !empty($_POST['Name_2']) ? $_POST['Name_2'] : $Name_1;
                $Name_3 = !empty($_POST['Name_3']) ? $_POST['Name_3'] : $Name_1;

                $insert_array = array(
                    'Parent_Id' => intval(Arr::getPost('categ')),
                    'Name_1'    => $Name_1,
                    'Name_2'    => $Name_2,
                    'Name_3'    => $Name_3,
                    'Sektion'   => $_SESSION['a_area']);
                $this->_db->insert_query('news_kategorie', $insert_array);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил категорию новостей (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('close');
            }
            $this->_view->assign('new', 1);
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['Global_NewCateg']);
            $this->_view->content('/news/news_categ.tpl');
    }

    public function showCateg() {
        if (!perm('news_category')) {
            $this->__object('AdminCore')->noAccess();
        }
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['News_Categs']);
            $this->_view->content('/news/categs.tpl');
    }

    public function add() {
        if (!perm('news_new')) {
            $this->__object('AdminCore')->noAccess();
        }
            if (Arr::getPost('save') == 1) {
                $end = '0';
                $start = explode('.', $_POST['ZeitStart']);
                $start = mktime(0, 0, 1, $start[1], $start[0], $start[2]);

                if (Arr::getPost('ZeitEnde') > 1) {
                    $end = explode('.', $_POST['ZeitEnde']);
                    $end = mktime(23, 59, 59, $end[1], $end[0], $end[2]);
                }

                $glr = !empty($_POST['Gallery']) ? implode(',', $_POST['Gallery']) : '';
                $titel = Arr::getPost('Titel');
                $intro = Arr::getPost('Intro');
                $news = Arr::getPost('News');

                $insert_array = array(
                    'Kategorie'       => intval(Arr::getPost('categ')),
                    'Autor'           => $_SESSION['benutzer_id'],
                    'Sektion'         => $_SESSION['a_area'],
                    'Zeit'            => time(),
                    'ZeitStart'       => $start,
                    'ZeitEnde'        => $end,
                    'Titel1'          => $titel,
                    'Titel2'          => $titel,
                    'Titel3'          => $titel,
                    'Intro1'          => $intro,
                    'Intro2'          => $intro,
                    'Intro3'          => $intro,
                    'News1'           => $news,
                    'News2'           => $news,
                    'News3'           => $news,
                    'Bild1'           => Arr::getPost('Bild_1'),
                    'Bild2'           => Arr::getPost('Bild_1'),
                    'Bild3'           => Arr::getPost('Bild_1'),
                    'BildAusrichtung' => Arr::getPost('BildAusrichtung'),
                    'Textbilder1'     => base64_decode(Arr::getPost('screenshots')),
                    'AlleSektionen'   => Arr::getPost('AlleSektionen'),
                    'Aktiv'           => Arr::getPost('Aktiv'),
                    'Suche'           => Arr::getPost('Suche'),
                    'Bewertung'       => Arr::getPost('Bewertung'),
                    'Kommentare'      => Arr::getPost('Kommentare'),
                    'Tags'            => Arr::getPost('Tags'),
                    'Galerien'        => $glr,
                    'Topnews'         => Arr::getPost('Topnews'),
                    'Topnews_Bild_1'  => Arr::getPost('Bild_2'),
                    'Topnews_Bild_2'  => Arr::getPost('Bild_2'),
                    'Topnews_Bild_3'  => Arr::getPost('Bild_2'));
                $this->_db->insert_query('news', $insert_array);
                $new_id = $this->_db->insert_id();

                // Добавляем задание на пинг
                $options = array(
                    'name' => $titel,
                    'url'  => BASE_URL . '/index.php?p=news&area=' . AREA . '&newsid=' . $new_id . '&name=' . translit($titel),
                    'lang' => $_SESSION['admin_lang']);

                $cron_array = array(
                    'datum'   => time(),
                    'type'    => 'sys',
                    'modul'   => 'ping',
                    'title'   => $titel,
                    'options' => serialize($options),
                    'aktiv'   => 1);
                $this->__object('Cron')->add($cron_array);

                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новость (' . $titel . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('close');
            }

        //    $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
            $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
            $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
            $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
            $this->_view->assign('Gallery', $this->categsGalery($_SESSION['a_area']));
            $this->_view->assign('Intro', $this->__object('Editor')->load('admin', ' ', 'Intro', 200, 'Basic'));
            $this->_view->assign('News', $this->__object('Editor')->load('admin', ' ', 'News', 450, 'Content'));
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['News_new']);
            $this->_view->content('/news/news_new.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor FROM " . PREFIX . "_news WHERE Id='" . $id . "' LIMIT 1");
        if (($check->Autor == $_SESSION['benutzer_id'] && perm('news_edit')) || perm('news_edit_all')) {
            $LC = $this->__object('AdminCore')->getLangcode();
            $isdb = $start = $end = $categ = $Bild = $TopnewsBild = $Gallery = $Sonstiges = $SetAll = '';

            if (Arr::getPost('save') == 1) {
                if (perm('screenshots') && isset($_POST['screenshots'])) {
                    $isdb = ",Textbilder{$LC} = '" . $this->_db->escape(base64_decode($_POST['screenshots'])) . "' ";
                }

                $Bild = (Arr::getPost('NoImg') == 1) ? ",Bild{$LC} = ''" : (!empty($_POST['Bild_1']) ? ",Bild{$LC} = '" . $this->_db->escape($_POST['Bild_1']) . "'" : '');
                $TopnewsBild = (Arr::getPost('NoTopnewsImg') == 1) ? ",Topnews_Bild_{$LC} = ''" : (!empty($_POST['Bild_2']) ? ",Topnews_Bild_{$LC} = '" . $this->_db->escape($_POST['Bild_2']) . "'" : '');

                if (Arr::getPost('langcode') == 1) {
                    $categ = ",Kategorie='" . intval(Arr::getPost('categ')) . "'";
                    $Gallery = (isset($_POST['Gallery'])) ? ",Galerien='" . $this->_db->escape(implode(',', $_POST['Gallery'])) . "'" : '';

                    if (!empty($_POST['ZeitStart'])) {
                        $start = explode('.', $_POST['ZeitStart']);
                        $start = mktime(0, 0, 1, $start[1], $start[0], $start[2]);
                        $start = ",ZeitStart='" . $start . "'";
                    }

                    if (Arr::getPost('saveAllLang') == 1) {
                        $SetAll = "
                            ,Intro2 = '" . $this->_db->escape($_POST['Intro']) . "'
                            ,News2 = '" . $this->_db->escape($_POST['News']) . "'
                            ,Intro3 = '" . $this->_db->escape($_POST['Intro']) . "'
                            ,News3 = '" . $this->_db->escape($_POST['News']) . "'
                            ";
                    }

                    $Sonstiges = "
                            {$SetAll}
                            ,Tags = '" . $this->_db->escape(trim(Arr::getPost('Tags'))) . "'
                            ,BildAusrichtung = '" . $this->_db->escape(Arr::getPost('BildAusrichtung')) . "'
                            ,Suche = '" . $this->_db->escape(Arr::getPost('Suche')) . "'
                            ,Bewertung = '" . $this->_db->escape(Arr::getPost('Bewertung')) . "'
                            ,Kommentare = '" . $this->_db->escape(Arr::getPost('Kommentare')) . "'
                            ,Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "'
                            ,AlleSektionen = '" . $this->_db->escape(Arr::getPost('AlleSektionen')) . "'
                            ";
                    if (!empty($_POST['ZeitEnde'])) {
                        $end = explode('.', $_POST['ZeitEnde']);
                        $end = mktime(23, 59, 59, $end[1], $end[0], $end[2]);
                        $end = ",ZeitEnde='" . $end . "'";
                    } else {
                        $end = ",ZeitEnde='0'";
                    }
                }

                $this->_db->query("UPDATE " . PREFIX . "_news SET
                            Topnews = '" . $this->_db->escape(Arr::getPost('Topnews')) . "',
                            Titel{$LC} = '" . $this->_db->escape(Arr::getPost('Titel')) . "',
                            Topnews = '" . $this->_db->escape(Arr::getPost('Topnews')) . "',
                            Intro{$LC} = '" . $this->_db->escape($_POST['Intro']) . "',
                            News{$LC} = '" . $this->_db->escape($_POST['News']) . "'
                            {$categ}
                            {$end}
                            {$Bild}
                            {$TopnewsBild}
                            {$Sonstiges}
                            {$start}
                            {$Gallery}
                            {$isdb}
                    WHERE Id='" . $id . "'");
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал новость (' . $_POST['Titel'] . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }

            $LC = $this->__object('AdminCore')->getLangcode();
            $news = $this->_db->cache_fetch_object("SELECT
                        *,
                        Bild{$LC} as Bild,
                        Textbilder{$LC} as Textbilder,
                        Titel{$LC} as Titel,
                        Topnews_Bild_{$LC} as Topnews_Bild,
                        Intro{$LC} as Intro,
                        News{$LC} as News
                FROM " . PREFIX . "_news WHERE Id='" . $id . "' LIMIT 1");

            $news->Galerien = explode(',', $news->Galerien);
            $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
            $this->_view->assign('Gallery', $this->categsGalery($_SESSION['a_area']));
            $this->_view->assign('Intro', $this->__object('Editor')->load('admin', $news->Intro, 'Intro', 200, 'Basic'));
            $this->_view->assign('News', $this->__object('Editor')->load('admin', $news->News, 'News', 450, 'Content'));
            $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
            $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
            $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
            $this->_view->assign('InlineShots', unserialize($news->Textbilder));
            $this->_view->assign('news', $news);
            $this->_view->assign('field_inline', "Textbilder{$LC}");
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['News_edit']);
            $this->_view->content('/news/news_edit.tpl');
        } else {
            $this->__object('AdminCore')->noAccess();
        }
    }

    protected function categsGalery($area) {
        $categs = array();
        $query = $this->_db->query("SELECT Id,Name_1 AS CategName FROM " . PREFIX . "_galerie_kategorien WHERE Sektion = '" . intval($area) . "' ORDER BY Name_1 ASC ");
        while ($row = $query->fetch_object()) {
            $row->Gals = $this->_db->fetch_object_all("SELECT Id AS GalId,Name_1 AS GalName FROM " . PREFIX . "_galerie WHERE Kategorie = '$row->Id' ORDER BY Name_1 ASC ");
            $categs[] = $row;
        }
        $query->close();
        return $categs;
    }

    public function delete($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor, Titel1 FROM " . PREFIX . "_news WHERE Id='" . $id . "' LIMIT 1");
        if (($check->Autor == $_SESSION['benutzer_id'] && perm('news_delete')) || perm('news_delete_all')) {
            $this->_db->query("DELETE FROM " . PREFIX . "_news WHERE Id='" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='news' AND Objekt_Id='" . $id . "'");
        }
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил новость (' . $check->Titel1 . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function show() {
        $def_order = 'ORDER BY Id DESC';
        $def_order_n = $def_search = $def_status = $def_status_n = $def_categ = $def_categ_n = '';
        $def_search_n = $def_datetill = $def_datetill_n = $def_hits = $def_hits_n = '';
        $area = $_SESSION['a_area'];
        $limit = $this->__object('AdminCore')->limit();

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['nid'] as $nid) {
                $this->_db->query("UPDATE " . PREFIX . "_news SET Topnews = '" . $this->_db->escape($_POST['Topnews'][$nid]) . "', Kategorie='" . intval($_POST['Kategorie'][$nid]) . "' WHERE Id='" . intval($nid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_POST['multi']) && isset($_SESSION['NewsSearch']) && perm('news_multioptions')) {
            switch ($_POST['multiaction']) {
                case 'movecateg':
                    $this->_db->query("UPDATE " . PREFIX . "_news SET Kategorie='" . intval(Arr::getPost('newcateg')) . "' " . $_SESSION['NewsSearch'] . " AND Sektion='{$area}'");
                    break;

                case 'open':
                    $this->_db->query("UPDATE " . PREFIX . "_news SET Aktiv='1' " . $_SESSION['NewsSearch'] . " AND Sektion='{$area}'");
                    break;

                case 'close':
                    $this->_db->query("UPDATE " . PREFIX . "_news SET Aktiv='0' " . $_SESSION['NewsSearch'] . " AND Sektion='{$area}'");
                    break;

                case 'delete':
                    $this->_db->query("DELETE FROM " . PREFIX . "_news " . $_SESSION['NewsSearch'] . " AND Sektion='{$area}'");
                    break;
            }

            $this->_view->assign('multi_done', 1);
            $this->__object('AdminCore')->script('save');
        } else {
            unset($_SESSION['NewsSearch']);
        }

        if (!empty($_REQUEST['categ'])) {
            $def_categ = "AND Kategorie = '" . intval($_REQUEST['categ']) . "'";
            $def_categ_n = "&amp;categ=" . intval($_REQUEST['categ']);
        }

        if (Arr::getRequest('hits_from') < Arr::getRequest('hits_to')) {
            $def_hits = "AND (Hits BETWEEN '" . intval($_REQUEST['hits_from']) . "' AND '" . intval($_REQUEST['hits_to']) . "')";
            $def_hits_n = "&amp;hits_from=" . intval($_REQUEST['hits_from']) . "&amp;hits_to=" . intval($_REQUEST['hits_to']);
        }

        if (!empty($_REQUEST['aktiv'])) {
            $def_status = "AND Aktiv = '" . intval($_REQUEST['aktiv']) . "'";
            $def_status_n = "&amp;aktiv=" . intval($_REQUEST['aktiv']);
        }

        if (!empty($_REQUEST['date_till'])) {
            $lafrom = $this->__object('AdminCore')->mktime($_REQUEST['date_till'], 23, 59, 59);
            if ($lafrom) {
                $def_datetill = " AND (Zeit <= $lafrom) ";
                $def_datetill_n = "&amp;date_till=" . Arr::getRequest('date_till');
            }
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.@ ');
            $def_search_n = '&amp;q=' . $pattern;
            $pattern = $this->_db->escape($pattern);
            $def_search = " AND ( Titel1 LIKE '%$pattern%' OR Titel2 LIKE '%$pattern%' OR Titel3 LIKE '%$pattern%' )";
        }

        if (isset($_REQUEST['sort'])) {
            $curr_page = '&amp;page=' . Arr::getRequest('page', 1);

            switch ($_REQUEST['sort']) {
                case 'name_asc':
                    $def_order = ' ORDER BY Titel1 ASC';
                    $def_order_n = '&sort=name_asc' . $curr_page;
                    $def_order_ns = '&sort=name_desc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'name_desc':
                    $def_order = ' ORDER BY Titel1 DESC';
                    $def_order_n = '&sort=name_desc' . $curr_page;
                    $def_order_ns = '&sort=name_asc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'username_asc':
                    $def_order = ' ORDER BY Autor ASC';
                    $def_order_n = '&sort=username_asc' . $curr_page;
                    $def_order_ns = '&sort=username_desc' . $curr_page;
                    $this->_view->assign('username_s', $def_order_ns);
                    break;

                case 'username_desc':
                    $def_order = ' ORDER BY Autor DESC';
                    $def_order_n = '&sort=username_desc' . $curr_page;
                    $def_order_ns = '&sort=username_asc' . $curr_page;
                    $this->_view->assign('username_s', $def_order_ns);
                    break;

                case 'date_asc':
                    $def_order = ' ORDER BY Zeit ASC';
                    $def_order_n = '&sort=date_asc' . $curr_page;
                    $def_order_ns = '&sort=date_desc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'date_desc':
                    $def_order = ' ORDER BY Zeit DESC';
                    $def_order_n = '&sort=date_desc' . $curr_page;
                    $def_order_ns = '&sort=date_asc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'start_asc':
                    $def_order = ' ORDER BY ZeitStart ASC';
                    $def_order_n = '&sort=start_asc' . $curr_page;
                    $def_order_ns = '&sort=start_desc' . $curr_page;
                    $this->_view->assign('start_s', $def_order_ns);
                    break;

                case 'start_desc':
                    $def_order = ' ORDER BY ZeitStart DESC';
                    $def_order_n = '&sort=start_desc' . $curr_page;
                    $def_order_ns = '&sort=start_asc' . $curr_page;
                    $this->_view->assign('start_s', $def_order_ns);
                    break;

                case 'end_asc':
                    $def_order = ' ORDER BY ZeitEnde ASC';
                    $def_order_n = '&sort=end_asc' . $curr_page;
                    $def_order_ns = '&sort=end_desc' . $curr_page;
                    $this->_view->assign('end_s', $def_order_ns);
                    break;

                case 'end_desc':
                    $def_order = ' ORDER BY ZeitEnde DESC';
                    $def_order_n = '&sort=end_desc' . $curr_page;
                    $def_order_ns = '&sort=end_asc' . $curr_page;
                    $this->_view->assign('end_s', $def_order_ns);
                    break;

                case 'hits_asc':
                    $def_order = ' ORDER BY Hits ASC';
                    $def_order_n = '&sort=hits_asc' . $curr_page;
                    $def_order_ns = '&sort=hits_desc' . $curr_page;
                    $this->_view->assign('hits_s', $def_order_ns);
                    break;

                case 'hits_desc':
                    $def_order = ' ORDER BY Hits DESC';
                    $def_order_n = '&sort=hits_desc' . $curr_page;
                    $def_order_ns = '&sort=hits_asc' . $curr_page;
                    $this->_view->assign('hits_s', $def_order_ns);
                    break;
            }
        }

        $a = Tool::getLimit($limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_news WHERE Sektion='{$area}' {$def_search} {$def_status} {$def_categ} {$def_hits} {$def_datetill} {$def_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $news = array();
        while ($row = $sql->fetch_object()) {
            $row->Comments = $this->__object('AdminCore')->countComments('news', $row->Id);
            $row->User = Tool::userName($row->Autor);
            $row->Wertung = Tool::rating($row->Id, 'news');
            $news[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=news&amp;sub=overview{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";
        $nastr = "{$def_order_n}{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";

        if (Arr::getPost('startsearch') == 1) {
            $_SESSION['NewsSearch'] = "WHERE Id!='0' {$def_search} {$def_status} {$def_categ} {$def_hits} {$def_datetill}";
        }
        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('news', $news);
        $this->_view->assign('newscategs', $this->categs());
        $this->_view->assign('title', $this->_lang['News_show']);
        $this->_view->assign('limit', $limit);
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=news&amp;sub=overview{$nastr}&pp={$limit}&page={s}\">{t}</a> "));
        }
        $this->_view->content('/news/news.tpl');
    }

    public function active($openclose, $id) {
        if (perm('news_openclose')) {
            $this->_db->query("UPDATE " . PREFIX . "_news SET Aktiv='{$openclose}' WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->backurl();
    }

    protected function categs($prefix = '') {
        $area = $_SESSION['a_area'];
        $categs = array();
        return $this->categsNews(0, $prefix, $categs, $area);
    }

    protected function categsNews($id, $prefix, &$news_categ, &$area) {
        $query = $this->_db->query("SELECT *, Name_1 AS Name FROM " . PREFIX . "_news_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY POSI ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $news_categ[] = $item;
            $this->categsNews($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

    protected function remove($id) {
        $id = intval($id);
        $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_news_kategorie WHERE Parent_Id='" . $id . "'");
        while ($item = $query->fetch_object()) {
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_news WHERE Kategorie='" . $id . "'");
            while ($row = $sql->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='news' AND Objekt_Id='" . $row->Id . "'");
            }
            $this->_db->query("DELETE FROM " . PREFIX . "_news WHERE Kategorie='" . $id . "'");
            $this->remove($item->Id);
        }
        $query->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_news_kategorie WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_news WHERE Kategorie='" . $id . "'");
    }

}
