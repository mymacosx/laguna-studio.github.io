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

class Manufacturer extends Magic {

    protected $_pageLimit = 15;
    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    public function show($q) {
        $search_db = $search_nav = '';
        $pattern = urldecode($q);
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 2) {
            $this->__object('Core')->monitor($pattern, 'manufacturer');
            $search_db = " AND (Name LIKE '%{$this->_db->escape($pattern)}%') ";
            $search_nav = '&amp;q=' . urlencode($pattern);
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'datedesc':
                $req = 'datedesc';
                $defsort = "ORDER BY Datum DESC";
                $defsort_n = '&amp;sort=datedesc';
                $this->_view->assign(array('img_date' => 'sorter_down', 'datesort' => 'datedesc'));
                break;

            case 'dateasc':
                $req = 'dateasc';
                $defsort = "ORDER BY Datum ASC";
                $defsort_n = '&amp;sort=dateasc';
                $this->_view->assign(array('img_date' => 'sorter_up', 'datesort' => 'datedesc'));
                break;

            case 'namedesc':
                $req = 'namedesc';
                $defsort = "ORDER BY Name DESC";
                $defsort_n = '&amp;sort=namedesc';
                $this->_view->assign(array('img_date' => 'sorter_down', 'namesort' => 'nameasc'));
                break;

            case 'nameasc':
                $req = 'nameasc';
                $defsort = "ORDER BY Name ASC";
                $defsort_n = '&amp;sort=nameasc';
                $this->_view->assign(array('img_name' => 'sorter_up', 'namesort' => 'namedesc'));
                break;

            case 'hitsdesc':
                $req = 'hitsdesc';
                $defsort = "ORDER BY Hits DESC";
                $defsort_n = '&amp;sort=hitsdesc';
                $this->_view->assign(array('img_hits' => 'sorter_down', 'hitssort' => 'hitsasc'));
                break;

            case 'hitsasc':
                $req = 'hitsasc';
                $defsort = "ORDER BY Hits ASC";
                $defsort_n = '&amp;sort=hitsasc';
                $this->_view->assign(array('img_hits' => 'sorter_up', 'hitssort' => 'hitsdesc'));
                break;
        }

        $_REQUEST['sort'] = $req;
        $limit = Tool::getLim($this->_pageLimit);
        $a = Tool::getLimit($limit);
        $query = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_hersteller WHERE Sektion='" . AREA . "' {$search_db} {$defsort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $query->fetch_object()) {
            $row->Beschreibung = strip_tags($row->Beschreibung, SX::get('system.allowed'));
            $row->ProdCount = $this->count($row->Id);
            $items[] = $row;
        }
        $query->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=manufacturer&amp;area=" . AREA . "&amp;page={s}{$defsort_n}{$search_nav}\">{t}</a> "));
        }

        $this->_view->assign('items', $items);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=manufacturer&amp;area=' . AREA . '">' . $this->_lang['Manufacturers'] . '</a>',
            'pagetitle' => $this->_lang['Manufacturers'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/manufacturer/overview.tpl'));
        $this->_view->finish($seo_array);
    }

    public function get($id) {
        $res = $this->_db->cache_fetch_object("SELECT *, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_hersteller WHERE Id='" . intval($id) . "' LIMIT 1");
        if (!is_object($res)) {
            $this->__object('Redir')->seoRedirect('index.php?p=manufacturer&area=' . AREA);
        }
        $res->Adresse = nl2br($res->Adresse);
        $this->_view->assign(array('res' => $res, 'Products' => $this->load($id)));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=manufacturer&amp;area=' . AREA . '">' . $this->_lang['Manufacturers'] . '</a>' . $this->_lang['PageSep'] . $res->Name,
            'pagetitle' => $res->Name . $this->_lang['PageSep'] . $this->_lang['Manufacturers'],
            'generate'  => $res->Name . ' ' . $res->Beschreibung,
            'content'   => $this->_view->fetch(THEME . '/manufacturer/details.tpl'));
        $this->_view->finish($seo_array);
    }

    public function search($q) {
        $value = NULL;
        $q = urldecode($q);
        if (!empty($q) && $this->_text->strlen($q) >= 2) {
            $result = $this->_db->query("SELECT Name FROM " . PREFIX . "_hersteller WHERE Name LIKE '%" . $this->_db->escape($q) . "%' AND Sektion='1' ");
            while ($row = $result->fetch_object()) {
                if ($this->_text->stripos($row->Name, $q) !== false) {
                    $value .= sanitize($row->Name) . PE;
                }
            }
            $result->close();
        }
        SX::output($value, true);
    }

    public function update($id) {
        if ($this->__object('Redir')->referer()) {
            $this->_db->query("UPDATE " . PREFIX . "_hersteller SET Hits=Hits+1 WHERE Id='" . intval($id) . "'");
        }
    }

    protected function load2($id) {
        $products = array();
        $order_sql = Tool::randQuery(array('Id', 'Benutzer', 'Datum', 'Datum_Veroffentlichung', 'Name1', 'Genre', 'Vertrieb', 'Hersteller', 'Preis', 'Hits'));
        $query = $this->_db->query("SELECT
                *,
                Name{$this->Lc} AS Name,
                Beschreibung{$this->Lc} AS Beschreibung,
                Textbilder{$this->Lc} AS Textbilder
        FROM
                " . PREFIX . "_produkte
        WHERE
                Hersteller='" . intval($id) . "'
        AND
                Sektion='" . AREA . "'
        ORDER BY " . $order_sql . " LIMIT 50");
        while ($row = $query->fetch_object()) {
            $row->Beschreibung = strip_tags($row->Beschreibung, '<br><br />');
            $products[] = $row;
        }
        $query->close();
        shuffle($products);
        return $products;
    }

    protected function whereGroup($field = 'Gruppen') {
        $group = intval($_SESSION['user_group']);
        $where = " AND (" . $field . " = '' OR " . $field . " = '" . $group . "' OR " . $field . " LIKE '%," . $group . "' OR " . $field . " LIKE '" . $group . ",%' OR " . $field . " LIKE '%," . $group . ",%') ";
        return  $where;
    }

    protected function load($id) {
        $width = SX::get('shop.thumb_width_small');
        $products = array();
        $order_sql = Tool::randQuery(array('Id', 'Kategorie', 'Titel_1', 'Artikelnummer', 'Preis', 'Preis_Liste', 'Beschreibung_1', 'Klicks', 'Schlagwoerter'));
        $query = $this->_db->query("SELECT
                a.*,
                a.Titel_{$this->Lc} AS Titel,
                a.Beschreibung_{$this->Lc} AS Beschreibung,
                a.Beschreibung_lang_{$this->Lc} AS BeschreibungLang,
                a.Beschreibung_1 AS BeschreibungDef,
                a.Beschreibung_lang_1 AS BeschreibungLangDef
        FROM
                " . PREFIX . "_shop_produkte AS a,
                " . PREFIX . "_shop_kategorie AS b
        WHERE
                a.Aktiv = '1'
        AND
                b.Id = a.Kategorie
        AND
                b.Aktiv = '1'
        AND
                a.Hersteller = '" . intval($id) . "'
                " . $this->whereGroup('a.Gruppen') . "
                " . $this->whereGroup('b.Gruppen') . "
        ORDER BY " . $order_sql . " LIMIT 50");
        while ($row = $query->fetch_object()) {
            if (empty($row->Beschreibung)) {
                $row->Beschreibung = $row->BeschreibungDef;
            }
            if (empty($row->BeschreibungLang)) {
                $row->BeschreibungLang = $row->BeschreibungLangDef;
            }
            $row->Beschreibung = strip_tags($row->Beschreibung . '<br />' . $row->BeschreibungLang, '<br><br />');
            if (!empty($row->Bild)) {
                $row->Bild = Tool::thumb('shop', $row->Bild, $width);
            }
            $products[] = $row;
        }
        $query->close();
        shuffle($products);
        return $products;
    }

    protected function count($id) {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS PCount FROM " . PREFIX . "_shop_produkte WHERE Aktiv = '1' AND Hersteller = '" . intval($id) . "'");
        return $res->PCount;
    }

}
