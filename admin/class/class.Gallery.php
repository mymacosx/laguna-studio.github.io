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

class Gallery extends Magic {

    protected $_settings;
    protected $_maxsize = 1285;
    protected $Lc;
    protected $_no_allowed = array('php', 'php3', 'php4', 'php5', 'php6', 'phtml', 'phps', 'cgi', 'pl', 'py', 'sh', 'xl', 'htaccess');

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->_settings = (object) SX::get('galerie');
        $this->_view->assign('gs', $this->_settings);
    }

    public function recent() {
        $this->_view->assign('NewGalleryEntries', $this->load(SX::get('section.LimitNewGalleries')));
        return $this->_view->fetch(THEME . '/gallery/gallery_new_start.tpl');
    }

    public function addFavorite($id, $galid) {
        $out = NULL;
        if ($this->_settings->Favoriten && $_SESSION['user_group'] != '2') {
            $insert_array = array(
                'Benutzer'   => $_SESSION['benutzer_id'],
                'Bild_Id'    => $id,
                'Galerie_Id' => $galid);
            $this->_db->insert_query('galerie_bilderfavoriten', $insert_array);
            $out = '<br /><small>' . $this->_lang['Gallery_AddedFavorite'] . '</small>';
        }
        SX::output($out, true);
    }

     /* Метод формирования ссылок на изображения */
    protected function thumb($image, $id, $width = 140) {
        $result = NULL;
        if (!empty($image)) {
            $file = md5($image . '_' . $id . '_' . $width) . Tool::extension($image, true);
            if (is_file(SX_DIR . '/temp/cache/' . $file)) {
                $result = BASE_URL . '/temp/cache/' . $file;
            } else {
                $result = BASE_URL . '/lib/image.php?action=gallery&amp;width=' . $width . '&amp;image=' . $id;
            }
        }
        return $result;
    }

    public function includedGallery($gals, $tpl = 'galleries_included.tpl') {
        $galleries = $where = array();
        $gals = explode(',', $gals);
        foreach ($gals as $gid) {
            if (!empty($gid)) {
                $where[] = "Id = '" . intval($gid) . "'";
            }
        }

        $order = Tool::randQuery(array('Id', 'Kategorie', 'Parent_Id', 'Name_1', 'Datum', 'Autor', 'Bilder'));
        $res = $this->_db->query("SELECT
                Id,
                Kategorie,
                Name_{$this->Lc} AS GalName
        FROM
                " . PREFIX . "_galerie
        WHERE
                (" . implode(' OR ', $where) . ")
        AND
                Aktiv = '1'
        ORDER BY " . $order . " LIMIT 10");
        while ($row_g = $res->fetch_object()) {
            $order_sql = Tool::randQuery(array('Id', 'Galerie_Id', 'Name_1', 'Voting', 'Datum', 'Klicks', 'Autor'));
            $row = $this->_db->fetch_object("SELECT SQL_CALC_FOUND_ROWS Id, Bildname FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '$row_g->Id' ORDER BY " . $order_sql . " LIMIT 1");
            $row_g->ICount = $this->_db->found_rows();
            if (isset($row->Bildname)) {
                $row_g->Img = $this->thumb($row->Bildname, $row->Id, $this->_settings->Bilder_Mittel);
                $row_g->Link = 'index.php?p=gallery&amp;action=showgallery&amp;id=' . $row_g->Id . '&amp;categ=' . $row_g->Kategorie . '&amp;name=' . translit($row_g->GalName) . '&amp;area=' . AREA;
                $galleries[] = $row_g;
            }
        }
        $this->_view->assign('externGals', $galleries);
        return $this->_view->fetch(THEME . '/gallery/' . $tpl);
    }

    public function delFavorite($id) {
        $out = NULL;
        if ($this->_settings->Favoriten && $_SESSION['user_group'] != 2) {
            $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Bild_Id = '" . intval($id) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "'");
            $out = '<br /><small>' . $this->_lang['Gallery_DeletedFavorite'] . '</small>';
        }
        SX::output($out, true);
    }

    public function delAllFavorites($id, $categ, $name) {
        $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Galerie_Id = '" . intval($id) . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "'");
        $this->__object('Redir')->seoRedirect('index.php?p=gallery&action=showgallery&id=' . $id . '&categ=' . $categ . '&name=' . $name . '&area=' . AREA);
    }

    public function slide($id, $ascdesc = 'desc', $blanc = 0, $firstid = '', $top = 0, $categ = 0) {
        $first = max(0, intval(Arr::getGet('first')) - 1);
        $last = max($first + 1, intval(Arr::getGet('last')) - 1);
        if ($top == 1) {
            $ascdesc = '';
            $orderby = 'Klicks DESC';
        } else {
            $orderby = 'Id';
        }
        $length = $last - $first + 1;
        $this->_view->configLoad(LANG_DIR . '/' . $_SESSION['lang'] . '/rewrite.txt');
        $c_vars = $this->_view->getConfigVars();
        $more = array();
        $sql_more = $this->_db->query("SELECT Klicks,Id,Galerie_Id,Bildname,Name_{$this->Lc} AS ImageName, Beschreibung_{$this->Lc} AS ImageDescr FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . intval($id) . "' ORDER BY {$orderby} {$ascdesc}");
        while ($row_more = $sql_more->fetch_object()) {
            $link_blanc = ($blanc == 1) ? '&amp;blanc=1&amp;first_id=' . $firstid : '';
            $imgage_link = BASE_URL . '/index.php?p=gallery&amp;action=showimage&amp;id=' . $row_more->Id . '&amp;galid=' . $row_more->Galerie_Id . "&amp;ascdesc=desc&amp;categ=" . $categ . '&amp;area=' . AREA . $link_blanc;
            if (SX::get('system.use_seo') == 1 && $blanc != 1) {
                $imgage_link = preg_replace('/index.php([?])p=gallery&amp;action=showimage&amp;id=([\d]*)&amp;galid=([\d]*)&amp;ascdesc=([\w-]*)&amp;categ=([\d]*)&amp;area=([\d]*)/iu', $c_vars['gallery'] . '/' . $c_vars['galleryimage'] . '/\\2/\\3/\\4/\\5/\\6/', $imgage_link);
            }
            $file = $this->thumb($row_more->Bildname, $row_more->Id, $this->_settings->Bilder_Klein);
            $images[] = "&lt;a href='$imgage_link'&gt; &lt;img alt='' border='0' src='" . $file. "' /&gt;&lt;/a&gt;";
            $row_more->ImageText = !empty($row_more->ImageName) ? '<strong>' . sanitize($row_more->ImageName) . '</strong><br />' . sanitize($row_more->ImageDescr) : '';
            $more[] = $row_more;
        }
        $sql_more->close();
        $total = count($images);
        $selected = array_slice($images, $first, $length);
        header('Content-Type: text/xml');
        $out = '<data>';
        $out .= '<total>' . $total . '</total>';
        foreach ($selected as $img) {
            $out .= '<image>' . $img . '</image>';
        }
        $out .= '</data>';
        SX::output($out, true);
    }

    public function search($query) {
        $value = NULL;
        $query = urldecode($query);
        if (!empty($query) && $this->_text->strlen($query) >= 2) {
            $result = $this->_db->query("SELECT Name_" . $this->Lc . " AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Aktiv='1' AND Sektion = '" . AREA . "' AND (Name_" . $this->Lc . " LIKE '%" . $this->_db->escape($query) . "%')");
            while ($row = $result->fetch_object()) {
                if ($this->_text->stripos($row->Name, $query) !== false) {
                    $value .= sanitize($row->Name) . PE;
                }
            }
            $result->close();
        }
        SX::output($value, true);
    }

    protected function load($limit = 5) {
        $galleries = array();
        $sql = $this->_db->query("SELECT SQL_CACHE
                a.Id,
                a.Kategorie,
                a.Name_{$this->Lc} AS Name,
                a.Beschreibung_{$this->Lc} AS Text,
                b.Bildname,
                b.Id AS BildId
            FROM
                " . PREFIX . "_galerie AS a
            LEFT JOIN
                " . PREFIX . "_galerie_bilder AS b
            ON
                    b.Galerie_Id = a.Id
            WHERE
                a.Aktiv = '1'
            AND
                a.Sektion = '" . AREA . "'
            AND
                b.Bildname IS NOT NULL
            GROUP BY b.Galerie_Id
            ORDER BY a.Datum DESC LIMIT " . intval($limit));
        while ($row = $sql->fetch_object()) {
            $row->Link = 'index.php?p=gallery&amp;action=showgallery&amp;id=' . $row->Id . '&amp;categ=' . $row->Kategorie . '&amp;name=' . translit($row->Name) . '&amp;area=' . AREA;
            $row->Text = strip_tags($row->Text);
            $row->Img = $this->thumb($row->Bildname, $row->BildId, $this->_settings->Bilder_Mittel);
            $galleries[] = $row;
        }
        $sql->close();
        return $galleries;
    }

    public function show() {
        $def_search_n = '&amp;q=empty';
        $def_search = $pattern_umlaut = '';

        $pattern = urldecode(Arr::getRequest('q'));
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $this->__object('Core')->monitor($pattern, 'gallery');
            $pattern_umlaut = sanitize($pattern);
            $def_search_n = "&amp;q=" . urlencode($pattern);

            if (!empty($_REQUEST['searchtype'])) {
                $like = $this->_db->escape($pattern);
                $like2 = $this->_db->escape($pattern_umlaut);
                switch ($_REQUEST['searchtype']) {
                    default:
                    case 'full':
                        $def_search = " AND ((Name_{$this->Lc} LIKE '%{$like}%' OR Text_{$this->Lc} LIKE '%{$like}%') OR (Name_{$this->Lc} LIKE '%{$like2}%' OR Text_{$this->Lc} LIKE '%{$like2}%'))";
                        break;
                    case 'tags':
                        $def_search = " AND ((Tags LIKE '%{$like}%' OR Tags LIKE '{$like},%' OR Tags LIKE '%,{$like}') OR (Tags LIKE '%{$like2}%' OR Tags LIKE '{$like2},%' OR Tags LIKE '%,{$like2}'))";
                        break;
                }
            }
        }

        if (isset($_REQUEST['ascdesc']) && $_REQUEST['ascdesc'] == 'desc') {
            $def_sort = " ORDER BY Name_{$this->Lc} DESC";
            $def_sort_n = '&amp;ascdesc=desc';
        } else {
            $def_sort = " ORDER BY Name_{$this->Lc} ASC";
            $def_sort_n = '&amp;ascdesc=asc';
        }

        if (isset($_REQUEST['searchtype']) && $_REQUEST['searchtype'] == 'tags') {
            $def_searcht_n = '&amp;searchtype=tags';
        } else {
            $def_searcht_n = '&amp;searchtype=full';
        }

        $limit = $this->_settings->Limit_Start;
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS Id, Name_{$this->Lc} AS Name, Text_{$this->Lc} AS Text, Bild, Tags FROM " . PREFIX . "_galerie_kategorien WHERE Aktiv='1' AND Sektion = '" . AREA . "' {$def_search} {$def_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $galleries = array();
        while ($row = $sql->fetch_object()) {
            if (!empty($pattern)) {
                if ($_SESSION['query_galerie_kategorien'] != $pattern_umlaut) {
                    $_SESSION['query_galerie_kategorien'] = $pattern_umlaut;
                }
                if ($_REQUEST['searchtype'] != 'tags') {
                    $row->Text = Tool::highlight($row->Text, $pattern_umlaut);
                }
            }

            if ($row->Tags) {
                $row->Tags = array_unique(explode(',', $row->Tags));
                sort($row->Tags);
            }
            $galleries[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('GalNavi', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=gallery{$def_search_n}{$def_searcht_n}{$def_sort_n}&amp;page={s}&amp;area=" . AREA . "\">{t}</a> "));
        }

        $tpl_array = array(
            'galleries'   => $galleries,
            'galsettings' => $this->_settings,
            'tagCloud'    => $this->tagcloud(),
            'def_sort_n'  => $def_sort_n);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['Gallery_Name'],
            'pagetitle' => $this->_lang['Gallery_Name'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/gallery/gallerycategs.tpl'));
        $this->_view->finish($seo_array);
    }

    public function included($galid) {
        $def_search = $pattern = $pattern_umlaut = '';
        $def_search_n = '&amp;q=empty';
        $def_sort_date = 'datedesc';
        $def_sort_author = 'userdesc';
        $def_sort_img_date = $def_sort_img_author = 'sorter_none';

        $_REQUEST['searchtype'] = (Arr::getRequest('searchtype') == 'full' || Arr::getRequest('searchtype') == 'tags') ? $_REQUEST['searchtype'] : 'full';
        $_REQUEST['sort'] = empty($_REQUEST['sort']) ? 'nameasc' : $_REQUEST['sort'];

        $pattern = urldecode(Arr::getRequest('q'));
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 2) {
            $this->__object('Core')->monitor($pattern, 'gallery');
            $pattern_umlaut = sanitize($pattern);
            $def_search_n = '&amp;q=' . urlencode($pattern);

            if (!empty($_REQUEST['searchtype'])) {
                $like = $this->_db->escape($pattern);
                $like2 = $this->_db->escape($pattern_umlaut);
                switch ($_REQUEST['searchtype']) {
                    default:
                    case 'full':
                        $def_search = " AND ((Name_{$this->Lc} LIKE '%{$like}%' OR Beschreibung_{$this->Lc} LIKE '%{$like}%') OR (Name_{$this->Lc} LIKE '%{$like2}%' OR Beschreibung_{$this->Lc} LIKE '%{$like2}%'))";
                        break;
                    case 'tags':
                        $def_search = " AND ((Tags LIKE '%{$like}%' OR Tags LIKE '{$like},%' OR Tags LIKE '%,{$like}') OR (Tags LIKE '%{$like2}%' OR Tags LIKE '{$like2},%' OR Tags LIKE '%,{$like2}'))";
                        break;
                }
            }
        }

        switch ($_REQUEST['sort']) {
            case 'namedesc':
                $def_sort = " ORDER BY Name_{$this->Lc} DESC";
                $def_sort_n = '&amp;sort=nameasc';
                $def_sort_name = 'nameasc';
                $def_sort_img_name = 'sorter_down';
                break;
            default:
            case 'nameasc':
                $def_sort = " ORDER BY Name_{$this->Lc} ASC";
                $def_sort_n = '&amp;sort=namedesc';
                $def_sort_name = 'namedesc';
                $def_sort_img_name = 'sorter_up';
                break;

            case 'datedesc':
                $def_sort = " ORDER BY Datum DESC";
                $def_sort_n = '&amp;sort=datedesc';
                $def_sort_date = 'dateasc';
                $def_sort_img_date = 'sorter_down';
                $def_sort_img_name = 'sorter_none';
                break;

            case 'dateasc':
                $def_sort = " ORDER BY Datum ASC";
                $def_sort_n = '&amp;sort=dateasc';
                $def_sort_date = 'datedesc';
                $def_sort_img_date = 'sorter_up';
                $def_sort_img_name = 'sorter_none';
                break;

            case 'userdesc':
                $def_sort = " ORDER BY Autor DESC";
                $def_sort_n = '&amp;sort=userdesc';
                $def_sort_author = 'userasc';
                $def_sort_img_author = 'sorter_down';
                $def_sort_img_name = 'sorter_none';
                break;

            case 'userasc':
                $def_sort = " ORDER BY Autor ASC";
                $def_sort_n = '&amp;sort=userasc';
                $def_sort_author = 'userdesc';
                $def_sort_img_author = 'sorter_up';
                $def_sort_img_name = 'sorter_none';
                break;
        }

        $limit = $this->_settings->Limit_Start;
        $a = Tool::getLimit($limit);
        $query = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Name_{$this->Lc} AS GalName, Beschreibung_{$this->Lc} AS GalText FROM " . PREFIX . "_galerie WHERE Parent_Id = '0' AND Kategorie = '" . intval($galid) . "' AND Sektion = '" . AREA . "' AND Aktiv = 1 {$def_search} {$def_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $galleries = array();
        while ($row = $query->fetch_object()) {
            if (!empty($pattern)) {
                if ($_SESSION['query_galerie_enthalten'] != $pattern_umlaut) {
                    $_SESSION['query_galerie_enthalten'] = $pattern_umlaut;
                }
                if ($_REQUEST['searchtype'] != 'tags') {
                    $row->GalText = Tool::highlight($row->GalText, $pattern_umlaut);
                }
            }

            if ($this->_settings->Zufall_Start == 'TRUE') {
                $order_sql = Tool::randQuery(array('Id', 'Galerie_Id', 'Name_1', 'Voting', 'Datum', 'Klicks', 'Autor'));
            } else {
                $order_sql = 'Id ' . ($this->_settings->Sortierung_Start == 'ASC' ? 'ASC' : 'DESC');
            }

            $querys = "SELECT
            Id,
                    Bildname
            FROM
                    " . PREFIX . "_galerie_bilder
            WHERE
                    Galerie_Id = '$row->Id'
            ORDER BY " . $order_sql . " LIMIT 1 ; ";
            $querys .= "SELECT COUNT(Id) AS ImageCount FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '$row->Id'";
            if ($this->_db->multi_query($querys)) {
                if (($result = $this->_db->store_result())) {
                    $LastImage = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $row->ImageCount = $result->fetch_object();
                    $result->close();
                }
            }
            $row->Thumb = '';
            if (is_object($LastImage)) {
                $file = $this->thumb($LastImage->Bildname, $LastImage->Id, $this->_settings->Bilder_Mittel);
                $row->Thumb = '<img class="gallery_categs_img" src="' . $file . '" border="0" alt="" />';
            }

            $row->Link = 'index.php?p=gallery&amp;action=showgallery&amp;id=' . $row->Id . '&amp;categ=' . Arr::getRequest('categ') . '&amp;name=' . translit($row->GalName) . '&amp;area=' . AREA;
            $row->Author = Tool::userName($row->Autor);
            $row->AuthorLink = 'index.php?p=user&amp;id=' . $row->Autor;
            $sub_galleries = array();
            $sql_sub = $this->_db->query("SELECT Id,Name_{$this->Lc} AS SubGalName FROM " . PREFIX . "_galerie WHERE Parent_Id = $row->Id AND Sektion = '" . AREA . "' AND Aktiv = 1");
            while ($row_sub = $sql_sub->fetch_object()) {
                $row_sub->Link = 'index.php?p=gallery&amp;action=showgallery&amp;id=' . $row_sub->Id . '&amp;categ=' . Arr::getRequest('categ') . '&amp;name=' . translit($row_sub->SubGalName) . '&amp;area=' . AREA;
                $sub_galleries[] = $row_sub;
            }
            $sql_sub->close();
            $row->subGalleries = $sub_galleries;
            if ($row->Tags) {
                $row->Tags = array_unique(explode(',', $row->Tags));
                sort($row->Tags);
            }
            $galleries[] = $row;
        }
        $query->close();

        $GalInf = $this->_db->cache_fetch_object("SELECT Name_{$this->Lc} AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Id = '" . intval(Arr::getRequest('categ')) . "' LIMIT 1");
        $GalName = sanitize($GalInf->Name);
        $thisLink = '<a href="index.php?p=gallery&amp;action=showincluded&amp;categ=' . $_REQUEST['categ'] . '&amp;name=' . translit($GalInf->Name) . '&amp;area=' . AREA . '">' . sanitize($GalInf->Name) . '</a>';
        if ($num > $limit) {
            $this->_view->assign('GalNavi', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=gallery&amp;action=showincluded&amp;categ=" . $_REQUEST['categ'] . "&amp;name=" . translit($GalName) . $def_search_n . "&amp;searchtype=" . $_REQUEST['searchtype'] . "&amp;page={s}&amp;sort=" . $_REQUEST['sort'] . "&amp;area=" . AREA . "\">{t}</a> "));
        }

        $tpl_array = array(
            'def_sort_n'          => $def_sort_n,
            'def_sort_name'       => $def_sort_name,
            'def_sort_img_name'   => $def_sort_img_name,
            'def_sort_date'       => $def_sort_date,
            'def_sort_img_date'   => $def_sort_img_date,
            'def_sort_author'     => $def_sort_author,
            'def_sort_img_author' => $def_sort_img_author,
            'galleries'           => $galleries,
            'categ'               => $_REQUEST['categ'],
            'galname'             => $GalName,
            'tagCloud'            => $this->tagcloud($_REQUEST['categ']));
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=gallery&amp;area=' . AREA . '">' . $this->_lang['Gallery_Name'] . '</a>' . $this->_lang['PageSep'] . $thisLink,
            'pagetitle' => $GalInf->Name . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'],
            'content'   => $this->_view->fetch(THEME . '/gallery/gallery.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Метод высчитывает вес тегов */
    protected function tagcloud($categ = '') {
        $tags = '';
        $cloud = array();
        $categ = intval($categ);
        if (!empty($categ)) {
            $where = "SELECT Tags FROM " . PREFIX . "_galerie WHERE Kategorie = '" . $categ . "'";
            $where_m = "FROM " . PREFIX . "_galerie WHERE Kategorie = '" . $categ . "'";
        } else {
            $where = "SELECT Tags FROM " . PREFIX . "_galerie_kategorien WHERE Aktiv='1' AND Sektion = '" . AREA . "'";
            $where_m = "FROM " . PREFIX . "_galerie_kategorien WHERE Aktiv='1' AND Sektion = '" . AREA . "'";
        }
        $query = $this->_db->query($where);
        while ($row = $query->fetch_object()) {
            if (!empty($row->Tags)) {
                $tags .= $row->Tags . ',';
            }
        }
        $query->close();

        $tags = array_unique(explode(',', $tags));
        $tags = array_map('trim', $tags);
        $tags = array_diff($tags, array(''));
        sort($tags);
        if (!empty($tags)) {
            $union = $array = array();
            foreach ($tags as $val) {
                $union[] = "SELECT COUNT(Id) AS Count $where_m AND (Tags LIKE '%{$val}%' OR Tags LIKE '{$val},%' OR Tags LIKE '%,{$val}')";
            }
            $sql = DB::get()->query(implode(' UNION ALL ', $union));
            while ($row = $sql->fetch_assoc()) {
                $array[] = $row;
            }
            $sql->close();

            foreach ($tags as $key => $val) {
                $obj = new stdClass;
                $obj->Name = $val;
                $obj->GCount = $array[$key]['Count'];
                switch ($obj->GCount) {
                    case ($obj->GCount <= 1):
                        $obj->Class = 'tagcloud1';
                        break;
                    case ($obj->GCount == 2):
                        $obj->Class = 'tagcloud2';
                        break;
                    case ($obj->GCount == 3):
                        $obj->Class = 'tagcloud3';
                        break;
                    case ($obj->GCount >= 4):
                        $obj->Class = 'tagcloud4';
                        break;
                }
                $cloud[] = $obj;
            }
        }
        return $cloud;
    }

    public function get($id) {
        $id = intval($id);
        $db_extra = $images_favs = '';
        if (Arr::getRequest('favorites') == 1 && $_SESSION['user_group'] != 2 && $this->_settings->Favoriten == 1) {
            $images = '';
            $qf = $this->_db->query("SELECT * FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Galerie_Id = $id AND Benutzer = '" . $_SESSION['benutzer_id'] . "'");
            while ($rf = $qf->fetch_object()) {
                $images[] = $rf->Bild_Id;
            }
            $qf->close();
            $db_extra = ($images) ? 'AND (Id = ' . implode(' OR Id = ', $images) . ')' : 'AND (Id = -1)';
        }

        if (!permission('gallery')) {
            $this->__object('Core')->noAccess();
        }

        $gallery_inf = $this->_db->cache_fetch_object("SELECT Id, Autor, Datum, Tags, Name_{$this->Lc} AS GalName, Beschreibung_{$this->Lc} AS GalText FROM " . PREFIX . "_galerie WHERE Id = $id LIMIT 1");
        if (!$gallery_inf) {
            $this->__object('Redir')->seoRedirect('index.php?p=gallery');
        }

        $sort = Arr::getRequest('ascdesc') == 'asc' ? 'asc' : 'desc';
        $navsort = strtolower($sort);

        $limit = (Arr::getRequest('pp') > 1) ? intval($_REQUEST['pp']) : $this->_settings->Bilder_Seite;
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS Klicks, Bildname, Id, Name_{$this->Lc} AS ImageName, Beschreibung_{$this->Lc} AS ImageText FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = $id {$db_extra} ORDER BY Id {$sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $row->Thumbnail_Gross = $this->thumb($row->Bildname, $row->Id, $this->_settings->Bilder_Gross);
            $row->PopUpText = !empty($row->ImageName) ? '<strong>' . (!empty($row->ImageName) ? sanitize($row->ImageName) : '?') . '</strong><br />' . (!empty($row->ImageText) ? $row->ImageText : '') : '';
            $row->Thumbnail = $this->thumb($row->Bildname, $row->Id, $this->_settings->Bilder_Mittel);
            $row->Comments = Tool::countComments('galerie', $row->Id);
            $this->_view->assign('row_i', $row);
            $items[] = $row;
        }
        $sql->close();

        $gallery_inf->AutorLink = '<a href="index.php?p=user&amp;id=' . $gallery_inf->Autor . '">' . Tool::userName($gallery_inf->Autor) . '</a>';
        $gallery_inf->Images = $num;
        $gallery_inf->TitleGalName = $gallery_inf->GalName;

        if ($num > $limit) {
            $fav_nav = (Arr::getRequest('favorites') == 1) ? '&amp;favorites=1' : '';
            $this->_view->assign('GalNavi', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?p=gallery&amp;action=showgallery&amp;id={$id}&amp;categ=" . $_REQUEST['categ'] . "&amp;name=" . translit($gallery_inf->GalName) . "&amp;ascdesc={$navsort}&amp;pp={$limit}&amp;page={s}{$fav_nav}&amp;area=" . AREA . "\">{t}</a> "));
        }

        $gallery_inf->First = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = $id ORDER BY Id DESC LIMIT 1");
        $sub_galleries = array();

        $query = "SELECT Id,Name_{$this->Lc} AS SubGalName FROM " . PREFIX . "_galerie WHERE Parent_Id = $gallery_inf->Id AND Sektion = '" . AREA . "' AND Aktiv = 1 ; ";
        $query .= "SELECT * FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Galerie_Id = $id AND Benutzer = '" . $_SESSION['benutzer_id'] . "'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($row_sub = $result->fetch_object()) {
                    $row_sub->Link = 'index.php?p=gallery&amp;action=showgallery&amp;id=' . $row_sub->Id . '&amp;categ=' . $_REQUEST['categ'] . '&amp;name=' . translit($row_sub->SubGalName) . '&amp;area=' . AREA;
                    $sub_galleries[] = $row_sub;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($rf = $result->fetch_object()) {
                    $images_favs[] = $rf->Bild_Id;
                }
                $result->close();
            }
        }

        $tpl_array = array(
            'Favorites'    => $images_favs,
            'subGalleries' => $sub_galleries,
            'Galsettings'  => $this->_settings,
            'Gallery_inf'  => $gallery_inf,
            'items'        => $items);
        $this->_view->assign($tpl_array);

        $headernav = $this->__object('Navigation')->path($id, 'galerie', 'gallery&amp;action=showgallery', 'id', 'Id', 'Name_' . $this->Lc, '', $this->_lang['Gallery_Name'], 'gallery', '1');

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $gallery_inf->GalName . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'],
            'generate'  => $gallery_inf->Tags,
            'content'   => $this->_view->fetch(THEME . '/gallery/' . ($this->_settings->GTyp == 'lightbox' ? 'lightbox.tpl' : 'galleryimages.tpl')));
        $this->_view->finish($seo_array);
    }

    public function image($image, $id) {
        if (!permission('gallery')) {
            $this->__object('Core')->noAccess();
        }

        $image = intval($image);
        $id = intval($id);
        if (empty($id)) {
            $row = $this->_db->cache_fetch_object("SELECT Galerie_Id FROM " . PREFIX . "_galerie_bilder WHERE Id = '" . $image . "' LIMIT 1");
            $id = $row->Galerie_Id;
        }

        if (Arr::getGet('download') == 1) {
            if ($this->_settings->Download == 1 && permission('gallery_download')) {
                $this->download($image, $id);
            }
        }

        $this->_db->query("UPDATE " . PREFIX . "_galerie_bilder SET Klicks=Klicks+1 WHERE Id = $image");
        $row = $this->_db->cache_fetch_object("SELECT Klicks, Bildname, Id, Name_{$this->Lc} AS ImageName, Beschreibung_{$this->Lc} AS ImageText FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = $id AND Id = $image LIMIT 1");
        if (!is_object($row)) {
            $this->__object('Redir')->seoRedirect('index.php?p=gallery');
        }

        $row->Image = $this->thumb($row->Bildname, $row->Id, $this->_settings->Bilder_Gross);
        if (is_file(UPLOADS_DIR . '/galerie/' . $row->Bildname)) {
            list($row->Image_Width, $row->Image_Height) = getimagesize(UPLOADS_DIR . '/galerie/' . $row->Bildname);
        }

        $ascdesc = !empty($_REQUEST['ascdesc']) && $_REQUEST['ascdesc'] == 'asc' ? 'asc' : 'desc';

        $i = 1;
        $curr_pic = '0';

        $query = "SELECT Id FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = $id ORDER BY Id {$ascdesc} LIMIT 1 ; ";
        $query .= "SELECT Id, Name_{$this->Lc} AS ImageName, Bildname, Beschreibung_{$this->Lc} AS ImageText FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = $id ORDER BY Id {$ascdesc} ; ";
        $query .= "SELECT * FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Galerie_Id = '" . $id . "' AND Benutzer = '" . $_SESSION['benutzer_id'] . "'";
        $gallery = array();
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $first = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($rowall = $result->fetch_object()) {
                    $n = $i++;
                    $rowall->number = $n;
                    if ($rowall->Id == $row->Id) {
                        $curr_pic = $rowall;
                    }
                    $gallery[] = $rowall;
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($rf = $result->fetch_object()) {
                    $images_favs[] = $rf->Bild_Id;
                }
                $result->close();
            }
        }

        $pic_index = array_search($curr_pic, $gallery);
        $prevImage_Id = $pic_index > 0 ? $gallery[$pic_index - 1] : '0';
        $nextImage_Id = $pic_index < (count($gallery) - 1) ? $gallery[$pic_index + 1] : '0';

        if (is_object($prevImage_Id) && $prevImage_Id->Id != 0) {
            $row->PrefImageLink = 'index.php?p=gallery&amp;action=showimage&amp;id=' . $prevImage_Id->Id . '&amp;galid=' . $_REQUEST['galid'] . '&amp;ascdesc=' . $ascdesc . '&amp;categ=' . Arr::getRequest('categ') . '&amp;area=' . AREA;
            $file = $this->thumb($prevImage_Id->Bildname, $prevImage_Id->Id, $this->_settings->Bilder_Klein);
            $row->PrefImage = '<img class="absmiddle" src="' . $file . '" border="0" alt="" />';
        } else {
            $row->PrefImageLink = $row->PrefImage = '';
        }

        $row->NextImageLink = $row->NextImage = '';
        if (is_object($nextImage_Id) && $nextImage_Id->Id != 0) {
            $row->NextImageLink = 'index.php?p=gallery&amp;action=showimage&amp;id=' . $nextImage_Id->Id . '&amp;galid=' . $_REQUEST['galid'] . '&amp;ascdesc=' . $ascdesc . '&amp;categ=' . Arr::getRequest('categ') . '&amp;area=' . AREA;
            $row->NextImageLink_Blanc = 'index.php?blanc=1&amp;p=gallery&amp;action=showimage&amp;id=' . $nextImage_Id->Id . '&amp;galid=' . $_REQUEST['galid'] . '&amp;ascdesc=' . $ascdesc . '&amp;categ=' . Arr::getRequest('categ') . '&amp;first_id=' . $first->Id . '&amp;area=' . AREA;
            $file = $this->thumb($nextImage_Id->Bildname, $nextImage_Id->Id, $this->_settings->Bilder_Klein);
            $row->NextImage = '<img class="absmiddle" src="' . $file . '" border="0" alt="" />';
        }

        if ($this->_settings->Kommentare == 1) { // Подключаем вывод комментариев
            $comment_url = 'index.php?p=gallery&amp;action=showimage&amp;id=' . $image . '&amp;galid=' . $id . '&amp;ascdesc=' . $ascdesc . '&amp;categ=' . Arr::getRequest('categ') . '&amp;area=' . AREA;
            $this->__object('Comments')->load('galerie', $image, $comment_url);
        }

        $title = !empty($row->ImageName) ? $row->ImageName : $this->_lang['GlobalNoName'];

        $tpl_array = array(
            'Favorites'   => (isset($images_favs) ? $images_favs : ''),
            'Galsettings' => $this->_settings,
            'listemos'    => $this->__object('Post')->listsmilies(),
            'data'        => $row,
            'first_id'    => $first->Id,
            'title_html'  => $title);
        $this->_view->assign($tpl_array);

        $headernav = $this->__object('Navigation')->path(Arr::getGet('galid'), 'galerie', 'gallery&amp;action=showgallery', 'id', 'Id', 'Name_' . $this->Lc, '', $this->_lang['Gallery_Name'], 'gallery');

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($title . $this->_lang['PageSep'] . $this->_lang['Gallery_Name']),
            'content'   => $this->_view->fetch(THEME . '/gallery/' . (Arr::getGet('blanc') == 1 ? 'gallerypic_diashow.tpl' : 'gallerypic.tpl')));
        $this->_view->finish($seo_array);
    }

    protected function download($image, $gallery) {
        if (!permission('gallery_download')) {
            $this->__object('Core')->noAccess();
        }
        $row = $this->_db->cache_fetch_object("SELECT Bildname FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . intval($gallery) . "' AND Id = '" . intval($image) . "' LIMIT 1");

        $file = UPLOADS_DIR . '/galerie/' . $row->Bildname;
        list($w, $h) = getimagesize($file);
        if (!empty($w) && !empty($h)) {
            if ($this->_settings->Wasserzeichen == 1 && $w < $this->_maxsize) {
                $object = SX::object('Image');
                if ($object->open($file)) {
                    if ($this->_settings->Wasserzeichen_Vorschau == 1) {
                        $object->watermark(UPLOADS_DIR . '/watermarks/' . $this->_settings->Watermark_File, $this->_settings->Watermark_Position, $this->_settings->Transparenz);
                    }
                    $object->download($file, $this->_settings->Quali_Gross, true);
                    $object->close();
                }
            } else {
                header('Cache-control: private');
                header('Content-type: application/octet-stream');
                header('Content-disposition: attachment; filename=' . $row->Bildname);
                header('Content-Length:' . filesize($file));
                readfile($file);
            }
        }
        exit;
    }

}
