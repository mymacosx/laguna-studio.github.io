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

class Products extends Magic {

    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    public function recent() {
        $this->_view->assign('NewProductEntries', $this->load(SX::get('section.LimitNewProducts')));
        return $this->_view->fetch(THEME . '/products/products_new_start.tpl');
    }

    protected function load($limit = 5) {
        $items = array();
        $query = $this->_db->query("SELECT SQL_CACHE
                Id,
                Bild,
                Name{$this->Lc} AS Name,
                Beschreibung{$this->Lc} AS Beschreibung
        FROM
                " . PREFIX . "_produkte
        WHERE
                Aktiv='1'
        AND
                Sektion='" . AREA . "'
        ORDER BY Id DESC LIMIT " . intval($limit));
        while ($row = $query->fetch_object()) {
            $row->Beschreibung = Tool::cleanTags($row->Beschreibung, array('screen', 'contact', 'audio', 'video'));
            $row->Beschreibung = strip_tags($row->Beschreibung, SX::get('system.allowed'));
            $items[] = $row;
        }
        $query->close();
        return $items;
    }

    protected function settings() {
        $settings = (object) SX::get('products');
        return $settings;
    }

    public function get($id) {
        if (!isset($_SESSION['ProdClick_' . $id])) {
            $this->_db->query("UPDATE " . PREFIX . "_produkte SET Hits=Hits+1 WHERE Id='" . intval($id) . "'");
            $_SESSION['ProdClick_' . $id] = 'seen';
        }

        $row = $this->_db->cache_fetch_assoc("SELECT *, Textbilder{$this->Lc}, Name{$this->Lc} AS Name, Beschreibung{$this->Lc} AS Inhalt FROM " . PREFIX . "_produkte WHERE Aktiv='1' AND Id='" . intval($id) . "' LIMIT 1");

        if (!is_array($row)) {
            $this->__object('Redir')->seoRedirect('index.php?p=products&area=' . AREA);
        }
        $row['Inhalt'] = $this->__object('Glossar')->get($row['Inhalt']);
        $row['Inhalt'] = !empty($row['Textbilder' . $this->Lc]) ? Tool::screens($row['Textbilder' . $this->Lc], $row['Inhalt']) : $row['Inhalt'];
        $row['Inhalt'] = Tool::cleanTags($row['Inhalt'], array('screen', 'contact', 'audio', 'video'));
        $_REQUEST['artpage'] = (!empty($_REQUEST['artpage']) && $_REQUEST['artpage'] >= 1) ? intval($_REQUEST['artpage']) : 1;
        $seite_anzeigen = explode("[--NEU--]", $row['Inhalt']);
        $anzahl_seiten = count($seite_anzeigen);

        if ($_REQUEST['artpage'] > $anzahl_seiten) {
            $_REQUEST['artpage'] = $anzahl_seiten;
            $row['Inhalt'] = $seite_anzeigen[$anzahl_seiten - 1];
        } else {
            $row['Inhalt'] = $seite_anzeigen[$_REQUEST['artpage'] - 1];
        }
        if ($anzahl_seiten > 1) {
            $article_pages = $this->__object('Navigation')->artpage($anzahl_seiten, $_REQUEST['artpage'], " <a class=\"page_navigation\" href=\"index.php?p=products&amp;area=" . AREA . "&amp;action=showproduct&amp;id=" . translit($row['Id']) . "&amp;name=" . translit($row['Name']) . "&amp;artpage={s}\">{t}</a> ");
            $this->_view->assign('article_pages', $article_pages);
        }

        $row['ManLink'] = $this->manufacturer($row['Hersteller']);
        $row['PubLink'] = $this->manufacturer($row['Vertrieb']);

        if (!empty($row['Links'])) {
            $alternatives = array();
            $mirrors = explode("\r\n", $row['Links']);
            $i = 1;
            foreach ($mirrors as $m) {
                if (!empty($m)) {
                    $mi = '';
                    $det = explode(';', $m);
                    $mi->Id = $i++;
                    $mi->Link = $det[0];
                    $mi->Name = $det[1];
                    $alternatives[] = $mi;
                }
            }
            $this->_view->assign('ProductLinks', $alternatives);
        }

        if (!empty($row['Galerien']) && get_active('gallery')) {
            $this->_view->assign('IncludedGalleries', $this->__object('Gallery')->includedGallery($row['Galerien']));
        }

        $Settings = $this->settings();
        if ($Settings->Kommentare == 1) {
            // Подключаем вывод комментариев
            $comment_url = 'index.php?p=products&amp;action=showdproduct&amp;id=' . $id . '&amp;name=' . translit($row['Name']);
            $this->__object('Comments')->load('products', $id, $comment_url);
        }

        if ($Settings->Wertung == 1) {
            $row['Wertung'] = Tool::rating($row['Id'], 'products');
            $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $id . '&where=products');
            $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
        }
        $row['Genre'] = $this->genre($row['Genre']);
        $this->_view->assign('res', $row);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=products&amp;area=' . AREA . '">' . $this->_lang['Products'] . '</a>',
            'pagetitle' => $row['Name'] . Tool::numPage('artpage') . $this->_lang['PageSep'] . $this->_lang['Products'],
            'generate'  => $row['Name'] . ' ' . $row['Inhalt'],
            'content'   => $this->_view->fetch(THEME . '/products/details.tpl'));
        $this->_view->finish($seo_array);
    }

    public function show() {
        $Settings = $this->settings();
        $search_db = $search_nav = '';
        $pattern = urldecode(Arr::getRequest('q'));
        if (!empty($pattern) && $this->_text->strlen($pattern) >= 2) {
            $this->__object('Core')->monitor($pattern, 'products');
            $search_db = " AND (Name{$this->Lc} LIKE '%{$this->_db->escape($pattern)}%') ";
            $search_nav = '&amp;q=' . urlencode($pattern);
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'datedesc':
                $req = 'datedesc';
                $defsort = "ORDER BY TopProduct DESC, Datum DESC";
                $defsort_n = '&amp;sort=datedesc';
                $this->_view->assign(array('img_date' => 'sorter_down', 'datesort' => 'dateasc'));
                break;

            case 'dateasc':
                $req = 'dateasc';
                $defsort = "ORDER BY TopProduct DESC, Datum ASC";
                $defsort_n = '&amp;sort=dateasc';
                $this->_view->assign(array('img_date' => 'sorter_up', 'datesort' => 'datedesc'));
                break;

            case 'namedesc':
                $req = 'namedesc';
                $defsort = "ORDER BY TopProduct DESC, Name{$this->Lc} DESC";
                $defsort_n = '&amp;sort=namedesc';
                $this->_view->assign(array('img_name' => 'sorter_down', 'namesort' => 'nameasc'));
                break;

            case 'nameasc':
                $req = 'nameasc';
                $defsort = "ORDER BY TopProduct DESC, Name{$this->Lc} ASC";
                $defsort_n = '&amp;sort=nameasc';
                $this->_view->assign(array('img_name' => 'sorter_up', 'namesort' => 'namedesc'));
                break;

            case 'genredesc':
                $req = 'genredesc';
                $defsort = "ORDER BY TopProduct DESC, Genre DESC";
                $defsort_n = '&amp;sort=genredesc';
                $this->_view->assign(array('img_genre' => 'sorter_down', 'genresort' => 'genreasc'));
                break;

            case 'genreasc':
                $req = 'genreasc';
                $defsort = "ORDER BY TopProduct DESC, Genre ASC";
                $defsort_n = '&amp;sort=genreasc';
                $this->_view->assign(array('img_genre' => 'sorter_up', 'genresort' => 'genredesc'));
                break;

            case 'hitsdesc':
                $req = 'hitsdesc';
                $defsort = "ORDER BY TopProduct DESC, Hits DESC";
                $defsort_n = '&amp;sort=hitsdesc';
                $this->_view->assign(array('img_hits' => 'sorter_down', 'hitssort' => 'hitsasc'));
                break;

            case 'hitsasc':
                $req = 'hitsasc';
                $defsort = "ORDER BY  TopProduct DESC, Hits ASC";
                $defsort_n = '&amp;sort=hitsasc';
                $this->_view->assign(array('img_hits' => 'sorter_up', 'hitssort' => 'hitsdesc'));
                break;
        }

        $_REQUEST['sort'] = $req;
        $limit = Tool::getLim($Settings->PageLimit);
        $a = Tool::getLimit($limit);
        $query = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Name{$this->Lc} AS Name, Beschreibung{$this->Lc} AS Beschreibung FROM " . PREFIX . "_produkte WHERE Aktiv='1' AND Sektion='" . AREA . "' {$search_db} {$defsort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $query->fetch_object()) {
            if ($Settings->Wertung == 1) {
                $row->Wertung = Tool::rating($row->Id, 'products');
            }
            if ($Settings->Kommentare == 1) {
                $row->CCount = Tool::countComments($row->Id, 'products');
            }
            $row->Genre = $this->genre($row->Genre);
            $row->Beschreibung = Tool::cleanTags($row->Beschreibung, array('screen', 'contact', 'audio', 'video'));
            $row->Beschreibung = strip_tags($row->Beschreibung, SX::get('system.allowed'));
            $items[] = $row;
        }
        $query->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=products&amp;area=" . AREA . "&amp;page={s}{$defsort_n}{$search_nav}\">{t}</a> "));
        }
        if ($Settings->Wertung == 1) {
            $this->_view->assign('product_rate', 1);
        }
        $this->_view->assign('items', $items);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=products&amp;area=' . AREA . '">' . $this->_lang['Products'] . '</a>',
            'pagetitle' => $this->_lang['Products'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/products/products.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function genre($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name, Id FROM " . PREFIX . "_genre WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? sanitize($res->Name) : '';
    }

    protected function manufacturer($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name, Id FROM " . PREFIX . "_hersteller WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? "<a href=\"index.php?p=manufacturer&amp;area=" . AREA . "&amp;action=showdetails&amp;id={$id}&amp;name=" . translit($res->Name) . "\">" . sanitize($res->Name) . "</a>" : '';
    }

    public function search($q) {
        $value = NULL;
        $q = urldecode($q);
        if (!empty($q) && $this->_text->strlen($q) >= 2) {
            $result = $this->_db->query("SELECT Name{$this->Lc} AS Name FROM " . PREFIX . "_produkte WHERE Name{$this->Lc} LIKE '%" . $this->_db->escape($q) . "%' AND Aktiv='1' AND Sektion='" . AREA . "'");
            while ($row = $result->fetch_object()) {
                if (stripos($row->Name, $q) !== false) {
                    $value .= sanitize($row->Name) . PE;
                }
            }
            $result->close();
        }
        SX::output($value, true);
    }

}
