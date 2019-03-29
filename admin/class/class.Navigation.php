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

class Navigation extends Magic {

    protected $query_array = array();
    protected $_sitemap_tpl = 'sitemap.tpl';
    protected $_navi_tpl = 'navigation.tpl';
    protected $_nhor = 0;
    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    /* Постраничная навигация */
    public function pagenav($anzahl_seiten, $tpl_off) {
        $nav = '<table border="0" cellspacing="0" cellpadding="0" class="navigation_container"><tr><td class="navigation_container_pagetext">';
        $nav .= $this->_lang['PageNavi_Page'] . Tool::aktPage() . $this->_lang['PageNavi_From'] . $anzahl_seiten . '</td><td class="navigation_container_pages">';
        $aktuelle_seite = Tool::prePage();
        $tpl_on = Tool::aktPage();
        $seiten = array($aktuelle_seite - 3, $aktuelle_seite - 2, $aktuelle_seite - 1, $aktuelle_seite, $aktuelle_seite + 1, $aktuelle_seite + 2, $aktuelle_seite + 3);
        $seiten = array_unique($seiten);
        if ($anzahl_seiten > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavStart'], str_replace('{s}', 1, $tpl_off));
        }
        if ($aktuelle_seite > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavBack'], str_replace('{s}', ($aktuelle_seite - 1), $tpl_off));
        }
        foreach ($seiten as $key => $val) {
            if ($val >= 1 && $val <= $anzahl_seiten) {
                if ($aktuelle_seite == $val) {
                    $nav .= str_replace(array('{s}', '{t}'), $val, '<span class="page_active">' . $tpl_on . '</span>');
                } else {
                    $nav .= str_replace(array('{s}', '{t}'), $val, $tpl_off);
                }
            }
        }
        if ($aktuelle_seite < $anzahl_seiten) {
            $nav .= str_replace('{t}', $this->_lang['NavNext'], str_replace('{s}', ($aktuelle_seite + 1), $tpl_off));
        }
        if ($anzahl_seiten > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavEnd'], str_replace('{s}', $anzahl_seiten, $tpl_off));
        }
        $nav .= '</td></tr></table>';
        return $nav;
    }

    /* Метод постраничного вывода при использовании разделителя [--NEU--] */
    public function artpage($num, $sel, $tpl_off) {
        $nav = '<table border="0" cellspacing="0" cellpadding="0" class="navigation_container"><tr><td class="navigation_container_pagetext">';
        $nav .= $this->_lang['PageNavi_Page'] . $sel . $this->_lang['PageNavi_From'] . $num . '</td><td class="navigation_container_pages">';
        $seiten = array($sel - 3, $sel - 2, $sel - 1, $sel, $sel + 1, $sel + 2, $sel + 3);
        $seiten = array_unique($seiten);
        if ($num > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavStart'], str_replace('{s}', 1, $tpl_off));
        }
        if ($sel > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavBack'], str_replace('{s}', ($sel - 1), $tpl_off));
        }
        foreach ($seiten as $key => $val) {
            if ($val >= 1 && $val <= $num) {
                $nav .= ( $sel == $val) ? str_replace(array('{s}', '{t}'), $val, '<span class="page_active">' . $sel . '</span> ') : str_replace(array('{s}', '{t}'), $val, $tpl_off);
            }
        }

        if ($sel < $num) {
            $nav .= str_replace('{t}', $this->_lang['NavNext'], str_replace('{s}', ($sel + 1), $tpl_off));
        }
        if ($num > 1) {
            $nav .= str_replace('{t}', $this->_lang['NavEnd'], str_replace('{s}', $num, $tpl_off));
        }
        $nav .= '</td></tr></table>';
        return $nav;
    }

    /* Метод вывода горизонтального меню навигации */
    public function quicknavi() {
        $items = array();
        $sql = $this->_db->query("SELECT SQL_CACHE Name_{$this->Lc} AS Name, Gruppe, Dokument, Ziel FROM " . PREFIX . "_quicknavi WHERE Aktiv='1' AND Sektion = '" . AREA . "' ORDER BY Position ASC");
        while ($row = $sql->fetch_object()) {
            $current = 'index.php?' . (!empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'area=' . AREA);
            if ($current == $row->Dokument && isset($_REQUEST['p'])) {
                $row->IsActive = 1;
            }
            $items[] = $row;
        }
        $sql->close();
        $this->_view->assign('quicknavi', $items);
        return $this->_view->fetch(THEME . '/navi/quiknavi.tpl');
    }

    public function path($id, $table, $link, $key, $idtype, $nametype, $result = null, $textra = '') {
        $item = $this->_db->cache_fetch_object("SELECT Name_1 AS DefName, Name_{$this->Lc} AS Name, $idtype, $nametype, Parent_Id FROM " . PREFIX . "_$table WHERE $idtype = '" . $this->_db->escape($id) . "' LIMIT 1");
        if (is_object($item)) {
            $p = $_REQUEST['p'];
            $l = $this->limit();
            if (!$item->$nametype) {
                $item->$nametype = $item->DefName;
            }
            if ($item->Parent_Id == 0) {
                $item->$nametype = sanitize($item->$nametype);
                switch ($p) {
                    case 'shop':
                        SX::set('options.shop_title_seo', (!$result ? $item->$nametype : $item->$nametype . strip_tags($result)));
                        return "<a href='index.php?p={$link}'>" . $textra . "</a>" . $this->_lang['PageSep'] . "<a title='" . $item->$nametype . "' href='index.php?p={$link}&amp;action=showproducts&amp;{$key}=" . $item->$idtype . "&amp;page=1&amp;limit=$l&amp;t=" . translit($item->$nametype) . "'>" . $item->$nametype . "</a>" . $result;

                    case 'gallery':
                        $GalInf = $this->_db->cache_fetch_object("SELECT Id, Name_{$this->Lc} AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Id = '" . intval($_REQUEST['categ']) . "' LIMIT 1");
                        return "<a href='index.php?p={$link}&amp;area=" . AREA . "'>" . $textra . "</a>" . $this->_lang['PageSep'] . '<a href="index.php?p=gallery&amp;action=showincluded&amp;categ=' . $GalInf->Id . '&amp;name=' . translit($GalInf->Name) . '&amp;area=' . AREA . '">' . sanitize($GalInf->Name) . '</a>' . $this->_lang['PageSep'] . "<a title='" . $item->$nametype . "' href='index.php?p={$link}&amp;{$key}=" . $item->$idtype . "&amp;categ=" . $GalInf->Id . "&amp;name=" . translit($item->Name) . "&amp;area=" . AREA . "'>" . $item->$nametype . "</a>" . $result;

                    default:
                        return "<a href='index.php?p={$link}'>" . $textra . "</a>" . $this->_lang['PageSep'] . "<a href='index.php?p={$link}&amp;{$key}=" . $item->$idtype . "&amp;name=" . translit($item->Name) . "'>" . $item->$nametype . "</a>" . $result;
                }
            }

            $item->$nametype = sanitize($item->$nametype);
            $parent = $this->_db->cache_fetch_object("SELECT Name_1 AS DefName, Name_{$this->Lc} AS Name, $idtype, $nametype FROM " . PREFIX . "_$table WHERE $idtype = " . $item->Parent_Id . " LIMIT 1");
            if (!$parent->$nametype) {
                $parent->$nametype = $parent->DefName;
            }

            switch ($p) {
                case 'shop':
                    $result = $this->_lang['PageSep'] . "<a title='" . $item->$nametype . "' href='index.php?p={$link}&amp;action=showproducts&amp;{$key}=" . $item->$idtype . "&amp;page=1&amp;limit=$l&amp;t=" . translit($item->$nametype) . "'>" . $item->$nametype . "</a>" . $result;
                    break;

                case 'gallery':
                    $GalInf = $this->_db->cache_fetch_object("SELECT Id, Name_{$this->Lc} AS Name FROM " . PREFIX . "_galerie_kategorien WHERE Id = '" . intval($_REQUEST['categ']) . "' LIMIT 1");
                    $result = $this->_lang['PageSep'] . "<a title='" . $item->$nametype . "' href='index.php?p={$link}&amp;{$key}=" . $item->$idtype . "&amp;categ=" . $GalInf->Id . "&amp;name=" . translit($item->Name) . "&amp;area=" . AREA . "'>" . $item->$nametype . "</a>" . $result;
                    break;

                default:
                    $result = $this->_lang['PageSep'] . "<a title='" . $item->$nametype . "' href='index.php?p={$link}&amp;{$key}=" . $item->$idtype . "&amp;name=" . translit($item->Name) . "'>" . $item->$nametype . "</a>" . $result;
                    break;
            }
            return $this->path($item->Parent_Id, $table, $link, $key, $idtype, $nametype, $result, $textra);
        }
    }

    protected function limit() {
        static $result = null;
        if ($result === null) {
            $result = 10;
            if (!empty($_REQUEST['limit']) && is_numeric($_REQUEST['limit']) && $_REQUEST['limit'] > 0) {
                $result = $_REQUEST['limit'];
            } elseif (!empty($_REQUEST['action']) && $_REQUEST['action'] === 'showproduct') {
                $result = SX::get('shop.Produkt_Limit_Seite');
            }
        }
        return $result;
    }

    protected function document() {
        $document = explode('/', $_SERVER['PHP_SELF']);
        $QueryString = Tool::cleanAllow($_SERVER['QUERY_STRING'], ';?&=');
        return $document[count($document) - 1] . '?' . $QueryString;
    }

    public function sitemap() {
        $sitemap = '';
        $query = $this->_db->query("SELECT Id, Sektion FROM " . PREFIX . "_navi_cat WHERE Aktiv='1' AND Sektion = '" . AREA . "' ORDER BY Position ASC");
        while ($row = $query->fetch_assoc()) {
            $row['tpl'] = $this->_sitemap_tpl;
            $sitemap .= $this->panel(array_change_key_case($row));
        }
        $query->close();
        return $sitemap;
    }

    public function panel($params = array()) {
        $params += array('id' => 1, 'tpl' => $this->_navi_tpl, 'sektion' => AREA, 'group' => $_SESSION['user_group']);
        $document = $this->document();
        $this->_view->assign('document', $document);
        $_SERVER['QUERY_STRING'] = preg_replace(array('/&page=([\d]*)/iu', '/&artpage=([\d]*)/iu'), '', $_SERVER['QUERY_STRING']);
        $row_title = $this->_db->cache_fetch_object("SELECT
			Name_{$this->Lc} AS name,
			Name_1 AS defname,
			Aktiv
		FROM
			" . PREFIX . "_navi_cat
		WHERE
			Id = '" . $params['id'] . "'
		AND
			Sektion = '" . $params['sektion'] . "'
		LIMIT 1");
        if (is_object($row_title) && $row_title->Aktiv == 1) {
            $row_title->name = empty($row_title->name) ? $row_title->defname : $row_title->name;
            if ($document != -1 && is_object($row_title)) {
                $navi = $this->_db->cache_fetch_object("SELECT
                        a.Link_Titel_{$this->Lc} AS AltTitle,
                        a.Id AS id,
                        a.ParentId AS parent_id,
                        a.Dokument AS document,
                        a.Ziel AS target,
                        a.DokumentRub AS document_rub
                FROM
                        " . PREFIX . "_navi AS a
                WHERE
                        a.Dokument = '$document'
                AND
                        Aktiv = '1'
                AND
                        a.NaviCat = '" . $params['id'] . "'
                AND
                        a.Sektion = " . $params['sektion'] . "
                LIMIT 1");
                if (isset($navi->parent_id) && $navi->parent_id != '0') {
                    $parent1 = $this->_db->cache_fetch_object("SELECT
                        Link_Titel_{$this->Lc} AS AltTitle,
                        Id AS id,
                        ParentId AS parent_id,
                        Dokument AS document,
                        Ziel AS target,
                        DokumentRub AS document_rub
                FROM
                        " . PREFIX . "_navi
                WHERE
                        id = '" . $navi->parent_id . "'
                AND
                        Aktiv = '1'
                AND
                        NaviCat = '" . $params['id'] . "'
                LIMIT 1");
                }
                if (isset($parent1->parent_id) && $parent1->parent_id != 0) {
                    $parent2 = $this->_db->cache_fetch_object("SELECT
                            Link_Titel_{$this->Lc} AS AltTitle,
                            Id AS id,
                            ParentId AS parent_id,
                            Dokument AS document,
                            Ziel AS target,
                            DokumentRub AS document_rub
                    FROM
                            " . PREFIX . "_navi
                    WHERE
                            Id = " . $parent1->parent_id . "
                    AND
                            Aktiv = '1'
                    AND
                            Sektion = " . $params['sektion'] . "
                    AND
                            NaviCat = '" . $params['id'] . "'
                    LIMIT 1");
                }
            }

            $r_navi = $this->_db->query("SELECT SQL_CACHE
                    Link_Titel_{$this->Lc} AS AltTitle,
                    Id AS id,
                    Titel_{$this->Lc} AS title,
                    Titel_1 AS deftitle,
                    Dokument AS document,
                    Sektion,
                    openonclick,
                    group_id,
                    Position AS posi,
                    Ziel AS target,
                    DokumentRub AS document_rub
            FROM
                    " . PREFIX . "_navi
            WHERE
                    ParentId = 0
            AND
                    Aktiv = '1'
            AND
                    NaviCat = '" . $params['id'] . "'
            AND
                    Sektion = " . $params['sektion'] . "
            ORDER BY Position ASC");
            $output = array();
            while ($navi = $r_navi->fetch_object()) {
                $navi->openonclick = ($this->_nhor == 1) ? 0 : $navi->openonclick;
                $navi->title = empty($navi->title) ? $navi->deftitle : $navi->title;
                $navi->group_array = !empty($navi->group_id) ? explode(',', $navi->group_id) : $navi->group_id;
                $navi_params = explode('&', trim(str_replace('index.php?', '', $navi->document)));
                $request_params = explode('&', $_SERVER['QUERY_STRING']);
                $intersect = array_intersect($navi_params, $request_params);

                foreach ($intersect as $inter) {
                    $pair = explode('=', $inter);
                    $defc = ($_REQUEST['p'] == 'content') ? 'p=' : 'page=';
                    $static_nav = explode($defc, $navi->document);

                    if (!empty($static_nav[1]) && is_array($static_nav) && !empty($static_nav[1])) {
                        $navi->active = false;
                    } else {
                        if ($pair[0] == 'p' || $navi->document_rub == $_REQUEST['p']) {
                            $navi->active = true;
                        }
                    }
                }

                if (in_array($params['group'], $navi->group_array)) {
                    $navi->document = (isset($navi->document)) ? $navi->document : '';
                    if (!isset($parent1)) {
                        $parent1 = new stdClass;
                    }
                    if (!isset($parent2)) {
                        $parent2 = new stdClass;
                    }
                    if (!isset($parent1->document)) {
                        $parent1->document = '';
                    }
                    if (!isset($parent2->document)) {
                        $parent2->document = '';
                    }
                    if (!$navi->openonclick || $navi->document == $document || $parent1->document == $navi->document || $parent2->document == $navi->document || $document == -1) {
                        if ($parent1->document == $navi->document || $parent2->document == $navi->document) {
                            $navi->active = true;
                        }
                        $r_sub_navi = $this->_db->query("SELECT
                                Link_Titel_{$this->Lc} AS AltTitle,
                                DokumentRub AS document_rub,
                                Id AS id,
                                Titel_{$this->Lc} AS title,
                                Dokument AS document,
                                Sektion AS area,
                                openonclick,
                                group_id,
                                Position,
                                Ziel AS target
                        FROM
                                " . PREFIX . "_navi
                        WHERE
                                ParentId = '" . $navi->id . "'
                        AND
                                Aktiv = '1'
                        AND
                                Titel_{$this->Lc} != ''
                        AND
                                Sektion = " . $params['sektion'] . "
                        ORDER BY Position ASC");
                        $navi->sub_navi = array();
                        while ($sub_navi = $r_sub_navi->fetch_object()) {
                            $sub_navi->openonclick = ($this->_nhor == 1) ? 0 : $sub_navi->openonclick;
                            $sub_navi->group_array = (!empty($sub_navi->group_id)) ? explode(',', $sub_navi->group_id) : $sub_navi->group_id;
                            if (in_array($params['group'], $sub_navi->group_array)) {
                                if (!$sub_navi->openonclick || $sub_navi->document == $document || $parent1->document == $sub_navi->document || $parent2->document == $sub_navi->document || $document == -1) {
                                    if ($parent1->document == $sub_navi->document || $parent2->document == $sub_navi->document) {
                                        $now_page = explode('index.php?', $_SERVER['REQUEST_URI']);
                                        $now = 'index.php?' . $now_page[1];
                                        $sub_navi->active = true;
                                    }
                                    $r_last_navi = $this->_db->query("SELECT
                                            DokumentRub AS document_rub,
                                            Link_Titel_{$this->Lc} AS AltTitle,
                                            Id AS id,
                                            Titel_{$this->Lc} AS title,
                                            Dokument AS document,
                                            Sektion AS area,
                                            openonclick,
                                            group_id,
                                            Position,
                                            Ziel AS target
                                    FROM
                                            " . PREFIX . "_navi
                                    WHERE
                                            ParentId = " . $sub_navi->id . "
                                    AND
                                            Aktiv = '1'
                                    AND
                                            Titel_{$this->Lc} != ''
                                    AND
                                            Sektion = " . $params['sektion'] . "
                                    ORDER BY Position ASC");
                                    $sub_navi->sub_navi = array();
                                    while ($last_navi = $r_last_navi->fetch_object()) {
                                        $last_navi->openonclick = ($this->_nhor == 1) ? 0 : $last_navi->openonclick;
                                        $last_navi->group_array = (!empty($last_navi->group_id)) ? explode(',', $last_navi->group_id) : $last_navi->group_id;
                                        $parent2->document = (!empty($parent2->document)) ? $parent2->document : '';
                                        $pos_last = (!empty($parent2->document)) ? $this->_text->stristr($last_navi->document, $parent2->document) : false;
                                        $now_page = explode('index.php?', $_SERVER['REQUEST_URI']);
                                        $now = 'index.php?' . $now_page[1];
                                        if ($pos_last !== false && $now == $last_navi->document) {
                                            $last_navi->active = true;
                                        }
                                        if (in_array($params['group'], $last_navi->group_array)) {
                                            $sub_navi->sub_navi[] = $last_navi;
                                        }
                                    }
                                    $r_last_navi->close();
                                }
                                $navi->sub_navi[] = $sub_navi;
                            }
                        }
                        $r_sub_navi->close();
                    }
                    $output[] = $navi;
                }
            }
            $r_navi->close();

            $this->_view->assign('navi_title_elem', $this->_text->lower(Tool::cleanAllow($row_title->name)));
            $this->_view->assign('navi_title', $row_title->name);
            $this->_view->assign('SiteNavigation', $output);
            $naviout = $this->_view->fetch(THEME . '/navi/' . $params['tpl']);
            $this->_view->assign('navi', $naviout);
            return $naviout;
        }
        return NULL;
    }

    public function fullmap() {
        $this->select();
        $key = $_SESSION['user_group'] . 'full_sitemap';
        $array = $this->__object('Cache')->get($key);
        if ($array === false) {
            $array = $this->query();
            $this->__object('Cache')->set($key, $array, 7200); // кешируем на 2 часа, тяжелый запрос
        }
        $this->_view->assign($array);

        $seo_array = array(
            'headernav' => $this->_lang['SitemapFull'],
            'pagetitle' => $this->_lang['SitemapFull'],
            'content'   => $this->_view->fetch(THEME . '/navi/site_map.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function base($select, $table, $where = '', $param = '') {
        if (!empty($table) && !empty($select)) {
            $this->query_array[$table] = "SELECT SQL_CACHE " . $select . " FROM " . PREFIX . "_" . $table . " " . $where . " " . $param;
        }
    }

    /* Метод выполнения выполнения мультизапроса */
    protected function query() {
        $array = array();
        if (!empty($this->query_array)) {
            $i = 0;
            $query = implode(' ; ', $this->query_array);
            if ($this->_db->multi_query($query)) {
                foreach ($this->query_array as $key => $val) {
                    $array[$key] = array();
                    $result = ($i == 0) ? $this->_db->store_result() : $this->_db->store_next_result();
                    if ($result) {
                        while ($row = $result->fetch_object()) {
                            $array[$key][] = $row;
                        }
                        $result->close();
                    }
                    $i++;
                }
            }
        }
        return $array;
    }

    protected function select() {
        $area = AREA;
        if ($_REQUEST['p'] == 'sitemap') {
            if (get_active('roadmap')) {
                $this->base('Id, Name', 'roadmap', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Pos ASC');
            }
            if (get_active('manufacturer')) {
                $this->base('Id, Name', 'hersteller', "WHERE Sektion = '$area'", 'ORDER BY Name ASC');
            }
            if (get_active('News')) {
                $this->base('Id, Name_' . $this->Lc . ' AS Name', 'news_kategorie', "WHERE Sektion = '$area'", 'ORDER BY Posi ASC');
                $this->base('Id, Kategorie, Titel' . $this->Lc . ' AS Titel, Sektion', 'news', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY ZeitStart ASC');
            }
            if (get_active('downloads')) {
                $this->base('Id, Name_' . $this->Lc . ' AS Name, Sektion', 'downloads_kategorie', "WHERE Sektion = '$area'", 'ORDER BY Id ASC');
                $this->base('Id, Kategorie, Name_' . $this->Lc . ' AS Name, Sektion', 'downloads', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Datum ASC');
            }
            if (get_active('gallery')) {
                $this->base('Id, Name_' . $this->Lc . ' AS Name, Sektion', 'galerie_kategorien', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Datum ASC');
                $this->base('Id, Kategorie, Name_' . $this->Lc . ' AS Name, Sektion', 'galerie', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Datum ASC');
            }
            if (get_active('links')) {
                $this->base('Id, Name_' . $this->Lc . ' AS Name, Sektion', 'links_kategorie', "WHERE Sektion = '$area'", 'ORDER BY Id ASC');
                $this->base('Id, Kategorie, Name_' . $this->Lc . ' AS Name, Sektion', 'links', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Datum ASC');
            }
            if (get_active('cheats')) {
                $this->base('Id, Name, Sektion', 'plattformen', "WHERE Sektion = '$area'", 'ORDER BY Id ASC');
                $this->base('Id, Plattform, Name_' . $this->Lc . ' AS Name, Sektion', 'cheats', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Id ASC');
            }
            if (get_active('forums')) {
                $this->base('id, title', 'f_category', '', 'ORDER BY position ASC');
                $this->base('id, title, category_id', 'f_forum', "WHERE active = '1'", 'ORDER BY position ASC');
                $this->base('id, title, forum_id', 'f_topic', '', 'ORDER BY last_post_int DESC');
            }
            if (get_active('faq')) {
                $this->base('Id, Parent_Id, Name_' . $this->Lc . ' AS Name', 'faq_kategorie', "WHERE Sektion = '$area'", 'ORDER BY Posi ASC');
            }
            if (get_active('poll')) {
                $this->base('Id, Titel_' . $this->Lc . ' AS Titel, Sektion', 'umfrage', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Start DESC');
            }
            if (get_active('articles')) {
                $this->base('Id, Name_' . $this->Lc . ' AS Name, Sektion', 'artikel_kategorie', "WHERE Sektion = '$area'", 'ORDER BY Posi ASC');
                $this->base('Id, Kategorie, Titel_' . $this->Lc . ' AS Titel, Typ, Sektion', 'artikel', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY ZeitStart DESC');
            }
            if (get_active('content')) {
                $this->base('Id, Name, Sektion', 'content_kategorien', "WHERE Sektion = '$area'", 'ORDER BY Id ASC');
                $this->base('Id, Kategorie, Titel' . $this->Lc . ' AS Titel, Sektion', 'content', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Datum ASC');
            }
            if (get_active('products')) {
                $this->base('Id, Sektion, Name', 'genre', "WHERE Sektion = '$area'", 'ORDER BY Id ASC');
                $this->base('Id, Name' . $this->Lc . ' AS Name, Genre, Sektion', 'produkte', "WHERE Aktiv = '1' AND Sektion = '$area'", 'ORDER BY Id ASC');
            }
            if (get_active('shop')) {
                $this->base('Id, Parent_Id, Name_' . $this->Lc . ' AS Name', 'shop_kategorie', "WHERE Sektion = '$area'", 'ORDER BY posi ASC');
                $this->base('Id, Kategorie, Titel_' . $this->Lc . ' AS Titel', 'shop_produkte', "WHERE Aktiv = '1' AND Sektion = '$area' AND ((Gruppen = '') OR (Gruppen LIKE '%," . $_SESSION['user_group'] . "') OR (Gruppen LIKE '" . $_SESSION['user_group'] . ",%') OR (Gruppen LIKE '%," . $_SESSION['user_group'] . ",%') OR (Gruppen = '" . $_SESSION['user_group'] . "'))", 'ORDER BY Hersteller ASC');
            }
        }
    }

}