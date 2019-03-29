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

class AdminSeo extends Magic {

    public function editKey($id, $text) {
        if (!perm('seo_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($text)) {
            $this->_db->query("UPDATE " . PREFIX . "_description SET Text = '" . Tool::cleanAllow($text, ' ') . "' WHERE id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('vkl', 1);
            $this->_view->assign('error', 1);
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['Validate_required']);
        }
        $this->showKey();
    }

    public function addKey($text) {
        if (!perm('seo_add')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($text)) {
            $this->_db->insert_query('description', array('Text' => Tool::cleanAllow($text, ' '), 'Aktiv' => 1));
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('error', 1);
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['Validate_required']);
        }
        $this->showKey();
    }

    public function deleteKey($id) {
        if (!perm('seo_del')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_description WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->script('save');
        $this->showKey();
    }

    public function cleanKey() {
        if (!perm('seo_del')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (perm('seo_del')) {
            Tool::cleanTable('description');
        }
        $this->__object('AdminCore')->script('save');
        $this->showKey();
    }

    public function activeKey($type, $id) {
        if (!perm('seo_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (isset($type) && !empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_description SET Aktiv='" . intval($type) . "' WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showKey();
    }

    public function getKey($id) {
        if (!perm('seo_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_description WHERE Id = '" . intval($id) . "' LIMIT 1");
            $this->_view->assign('vkl', 1);
            $this->_view->assign('row', $row);
        }
        $this->showKey();
    }

    protected function uploadKey() {
        $options = array(
            'rand'   => true,
            'type'   => 'file',
            'result' => 'data',
            'upload' => '/temp/cache/',
            'input'  => 'file_description',
        );
        return SX::object('Upload')->load($options);
    }

    public function importKey() {
        if (!perm('seo_imex')) {
            $this->__object('AdminCore')->noAccess();
        }

        $file = $this->uploadKey();
        if (!empty($file)) {
            $array = file(TEMP_DIR . '/cache/' . $file);
            $array = str_ireplace(array('\r\n', '\r', '\n', ';', ':', '.', '|'), ',', $array);
            $array_d = array_unique(explode(',', implode(',', $array)));

            foreach ($array_d as $value) {
                $value = Tool::cleanAllow(trim($value), ' ');
                if (!empty($value) && $this->_text->strlen($value) >= 3) {
                    $this->_db->insert_query('description', array('Text' => $value, 'Aktiv' => 1));
                }
            }
            File::delete(TEMP_DIR . '/cache/' . $file);
            $this->__object('AdminCore')->script('save');
        } else {
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['UploadFileError']);
        }
        $this->showKey();
    }

    public function exportKey() {
        if (!perm('seo_imex')) {
            $this->__object('AdminCore')->noAccess();
        }
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_description ORDER BY Text ASC");
        $export = '';
        while ($row = $sql->fetch_object()) {
            $export .= $row->Text . "\r\n";
        }
        $sql->close();
        File::download($export, 'Экспорт_фраз_от_' . date('d-m-Y') . '.txt');
    }

    public function showKey() {
        $db_sort = " ORDER BY Text ASC";
        $nav_sort = '&amp;sort=text_asc';
        $textsort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'text_asc':
            default:
                $db_sort = 'ORDER BY Text ASC';
                $nav_sort = '&amp;sort=text_asc';
                $textsort = 'text_desc';
                break;
            case 'text_desc':
                $db_sort = 'ORDER BY Text DESC';
                $nav_sort = '&amp;sort=text_desc';
                $textsort = 'text_asc';
                break;
        }
        $this->_view->assign('textsort', $textsort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '. ');
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " WHERE (Text LIKE '%{$this->_db->escape($pattern)}%' ) ";
        }

        $limit = $this->__object('AdminCore')->limit(25);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_description {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=seo&amp;sub=description{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Description']);
        $this->_view->content('/seo/seo.tpl');
    }

    public function sendPing($name, $url) {
        if (!perm('seo_p_add')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($name) && !empty($url)) {
            $this->__object('RPC')->ping($name, $url);
            $this->__object('AdminCore')->script('save');
        } else {
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['Validate_required']);
        }
        $this->showPing();
    }

    public function editPing($id, $text) {
        if (!perm('seo_p_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($text)) {
            $this->_db->query("UPDATE " . PREFIX . "_ping SET Dokument = '" . preg_replace('#[^-a-z0-9._:/]#iu', '', $text) . "' WHERE id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('vkl', 1);
            $this->_view->assign('error', 1);
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['Validate_required']);
        }
        $this->showPing();
    }

    public function addPing($text) {
        if (!perm('seo_p_add')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($text)) {
            $this->_db->insert_query('ping', array('Dokument' => preg_replace('#[^-a-z0-9._:/]#iu', '', $text), 'Aktiv' => 1));
            $this->__object('AdminCore')->script('save');
        } else {
            $this->_view->assign('error', 1);
            $this->__object('AdminCore')->script('message', 5000, $this->_lang['Validate_required']);
        }
        $this->showPing();
    }

    public function deletePing($id) {
        if (!perm('seo_p_del')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_ping WHERE Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->script('save');
        $this->showPing();
    }

    public function cleanPing() {
        if (!perm('seo_p_del')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (perm('seo_del')) {
            Tool::cleanTable('ping');
        }
        $this->__object('AdminCore')->script('save');
        $this->showPing();
    }

    public function activePing($type, $id) {
        if (!perm('seo_p_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (isset($type) && !empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_ping SET Aktiv='" . intval($type) . "' WHERE Id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showPing();
    }

    public function getPing($id) {
        if (!perm('seo_p_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_ping WHERE Id = '" . intval($id) . "' LIMIT 1");
            $this->_view->assign('vkl', 1);
            $this->_view->assign('row', $row);
        }
        $this->showPing();
    }

    public function showPing() {
        $db_sort = " ORDER BY Dokument ASC";
        $nav_sort = '&amp;sort=text_asc';
        $textsort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'text_asc':
            default:
                $db_sort = 'ORDER BY Dokument ASC';
                $nav_sort = '&amp;sort=text_asc';
                $textsort = 'text_desc';
                break;
            case 'text_desc':
                $db_sort = 'ORDER BY Dokument DESC';
                $nav_sort = '&amp;sort=text_desc';
                $textsort = 'text_asc';
                break;
        }
        $this->_view->assign('textsort', $textsort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $_REQUEST['q'] = $pattern = preg_replace('#[^-a-z0-9._:/]#iu', '', $pattern);
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " WHERE (Dokument LIKE '%{$pattern}%' ) ";
        }

        $limit = $this->__object('AdminCore')->limit(10);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_ping {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=seo&amp;sub=ping{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Ping']);
        $this->_view->content('/seo/ping.tpl');
    }

    public function showSitemap() {
        $items = $areas = $news_cats = $articles_cats = array();
        $query = "SELECT * FROM " . PREFIX . "_sitemap_items ORDER BY Id ASC ; ";
        $query .= "SELECT Id, Name FROM " . PREFIX . "_sektionen WHERE Aktiv ='1' ; ";
        $query .= "SELECT Id, Name_" . $_SESSION['admin_lang_num'] . " AS Name FROM " . PREFIX . "_news_kategorie ;  ";
        $query .= "SELECT Id, Name_" . $_SESSION['admin_lang_num'] . " AS Name FROM " . PREFIX . "_artikel_kategorie";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row = $result->fetch_object()) {
                    $row->title = $this->_lang[$row->title];
                    $items[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $areas[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $news_cats[] = $row;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_object()) {
                    $articles_cats[] = $row;
                }
                $result->close();
            }
        }

        $row_e = SX::get('sitemap');
        $this->_view->assign('areas', $areas);
        $this->_view->assign('areas_form', explode(',', $row_e['areas']));
        $this->_view->assign('news_cats', $news_cats);
        $this->_view->assign('articles_cats', $articles_cats);
        $this->_view->assign('news_form', (!empty($row_e['news']) ? explode(',', $row_e['news']) : 0));
        $this->_view->assign('articles_form', (!empty($row_e['articles']) ? explode(',', $row_e['articles']) : 0));
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Sitemap'] . ' - ' . $this->_lang['Global_Settings']);
        $this->_view->content('/seo/sitemap.tpl');
    }

    public function saveSitemap() {
        $array = array(
            'areas'    => implode(',', Arr::getRequest('areas')),
            'news'     => implode(',', Arr::getRequest('news')),
            'articles' => implode(',', Arr::getRequest('articles')));
        SX::save('sitemap', $array);

        $change = Arr::getRequest('change');
        $prio = Arr::getRequest('prio');
        $aktiv = Arr::getRequest('aktiv');
        foreach (array_keys($change) as $id) {
            if (!empty($change[$id])) {
                $array = array(
                    'prio'    => $prio[$id],
                    'changef' => $change[$id],
                    'active'  => $aktiv[$id],
                );
                $this->_db->update_query('sitemap_items', $array, "id = '" . intval($id) . "'");
                $this->__object('AdminCore')->script('save');
            }
        }
        $this->__object('Redir')->redirect('index.php?do=seo&sub=sitemap');
    }

    public function startSitemap($tpl = '0', $type = '', $link = '') {
        set_time_limit(600);
        if ($type == 'cron' && !empty($link)) {
            $baseurl = $link;
        } else {
            $baseurl = SX::protocol() . $_SERVER['HTTP_HOST'] . str_replace(array('/lib/cron.php', '/admin/index.php'), '', $_SERVER['PHP_SELF']);
        }

        $o = '/';
        $u = '___';
        $items = $bereiche = $treffer = array();
        $query = "SELECT * FROM " . PREFIX . "_sitemap_items WHERE active = '1' ; ";
        $query .= "SELECT Id, Sprachcode FROM " . PREFIX . "_sprachen WHERE Aktiv = '1'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($r = $result->fetch_assoc()) {
                    $bereiche[] = $r;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_lang = $result->fetch_assoc()) {
                    $langs[] = $row_lang['Id'];
                    $sprachcode[$row_lang['Id']] = $row_lang['Sprachcode'];
                }
                $result->close();
            }
        }
        $row = SX::get('sitemap');
        $areas = explode(',', $row['areas']);
        $cats_news = explode(',', $row['news']);
        $cats_articles = explode(',', $row['articles']);

        foreach ($langs as $lang) {
            $row_lc = $this->_db->cache_fetch_assoc("SELECT Sprachcode FROM " . PREFIX . "_sprachen WHERE Id = '" . $lang . "' LIMIT 1");
            $langcode = $row_lc['Sprachcode'];
            if (is_file(LANG_DIR . $o . $langcode . '/rewrite.txt')) {
                $this->_view->configLoad(LANG_DIR . $o . $langcode . '/rewrite.txt');
                $lang_var = $this->_view->getConfigVars();
                foreach ($areas as $area) {
                    $items[] = $lang_var['startindex'] . $o . $area . $o . $u . 'always' . $u . '1.0';
                    $items[] = $lang_var['sitemap'] . $o . $area . $o . $u . 'always' . $u . '1.0';
                    $items[] = $lang_var['sitemap'] . $o . $lang_var['full'] . $o . '1' . $o . $u . 'always' . $u . '1.0';
                    $items[] = $area . $o . 'rss.xml' . $u . 'always' . $u . '1.0';
                    $items[] = $lang_var['guestbook'] . $o . $area . $o . $u . 'always' . $u . '0.5';
                    foreach ($bereiche as $i) {
                        switch ($i['title']) {
                            case 'Articles':
                                $items[] = $lang_var['rss'] . $o . $area . $o . 'articles.xml' . $u . 'always' . $u . '1.0';
                                $items[] = $lang_var['articles'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $items[] = $lang_var['articles'] . $o . $lang_var['articles_reviews'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $items[] = $lang_var['articles'] . $o . $lang_var['articles_previews'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $items[] = $lang_var['articles'] . $o . $lang_var['articles_specials'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Titel_{$lang} AS Titel, Inhalt_{$lang} AS Inhalt FROM " . PREFIX . "_artikel WHERE Aktiv = '1' AND Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['articles'] . $o . $area . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                    preg_match_all('/\[--NEU--\]/isu', $r['Inhalt'], $treffer);
                                    $num = count($treffer[0]) + 1;
                                    for ($k = 2; $k <= $num; $k++) {
                                        $items[] = $lang_var['articles'] . $o . $area . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $k . $o . $u . $i['changef'] . $u . $i['prio'];
                                    }
                                }

                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_artikel_kategorie WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['articles_archive'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'News':
                                $items[] = $lang_var['rss'] . $o . $area . $o . 'news.xml' . $u . 'always' . $u . '1.0';
                                $items[] = $lang_var['newsarchive'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Titel{$lang} AS Titel, News{$lang} AS News FROM " . PREFIX . "_news WHERE (Sektion = '" . $area . "' OR AlleSektionen = '1') AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['news'] . $o . $area . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                    preg_match_all('/\[--NEU--\]/isu', $r['News'], $treffer);
                                    $num = count($treffer[0]) + 1;
                                    for ($k = 2; $k <= $num; $k++) {
                                        $items[] = $lang_var['news'] . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $k . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                    }
                                }

                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_news_kategorie WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['newsarchive'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Downloads':
                                $items[] = $lang_var['downloads'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Kategorie, Name_{$lang} AS Name FROM " . PREFIX . "_downloads WHERE Aktiv = '1' AND Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['downloads'] . $o . $area . $o . $r['Kategorie'] . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }

                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_downloads_kategorie WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['downloads'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Gaming_cheats':
                                $items[] = $lang_var['cheats'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id,Plattform,Name_{$lang} AS Name FROM " . PREFIX . "_cheats WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['cheats'] . $o . $area . $o . $r['Plattform'] . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }

                                $sq = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['cheats'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Gallery':
                                $items[] = $lang_var['gallery'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['gallery'] . $o . $r['Id'] . $o . translit($r['Name']) . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                    $sqq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_galerie WHERE Kategorie = '" . $r['Id'] . "'");
                                    while ($rq = $sqq->fetch_assoc()) {
                                        $items[] = $lang_var['gallery'] . $o . $lang_var['galleryimages'] . $o . $rq['Id'] . $o . $r['Id'] . $o . translit($rq['Name']) . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                    }
                                }
                                break;

                            case 'Links':
                                $items[] = $lang_var['links'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Kategorie, Name_{$lang} AS Name FROM " . PREFIX . "_links WHERE Aktiv = '1' AND Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['links'] . $o . $area . $o . $r['Kategorie'] . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }

                                $sq = $this->_db->query("SELECT Id, Name_{$lang} as Name FROM " . PREFIX . "_links_kategorie WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['links'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'User_nameS':
                                $items[] = $lang_var['users'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id FROM " . PREFIX . "_benutzer WHERE Aktiv = '1' AND Profil_public = '1' AND Profil_Alle = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['userprofile'] . $o . $r['Id'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Manufacturer':
                                $items[] = $lang_var['manufacturer'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['manufacturer'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Products':
                                $items[] = $lang_var['products'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Name{$lang} AS Name, Beschreibung{$lang} AS Beschreibung FROM " . PREFIX . "_produkte WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['products'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                    preg_match_all('/\[--NEU--\]/isu', $r['Beschreibung'], $treffer);
                                    $num = count($treffer[0]) + 1;
                                    for ($k = 2; $k <= $num; $k++) {
                                        $items[] = $lang_var['products'] . $o . $area . $o . $r['Id'] . $o . translit($r['Name']) . $o . $lang_var['page'] . $o . $k . $o . $u . $i['changef'] . $u . $i['prio'];
                                    }
                                }
                                break;

                            case 'Faq':
                                $items[] = $lang_var['faq'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_faq_kategorie WHERE Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['faq'] . $o . $r['Id'] . $o . $area . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                $sq = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_faq WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['faq'] . $o . $lang_var['show'] . $o . $r['Id'] . $o . $area . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Roadmaps':
                                $items[] = $lang_var['roadmap'] . $o . '1' . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_roadmap WHERE Aktiv = '1' AND Sektion = '" . $area . "'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['roadmap'] . $o . $lang_var['roadmap_etap'] . $o . $r['Id'] . $o . '0' . $o . '1' . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                    $items[] = $lang_var['roadmap'] . $o . $lang_var['roadmap_etap'] . $o . $r['Id'] . $o . '1' . $o . '1' . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Polls':
                                $items[] = $lang_var['poll'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $items[] = $lang_var['pollarchive'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Titel_{$lang} AS Titel FROM " . PREFIX . "_umfrage WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['poll'] . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;

                            case 'Content':
                                $sq = $this->_db->query("SELECT Id, Titel{$lang} AS Titel, Inhalt{$lang} AS Inhalt FROM " . PREFIX . "_content WHERE Sektion = '" . $area . "' AND Aktiv = '1'");
                                while ($r = $sq->fetch_assoc()) {
                                    $items[] = $lang_var['content'] . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                    preg_match_all('/\[--NEU--\]/isu', $r['Inhalt'], $treffer);
                                    $num = count($treffer[0]) + 1;
                                    for ($k = 2; $k <= $num; $k++) {
                                        $items[] = $lang_var['content'] . $o . $r['Id'] . $o . translit($r['Titel']) . $o . $area . $o . $k . $o . $u . $i['changef'] . $u . $i['prio'];
                                    }
                                }
                                break;

                            case 'Calendar':
                                $items[] = $lang_var['calendar'] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                $sq = $this->_db->query("SELECT Id, Datum FROM " . PREFIX . "_kalender WHERE Typ = 'public'");
                                while ($r = $sq->fetch_assoc()) {
                                    $date = explode('-', $r['Datum']);
                                    $items[] = $lang_var['calendar'] . $o . $lang_var['calendar_events'] . $o . 'public' . $o . $date[1] . $o . $date[2] . $o . $date[0] . $o . $area . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                break;
                        }
                    }
                }

                $items[] = $lang_var['imprint'] . $o . $u . 'always' . $u . '1.0';
                foreach ($bereiche as $i) {
                    switch ($i['title']) {
                        case 'Global_Shop':
                            $items[] = $lang_var['shop'] . $o . 'start' . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $lang_var['shop_agb'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $lang_var['shippingcost'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $lang_var['privacy'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $lang_var['refusal'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['shop'] . $o . $lang_var['search'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $sq = $this->_db->query("SELECT Id, Kategorie, Titel_{$lang} AS Titel FROM " . PREFIX . "_shop_produkte WHERE Aktiv = '1'");
                            while ($r = $sq->fetch_assoc()) {
                                $items[] = $lang_var['shop'] . $o . $lang_var['shop_product'] . $o . $r['Id'] . $o . $r['Kategorie'] . $o . translit($r['Titel']) . $o . $u . $i['changef'] . $u . $i['prio'];
                            }

                            $l = SX::get('shop.Produkt_Limit_Seite');
                            $sq = $this->_db->query("SELECT Id, Name_{$lang} as Name FROM " . PREFIX . "_shop_kategorie");
                            while ($r = $sq->fetch_assoc()) {
                                $items[] = $lang_var['shop'] . $o . $lang_var['shop_products'] . $o . $r['Id'] . $o . $r['Sektion'] . $o . $l . $o . translit($r['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                            }
                            break;

                        case 'Forums_nt':
                            $items[] = $lang_var['rss'] . $o . $area . $o . 'forum.xml' . $u . 'always' . $u . '1.0';
                            $items[] = $lang_var['forums'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['forumshelp'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $items[] = $lang_var['last24'] . $o . $u . $i['changef'] . $u . $i['prio'];
                            $sq = $this->_db->query("SELECT id, title, group_id FROM " . PREFIX . "_f_forum WHERE active = '1'");
                            while ($r = $sq->fetch_assoc()) {
                                if (in_array('2', explode(',', $r['group_id']))) {
                                    $items[] = $lang_var['forum'] . $o . $r['id'] . $o . translit($r['title']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                                $sqq = $this->_db->query("SELECT id, title, replies FROM " . PREFIX . "_f_topic WHERE forum_id = '" . $r['id'] . "'");
                                while ($rq = $sqq->fetch_assoc()) {
                                    if (in_array('2', explode(',', $r['group_id']))) {
                                        $items[] = $lang_var['topic'] . $o . $rq['id'] . $o . $r['id'] . $o . translit($rq['title']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                        if ($rq['replies'] >= 15) {
                                            for ($k = 2; $rq['replies'] >= 15; $k++) {
                                                $items[] = $lang_var['topic'] . $o . $rq['id'] . $o . $r['id'] . $o . $k . $o . translit($rq['title']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                                $rq['replies'] = $rq['replies'] - 15;
                                            }
                                        }
                                        $items[] = $lang_var['related'] . $o . $rq['id'] . $o . $u . $i['changef'] . $u . $i['prio'];
                                        $sqqq = $this->_db->query("SELECT id, title, message FROM " . PREFIX . "_f_post WHERE topic_id = '" . $rq['id'] . "'");
                                        while ($rqq = $sqqq->fetch_assoc()) {
                                            $rqq['title'] = !empty($rqq['title']) ? $rqq['title'] : $this->_text->chars(strip_tags($this->clean($rqq['message'])), 60, '');
                                            $items[] = $lang_var['postprint'] . $o . $rqq['id'] . $o . $rq['id'] . $o . translit($rqq['title']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                        }
                                    }
                                }
                            }
                            $sqw = $this->_db->query("SELECT id, title, group_id FROM " . PREFIX . "_f_category");
                            while ($rw = $sqw->fetch_assoc()) {
                                if (in_array('2', explode(',', $rw['group_id']))) {
                                    $items[] = $lang_var['forums'] . $o . $rw['id'] . $o . translit($rw['title']) . $o . $u . $i['changef'] . $u . $i['prio'];
                                }
                            }

                            $sqh = $this->_db->query("SELECT Id, Name_{$lang} AS Name FROM " . PREFIX . "_f_hilfe WHERE Aktiv = '1'");
                            while ($rh = $sqh->fetch_assoc()) {
                                $items[] = $lang_var['forumshelp'] . $o . $rh['Id'] . $o . translit($rh['Name']) . $o . $u . $i['changef'] . $u . $i['prio'];
                            }
                            break;
                    }
                }
            }
        }

        $items = array_unique($items);
        $data = array();
        $i = 0;
        $chunk = array_chunk($items, 50000);

        $links = array();
        foreach ($chunk as $value) {
            $i++;
            $num = $i == 1 ? NULL : $i;
            $xml = $this->createXml($baseurl, $value, $u, $o);
            if (File::set(SX_DIR . '/sitemap' . $num . '.xml', $xml)) {
                $this->compress(SX_DIR . '/sitemap' . $num . '.xml', SX_DIR . '/sitemap' . $num . '.xml.gz');
                $links[] = $baseurl . '/sitemap' . $num . '.xml';
            }
            if ($tpl == '1') {
                $data[$num] = highlight_string($xml, true);
            }
        }

        $this->newsSitemap($langs, $sprachcode, $cats_news, $cats_articles, $lang_var, $baseurl);

        if ($tpl == '1') {
            $this->_view->assign('data', $data);
            $this->_view->assign('title', $lang_var['Sitemap']);
            $this->_view->content('/seo/sitemap_set.tpl');
        }
        $this->robots($baseurl, $links);
        $this->pingSitemap($baseurl, $links);
    }

    /* Оповещаем серверов о обновлении карты сайты */
    protected function pingSitemap($baseurl, $links) {
        if (stripos($baseurl, 'localhost') === false) {
            foreach ($links as $link) {
                $link = urlencode($link);
                $urls = array(
                    'http://google.com/webmasters/sitemaps/ping?sitemap=' . $link,
                    'http://submissions.ask.com/ping?sitemap=' . $link,
                    'http://webmaster.live.com/ping.aspx?siteMap=' . $link,
                    'http://www.bing.com/webmaster/ping.aspx?siteMap=' . $link,
                    'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=SitemapWriter&url=' . $link,
                    'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=' . $link,
                );
                foreach ($urls as $url) {
                    $this->ping($url, 5);
                }
            }
        }
    }

    /* Выполняем пинг */
    protected function ping($target, $time = 10) {
        $target = parse_url($target);
        if (is_array($target)) {
            $target += array('host' => '', 'port' => 80, 'path' => '/', 'query' => '');
            if (!empty($target['host'])) {
                $fp = fsockopen($target['host'], $target['port'], $errno, $errstr, $time);
                if ($fp) {
                    $out = 'GET ' . $target['path'] . $target['query'] . ' ' . HTTP . PE;
                    $out .= 'User-Agent: SX CMS' . PE;
                    $out .= 'Connection: Close' . PE . PE;
                    fwrite($fp, $out);
                    fclose($fp);
                    return true;
                }
            }
        }
        return false;
    }

    /* Создаем файл robots.txt */
    protected function robots($baseurl, $links) {
        $out = "User-agent: *\n";
        $out .= "Crawl-delay: 10\n";
        $out .= "Disallow: /lang/\n";
        $out .= "Disallow: /admin/\n";
        $out .= "Disallow: /class/\n";
        $out .= "Disallow: /action/\n";
        $out .= "Disallow: /config/\n";
        $out .= "Host: " . str_replace(array('http://', 'https://'), '', $baseurl) . "\n";
        foreach ($links as $link) {
            $out .= "Sitemap: " . $link . "\n";
            $out .= "Sitemap: " . $link . ".gz\n";
        }
        File::set(SX_DIR . '/robots.txt', $out);
    }

    /* Создаем структуру карты сайта */
    protected function createXml($baseurl, $array, $u, $o) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<?xml-stylesheet type=\"text/xsl\" href=\"" . $baseurl . "/lib/gss/gss.xsl\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";
        foreach ($array as $i) {
            $value = explode($u, $i);
            $xml .= "<url>\n\t";
            $xml .= "<loc>" . $baseurl . $o . $value['0'] . "</loc>\n\t";
            $xml .= "<changefreq>" . $value['1'] . "</changefreq>\n\t";
            $xml .= "<priority>" . $value['2'] . "</priority>\n";
            $xml .= "</url>\n";
        }
        $xml .= '</urlset>';
        return $xml;
    }

    /* Создаем карту новостей и статей */
    protected function newsSitemap($langs, $sprachcode, $cats_news, $cats_articles, $lang_var, $baseurl) {
        $xml_n = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml_n .= "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\">\n";
        $stime = time();
        $seitenname = SX::get('system.Seitenname');

        foreach ($langs as $lang) {
            foreach ($cats_news as $cat) {
                $c = $this->_db->query("SELECT Id FROM " . PREFIX . "_news_kategorie WHERE Id = '" . $cat . "'");
                while ($r = $c->fetch_assoc()) {
                    $n = $this->_db->query("SELECT Id, Sektion, ZeitStart, Tags, Titel{$lang} AS Titel FROM " . PREFIX . "_news WHERE Kategorie = '" . $r['Id'] . "' AND	Aktiv = '1' AND	ZeitStart < " . $stime . " AND (ZeitEnde > " . $stime . " OR ZeitEnde = '0')");
                    while ($rn = $n->fetch_assoc()) {
                        $xml_n .= "<url>\n\t";
                        $xml_n .= "<loc>" . $baseurl . "/" . $lang_var['news'] . "/" . $rn['Sektion'] . "/" . $rn['Id'] . "/" . translit($rn['Titel']) . "/</loc>\n\t";
                        $xml_n .= "<news:news>\n\t\t";
                        $xml_n .= "<news:publication>\n\t\t\t";
                        $xml_n .= "<news:name>" . $seitenname . "</news:name>\n\t\t\t";
                        $xml_n .= "<news:language>" . $sprachcode[$lang] . "</news:language>\n\t\t";
                        $xml_n .= "</news:publication>\n\t\t";
                        $xml_n .= "<news:publication_date>" . date('Y-m-d', $rn['ZeitStart']) . "T" . date('h:i:s', $rn['ZeitStart']) . "Z</news:publication_date>\n\t\t";
                        $xml_n .= "<news:title>" . $rn['Titel'] . "</news:title>\n\t";
                        $xml_n .= "<news:keywords>" . $rn['Tags'] . "</news:keywords>\n\t";
                        $xml_n .= "</news:news>\n";
                        $xml_n .= "</url>\n";
                    }
                }
            }

            foreach ($cats_articles as $cat) {
                $c = $this->_db->query("SELECT Id FROM " . PREFIX . "_artikel_kategorie WHERE Id = '" . $cat . "'");
                while ($r = $c->fetch_assoc()) {
                    $n = $this->_db->query("SELECT Id, Sektion, ZeitStart, Tags, Titel_{$lang} AS Titel FROM " . PREFIX . "_artikel WHERE Kategorie = '" . $r['Id'] . "' AND	Aktiv = '1' AND	ZeitStart < " . $stime . " AND (ZeitEnde > " . $stime . " OR ZeitEnde = '0')");
                    while ($rn = $n->fetch_assoc()) {
                        $xml_n .= "<url>\n\t";
                        $xml_n .= "<loc>" . $baseurl . "/" . $lang_var['articles'] . "/" . $rn['Sektion'] . "/" . $rn['Id'] . "/" . translit($rn['Titel']) . "/</loc>\n\t";
                        $xml_n .= "<news:news>\n\t\t";
                        $xml_n .= "<news:publication>\n\t\t\t";
                        $xml_n .= "<news:name>" . $seitenname . "</news:name>\n\t\t\t";
                        $xml_n .= "<news:language>" . $sprachcode[$lang] . "</news:language>\n\t\t";
                        $xml_n .= "</news:publication>\n\t\t";
                        $xml_n .= "<news:publication_date>" . date('Y-m-d', $rn['ZeitStart']) . "T" . date('h:i:s', $rn['ZeitStart']) . "Z</news:publication_date>\n\t\t";
                        $xml_n .= "<news:title>" . $rn['Titel'] . "</news:title>\n\t";
                        $xml_n .= "<news:keywords>" . $rn['Tags'] . "</news:keywords>\n\t";
                        $xml_n .= "</news:news>\n";
                        $xml_n .= "</url>\n";
                    }
                }
            }
        }

        $xml_n .= '</urlset>';

        File::set(SX_DIR . '/news.xml', $xml_n);
    }

    protected function clean($text) {
        $text = preg_replace('#\[(\/?)(hide|mod|reg|quote|spoiler|php|code|email|url|highlight|youtube|u|i|b|s|img|face|size|color|left|center|right|list|justify)([^\]]*)\]#isu', '', $text);
        return $text;
    }

    /* Гзип версия карты сайта */
    protected function compress($src, $dst) {
        $data = File::get($src);
        $zp = gzopen($dst, 'w9');
        gzwrite($zp, $data);
        gzclose($zp);
    }

    /* Метод выводит список страниц */
    public function showTags() {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        $db_sort = " ORDER BY page ASC";
        $nav_sort = '&amp;sort=page_asc';
        $pagesort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'page_asc':
            default:
                $db_sort = 'ORDER BY page ASC';
                $nav_sort = '&amp;sort=page_asc';
                $pagesort = 'page_desc';
                break;
            case 'page_desc':
                $db_sort = 'ORDER BY page DESC';
                $nav_sort = '&amp;sort=page_desc';
                $pagesort = 'page_asc';
                break;
        }
        $this->_view->assign('pagesort', $pagesort);

        $pattern = Arr::getRequest('q');
        if (!empty($pattern)) {
            $_REQUEST['q'] = $pattern = Tool::cleanUrl($pattern);
            $def_search_n = "&amp;q=" . urlencode($pattern);
            $def_search = " WHERE (page LIKE '%{$this->_db->escape($pattern)}%' ) ";
        }

        $limit = $this->__object('AdminCore')->limit(Arr::getRequest('limit', 20));
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_seotags {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=seo&amp;sub=seotags{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Seotags']);
        $this->_view->content('/seo/seotags.tpl');
    }

    /* Метод добавления пункта */
    public function addTags() {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1 && !empty($_POST['url'])) {
            $this->checkTags();
        }
        $this->_view->content('/seo/seotags_add.tpl');
    }

    /* Метод редактирования пункта */
    public function editTags($id) {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1 && !empty($_POST['url'])) {
            $this->checkTags();
        }
        $items = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_seotags WHERE id = '" . intval($id) . "' LIMIT 1");
        $this->_view->assign('items', $items);
        $this->_view->content('/seo/seotags_edit.tpl');
    }

    /* Метод обновляет запись таблицы */
    protected function updateTags($array) {
        $array = array(
            'page'        => $array['page'],
            'title'       => $array['title'],
            'keywords'    => $array['keywords'],
            'description' => $array['description'],
            'canonical'   => $array['canonical'],
            'aktiv'       => $array['aktiv'],
        );
        $this->_db->update_query('seotags', $array, "page='" . $this->_db->escape($array['page']) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал сео-теги на странице (' . $array['page'] . ')', '0', $_SESSION['benutzer_id']);
    }

    /* Метод добавляет запись в таблицу */
    protected function insertTags($array) {
        $insert_array = array(
            'page'        => $array['page'],
            'title'       => $array['title'],
            'keywords'    => $array['keywords'],
            'description' => $array['description'],
            'canonical'   => $array['canonical'],
            'aktiv'       => $array['aktiv']);
        $this->_db->insert_query('seotags', $insert_array);
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' назначил сео-теги на странице (' . $array['page'] . ')', '0', $_SESSION['benutzer_id']);
    }

    /* Метод проверяет существует ли такая страница в базе, если нет то добавляет, если да то обновляет */
    protected function checkTags() {
        $page = $this->checkLink($this->_text->lower(Tool::cleanUrl(Arr::getPost('url'))));
        $array = array();
        $array['aktiv'] = Arr::getPost('aktiv', 0);
        $array['page'] = $page == '/' ? $page : ltrim($page, '/');
        $array['title'] = !empty($_POST['title']) ? Tool::cleanAllow(Arr::getPost('title'), ' ,') : '';
        $array['keywords'] = !empty($_POST['keywords']) ? Tool::cleanAllow(Arr::getPost('keywords'), ' ,') : '';
        $array['description'] = !empty($_POST['description']) ? Tool::cleanAllow(Arr::getPost('description'), ' ,.') : '';
        $array['canonical'] = !empty($_POST['canonical']) ? $this->_text->lower(Tool::cleanUrl(Arr::getPost('canonical'))) : '';
        $row = $this->_db->fetch_object("SELECT id FROM " . PREFIX . "_seotags WHERE page = '" . $this->_db->escape($array['page']) . "' LIMIT 1");
        if (is_object($row)) {
            $this->updateTags($array);
        } else {
            $this->insertTags($array);
        }
        $this->__object('AdminCore')->script('close');
    }

    /* Метод удаляет пункт по id */
    public function deleteTags($id) {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_seotags WHERE id='" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showTags();
    }

    /* Метод активирует/деактивирует пункт */
    public function activeTags($id) {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $this->_db->query("UPDATE " . PREFIX . "_seotags SET aktiv='" . intval(Arr::getRequest('type')) . "' WHERE id = '" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showTags();
    }

    /* Метод очищает таблицу seotags */
    public function cleanTags() {
        if (!perm('seo')) {
            $this->__object('AdminCore')->noAccess();
        }
        Tool::cleanTable('seotags');
        $this->__object('Redir')->redirect('index.php?do=seo&sub=seotags');
    }

    protected function checkLink($link) {
        $host = trim($_SERVER['HTTP_HOST'], '\/');
        $host2 = str_replace('www', '', $host);
        $array = array(
            'http://' . $host, 'http://' . $host . '/', 'https://' . $host, 'https://' . $host . '/',
            'http://' . $host2, 'http://' . $host2 . '/', 'https://' . $host2, 'https://' . $host2 . '/',
            $host, $host . '/', $host2, $host2 . '/'
        );
        return str_replace($array, '', $link);
    }

}
