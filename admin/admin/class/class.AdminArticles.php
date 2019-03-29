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

class AdminArticles extends Magic {

    public function delRating($id) {
        if (!perm('del_rating')) {
            SX::object('AdminCore')->noAccess();
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='articles' AND Objekt_Id='" . intval($id) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обнулил рейтинг статьи', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function editCateg($id) {
        if (!perm('articles_category')) {
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
            $this->_db->update_query('artikel_kategorie', $array, "Id='" . $id . "'");
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_artikel_kategorie  WHERE Id='" . $id . "' LIMIT 1");
        $this->_view->assign('res', $res);
        $this->_view->assign('newscategs', $this->loadCategs());
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/articles/categ.tpl');
    }

    public function addCateg() {
        if (!perm('articles_category')) {
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
            $this->_db->insert_query('artikel_kategorie', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил категорию статей (' . $Name_1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('new', 1);
        $this->_view->assign('newscategs', $this->loadCategs());
        $this->_view->assign('title', $this->_lang['Global_CategEdit']);
        $this->_view->content('/articles/categ.tpl');
    }

    public function deleteCateg($id) {
        if (perm('articles_category')) {
            $res = $this->_db->cache_fetch_object("SELECT Name_1 FROM " . PREFIX . "_artikel_kategorie WHERE Id='" . intval($id) . "' LIMIT 1");
            $this->categDel($id);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил категорию статей (' . $res->Name_1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    protected function categDel($id) {
        $id = intval($id);
        $query = $this->_db->query("SELECT Id, Parent_Id FROM " . PREFIX . "_artikel_kategorie WHERE Parent_Id='" . $id . "'");
        while ($item = $query->fetch_object()) {
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_artikel WHERE Kategorie='" . $id . "'");
            while ($row = $sql->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='articles' AND Objekt_Id='" . $row->Id . "'");
            }
            $this->_db->query("DELETE FROM " . PREFIX . "_artikel WHERE Kategorie='" . $id . "'");
            $this->categDel($item->Id);
        }
        $query->close();
        $this->_db->query("DELETE FROM " . PREFIX . "_artikel_kategorie WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_artikel WHERE Kategorie='" . $id . "'");
    }

    public function showCategs() {
        if (!perm('articles_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->_view->assign('newscategs', $this->loadCategs());
        $this->_view->assign('title', $this->_lang['Gaming_articles_category']);
        $this->_view->content('/articles/categs.tpl');
    }

    public function show() {
        $def_order = 'ORDER BY Id DESC';
        $def_order_n = $def_search = $def_status = $def_status_n = $def_categ = $def_categ_n = $def_search_n = '';
        $def_datetill = $def_datetill_n = $def_top = $def_top_n = $def_typ = $def_typ_n = $def_hits = $def_hits_n = '';
        $area = $_SESSION['a_area'];

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['nid'] as $nid) {
                $array = array(
                    'Topartikel' => $_POST['Topartikel'][$nid],
                    'Kategorie'  => $_POST['Kategorie'][$nid],
                    'Typ'        => $_POST['Typ'][$nid],
                );
                $this->_db->update_query('artikel', $array, "Id='" . intval($nid) . "'");
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил статью', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_POST['multi']) && isset($_SESSION['ArticlesSearch']) && perm('articles_multioptions')) {
            switch ($_POST['multiaction']) {
                case 'movecateg':
                    $this->_db->query("UPDATE " . PREFIX . "_artikel SET Kategorie='" . intval(Arr::getPost('newcateg')) . "' " . $_SESSION['ArticlesSearch'] . " AND Sektion='{$area}'");
                    break;
                case 'open':
                    $this->_db->query("UPDATE " . PREFIX . "_artikel SET Aktiv='1' " . $_SESSION['ArticlesSearch'] . " AND Sektion='{$area}'");
                    break;
                case 'close':
                    $this->_db->query("UPDATE " . PREFIX . "_artikel SET Aktiv='0' " . $_SESSION['ArticlesSearch'] . " AND Sektion='{$area}'");
                    break;
                case 'delete':
                    $this->_db->query("DELETE FROM " . PREFIX . "_artikel " . $_SESSION['ArticlesSearch'] . " AND Sektion='{$area}'");
                    break;
            }
            $this->_view->assign('multi_done', 1);
            $this->__object('AdminCore')->script('save');
        } else {
            unset($_SESSION['ArticlesSearch']);
        }

        if (!empty($_REQUEST['typ']) && ($_REQUEST['typ'] == 'special' || $_REQUEST['typ'] == 'review' || $_REQUEST['typ'] == 'preview')) {
            $def_typ = "AND Typ = '" . $this->_db->escape(Arr::getRequest('typ')) . "'";
            $def_typ_n = "&amp;typ=" . $_REQUEST['typ'];
        }

        if (!empty($_REQUEST['top'])) {
            $def_top = "AND Topartikel = '" . intval($_REQUEST['top']) . "'";
            $def_top_n = "&amp;categ=" . intval($_REQUEST['top']);
        }

        if (!empty($_REQUEST['categ'])) {
            $def_categ = "AND Kategorie = '" . intval($_REQUEST['categ']) . "'";
            $def_categ_n = "&amp;categ=" . intval($_REQUEST['categ']);
        }

        if (isset($_REQUEST['hits_from']) && isset($_REQUEST['hits_to']) && $_REQUEST['hits_from'] < $_REQUEST['hits_to']) {
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
                $def_datetill_n = "&amp;date_till=" . $_REQUEST['date_till'];
            }
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.@ ');
            $def_search_n = '&amp;q=' . $pattern;
            $pattern = $this->_db->escape($pattern);
            $def_search = " AND (Titel_1 LIKE '%$pattern%' OR Titel_2 LIKE '%$pattern%' OR Titel_3 LIKE '%$pattern%')";
        }

        if (isset($_REQUEST['sort'])) {
            $curr_page = '&amp;page=' . Arr::getRequest('page', 1);

            switch ($_REQUEST['sort']) {
                case 'name_asc':
                    $def_order = ' ORDER BY Titel_1 ASC';
                    $def_order_n = '&sort=name_asc' . $curr_page;
                    $def_order_ns = '&sort=name_desc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'name_desc':
                    $def_order = ' ORDER BY Titel_1 DESC';
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

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS *,Titel_1 AS Titel1 FROM " . PREFIX . "_artikel WHERE Sektion='{$area}' {$def_search} {$def_status} {$def_categ} {$def_top} {$def_hits} {$def_typ} {$def_datetill} {$def_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $news = array();
        while ($row = $sql->fetch_object()) {
            $row->Comments = $this->__object('AdminCore')->countComments('articles', $row->Id);
            $row->User = Tool::userName($row->Autor);
            $row->Wertung = Tool::rating($row->Id, 'articles');
            $news[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=articles&amp;sub=show{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}{$def_typ_n}{$def_top_n}";
        $nastr = "{$def_order_n}{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}{$def_typ_n}{$def_top_n}";

        if (Arr::getPost('startsearch') == 1) {
            $_SESSION['ArticlesSearch'] = "WHERE Id!='0' {$def_search} {$def_status} {$def_categ} {$def_hits} {$def_datetill}";
        }

        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('news', $news);
        $this->_view->assign('newscategs', $this->loadCategs());
        $this->_view->assign('title', $this->_lang['Gaming_articles']);
        $this->_view->assign('limit', $limit);
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=articles&amp;sub=show{$nastr}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->content('/articles/show.tpl');
    }

    public function copy($id) {
        if (!perm('articles_new')) {
            SX::object('AdminCore')->noAccess();
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_artikel WHERE Id='" . intval($id) . "' LIMIT 1");
        $insert_array = array(
            'Kategorie'         => $res->Kategorie,
            'Titel_1'           => $res->Titel_1 . $this->_lang['DbCopy'],
            'Titel_2'           => $res->Titel_2 . $this->_lang['DbCopy'],
            'Titel_3'           => $res->Titel_3 . $this->_lang['DbCopy'],
            'Untertitel_1'      => $res->Untertitel_1,
            'Untertitel_2'      => $res->Untertitel_2,
            'Untertitel_3'      => $res->Untertitel_3,
            'Inhalt_1'          => $res->Inhalt_1,
            'Inhalt_2'          => $res->Inhalt_2,
            'Inhalt_3'          => $res->Inhalt_3,
            'Textbilder_1'      => $res->Textbilder_1,
            'Textbilder_2'      => $res->Textbilder_2,
            'Textbilder_3'      => $res->Textbilder_3,
            'WertungsDaten'     => $res->WertungsDaten,
            'Genre'             => $res->Genre,
            'Hersteller'        => $res->Hersteller,
            'Vertrieb'          => $res->Vertrieb,
            'Wertung'           => $res->Wertung,
            'Kommentare'        => $res->Kommentare,
            'Plattform'         => $res->Plattform,
            'Veroeffentlichung' => $res->Veroeffentlichung,
            'Preis'             => $res->Preis,
            'Shop'              => $res->Shop,
            'Bild_1'            => $res->Bild_1,
            'Bild_2'            => $res->Bild_2,
            'Bild_3'            => $res->Bild_3,
            'Links'             => $res->Links,
            'Galerien'          => $res->Galerien,
            'Autor'             => $_SESSION['benutzer_id'],
            'Zeit'              => time(),
            'Hits'              => 0,
            'Typ'               => $res->Typ,
            'Sektion'           => $res->Sektion,
            'Druck'             => 0,
            'Aktiv'             => 0,
            'Top'               => $res->Top,
            'Flop'              => $res->Flop,
            'Minimum'           => $res->Minimum,
            'Optimum'           => $res->Optimum,
            'ZeitStart'         => $res->ZeitStart,
            'ZeitEnde'          => $res->ZeitEnde,
            'Suche'             => $res->Suche,
            'Topartikel'        => $res->Topartikel,
            'TopartikelBild_1'  => $res->TopartikelBild_1,
            'TopartikelBild_2'  => $res->TopartikelBild_2,
            'TopartikelBild_3'  => $res->TopartikelBild_3,
            'AlleSektionen'     => $res->AlleSektionen,
            'Bildausrichtung'   => $res->Bildausrichtung,
            'Tags'              => $res->Tags,
            'ShopArtikel'       => $res->ShopArtikel,
            'Kennwort'          => $res->Kennwort);
        $this->_db->insert_query('artikel', $insert_array);
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал статью (' . $res->Titel_1 . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function delete($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor FROM " . PREFIX . "_artikel WHERE Id='" . $id . "' LIMIT 1");
        if (($check->Autor == $_SESSION['benutzer_id'] && perm('articles_delete')) || perm('articles_delete_all')) {
            $this->_db->query("DELETE FROM " . PREFIX . "_artikel WHERE Id='" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='articles' AND Objekt_Id='" . $id . "'");
        }
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил статью (' . $id . ')', '0', $_SESSION['benutzer_id']);
        $this->__object('AdminCore')->backurl();
    }

    public function active($openclose, $id) {
        if (perm('articles_openclose')) {
            $this->_db->query("UPDATE " . PREFIX . "_artikel SET Aktiv='" . $this->_db->escape($openclose) . "' WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->backurl();
    }

    public function add() {
        if (!perm('articles_new')) {
            SX::object('AdminCore')->noAccess();
        }
        $LC = 1;
        if (Arr::getPost('save') == 1) {
            $end = '0';
            $start = explode('.', $_POST['ZeitStart']);
            $start = mktime(0, 0, 1, $start[1], $start[0], $start[2]);

            if (Arr::getPost('ZeitEnde') > 1) {
                $end = explode('.', $_POST['ZeitEnde']);
                $end = mktime(23, 59, 59, $end[1], $end[0], $end[2]);
            }

            if (!empty($_POST['WertungsDaten'])) {
                $datas = explode("\r\n", $_POST['WertungsDaten']);
                foreach ($datas as $m) {
                    $det = explode(';', $m);
                    if (!empty($det[1])) {
                        $Wert = $det[1];

                        switch ($det[1]) {
                            case ($det[1] < 1):
                                $Wert = 1;
                                break;

                            case ($det[1] > 5):
                                $Wert = 5;
                                break;
                        }
                        if (!empty($det[0])) {
                            $data_res_all[] = $det[0] . ';' . $Wert;
                        }
                    }
                }
                $data_res_all_f = implode("\r\n", $data_res_all);
            }
            $glr = isset($_POST['Gallery']) ? implode(',', $_POST['Gallery']) : '';
            $titel = Arr::getPost('Titel');

            $insert_array = array(
                'Kategorie'         => intval(Arr::getPost('categ')),
                'Titel_1'           => $titel,
                'Titel_2'           => $titel,
                'Titel_3'           => $titel,
                'Untertitel_1'      => Arr::getPost('Untertitel'),
                'Untertitel_2'      => Arr::getPost('Untertitel'),
                'Untertitel_3'      => Arr::getPost('Untertitel'),
                'Inhalt_1'          => Arr::getPost('Inhalt'),
                'Inhalt_2'          => Arr::getPost('Inhalt'),
                'Inhalt_3'          => Arr::getPost('Inhalt'),
                'Textbilder_1'      => base64_decode(Arr::getPost('screenshots')),
                'Textbilder_2'      => '',
                'Textbilder_3'      => '',
                'WertungsDaten'     => (isset($data_res_all_f) ? $this->_db->escape($data_res_all_f) : ''),
                'Genre'             => Arr::getPost('Genre'),
                'Hersteller'        => Arr::getPost('Hersteller'),
                'Vertrieb'          => Arr::getPost('Vertrieb'),
                'Wertung'           => Arr::getPost('Wertung'),
                'Kommentare'        => Arr::getPost('Kommentare'),
                'Plattform'         => Arr::getPost('Plattform'),
                'Veroeffentlichung' => Arr::getPost('Veroeffentlichung'),
                'Preis'             => Arr::getPost('Preis'),
                'Shop'              => Arr::getPost('Shop'),
                'Bild_1'            => Arr::getPost('Bild_1'),
                'Bild_2'            => Arr::getPost('Bild_1'),
                'Bild_3'            => Arr::getPost('Bild_1'),
                'Links'             => Arr::getPost('Links'),
                'Galerien'          => $glr,
                'Autor'             => $_SESSION['benutzer_id'],
                'Zeit'              => time(),
                'Hits'              => 0,
                'Typ'               => Arr::getPost('Typ'),
                'Sektion'           => AREA,
                'Druck'             => 0,
                'Aktiv'             => 1,
                'Top'               => Arr::getPost('Top'),
                'Flop'              => Arr::getPost('Flop'),
                'Minimum'           => Arr::getPost('Minimum'),
                'Optimum'           => Arr::getPost('Optimum'),
                'ZeitStart'         => $start,
                'ZeitEnde'          => $end,
                'Suche'             => Arr::getPost('Suche'),
                'Topartikel'        => Arr::getPost('Topartikel'),
                'TopartikelBild_1'  => Arr::getPost('Bild_2'),
                'TopartikelBild_2'  => Arr::getPost('Bild_2'),
                'TopartikelBild_3'  => Arr::getPost('Bild_2'),
                'AlleSektionen'     => Arr::getPost('AlleSektionen'),
                'Bildausrichtung'   => Arr::getPost('Bildausrichtung'),
                'Tags'              => Arr::getPost('Tags'),
                'ShopArtikel'       => Arr::getPost('ShopArtikel'),
                'Kennwort'          => Arr::getPost('Kennwort'));
            $this->_db->insert_query('artikel', $insert_array);
            $new_id = $this->_db->insert_id();

            // Добавляем задание на пинг
            $options = array(
                'name' => $titel,
                'url'  => BASE_URL . '/index.php?p=articles&area=' . AREA . '&action=displayarticle&id=' . $new_id . '&name=' . translit($titel),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $titel,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' добавил новую статью (' . $titel . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
        $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
        $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
        $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
        $this->_view->assign('Gallery', $this->galerie($_SESSION['a_area']));
        $this->_view->assign('Inhalt', $this->__object('Editor')->load('admin', ' ', 'Inhalt', 350, 'Content'));
        $this->_view->assign('field_inline', "Textbilder_{$LC}");
        $this->_view->assign('articlecategs', $this->loadCategs());
        $this->_view->assign('genres', $this->genres());
        $this->_view->assign('mf', $this->manufacturer());
        $this->_view->assign('pf', $this->plattforms());
        $this->_view->assign('title', $this->_lang['Gaming_articles_new']);
        $this->_view->content('/articles/new.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor FROM " . PREFIX . "_artikel WHERE Id='" . $id . "' LIMIT 1");
        if (($check->Autor == $_SESSION['benutzer_id'] && perm('articles_edit')) || perm('articles_edit_all')) {
            $LC = $this->__object('AdminCore')->getLangcode();
            $isdb = $start = $end = $Bild = $TopnewsBild = $Gallery = $Sonstiges = $SetAll = '';

            if (Arr::getPost('save') == 1) {
                if (perm('screenshots') && isset($_POST['screenshots'])) {
                    $isdb = ",Textbilder_{$LC} = '" . $this->_db->escape(base64_decode($_POST['screenshots'])) . "' ";
                }

                $Bild = (Arr::getPost('NoImg') == 1) ? ",Bild_{$LC} = ''" : (!empty($_POST['Bild_1']) ? ",Bild_{$LC} = '" . $this->_db->escape($_POST['Bild_1']) . "'" : '');
                $TopnewsBild = (Arr::getPost('NoTopnewsImg') == 1) ? ",TopartikelBild_{$LC} = ''" : ((!empty($_POST['Bild_2'])) ? ",TopartikelBild_{$LC} = '" . $this->_db->escape($_POST['Bild_2']) . "'" : '');

                if (Arr::getPost('langcode') == 1) {
                    $categ = (isset($_POST['categ'])) ? ",Kategorie='" . intval(Arr::getPost('categ')) . "'" : '';
                    $Gallery = (isset($_POST['Gallery'])) ? ",Galerien='" . $this->_db->escape(implode(',', $_POST['Gallery'])) . "'" : '';

                    if (!empty($_POST['ZeitStart'])) {
                        $start = explode('.', $_POST['ZeitStart']);
                        $start = mktime(0, 0, 1, $start[1], $start[0], $start[2]);
                        $start = ",ZeitStart='{$start}'";
                    }

                    if (!empty($_POST['WertungsDaten'])) {
                        $datas = explode("\r\n", $_POST['WertungsDaten']);
                        foreach ($datas as $m) {
                            $det = explode(";", $m);
                            if (!empty($det[1])) {
                                $Wert = $det[1];

                                switch ($det[1]) {
                                    case ($det[1] < 1):
                                        $Wert = 1;
                                        break;

                                    case ($det[1] > 5):
                                        $Wert = 5;
                                        break;
                                }
                                if (!empty($det[0])) {
                                    $data_res_all[] = $det[0] . ';' . $Wert;
                                }
                            }
                        }
                        $data_res_all_f = implode("\r\n", $data_res_all);
                    }

                    if (Arr::getPost('saveAllLang') == 1) {
                        $SetAll = "
                            ,Titel_2 = '" . $this->_db->escape(Arr::getPost('Titel')) . "'
                            ,Untertitel_2 = '" . $this->_db->escape($_POST['Untertitel']) . "'
                            ,Inhalt_2 = '" . $this->_db->escape($_POST['Inhalt']) . "'
                            ,Titel_3 = '" . $this->_db->escape(Arr::getPost('Titel')) . "'
                            ,Untertitel_3 = '" . $this->_db->escape($_POST['Untertitel']) . "'
                            ,Inhalt_3 = '" . $this->_db->escape($_POST['Inhalt']) . "'";
                    }

                    $Sonstiges = "
                        {$SetAll}
                        ,Links = '" . $this->_db->escape(trim(Arr::getPost('Links'))) . "'
                        ,Preis = '" . $this->_db->escape(Arr::getPost('Preis')) . "'
                        ,Kennwort = '" . $this->_db->escape(Arr::getPost('Kennwort')) . "'
                        ,Shop = '" . $this->_db->escape(Arr::getPost('Shop')) . "'
                        ,ShopArtikel = '" . $this->_db->escape(Arr::getPost('ShopArtikel')) . "'
                        ,Veroeffentlichung = '" . $this->_db->escape(Arr::getPost('Veroeffentlichung')) . "'
                        ,Plattform = '" . $this->_db->escape(Arr::getPost('Plattform')) . "'
                        ,Genre = '" . $this->_db->escape(Arr::getPost('Genre')) . "'
                        ,Hersteller = '" . $this->_db->escape(Arr::getPost('Hersteller')) . "'
                        ,Vertrieb = '" . $this->_db->escape(Arr::getPost('Vertrieb')) . "'
                        ,Top = '" . $this->_db->escape(trim(Arr::getPost('Top'))) . "'
                        ,Flop = '" . $this->_db->escape(trim(Arr::getPost('Flop'))) . "'
                        ,Minimum = '" . $this->_db->escape(trim(Arr::getPost('Minimum'))) . "'
                        ,Optimum = '" . $this->_db->escape(trim(Arr::getPost('Optimum'))) . "'
                        ,WertungsDaten = '" . (isset($data_res_all_f) ? $this->_db->escape($data_res_all_f) : '') . "'
                        ,Typ = '" . $this->_db->escape(trim(Arr::getPost('Typ'))) . "'
                        ,Tags = '" . $this->_db->escape(trim(Arr::getPost('Tags'))) . "'
                        ,Bildausrichtung = '" . $this->_db->escape(Arr::getPost('Bildausrichtung')) . "'
                        ,Suche = '" . $this->_db->escape(Arr::getPost('Suche')) . "'
                        ,Wertung = '" . $this->_db->escape(Arr::getPost('Wertung')) . "'
                        ,Kommentare = '" . $this->_db->escape(Arr::getPost('Kommentare')) . "'
                        ,Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "'
                        ,AlleSektionen = '" . $this->_db->escape(Arr::getPost('AlleSektionen')) . "'";

                    if (!empty($_POST['ZeitEnde'])) {
                        $end = explode('.', $_POST['ZeitEnde']);
                        $end = mktime(23, 59, 59, $end[1], $end[0], $end[2]);
                        $end = ",ZeitEnde='{$end}'";
                    } else {
                        $end = ",ZeitEnde='0'";
                    }
                }

                $q = "UPDATE " . PREFIX . "_artikel SET
                            Topartikel = '" . $this->_db->escape(Arr::getPost('Topartikel')) . "',
                            Titel_{$LC} = '" . $this->_db->escape(Arr::getPost('Titel')) . "',
                            Untertitel_{$LC} = '" . $this->_db->escape($_POST['Untertitel']) . "',
                            Inhalt_{$LC} = '" . $this->_db->escape($_POST['Inhalt']) . "'
                            {$categ}
                            {$end}
                            {$Bild}
                            {$TopnewsBild}
                            {$Sonstiges}
                            {$start}
                            {$Gallery}
                            {$isdb}
                    WHERE Id='" . $id . "'";
                $this->_db->query($q);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал статью (' . Arr::getPost('Titel') . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }

            $article = $this->_db->cache_fetch_object("SELECT
                        *,
                        Bild_{$LC} as Bild,
                        Textbilder_{$LC} as Textbilder,
                        Titel_{$LC} as Titel,
                        TopartikelBild_{$LC} as TopartikelBild,
                        Untertitel_{$LC} as  Untertitel,
                        Inhalt_{$LC} as Inhalt
                FROM " . PREFIX . "_artikel WHERE Id='" . $id . "' LIMIT 1");

            $article->Galerien = explode(',', $article->Galerien);
            $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
            $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
            $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
            $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
            $this->_view->assign('Gallery', $this->galerie($_SESSION['a_area']));
            $this->_view->assign('Inhalt', $this->__object('Editor')->load('admin', $article->Inhalt, 'Inhalt', 350, 'Content'));
            $this->_view->assign('InlineShots', unserialize($article->Textbilder));
            $this->_view->assign('article', $article);
            $this->_view->assign('field_inline', "Textbilder_{$LC}");
            $this->_view->assign('articlecategs', $this->loadCategs());
            $this->_view->assign('genres', $this->genres());
            $this->_view->assign('mf', $this->manufacturer());
            $this->_view->assign('pf', $this->plattforms());
            $this->_view->assign('title', $this->_lang['Gaming_articles_edit']);
            $this->_view->content('/articles/edit.tpl');
        } else {
            $this->__object('AdminCore')->noAccess();
        }
    }

    protected function loadCategs($prefix = '') {
        $area = $_SESSION['a_area'];
        $categs = array();
        return $this->categs(0, $prefix, $categs, $area);
    }

    protected function categs($id, $prefix, &$news_categ, &$area) {
        $query = $this->_db->query("SELECT *, Name_1 AS Name FROM " . PREFIX . "_artikel_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY POSI ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $news_categ[] = $item;
            $this->categs($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

    protected function galerie($area) {
        $categs = array();
        $query = $this->_db->query("SELECT Id,Name_1 AS CategName FROM " . PREFIX . "_galerie_kategorien WHERE Sektion = '" . intval($area) . "' ORDER BY Name_1 ASC ");
        while ($row = $query->fetch_object()) {
            $row->Gals = $this->_db->fetch_object_all("SELECT Id AS GalId,Name_1 AS GalName FROM " . PREFIX . "_galerie WHERE Kategorie = '" . $row->Id . "' ORDER BY Name_1 ASC ");
            $categs[] = $row;
        }
        $query->close();
        return $categs;
    }

    protected function manufacturer() {
        $query = $this->_db->fetch_object_all("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Sektion = '" . AREA . "' ORDER BY Name ASC");
        return $query;
    }

    public function plattforms() {
        $query = $this->_db->fetch_object_all("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Sektion = '" . AREA . "' ORDER BY Name ASC");
        return $query;
    }

    protected function genres() {
        $query = $this->_db->fetch_object_all("SELECT Name,Id FROM " . PREFIX . "_genre WHERE Sektion='" . AREA . "' ORDER BY Name ASC");
        return $query;
    }

}
