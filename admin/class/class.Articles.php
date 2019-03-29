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

class Articles extends Magic {

    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    protected function genre($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name, Id FROM " . PREFIX . "_genre WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? sanitize($res->Name) : '';
    }

    protected function plattform($id) {
        $res = $this->_db->cache_fetch_object("SELECT Name, Id FROM " . PREFIX . "_plattformen WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? sanitize($res->Name) : '';
    }

    protected function manufacturer($id) {
        $res = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? '<a href="index.php?p=manufacturer&amp;area=' . AREA . '&amp;action=showdetails&amp;id=' . $res->Id . '&amp;name=' . translit($res->Name) . '">' . sanitize($res->Name) . '</a>' : '';
    }

    protected function article($id) {
        $res = $this->_db->cache_fetch_object("SELECT Id, Titel_1, Kategorie FROM " . PREFIX . "_shop_produkte WHERE Artikelnummer='" . $this->_db->escape($id) . "' LIMIT 1");
        return is_object($res) ? '<a href="index.php?p=shop&amp;action=showproduct&amp;id=' . $res->Id . '&amp;cid=' . $res->Kategorie . '&amp;pname=' . translit($res->Titel_1) . '">' . sanitize($res->Titel_1) . '</a>' : '';
    }

    public function top($lim = 10) {
        $show_start = mktime(0, 0, 0, 1, 1, 2000);
        $show_end = time();
        $tbetween = "(ZeitStart BETWEEN " . $show_start . " AND " . $show_end . ") AND ";
        $array = array();
        $sql = $this->_db->query("SELECT SQL_CACHE
                Id,
                Zeit AS Datum,
                Sektion,
                Titel_{$this->Lc} AS Titel,
                Titel_1 AS DefTitel,
                Inhalt_{$this->Lc} AS Inhalt,
                Inhalt_1 AS DefInhalt,
                Bild_1 AS Bild,
                TopartikelBild_{$this->Lc} AS TopcontentBild,
                Bildausrichtung
        FROM
                " . PREFIX . "_artikel
        WHERE
                {$tbetween} (ZeitEnde >= " . $show_end . " OR ZeitEnde = 0)
        AND
                Sektion='" . AREA . "'
        AND
                Topartikel='1'
        AND
                Kennwort=''
        AND
                Aktiv='1'
                ORDER BY ZeitStart DESC, Zeit DESC, Id DESC LIMIT " . $this->_db->escape($lim));
        while ($row = $sql->fetch_assoc()) {
            $row['Inhalt'] = (empty($row['Inhalt'])) ? $row['DefInhalt'] : $row['Inhalt'];
            $row['Inhalt'] = Tool::cleanTags($row['Inhalt'], array('screen', 'contact', 'audio', 'video', 'neu'));
            $row['Inhalt'] = strip_tags($row['Inhalt'], '');
            $array[] = $row;
        }
        $sql->close();
        $this->_view->assign('toparticleitems', $array);
    }

    public function all() {
        $rss = Arr::getRequest('mode') == 'rss' ? 1 : 0;
        $getid = !empty($_REQUEST['catid']) ? intval($_REQUEST['catid']) : 0;
        $this->Limit = !empty($_REQUEST['limit']) ? intval($_REQUEST['limit']) : SX::get('section.LimitNewsArchive');
        $_REQUEST['type'] = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';
        switch ($_REQUEST['type']) {
            default:
                $db_type = '';
                break;

            case 'reviews':
                $db_type = " AND (Typ='review')";
                $this->_view->assign('TypArchive', '&amp;type=reviews');
                break;

            case 'previews':
                $db_type = " AND (Typ='preview')";
                $this->_view->assign('TypArchive', '&amp;type=previews');
                break;

            case 'specials':
                $db_type = " AND (Typ='special')";
                $this->_view->assign('TypArchive', '&amp;type=specials');
                break;
        }

        $db_categ = !empty($getid) ? "AND Kategorie = '" . $getid . "'" : '';
        $show_start = (!empty($_REQUEST['s_year']) && !empty($_REQUEST['s_month']) && !empty($_REQUEST['s_day'])) ? mktime(0, 0, 1, $_REQUEST['s_month'], $_REQUEST['s_day'], $_REQUEST['s_year']) : mktime(0, 0, 0, 1, 1, 2000);
        $show_end = (!empty($_REQUEST['e_year']) && !empty($_REQUEST['e_month']) && !empty($_REQUEST['e_day'])) ? mktime(23, 59, 59, $_REQUEST['e_month'], $_REQUEST['e_day'], $_REQUEST['e_year']) : time();
        $tbetween = ((Arr::getRequest('s_year') > 1) && (Arr::getRequest('e_year') > 1) || (($show_start > 1) && ($show_end > 1))) ? "(ZeitStart BETWEEN " . $this->_db->escape($show_start) . " AND " . $this->_db->escape($show_end) . ") AND " : '';

        $search_request = urldecode(Arr::getRequest('q_news'));
        if (!empty($search_request) && $this->_text->strlen($search_request) >= 2) {
            $search_and = $search_or = '';
            $this->__object('Core')->monitor($search_request, 'articles');
            if (!empty($_REQUEST['st']) && $_REQUEST['st'] == 'and') {
                $_REQUEST['st'] = 'and';
                $and = explode(' ', $search_request);
                foreach ($and as $a) {
                    $search_and .= " AND (Titel_{$this->Lc} LIKE '%" . $this->_db->escape($a) . "%' OR Inhalt_{$this->Lc} LIKE '%" . $this->_db->escape($a) . "%') \n";
                }
            } else {
                $_REQUEST['st'] = 'or';
                $or = explode(' ', $search_request);
                $search_or = "AND (Titel_{$this->Lc} LIKE '%" . $this->_db->escape($search_request) . "%' OR Inhalt_{$this->Lc} LIKE '%" . $this->_db->escape($search_request) . "%')";
                foreach ($or as $o) {
                    $search_or .= " OR (Titel_{$this->Lc} LIKE '%" . $this->_db->escape($o) . "%' OR Inhalt_{$this->Lc} LIKE '%" . $this->_db->escape($o) . "%') \n";
                }
            }

            $db_title_search = " AND ((Suche = 1) $search_and $search_or)";
            $nav_search_title = '&amp;q_news=' . urlencode($search_request);
        } else {
            $db_title_search = $nav_search_title = '';
        }

        $a = Tool::getLimit($this->Limit);
        $q = "SELECT SQL_CALC_FOUND_ROWS
		        Id,
			Kategorie,
			Typ,
			Wertung,
			Kennwort,
			Zeit,
			ZeitStart,
			Autor,
			Hits,
			Sektion,
			Topartikel,
			Bildausrichtung,
			Bild_{$this->Lc} AS Bild,
			Titel_{$this->Lc} AS Titel,
			Untertitel_{$this->Lc} AS Intro,
			Inhalt_{$this->Lc} AS News,
			Titel_1 AS DefTitel,
			Inhalt_1 AS DefNews,
			Untertitel_1 AS DefIntro
		FROM
			" . PREFIX . "_artikel
		WHERE
			{$tbetween}
			(ZeitEnde >= " . time() . " OR ZeitEnde = '0')
		AND
			(Sektion = '" . AREA . "' OR AlleSektionen = '1')
		AND
			Aktiv = '1'
			{$db_type} {$db_categ} {$db_title_search}
		ORDER BY ZeitStart DESC, Zeit DESC, Id DESC LIMIT $a, " . $this->Limit;
        $sql = $this->_db->query($q);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $this->Limit);
        $news = array();
        while ($row = $sql->fetch_assoc()) {
            switch ($row['Typ']) {
                case 'review':
                    $row['TypLink'] = 'index.php?p=articles&amp;area=' . $row['Sektion'] . '&amp;type=reviews';
                    $row['TypName'] = $this->_lang['Gaming_articles_reviews'];
                    break;

                case 'preview':
                    $row['TypLink'] = 'index.php?p=articles&amp;area=' . $row['Sektion'] . '&amp;type=previews';
                    $row['TypName'] = $this->_lang['Gaming_articles_previews'];
                    break;

                case 'special':
                    $row['TypLink'] = 'index.php?p=articles&amp;area=' . $row['Sektion'] . '&amp;type=specials';
                    $row['TypName'] = $this->_lang['Gaming_articles_specials'];
                    break;
            }

            $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
            $row['Intro'] = empty($row['Intro']) ? $row['DefIntro'] : $row['Intro'];
            $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
            $row['News'] = Tool::cleanTags($row['News'], array('screen', 'contact', 'audio', 'video', 'neu'));
            $row['News'] = strip_tags($row['News'], SX::get('system.allowed'));
            $row['LinkTitle'] = translit($row['Titel']);
            $row['User'] = Tool::userName($row['Autor']);
            $news[] = $row;
        }
        $sql->close();

        $this->_view->assign('news_limit', $this->Limit);

        if ($num > $this->Limit) {
            $nav_categ = !empty($getid) ? "&amp;catid=" . $getid : '&amp;catid=0';
            $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=articles&amp;area=" . AREA . "{$nav_categ}&amp;page={s}{$nav_search_title}&amp;limit=" . $this->Limit . "\">{t}</a> "));
        }

        $news_categs = array();
        $dropdown = $this->categs(0, '', $news_categs, $_SESSION['area']);
        $this->_view->assign('dropdown', $dropdown);

        $news_categs_list = array();
        $Categs = $this->listCategs($getid, '', $news_categs_list, $_SESSION['area']);
        $this->_view->assign('Categs', $Categs);

        if (!empty($getid)) {
            $row = $this->_db->cache_fetch_assoc("SELECT Name_{$this->Lc} AS Name FROM " . PREFIX . "_artikel_kategorie WHERE Id = '$getid'");
            $pagetitle = sanitize($row['Name']);
        } else {
            $pagetitle = $this->_lang['Gaming_articles'];
        }
        if ($rss == 1) {
            if (!permission('articles_rss')) {
                $this->__object('Core')->noAccess();
            }
            $url_host = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            $rss = "<?xml version=\"1.0\" encoding=\"" . CHARSET . "\" ?>\n";
            $rss .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
            $rss .= "<channel>\n";
            $rss .= "<pubDate>" . date("r") . "</pubDate>\n";
            $rss .= "<lastBuildDate>" . date("r") . "</lastBuildDate>\n";
            $rss .= "<title>" . SX::get('system.Seitenname') . ' :: ' . $pagetitle . "</title>\n";
            $rss .= "<link>" . $this->__object('Redir')->link() . "</link>\n";
            $rss .= "<description>" . $this->_lang['meta_description_rss'] . " / " . SHEME_URL . $url_host . "</description>\n";
            $rss .= "<generator>" . $this->_lang['meta_generator_rss'] . "</generator>\n";
            $rss .= "<language>" . $this->_lang['LangShort'] . "</language>\n";
            foreach ($news as $n) {
                if (empty($n['Kennwort'])) {
                    $rss .= "<item>\n";
                    $rss .= "<title>" . sanitizeRss($n['Titel']) . "</title>\n";
                    $rss .= "<link>" . SHEME_URL . $url_host . "?p=articles&amp;area=$n[Sektion]&amp;action=displayarticle&amp;id=$n[Id]&amp;name=" . translit($n['Titel']) . "</link>\n";
                    $rss .= "<description><![CDATA[" . $this->_text->substr(sanitizeRss($n['News']), 0, 400) . "...]]></description>\n";
                    $rss .= "<content:encoded><![CDATA[" . sanitizeRss(nl2br($n['News'])) . "]]></content:encoded>\n";
                    $rss .= "<pubDate>" . date("r", $n['ZeitStart']) . "</pubDate>\n";
                    $rss .= "<guid>" . SHEME_URL . $url_host . "?p=articles&amp;area=$n[Sektion]&amp;action=displayarticle&amp;id=$n[Id]&amp;name=" . translit($n['Titel']) . "</guid>\n";
                    $rss .= "<comments>" . SHEME_URL . $url_host . "?p=articles&amp;area=$n[Sektion]&amp;action=displayarticle&amp;id=$n[Id]&amp;name=" . translit($n['Titel']) . "</comments>\n";
                    $rss .= "</item>\n";
                }
            }
            $rss .= "</channel>\n";
            $rss .= "</rss>\n";
            header('Content-Type: text/xml; charset=' . CHARSET);
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
            header('Content-Length: ' . strlen($rss));
            if (SX::get('system.use_seo') == 1) {
                $rss = $this->__object('Rewrite')->get($rss);
            }
            SX::output($rss, true);
        } else {
            $tpl_array = array(
                'rss_article_link' => 'index.php?p=articles&amp;area=' . AREA . '&amp;mode=rss',
                'articlesitems'    => $news);
            $this->_view->assign($tpl_array);

            $end = $pagetitle != $this->_lang['Gaming_articles'] ? $this->_lang['PageSep'] . $this->_lang['Gaming_articles'] : '';

            $headernav = '<a href="index.php?p=articles&amp;area=' . AREA . '">' . $this->_lang['Gaming_articles'] . '</a>' . $this->__object('Navigation')->path($getid, 'artikel_kategorie', 'articles&amp;area=' . AREA, 'catid', 'Id', 'Name_' . $this->Lc, '');
            $seo_array = array(
                'headernav' => $headernav,
                'pagetitle' => $pagetitle . Tool::numPage() . $end,
                'content'   => $this->_view->fetch(THEME . '/articles/archive.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    protected function linkCount($categ, &$count = 0) {
        $query = $this->_db->query("SELECT
            a.Id,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_artikel_kategorie AS a,
            " . PREFIX . "_artikel AS b
        WHERE
            a.Parent_Id = '" . intval($categ) . "'
        AND
            b.Kategorie = a.Id
        AND
            b.Aktiv = '1'
        GROUP BY a.Id");
        while ($item = $query->fetch_object()) {
            $count += $item->LinkCount;
            $this->linkCount($item->Id, $count);
        }
        $query->close();
        return $count;
    }

    protected function listCategs($id, $prefix, &$news_categs_list, &$area) {
        switch ($_REQUEST['type']) {
            default:
                $TypArchive = '';
                break;

            case 'reviews':
                $TypArchive = '&amp;type=reviews';
                break;

            case 'previews':
                $TypArchive = '&amp;type=previews';
                break;

            case 'specials':
                $TypArchive = '&amp;type=specials';
                break;
        }

        $query = $this->_db->query("SELECT
            a.*,
            a.Name_" . $this->Lc . " AS Name,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_artikel_kategorie AS a,
            " . PREFIX . "_artikel AS b
        WHERE
            a.Parent_Id = '" . intval($id) . "'
        AND
            a.Sektion = '" . intval($area) . "'
        AND
            b.Kategorie = a.Id
        AND
            b.Aktiv = '1'
        GROUP BY a.Id
        ORDER BY a.Posi ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $item->HLink = "index.php?p=articles&amp;area={$item->Sektion}{$TypArchive}&amp;catid={$item->Id}&amp;name=" . translit($item->Name);
            $item->LinkCount += $this->linkCount($item->Id);
            $news_categs_list[] = $item;
            $this->listCategs($item->Id, $prefix . ' - ', $news_categs_list, $area);
        }
        $query->close();
        return $news_categs_list;
    }

    public function get($artid) {
        if (!permission('articles')) {
            $this->__object('Core')->noAccess();
        }
        $row = $this->_db->cache_fetch_assoc("SELECT *, Bild_{$this->Lc} AS Bild, Titel_{$this->Lc} AS Titel, Untertitel_{$this->Lc} AS Intro, Inhalt_{$this->Lc} AS News, Titel_1 AS DefTitel, Inhalt_1 AS DefNews, Untertitel_1 AS DefIntro FROM " . PREFIX . "_artikel WHERE Id = '" . intval($artid) . "' AND Aktiv = '1' AND Sektion = '" . AREA . "' AND ZeitStart <= '" . time() . "' LIMIT 1");
        $Abgelaufen = ($row['ZeitEnde'] > 1 && $row['ZeitEnde'] < time()) ? 1 : 0;

        if (is_array($row) && $Abgelaufen != 1) {
            if (isset($_POST['Artikel_Kennwort_' . $row['Id']]) && $_POST['Artikel_Kennwort_' . $row['Id']] == $row['Kennwort']) {
                $_SESSION['Artikel_Kennwort_' . $row['Id']] = $row['Kennwort'];
            }

            if (!empty($row['Kennwort']) && ($_SESSION['Artikel_Kennwort_' . $row['Id']] != $row['Kennwort'])) {
                $this->_view->assign('res', $row);

                $seo_array = array(
                    'headernav' => $this->_lang['Content_passwordProtected'],
                    'pagetitle' => sanitize($this->_lang['Content_passwordProtected'] . $this->_lang['PageSep'] . $this->_lang['Gaming_articles']),
                    'content'   => $this->_view->fetch(THEME . '/articles/login.tpl'));
                $this->_view->finish($seo_array);
            } else {
                $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
                $row['Intro'] = empty($row['Intro']) ? $row['DefIntro'] : $row['Intro'];
                $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
                $row['News'] = $this->__object('Glossar')->get($row['News']);
                $row['News'] = !empty($row['Textbilder_' . $this->Lc]) ? Tool::screens($row['Textbilder_' . $this->Lc], $row['News']) : $row['News'];
                $row['News'] = $this->__object('Contactform')->get($row['News']);
                $row['News'] = $this->__object('Media')->get($row['News']);
                $row['News'] = Tool::cleanTags($row['News'], array('screen', 'contact', 'audio', 'video'));
                $row['LinkTitel'] = translit($row['Titel']);
                $row['User'] = Tool::userName($row['Autor']);
                $row['Top'] = !empty($row['Top']) ? explode("\r\n", strip_tags($row['Top'])) : '';
                $row['Flop'] = !empty($row['Flop']) ? explode("\r\n", strip_tags($row['Flop'])) : '';
                $row['Minimum'] = !empty($row['Minimum']) ? explode("\r\n", strip_tags($row['Minimum'])) : '';
                $row['Optimum'] = !empty($row['Optimum']) ? explode("\r\n", strip_tags($row['Optimum'])) : '';
                $row['Genre'] = !empty($row['Genre']) ? $this->genre($row['Genre']) : '';
                $row['Plattform'] = !empty($row['Plattform']) ? $this->plattform($row['Plattform']) : '';
                $row['ManLink'] = $this->manufacturer($row['Hersteller']);
                $row['PubLink'] = $this->manufacturer($row['Vertrieb']);
                $row['ShopArtikel'] = !empty($row['ShopArtikel']) ? $this->article($row['ShopArtikel']) : '';

                switch ($row['Typ']) {
                    case 'review':
                        $row['TypName'] = $this->_lang['Gaming_articles_reviews'];
                        break;

                    case 'preview':
                        $row['TypName'] = $this->_lang['Gaming_articles_previews'];
                        break;
                    default:
                    case 'special':
                        $row['TypName'] = $this->_lang['Gaming_articles_specials'];
                        break;
                }

                if ($row['WertungsDaten']) {
                    $datas = array();
                    $dataval = explode("\r\n", $row['WertungsDaten']);
                    $i = 1;
                    foreach ($dataval as $m) {
                        $mi = '';
                        $det = explode(';', $m);

                        if (!empty($det[1])) {
                            $mi->Wert = $det[1];
                            $mi->Name = $det[0];
                            $mi->Id = $i++;
                            $datas[] = $mi;
                            $all = $det[1] += $all;
                        }
                    }

                    $all_datas = $i - 1;
                    $max_points = $all_datas * 5;
                    $this->_view->assign(array('Ges' => (($all * 100) / $max_points), 'DataVal' => $datas));
                }

                if (!empty($row['Links'])) {
                    $alternatives = array();
                    $links = explode("\r\n", $row['Links']);
                    $i = 1;
                    foreach ($links as $m) {
                        $mi = '';
                        $det = explode(';', $m);
                        $mi->Id = $i++;
                        $mi->Link = $det[0];
                        $mi->Name = $det[1];
                        $alternatives[] = $mi;
                    }
                    $this->_view->assign('LinksExtern', $alternatives);
                }

                $_REQUEST['artpage'] = (!empty($_REQUEST['artpage']) && $_REQUEST['artpage'] >= 1) ? intval($_REQUEST['artpage']) : 1;
                $seite_anzeigen = explode('[--NEU--]', $row['News']);
                $anzahl_seiten = count($seite_anzeigen);

                if ($_REQUEST['artpage'] > $anzahl_seiten) {
                    $_REQUEST['artpage'] = $anzahl_seiten;
                    $row['News'] = $seite_anzeigen[$anzahl_seiten - 1];
                } else {
                    $row['News'] = $seite_anzeigen[$_REQUEST['artpage'] - 1];
                }

                $this->included($row);

                if ($anzahl_seiten > 1) {
                    $article_pages = $this->__object('Navigation')->artpage($anzahl_seiten, $_REQUEST['artpage'], " <a class=\"page_navigation\" href=\"index.php?p=articles&amp;area=" . AREA . "&amp;action=displayarticle&amp;id={$artid}&amp;name=" . $row['LinkTitel'] . "&amp;artpage={s}\">{t}</a> ");
                    $this->_view->assign('article_pages', $article_pages);
                }

                if ($row['Kommentare'] == 1) {
                    // Подключаем вывод комментариев
                    $comment_url = 'index.php?p=articles&amp;area=' . AREA . '&amp;id=' . $row['Id'] . '&amp;name=' . translit($row['Titel']);
                    $this->__object('Comments')->load('articles', $row['Id'], $comment_url);
                }

                if ($row['Wertung'] == 1) {
                    $row['Bewertung'] = 1;
                    $row['Wertung'] = Tool::rating($row['Id'], 'articles');
                    $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $row['Id'] . '&where=articles');
                    $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
                }

                if (!isset($_SESSION['nr'][$artid])) {
                    $this->_db->query("UPDATE " . PREFIX . "_artikel SET Hits = Hits+1 WHERE Id = '" . intval($artid) . "'");
                    $_SESSION['nr'][$artid] = 1;
                }

                $this->_view->assign('row', $row);
                $headernav = !is_array($row) ? '' : '<a href="index.php?p=articles&amp;area=' . AREA . '">' . $this->_lang['Gaming_articles'] . '</a>' . $this->__object('Navigation')->path($row['Kategorie'], 'artikel_kategorie', 'articles&amp;area=' . AREA, 'catid', 'Id', 'Name_' . $this->Lc, '');

                $seo_array = array(
                    'headernav'     => $headernav,
                    'tags_keywords' => $row['Tags'],
                    'pagetitle'     => sanitize($row['Titel'] . Tool::numPage('artpage') . $this->_lang['PageSep'] . $this->_lang['Gaming_articles']),
                    'generate'      => $row['Tags'] . ' ' . $row['News'],
                    'content'       => $this->_view->fetch(THEME . '/articles/show.tpl'));
                $this->_view->finish($seo_array);
            }
        } else {
            $this->__object('Redir')->seoRedirect('index.php?p=articles&area=' . AREA);
        }
    }

    /* Метод вывода связанных документов */
    protected function included($row = array()) {
        $included = array('IncludedArticles' => '', 'IncludedNews' => '', 'IncludedContent' => '', 'IncludedGalleries' => '');
        if (!empty($row['Galerien']) && get_active('gallery')) {
            $included['IncludedGalleries'] = $this->__object('Gallery')->includedGallery($row['Galerien'], 'galleries_included_1row.tpl');
        }
        if (!empty($row['Tags'])) {
            $included['IncludedArticles'] = $this->includedArticles($row['Tags'], $row['Id']);
            if (get_active('News')) {
                $included['IncludedNews'] = $this->__object('News')->includedNews($row['Tags']);
            }
            if (get_active('content')) {
                $included['IncludedContent'] = $this->__object('Content')->includedContent($row['Tags']);
            }
        }
        $this->_view->assign($included);
    }

    public function includedArticles($tags, $artid = 0) {
        $found_news = $where = array();
        $tags = explode(',', $tags);
        $tags = array_unique($tags);
        foreach ($tags as $word) {
            if (!empty($word)) {
                $where[] = "Tags LIKE '%" . $this->_db->escape(trim($word)) . "%'";
            }
        }
        if (!empty($where)) {
            $order_sql = Tool::randQuery(array('Id', 'Kategorie', 'Titel_1', 'Plattform', 'Autor', 'Zeit', 'Hits', 'ZeitStart', 'ZeitEnde'));
            $where_not = ($artid != 0) ? "AND Id != '" . intval($artid) . "'" : '';
            $res = $this->_db->query("SELECT
                    Id,
                    ZeitStart,
                    Kategorie,
                    Autor,
                    Inhalt_{$this->Lc} AS News,
                    Titel_{$this->Lc} AS Titel
                    FROM
                    " . PREFIX . "_artikel
            WHERE
                    Aktiv = '1'
            AND
                    Sektion = '" . AREA . "'
            AND
                    ZeitStart <= '" . time() . "'
            AND
                    (" . implode(' OR ', $where) . ") {$where_not}
            ORDER BY " . $order_sql . " LIMIT 20");
            while ($row = $res->fetch_assoc()) {
                $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
                $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
                $row['News'] = preg_replace('/\[SCREEN:(.*)\]/iu', '', $row['News']);
                $row['User'] = Tool::userName($row['Autor']);
                if (!in_array($row, $found_news)) {
                    $found_news[] = $row;
                }
            }
            $res->close();
            shuffle($found_news);
        }
        $this->_view->assign('externArticles', $found_news);
        return $this->_view->fetch(THEME . '/articles/extern.tpl');
    }

    protected function categs($id, $prefix, &$news_categ, &$area) {
        switch ($_REQUEST['type']) {
            default:
                $TypArchive = '';
                break;

            case 'reviews':
                $TypArchive = '&amp;type=reviews';
                break;

            case 'previews':
                $TypArchive = '&amp;type=previews';
                break;

            case 'specials':
                $TypArchive = '&amp;type=specials';
                break;
        }
        $query = $this->_db->query("SELECT *, Name_" . $this->Lc . " AS Name FROM " . PREFIX . "_artikel_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY POSI ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $item->HLink = "index.php?p=articles&amp;area={$item->Sektion}{$TypArchive}&amp;catid={$item->Id}&amp;name=" . translit($item->Name);
            $news_categ[] = $item;
            $this->categs($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

}
