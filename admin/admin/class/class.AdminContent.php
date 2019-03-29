<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404);
    exit;
}

class AdminContent extends Magic {

    public function delRating($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='content' AND Objekt_Id='" . $this->_db->escape($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function delCateg($id) {
        if (perm('content_category')) {
            if ($id != 1) {
                $this->_db->query("DELETE FROM " . PREFIX . "_content_kategorien WHERE Id='" . $this->_db->escape($id) . "'");
            }
        }
        $this->__object('AdminCore')->backurl();
    }

    protected function categs() {
        $categs = array();
        $query = $this->_db->query("SELECT * FROM " . PREFIX . "_content_kategorien WHERE Sektion = '" . AREA . "' ORDER BY Name ASC");
        while ($row = $query->fetch_object()) {
            $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '" . AREA . "' LIMIT 1");
            $d = SX_DIR . '/theme/' . $res->Template . '/page/';
            $templates = array();
            $handle = opendir($d);
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_file($d . $file)) {
                    $f = new stdClass;
                    $f->Name = $file;
                    $templates[] = $f;
                }
            }
            closedir($handle);

            $row->templates = $templates;
            $categs[] = $row;
        }
        $query->close();
        return $categs;
    }

    public function showCategs() {
        if (!perm('content_category')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $cid => $c) {
                if (!empty($_POST['Name'][$cid])) {
                    $this->_db->query("UPDATE " . PREFIX . "_content_kategorien SET Name = '" . $this->_db->escape($_POST['Name'][$cid]) . "',Tpl_Extra = '" . $this->_db->escape($_POST['Tpl_Extra'][$cid]) . "' WHERE Id='" . $this->_db->escape($cid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1 && !empty($_POST['Name'])) {
            $insert_array = array(
                'Name'      => Arr::getPost('Name'),
                'Sektion'   => AREA,
                'Tpl_Extra' => Arr::getPost('Tpl_Extra'));
            $this->_db->insert_query('content_kategorien', $insert_array);
            $this->__object('AdminCore')->script('save');
        }

        $this->_view->assign('categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Content_Categs']);
        $this->_view->content('/content/categs.tpl');
    }

    public function delete($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor, Titel1 FROM " . PREFIX . "_content WHERE Id='" . $id . "' LIMIT 1");
        if (($check->Autor == $_SESSION['benutzer_id'] && perm('content_delete')) && perm('content_delete_all')) {
            $this->_db->query("DELETE FROM " . PREFIX . "_content WHERE Id='" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Bereich='content' AND Objekt_Id='" . $id . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил страницу (' . $check->Titel1 . ')', '0', $_SESSION['benutzer_id']);
        }
        $this->__object('AdminCore')->backurl();
    }

    public function show() {
        $def_order = 'ORDER BY Id DESC';
        $def_order_n = $def_search = $def_status = $def_status_n = $def_categ = '';
        $def_categ_n = $def_search_n = $def_datetill = $def_datetill_n = $def_hits = $def_hits_n = '';

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['nid'] as $nid) {
                $this->_db->query("UPDATE " . PREFIX . "_content SET Topcontent = '" . $this->_db->escape($_POST['Topcontent'][$nid]) . "', Kategorie='" . intval($_POST['Kategorie'][$nid]) . "' WHERE Id='" . intval($nid) . "'");
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_POST['multi']) && isset($_SESSION['ContentSearch']) && perm('content_multioptions')) {
            switch ($_POST['multiaction']) {
                case 'movecateg':
                    $this->_db->query("UPDATE " . PREFIX . "_content SET Kategorie='" . intval(Arr::getPost('newcateg')) . "' " . $_SESSION['ContentSearch'] . '');
                    break;
                case 'open':
                    $this->_db->query("UPDATE " . PREFIX . "_content SET Aktiv='1' " . $_SESSION['ContentSearch']);
                    break;
                case 'close':
                    $this->_db->query("UPDATE " . PREFIX . "_content SET Aktiv='0' " . $_SESSION['ContentSearch']);
                    break;
                case 'delete':
                    $this->_db->query("DELETE FROM " . PREFIX . "_content " . $_SESSION['ContentSearch']);
                    break;
            }
            $this->_view->assign('multi_done', 1);
            $this->__object('AdminCore')->script('save');
        } else {
            unset($_SESSION['ContentSearch']);
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
                $def_datetill = " AND (Datum <= $lafrom) ";
                $def_datetill_n = "&amp;date_till=" . $_REQUEST['date_till'];
            }
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.@ ');
            $def_search_n = '&amp;q=' . $pattern;
            $pattern = $this->_db->escape($pattern);
            $def_search = " AND (Titel1 LIKE '%{$pattern}%' OR Titel2 LIKE '%{$pattern}%' OR Titel3 LIKE '%{$pattern}%' )";
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
                    $def_order = ' ORDER BY Datum ASC';
                    $def_order_n = '&sort=date_asc' . $curr_page;
                    $def_order_ns = '&sort=date_desc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'date_desc':
                    $def_order = ' ORDER BY Datum DESC';
                    $def_order_n = '&sort=date_desc' . $curr_page;
                    $def_order_ns = '&sort=date_asc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
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
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_content WHERE Sektion='" . AREA . "'  {$def_search} {$def_status} {$def_categ} {$def_hits} {$def_datetill} {$def_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $news = array();
        while ($row = $sql->fetch_object()) {
            $row->Comments = $this->__object('AdminCore')->countComments('content', $row->Id);
            $row->User = Tool::userName($row->Autor);
            $row->Wertung = Tool::rating($row->Id, 'content');
            $news[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=content&amp;sub=overview{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";
        $nastr = "{$def_order_n}{$def_search_n}{$def_status_n}{$def_categ_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";

        if (Arr::getPost('startsearch') == 1) {
            $_SESSION['ContentSearch'] = "WHERE Id!='0' {$def_search} {$def_status} {$def_categ} {$def_hits} {$def_datetill}";
        }
        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('news', $news);
        $this->_view->assign('newscategs', $this->categs());
        $this->_view->assign('title', $this->_lang['Content']);
        $this->_view->assign('limit', $limit);
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=content&amp;sub=overview{$nastr}&pp={$limit}&page={s}\">{t}</a> "));
        }
        $this->_view->content('/content/content.tpl');
    }

    public function add() {
        if (!perm('content_new')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $titel = Arr::getPost('Titel');
            $gr = isset($_POST['Gruppen']) ? implode(',', $_POST['Gruppen']) : '';
            $glr = isset($_POST['Gallery']) ? implode(',', $_POST['Gallery']) : '';
            $insert_array = array(
                'Sektion'           => AREA,
                'Datum'             => time(),
                'Kennwort'          => Arr::getPost('Kennwort'),
                'Autor'             => $_SESSION['benutzer_id'],
                'Aktiv'             => Arr::getPost('Aktiv'),
                'Kategorie'         => Arr::getPost('categ'),
                'Titel1'            => $titel,
                'Titel2'            => $titel,
                'Titel3'            => $titel,
                'Topcontent_Bild_1' => Arr::getPost('Bild_2'),
                'Topcontent_Bild_2' => Arr::getPost('Bild_2'),
                'Topcontent_Bild_3' => Arr::getPost('Bild_2'),
                'Inhalt1'           => Arr::getPost('Content'),
                'Inhalt2'           => Arr::getPost('Content'),
                'Inhalt3'           => Arr::getPost('Content'),
                'Bild1'             => Arr::getPost('Bild_1'),
                'Bild2'             => Arr::getPost('Bild_1'),
                'Bild3'             => Arr::getPost('Bild_1'),
                'Textbilder1'       => base64_decode(Arr::getPost('screenshots')),
                'BildAusrichtung'   => Arr::getPost('BildAusrichtung'),
                'Bewertung'         => Arr::getPost('Bewertung'),
                'Kommentare'        => Arr::getPost('Kommentare'),
                'Tags'              => Arr::getPost('Tags'),
                'Galerien'          => $glr,
                'Topcontent'        => Arr::getPost('Topcontent'),
                'Suche'             => Arr::getPost('Suche'),
                'Gruppen'           => $gr);
            $this->_db->insert_query('content', $insert_array);
            $new_id = $this->_db->insert_id();
            $doc = "index.php?p=content&id=" . $new_id . "&name=" . translit($titel) . "&area=" . AREA;

            if (Arr::getPost('ToNavi') == 1) {
                $na = explode('|', Arr::getPost('NaviCat2'));
                $ParentId = $na[1];
                $NaCat = $na[0];

                $insert_array = array(
                    'ParentId' => $ParentId,
                    'NaviCat'  => $NaCat,
                    'Sektion'  => AREA,
                    'Titel_1'  => $titel,
                    'Titel_2'  => $titel,
                    'Titel_3'  => $titel,
                    'Dokument' => $doc,
                    'group_id' => (strlen($gr) >= 1 ? '1,' . $gr : implode(',', Arr::getPost('Groups'))),
                    'Ziel'     => '_self',
                    'Position' => intval(Arr::getPost('PosN')));
                $this->_db->insert_query('navi', $insert_array);
            }

            if (Arr::getPost('ToQuickNavi') == 1) {
                $insert_array = array(
                    'Sektion'  => AREA,
                    'Name_1'   => $titel,
                    'Name_2'   => $titel,
                    'Name_3'   => $titel,
                    'Dokument' => $doc,
                    'Aktiv'    => 1,
                    'Ziel'     => '_self',
                    'Position' => intval(Arr::getPost('PosQN')));
                $this->_db->insert_query('quicknavi', $insert_array);
            }

            // Добавляем задание на пинг
            $options = array(
                'name' => $titel,
                'url'  => BASE_URL . '/index.php?p=content&id=' . $new_id . '&name=' . translit($titel) . '&area=' . AREA,
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $titel,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал страницу (' . $titel . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }

        $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
        $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
        $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
        $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('Navis', $this->__object('AdminCore')->getNavigation(AREA));
        $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
        $this->_view->assign('Content', $this->__object('Editor')->load('admin', ' ', 'Content', 450, 'Content'));
        $this->_view->assign('newscategs', $this->categs());
        $this->_view->assign('title', $this->_lang['Content_new']);
        $this->_view->content('/content/content_new.tpl');
    }

    public function edit($id) {
        $id = intval($id);
        $check = $this->_db->cache_fetch_object("SELECT Autor FROM " . PREFIX . "_content WHERE Id='" . $id . "' LIMIT 1");

        if (($check->Autor == $_SESSION['benutzer_id'] && perm('content_edit')) || perm('content_edit_all')) {
            $LC = $this->__object('AdminCore')->getLangcode();
            $isdb = $categ = $Bild = $TopContentBild = $Gallery = $Sonstiges = $SetAll = '';

            if (Arr::getPost('save') == 1) {
                $Titel = $this->_db->escape(Arr::getPost('Titel'));
                if (perm('screenshots') && isset($_POST['screenshots'])) {
                    $isdb = ",Textbilder{$LC} = '" . $this->_db->escape(base64_decode($_POST['screenshots'])) . "' ";
                }

                $Bild = (Arr::getPost('NoImg') == 1) ? ",Bild{$LC} = ''" : ((!empty($_POST['Bild_1'])) ? ",Bild{$LC} = '" . $this->_db->escape($_POST['Bild_1']) . "'" : '');
                $TopContentBild = (Arr::getPost('NoContentImg') == 1) ? ",Topcontent_Bild_{$LC} = ''" : ((!empty($_POST['Bild_2'])) ? ",Topcontent_Bild_{$LC} = '" . $this->_db->escape($_POST['Bild_2']) . "'" : '');

                if (Arr::getPost('langcode') == 1) {
                    $categ = ",Kategorie='" . $this->_db->escape(Arr::getPost('categ')) . "'";
                    $Gallery = (isset($_POST['Gallery'])) ? ",Galerien='" . $this->_db->escape(implode(',', $_POST['Gallery'])) . "'" : '';

                    if (Arr::getPost('saveAllLang') == 1) {
                        $SetAll = " ,Titel2 = '" . $this->_db->escape(Arr::getPost('Titel')) . "' ,Inhalt2 = '" . $this->_db->escape($_POST['Content']) . "' ,Titel3 = '" . $this->_db->escape(Arr::getPost('Titel')) . "' ,Inhalt3 = '" . $this->_db->escape($_POST['Content']) . "'";
                    }

                    $Sonstiges = "
                        {$SetAll}
                        ,Kennwort = '" . $this->_db->escape(trim(Arr::getPost('Kennwort'))) . "'
                        ,Tags = '" . $this->_db->escape(trim(Arr::getPost('Tags'))) . "'
                        ,BildAusrichtung = '" . $this->_db->escape(Arr::getPost('BildAusrichtung')) . "'
                        ,Suche = '" . $this->_db->escape(Arr::getPost('Suche')) . "'
                        ,Bewertung = '" . $this->_db->escape(Arr::getPost('Bewertung')) . "'
                        ,Kommentare = '" . $this->_db->escape(Arr::getPost('Kommentare')) . "'
                        ,Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "'";
                }

                $gr = (isset($_POST['Gruppen'])) ? implode(',', $_POST['Gruppen']) : '';
                $q = "UPDATE " . PREFIX . "_content SET
                            Gruppen = '" . $this->_db->escape($gr) . "',
                            Topcontent = '" . $this->_db->escape(Arr::getPost('Topcontent')) . "',
                            Titel{$LC} = '" . $Titel . "',
                            Inhalt{$LC} = '" . $this->_db->escape($_POST['Content']) . "'
                            {$categ}
                            {$Bild}
                            {$TopContentBild}
                            {$Sonstiges}
                            {$Gallery}
                            {$isdb}
                    WHERE Id='" . $id . "'";

                $this->_db->query($q);
                $doc_s = "index.php?p=content&id=$id";
                $doc = "index.php?p=content&id=$id&name=" . translit($_POST['Titel']) . "&area=" . AREA;

                if (Arr::getPost('ToNavi') == 1) {
                    $na = explode('|', $_POST['NaviCat2']);
                    $ParentId = $na[1];
                    $NaCat = $na[0];

                    $insert_array = array(
                        'ParentId' => intval($ParentId),
                        'NaviCat'  => $NaCat,
                        'Sektion'  => AREA,
                        'Titel_1'  => $Titel,
                        'Titel_2'  => $Titel,
                        'Titel_3'  => $Titel,
                        'Dokument' => $doc,
                        'group_id' => (strlen($gr) >= 1 ? '1,' . $gr : implode(',', Arr::getPost('Groups'))),
                        'Ziel'     => '_self',
                        'Position' => intval(Arr::getPost('PosN')));
                    $this->_db->insert_query('navi', $insert_array);
                }

                if (Arr::getPost('ToQuickNavi') == 1) {
                    $insert_array = array(
                        'Sektion'  => AREA,
                        'Name_1'   => $Titel,
                        'Name_2'   => $Titel,
                        'Name_3'   => $Titel,
                        'Dokument' => $doc,
                        'Aktiv'    => 1,
                        'Ziel'     => '_self',
                        'Position' => intval(Arr::getPost('PosQN')));
                    $this->_db->insert_query('quicknavi', $insert_array);
                }
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил страницу (' . $_POST['Titel'] . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }

            $LC = $this->__object('AdminCore')->getLangcode();
            $content = $this->_db->cache_fetch_object("SELECT
                        *,
                        Bild{$LC} as Bild,
                        Textbilder{$LC} as Textbilder,
                        Titel{$LC} as Titel,
                        Topcontent_Bild_{$LC} as Topcontent_Bild,
                        Inhalt{$LC} as Inhalt
                FROM " . PREFIX . "_content WHERE Id='" . $id . "' LIMIT 1");

            $doc_s = "index.php?p=content&id=$id&name=" . translit($content->Titel);

            $query = "SELECT Id FROM " . PREFIX . "_quicknavi WHERE Dokument LIKE '" . $doc_s . "%' AND Sektion='" . AREA . "' ; ";
            $query .= "SELECT Id FROM " . PREFIX . "_navi WHERE Dokument LIKE '" . $doc_s . "%' AND Sektion='" . AREA . "'";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $res_qn = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $res_n = $result->fetch_object();
                    $result->close();
                }
            }

            $content->Gruppe_Perms = explode(',', $content->Gruppen);
            $content->inQuicknavi = (isset($res_qn->Id)) ? $res_qn->Id : '';
            $content->inNavi = (isset($res_n->Id)) ? $res_n->Id : '';
            $content->Galerien = explode(',', $content->Galerien);
            $this->_view->assign('ContentVideos', $this->__object('AdminCore')->getVideos(AREA));
            $this->_view->assign('ContentAudios', $this->__object('AdminCore')->getAudios(AREA));
            $this->_view->assign('ContentLinks', $this->__object('AdminCore')->getContents(AREA));
            $this->_view->assign('cforms', $this->__object('AdminCore')->getContactforms());
            $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
            $this->_view->assign('Navis', $this->__object('AdminCore')->getNavigation(AREA));
            $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
            $this->_view->assign('Content', $this->__object('Editor')->load('admin', $content->Inhalt . ' ', 'Content', 350, 'Content'));
            $this->_view->assign('InlineShots', unserialize($content->Textbilder));
            $this->_view->assign('content', $content);
            $this->_view->assign('field_inline', "Textbilder{$LC}");
            $this->_view->assign('newscategs', $this->categs());
            $this->_view->assign('title', $this->_lang['Content_edit']);
            $this->_view->content('/content/content_edit.tpl');
        } else {
            $this->__object('AdminCore')->noAccess();
        }
    }

    public function active($openclose, $id) {
        if (perm('news_openclose')) {
            $this->_db->query("UPDATE " . PREFIX . "_content SET Aktiv='" . $this->_db->escape($openclose) . "' WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->backurl();
    }

}
