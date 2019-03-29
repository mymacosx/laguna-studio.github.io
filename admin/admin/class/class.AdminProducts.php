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

class AdminProducts extends Magic {

    public function showGenres() {
        if (!perm('genres')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('new') == 1) {
            foreach ($_POST['Name'] as $nn => $nid) {
                if (!empty($_POST['Name'][$nn])) {
                    $this->_db->insert_query('genre', array('Name' => $_POST['Name'][$nn], 'Sektion' => AREA));
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Name'] as $cat => $nid) {
                if (!empty($_POST['Name'][$cat])) {
                    $this->_db->query("UPDATE " . PREFIX . "_genre SET Name='" . $this->_db->escape($_POST['Name'][$cat]) . "' WHERE Id='" . intval($cat) . "'");
                }
                if (isset($_POST['del'][$cat]) && $_POST['del'][$cat] == 1 && perm('genres')) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_genre WHERE Id='" . intval($cat) . "'");
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил раздел товаров (' . $_POST['Name'][$cat] . ')', '0', $_SESSION['benutzer_id']);
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        $genres = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_genre WHERE Sektion='" . AREA . "' ORDER BY Name ASC");

        $this->_view->assign('genres', $genres);
        $this->_view->assign('title', $this->_lang['Global_Categories']);
        $this->_view->content('/products/genres.tpl');
    }

    public function delRating($id) {
        $this->_db->query("DELETE FROM " . PREFIX . "_wertung WHERE Bereich='products' AND Objekt_Id='" . intval($id) . "'");
        $this->__object('AdminCore')->backurl();
    }

    public function settings() {
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Kommentare' => intval(Arr::getPost('Kommentare')),
                'Wertung'    => intval(Arr::getPost('Wertung')),
                'PageLimit'  => intval(Arr::getPost('PageLimit')));
            SX::save('products', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('products');
        }
        $res = SX::get('products');
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' ' . $this->_lang['Products']);
        $this->_view->content('/products/settings.tpl');
    }

    protected function manufaturer() {
        $query = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_hersteller ORDER BY Name ASC");
        return $query;
    }

    protected function genres() {
        $query = $this->_db->fetch_object_all("SELECT Name,Id FROM " . PREFIX . "_genre WHERE Sektion='" . AREA . "' ORDER BY Name ASC");
        return $query;
    }

    public function show() {
        $def_order = 'ORDER BY Id DESC';
        $def_order_n = $def_search = $def_status = $def_status_n = $def_genre = $def_genre_n = $def_search_n = '';
        $def_datetill = $def_datetill_n = $def_hits = $def_hits_n = $def_istop = $def_istop_n = '';
        $limit = $this->__object('AdminCore')->limit();

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['nid'] as $nid) {
                $array = array(
                    'TopProduct' => $_POST['TopProduct'][$nid],
                    'Genre'      => $_POST['Genre'][$nid],
                    'Aktiv'      => $_POST['Aktiv'][$nid],
                    'Hits'       => $_POST['Hits'][$nid],
                );
                $this->_db->update_query('produkte', $array, "Id='" . intval($nid) . "'");

                if (isset($_POST['del'][$nid]) && $_POST['del'][$nid] == 1 && perm('products_del')) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_produkte WHERE Id='" . intval($nid) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (!empty($_REQUEST['genre'])) {
            $def_genre = "AND Genre = '" . intval($_REQUEST['genre']) . "'";
            $def_genre_n = '&amp;genre=' . intval($_REQUEST['genre']);
        }

        if (!empty($_REQUEST['istop'])) {
            $def_istop = "AND TopProduct = '" . intval($_REQUEST['istop']) . "'";
            $def_istop_n = '&amp;istop=' . intval($_REQUEST['istop']);
        }

        if (Arr::getRequest('hits_from') < Arr::getRequest('hits_to')) {
            $def_hits = "AND (Hits BETWEEN '" . intval($_REQUEST['hits_from']) . "' AND '" . intval($_REQUEST['hits_to']) . "')";
            $def_hits_n = '&amp;hits_from=' . intval($_REQUEST['hits_from']) . "&amp;hits_to=" . intval($_REQUEST['hits_to']);
        }

        if (!empty($_REQUEST['aktiv'])) {
            $def_status = "AND Aktiv = '" . intval($_REQUEST['aktiv']) . "'";
            $def_status_n = '&amp;aktiv=' . intval($_REQUEST['aktiv']);
        }

        if (!empty($_REQUEST['date_till'])) {
            $lafrom = $this->__object('AdminCore')->mktime($_REQUEST['date_till'], 23, 59, 59);
            if ($lafrom) {
                $def_datetill = " AND (Datum <= $lafrom) ";
                $def_datetill_n = '&amp;date_till=' . Arr::getRequest('date_till');
            }
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.@ ');
            $def_search_n = '&amp;q=' . $pattern;
            $pattern = $this->_db->escape($pattern);
            $def_search = " AND (Name1 LIKE '%$pattern%' OR Name2 LIKE '%$pattern%' OR Name3 LIKE '%$pattern%')";
        }

        if (isset($_REQUEST['sort'])) {
            $curr_page = '&amp;page=' . Arr::getRequest('page', 1);

            switch ($_REQUEST['sort']) {
                case 'name_asc':
                    $def_order = ' ORDER BY Name1 ASC';
                    $def_order_n = '&sort=name_asc' . $curr_page;
                    $def_order_ns = '&sort=name_desc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'name_desc':
                    $def_order = ' ORDER BY Name1 DESC';
                    $def_order_n = '&sort=name_desc' . $curr_page;
                    $def_order_ns = '&sort=name_asc' . $curr_page;
                    $this->_view->assign('name_s', $def_order_ns);
                    break;

                case 'username_asc':
                    $def_order = ' ORDER BY Benutzer ASC';
                    $def_order_n = '&sort=username_asc' . $curr_page;
                    $def_order_ns = '&sort=username_desc' . $curr_page;
                    $this->_view->assign('username_s', $def_order_ns);
                    break;

                case 'username_desc':
                    $def_order = ' ORDER BY Benutzer DESC';
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

                case 'genre_desc':
                    $def_order = ' ORDER BY Genre DESC';
                    $def_order_n = '&sort=genre_desc' . $curr_page;
                    $def_order_ns = '&sort=genre_asc' . $curr_page;
                    $this->_view->assign('genre_s', $def_order_ns);
                    break;

                case 'genre_asc':
                    $def_order = ' ORDER BY Genre ASC';
                    $def_order_n = '&sort=genre_asc' . $curr_page;
                    $def_order_ns = '&sort=genre_desc' . $curr_page;
                    $this->_view->assign('genre_s', $def_order_ns);
                    break;

                case 'top_desc':
                    $def_order = ' ORDER BY TopProduct DESC';
                    $def_order_n = '&sort=top_desc' . $curr_page;
                    $def_order_ns = '&sort=top_asc' . $curr_page;
                    $this->_view->assign('top_s', $def_order_ns);
                    break;

                case 'top_asc':
                    $def_order = ' ORDER BY TopProduct ASC';
                    $def_order_n = '&sort=top_asc' . $curr_page;
                    $def_order_ns = '&sort=top_desc' . $curr_page;
                    $this->_view->assign('top_s', $def_order_ns);
                    break;

                case 'active_desc':
                    $def_order = ' ORDER BY Aktiv DESC';
                    $def_order_n = '&sort=active_desc' . $curr_page;
                    $def_order_ns = '&sort=active_asc' . $curr_page;
                    $this->_view->assign('active_s', $def_order_ns);
                    break;

                case 'active_asc':
                    $def_order = ' ORDER BY Aktiv ASC';
                    $def_order_n = '&sort=active_asc' . $curr_page;
                    $def_order_ns = '&sort=active_desc' . $curr_page;
                    $this->_view->assign('active_s', $def_order_ns);
                    break;
            }
        }

        $a = Tool::getLimit($limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_produkte WHERE Sektion='" . AREA . "' {$def_search} {$def_status} {$def_istop} {$def_genre} {$def_hits} {$def_datetill} {$def_order} LIMIT $a, $limit";
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $products = array();
        while ($row = $sql->fetch_object()) {
            $row->Comments = $this->__object('AdminCore')->countComments('products', $row->Id);
            $row->User = Tool::userName($row->Benutzer);
            $row->Wertung = Tool::rating($row->Id, 'products');
            $products[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=products&amp;sub=overview{$def_search_n}{$def_status_n}{$def_genre_n}{$def_istop_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";
        $nastr = "{$def_order_n}{$def_search_n}{$def_status_n}{$def_genre_n}{$def_istop_n}&amp;pp={$limit}{$def_hits_n}{$def_datetill_n}";
        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('products', $products);
        $this->_view->assign('title', $this->_lang['Products']);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('genres', $this->genres());
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=products&amp;sub=overview{$nastr}&pp={$limit}&page={s}\">{t}</a> "));
        }
        $this->_view->content('/products/products.tpl');
    }

    public function add() {
        if (!perm('products_newedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $date_published = '';
            if (!empty($_POST['Datum_Veroffentlichung'])) {
                $dp = explode('.', $_POST['Datum_Veroffentlichung']);
                $date_published = mktime(0, 0, 1, $dp[1], $dp[0], $dp[2]);
            }

            $titel = Arr::getPost('Titel');
            $content = $_POST['Content'];
            $gal = isset($_POST['Galerien']) ? implode(',', $_POST['Galerien']) : '';

            $insert_array = array(
                'Benutzer'               => $_SESSION['benutzer_id'],
                'Datum'                  => time(),
                'Datum_Veroffentlichung' => $date_published,
                'Name1'                  => $titel,
                'Name2'                  => $titel,
                'Name3'                  => $titel,
                'Beschreibung1'          => $content,
                'Beschreibung2'          => $content,
                'Beschreibung3'          => $content,
                'Textbilder1'            => base64_decode(Arr::getPost('screenshots')),
                'Genre'                  => Arr::getPost('Genre'),
                'Vertrieb'               => Arr::getPost('Vertrieb'),
                'Hersteller'             => Arr::getPost('Hersteller'),
                'Asin'                   => Arr::getPost('Asin'),
                'Plattform'              => Arr::getPost('Plattform'),
                'Preis'                  => Arr::getPost('Preis'),
                'Shopurl'                => Arr::getPost('Shopurl'),
                'Shop'                   => Arr::getPost('Shop'),
                'Bild'                   => Arr::getPost('newImg_1'),
                'Links'                  => Arr::getPost('Links'),
                'Galerien'               => $gal,
                'Hits'                   => 0,
                'Sektion'                => AREA,
                'TopProduct'             => Arr::getPost('TopProduct'),
                'Aktiv'                  => Arr::getPost('Aktiv'));
            $this->_db->insert_query('produkte', $insert_array);
            $new_id = $this->_db->insert_id();
            $doc = 'index.php?p=products&area=' . AREA . '&action=showproduct&id=' . $new_id . '&name=' . translit($titel);

            if (Arr::getPost('ToNavi') == 1) {
                $na = explode('|', $_POST['NaviCat2']);
                $ParentId = $na[1];
                $NaCat = $na[0];

                $insert_array = array(
                    'ParentId' => intval($ParentId),
                    'NaviCat'  => $NaCat,
                    'Sektion'  => AREA,
                    'Titel_1'  => $titel,
                    'Titel_2'  => $titel,
                    'Titel_3'  => $titel,
                    'Dokument' => $doc,
                    'group_id' => implode(',', Arr::getPost('Groups')),
                    'Ziel'     => '_self',
                    'Position' => intval(Arr::getPost('PosQN')));
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
                'url'  => BASE_URL . '/index.php?p=products&area=' . AREA . '&action=showproduct&id=' . $new_id . '&name=' . translit($titel),
                'lang' => $_SESSION['admin_lang']);

            $cron_array = array(
                'datum'   => time(),
                'type'    => 'sys',
                'modul'   => 'ping',
                'title'   => $titel,
                'options' => serialize($options),
                'aktiv'   => 1);
            $this->__object('Cron')->add($cron_array);

            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал новый товар (' . $titel . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('Navis', $this->__object('AdminCore')->getNavigation(AREA));
        $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
        $this->_view->assign('Content', $this->__object('Editor')->load('admin', ' ', 'Content', 350, 'Content'));
        $this->_view->assign('genres', $this->genres());
        $this->_view->assign('title', $this->_lang['Products_new']);
        $this->_view->assign('mf', $this->manufaturer());
        $this->_view->content('/products/new.tpl');
    }

    public function edit($id) {
        if (!perm('products_newedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $id = intval($id);
        $LC = $this->__object('AdminCore')->getLangcode();
        $isdb = $categ = $Bild = $Gallery = $Sonstiges = $SetAll = '';

        if (Arr::getPost('save') == 1) {
            if (perm('screenshots') && isset($_POST['screenshots'])) {
                $isdb = ",Textbilder{$LC} = '" . $this->_db->escape(base64_decode($_POST['screenshots'])) . "' ";
            }

            $Bild = (Arr::getPost('NoImg') == 1) ? ",Bild = ''" : (!empty($_POST['newImg_1'])) ? ",Bild = '" . $this->_db->escape(Arr::getPost('newImg_1')) . "'" : '';

            if (Arr::getPost('langcode') == 1) {
                $dp = explode('.', $_POST['Datum_Veroffentlichung']);
                $date_published = mktime(0, 0, 1, $dp[1], $dp[0], $dp[2]);
                $titel = Arr::getPost('Titel');

                if (Arr::getPost('saveAllLang') == 1) {
                    $SetAll = "
							,Name2 = '" . $this->_db->escape($titel) . "'
							,Beschreibung2 = '" . $this->_db->escape($_POST['Content']) . "'
							,Name3 = '" . $this->_db->escape($titel) . "'
							,Beschreibung3 = '" . $this->_db->escape($_POST['Content']) . "'
							";
                }

                $Gallery = isset($_POST['Gallery']) ? ",Galerien='" . $this->_db->escape(implode(',', $_POST['Gallery'])) . "'" : '';
                $Sonstiges = "
						{$SetAll}
						,Genre='" . $this->_db->escape(Arr::getPost('Genre')) . "'
						,Vertrieb='" . $this->_db->escape(Arr::getPost('Vertrieb')) . "'
						,Hersteller='" . $this->_db->escape(Arr::getPost('Hersteller')) . "'
						,Preis='" . $this->_db->escape(Arr::getPost('Preis')) . "'
						,Shopurl='" . $this->_db->escape(Arr::getPost('Shopurl')) . "'
						,Shop='" . $this->_db->escape(Arr::getPost('Shop')) . "'
						,TopProduct='" . $this->_db->escape(Arr::getPost('TopProduct')) . "'
						,Links='" . $this->_db->escape(Arr::getPost('Links')) . "'
						,Datum_Veroffentlichung='" . $date_published . "'
						,Aktiv = '" . $this->_db->escape(Arr::getPost('Aktiv')) . "'";
            }

            $q = "UPDATE " . PREFIX . "_produkte
				SET
					Name{$LC} = '" . $this->_db->escape($titel) . "',
					Beschreibung{$LC} = '" . $this->_db->escape($_POST['Content']) . "'
					{$categ}
					{$Bild}
					{$Sonstiges}
					{$Gallery}
					{$isdb}
				WHERE Id='" . $id . "'";

            $this->_db->query($q);
            $doc = "index.php?p=products&area=" . AREA . "&action=showproduct&id=$id&name=" . translit($titel);

            if (Arr::getPost('ToNavi') == 1) {
                $na = explode('|', $_POST['NaviCat2']);
                $ParentId = $na[1];
                $NaCat = $na[0];

                $insert_array = array(
                    'ParentId' => intval($ParentId),
                    'NaviCat'  => $NaCat,
                    'Sektion'  => AREA,
                    'Titel_1'  => $titel,
                    'Titel_2'  => $titel,
                    'Titel_3'  => $titel,
                    'Dokument' => $doc,
                    'group_id' => implode(',', Arr::getPost('Groups')),
                    'Ziel'     => '_self',
                    'Position' => intval(Arr::getPost('PosQN')));
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
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил товар (' . $titel . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT
				*,
				Textbilder{$LC} as Textbilder,
				Name{$LC} as Titel,
				Beschreibung{$LC} as Inhalt
			FROM " . PREFIX . "_produkte WHERE Id='" . $id . "' LIMIT 1");

        $doc_s = 'index.php?p=products&area=' . AREA . '&action=showproduct&id=' . $id;

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

        $res->inQuicknavi = !empty($res_qn) ? $res_qn->Id : '';
        $res->inNavi = !empty($res_n) ? $res_n->Id : '';
        $res->Galerien = explode(',', $res->Galerien);
        $this->_view->assign('UserGroups', $this->__object('AdminCore')->groups());
        $this->_view->assign('Navis', $this->__object('AdminCore')->getNavigation(AREA));
        $this->_view->assign('Gallery', $this->__object('AdminCore')->categsGallery($_SESSION['a_area']));
        $this->_view->assign('Content', $this->__object('Editor')->load('admin', $res->Inhalt, 'Content', 350, 'Content'));
        $this->_view->assign('InlineShots', unserialize($res->Textbilder));
        $this->_view->assign('content', $res);
        $this->_view->assign('field_inline', 'Textbilder' . $LC);
        $this->_view->assign('genres', $this->genres());
        $this->_view->assign('mf', $this->manufaturer());
        $this->_view->assign('title', $this->_lang['Content_edit']);
        $this->_view->content('/products/edit.tpl');
    }

    public function copy($id) {
        if (!perm('products_newedit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_produkte WHERE Id='" . intval($id) . "' LIMIT 1");
        if (is_object($res)) {
            $insert_array = array(
                'Benutzer'               => $_SESSION['benutzer_id'],
                'Datum'                  => time(),
                'Datum_Veroffentlichung' => $res->Datum_Veroffentlichung,
                'Name1'                  => $res->Name1 . $this->_lang['DbCopy'],
                'Name2'                  => $res->Name2 . $this->_lang['DbCopy'],
                'Name3'                  => $res->Name3 . $this->_lang['DbCopy'],
                'Beschreibung1'          => $res->Beschreibung1,
                'Beschreibung2'          => $res->Beschreibung2,
                'Beschreibung3'          => $res->Beschreibung3,
                'Textbilder1'            => $res->Textbilder1,
                'Textbilder2'            => $res->Textbilder2,
                'Textbilder3'            => $res->Textbilder3,
                'Genre'                  => $res->Genre,
                'Vertrieb'               => $res->Vertrieb,
                'Hersteller'             => $res->Hersteller,
                'Wertung'                => $res->Wertung,
                'Asin'                   => $res->Asin,
                'Plattform'              => $res->Plattform,
                'Preis'                  => $res->Preis,
                'Shopurl'                => $res->Shopurl,
                'Shop'                   => $res->Shop,
                'Bild'                   => $res->Bild,
                'Links'                  => $res->Links,
                'Galerien'               => $res->Galerien,
                'Hits'                   => 0,
                'Sektion'                => $res->Sektion,
                'TopProduct'             => $res->TopProduct,
                'Aktiv'                  => 0);
            $this->_db->insert_query('produkte', $insert_array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал товар (' . $res->Name1 . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->backurl();
        }
    }

}
