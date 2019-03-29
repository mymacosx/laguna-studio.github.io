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

class News extends Magic {

    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    public function show($archive = 0, $topnews = 0) {
        $url_host = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $rss = 0;
        $stime = time();
        if (Arr::getRequest('mode') == 'rss') {
            $rss = 1;
            $topnews = 0;
        }

        $getid = !empty($_REQUEST['catid']) ? intval($_REQUEST['catid']) : 0;

        if ($archive == 1) {
            $limit = (!empty($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : SX::get('section.LimitNewsArchive');
            $page = 'newsarchive';
            $this->_view->assign('rss_newslink', SHEME_URL . $url_host . '?area=' . AREA . '&amp;p=newsarchive&amp;catid=' . $getid . '&amp;t=1&amp;mode=rss');
        } else {
            $limit = SX::get('section.LimitNews');
            $page = 'index';
        }

        $db_categ = !empty($getid) ? "AND Kategorie = '" . $getid . "'" : '';
        $show_start = !empty($_REQUEST['s_year']) && !empty($_REQUEST['s_month']) && !empty($_REQUEST['s_day']) ? mktime(0, 0, 1, $_REQUEST['s_month'], $_REQUEST['s_day'], $_REQUEST['s_year']) : mktime(0, 0, 0, 1, 1, 2000);
        $show_end = !empty($_REQUEST['e_year']) && !empty($_REQUEST['e_month']) && !empty($_REQUEST['e_day']) ? mktime(23, 59, 59, $_REQUEST['e_month'], $_REQUEST['e_day'], $_REQUEST['e_year']) : $stime;
        $tbetween = (Arr::getRequest('s_year') > 1 && Arr::getRequest('e_year') > 1) || ($show_start > 1 && $show_end > 1) ? "(ZeitStart BETWEEN " . $this->_db->escape($show_start) . " AND " . $this->_db->escape($show_end) . ") AND " : '';
        $db_title_search = $nav_search_title = $stn = '';

        $search_request = urldecode(Arr::getRequest('q_news'));
        if (!empty($search_request) && $this->_text->strlen($search_request) >= 2) {
            $search_and = $search_or = '';
            $this->__object('Core')->monitor($search_request, 'news');
            if (!empty($_REQUEST['st']) && $_REQUEST['st'] == 'and') {
                $_REQUEST['st'] = 'and';
                $and = explode(' ', $search_request);
                foreach ($and as $a) {
                    $search_and .= " AND (Titel{$this->Lc} LIKE '%" . $this->_db->escape($a) . "%' OR News{$this->Lc} LIKE '%" . $this->_db->escape($a) . "%') \n";
                }
            } else {
                $_REQUEST['st'] = 'or';
                $or = explode(' ', $search_request);
                $search_or = "AND (Titel{$this->Lc} LIKE '%" . $this->_db->escape($search_request) . "%' OR News{$this->Lc} LIKE '%" . $this->_db->escape($search_request) . "%')";
                foreach ($or as $o) {
                    $search_or .= " OR (Titel{$this->Lc} LIKE '%" . $this->_db->escape($o) . "%' OR News{$this->Lc} LIKE '%" . $this->_db->escape($o) . "%') \n";
                }
            }

            $db_title_search = " AND ((Suche = 1) $search_and $search_or)";
            $nav_search_title = "&amp;q_news=" . urlencode($search_request);
        }

        if ($topnews == 1) {
            $stn = " AND Topnews = '1' ";
            $limit = 25;
        }

        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
		        Id,
			Kategorie,
			Bewertung,
			Zeit,
			ZeitStart,
			Autor,
			Hits,
			Sektion,
			Topnews,
			Topnews_Bild_{$this->Lc} AS TopnewsBild,
			Topnews_Bild_1 AS DefTopnewsBild,
			BildAusrichtung,
			Bild{$this->Lc} AS Bild,
			Titel{$this->Lc} AS Titel,
			Intro{$this->Lc} AS Intro,
			News{$this->Lc} AS News,
			Titel1 AS DefTitel,
			News1 AS DefNews,
			Intro1 AS DefIntro,
			Kommentare
		FROM
			" . PREFIX . "_news
		WHERE
			$tbetween (ZeitEnde >= " . $stime . " OR ZeitEnde = '0')
		AND
			(Sektion = '" . AREA . "' OR AlleSektionen = '1') $stn
		AND
			Aktiv = 1 $db_categ $db_title_search
		ORDER BY ZeitStart DESC, Zeit DESC LIMIT $a, " . $limit);
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $news = array();
        $width = SX::get('news.size');
        while ($row = $sql->fetch_assoc()) {
            $row['Thumb'] = Tool::thumb('news', $row['Bild'], $width);
            $row['TopnewsBild'] = empty($row['TopnewsBild']) ? $row['DefTopnewsBild'] : $row['TopnewsBild'];
            $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
            $row['Intro'] = empty($row['Intro']) ? $row['DefIntro'] : $row['Intro'];
            $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
            $row['News'] = Tool::cleanTags($row['News'], array('screen', 'contact', 'audio', 'video', 'neu'));

            if ($archive != 1) {
                $allowed = SX::get('system.allowed');
                $row['News'] = Tool::cleanTags($row['News'], array('audio', 'video'));
                $row['News'] = strip_tags($row['News'], $allowed);
                $row['Intro'] = strip_tags($row['Intro'], $allowed);
            }
            $row['News'] = $this->__object('Media')->get($row['News']);
            $row['LinkTitle'] = translit($row['Titel']);
            $row['User'] = Tool::userName($row['Autor']);
            $news[] = $row;
        }
        $sql->close();

        $this->_view->assign('news_limit', $limit);
        if ($num > $limit) {
            if ($archive == 1) {
                $nav_categ = !empty($getid) ? '&amp;catid=' . $getid : '&amp;catid=0';
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?arc=1&amp;area=" . AREA . "&amp;p=" . $page . "{$nav_categ}&amp;page={s}{$nav_search_title}&amp;limit=" . $limit . "\">{t}</a> "));
            } else {
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?area=" . AREA . "&amp;p=" . $page . "&amp;page={s}\">{t}</a> "));
            }
        }

        if ($topnews == 0 && $archive == 1) {
            $news_categs = array();
            $dropdown = $this->categs(0, '', $news_categs, $_SESSION['area']);
            $this->_view->assign('dropdown', $dropdown);
        }

        if ($archive == 1) {
            $list_categs = array();
            $Categs = $this->listCategs($getid, '', $list_categs, $_SESSION['area']);
            $this->_view->assign('Categs', $Categs);
        }

        if (!empty($getid)) {
            $headernav = $this->__object('Navigation')->path($getid, 'news_kategorie', 'newsarchive', 'catid', 'Id', 'Name_1', '', $this->_lang['Newsarchive']);
            $row = $this->_db->cache_fetch_assoc("SELECT Name_{$this->Lc} AS Name FROM " . PREFIX . "_news_kategorie WHERE Id = '$getid' LIMIT 1");
            $pagetitle = sanitize($row['Name']);
        } else {
            $headernav = $archive == 1 ? $this->_lang['Newsarchive'] : '';
            $pagetitle = $this->_lang['Newsarchive'];
        }

        if ($rss == 1) {
            if (!permission('news_rss')) {
                $this->__object('Core')->noAccess();
            }
            $rss = "<?xml version=\"1.0\" encoding=\"" . CHARSET . "\" ?>\n";
            $rss .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
            $rss .= "<channel>\n";
            $rss .= "<pubDate>" . date('r') . "</pubDate>\n";
            $rss .= "<lastBuildDate>" . date('r') . "</lastBuildDate>\n";
            $rss .= "<title>" . SX::get('system.Seitenname') . ' :: ' . $pagetitle . "</title>\n";
            $rss .= "<link>" . $this->__object('Redir')->link() . "</link>\n";
            $rss .= "<description>" . $this->_lang['meta_description_rss'] . " / " . SHEME_URL . $url_host . "</description>\n";
            $rss .= "<generator>" . $this->_lang['meta_generator_rss'] . "</generator>\n";
            $rss .= "<language>" . $this->_lang['LangShort'] . "</language>\n";

            foreach ($news as $n) {
                $rss .= "<item>\n";
                $rss .= "<title>" . sanitizeRss($n['Titel']) . "</title>\n";
                $rss .= "<link>" . SHEME_URL . $url_host . "?p=news&amp;area=$n[Sektion]&amp;newsid=$n[Id]&amp;name=" . translit($n['Titel']) . "</link>\n";
                $rss .= "<description><![CDATA[" . $this->_text->substr(sanitizeRss($n['News']), 0, 400) . "...]]></description>\n";
                $rss .= "<content:encoded><![CDATA[" . sanitizeRss(nl2br($n['News'])) . "]]></content:encoded>\n";
                $rss .= "<pubDate>" . date('r', $n['ZeitStart']) . "</pubDate>\n";
                $rss .= "<guid>" . SHEME_URL . $url_host . "?p=news&amp;area=$n[Sektion]&amp;newsid=$n[Id]&amp;name=" . translit($n['Titel']) . "</guid>\n";
                $rss .= "<comments>" . SHEME_URL . $url_host . "?p=news&amp;area=$n[Sektion]&amp;newsid=$n[Id]&amp;name=" . translit($n['Titel']) . "</comments>\n";
                $rss .= "</item>\n";
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
            if ($topnews == 1) {
                $this->_view->assign('topnewsitems', $news);
            } else {
                $this->_view->assign('newsitems', $news);
            }

            if ($archive == 1) {
                $this->_view->assign('news', $this->_view->fetch(THEME . '/news/container_archive.tpl'));

                $end = $pagetitle != $this->_lang['Newsarchive'] ? $this->_lang['PageSep'] . $this->_lang['Newsarchive'] : '';

                $seo_array = array(
                    'headernav' => $headernav,
                    'pagetitle' => $pagetitle . Tool::numPage() . $end,
                    'content'   => $this->_view->fetch(THEME . '/news/newsarchive.tpl'));
                $this->_view->finish($seo_array);
            }
        }
    }

    public function get($newsid) {
        if (!permission('news')) {
            $this->__object('Core')->noAccess();
        }
        $row = $this->_db->cache_fetch_assoc("SELECT *, Bild{$this->Lc} AS Bild, Titel{$this->Lc} AS Titel, Intro{$this->Lc} AS Intro, News{$this->Lc} AS News, Titel1 AS DefTitel, News1 AS DefNews, Intro1 AS DefIntro FROM " . PREFIX . "_news WHERE Id = '$newsid' AND Aktiv = '1' AND (Sektion = '" . AREA . "' OR AlleSektionen = '1') AND ZeitStart <= '" . time() . "' LIMIT 1");

        if (is_array($row)) {
            $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
            $row['Intro'] = empty($row['Intro']) ? $row['DefIntro'] : $row['Intro'];
            $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
            $row['News'] = $this->__object('Glossar')->get($row['News']);
            $row['News'] = !empty($row['Textbilder' . $this->Lc]) ? Tool::screens($row['Textbilder' . $this->Lc], $row['News']) : $row['News'];
            $row['News'] = $this->__object('Contactform')->get($row['News']);
            $row['News'] = $this->__object('Media')->get($row['News']);
            $row['News'] = Tool::cleanTags($row['News'], array('screen', 'contact', 'audio', 'video'));
            $row['LinkTitel'] = translit($row['Titel']);
            $row['User'] = Tool::userName($row['Autor']);
            $_REQUEST['artpage'] = (!empty($_REQUEST['artpage']) && $_REQUEST['artpage'] >= 1) ? intval($_REQUEST['artpage']) : 1;
            $seite_anzeigen = explode('[--NEU--]', $row['News']);
            $anzahl_seiten = count($seite_anzeigen);

            if ($_REQUEST['artpage'] > $anzahl_seiten) {
                $_REQUEST['artpage'] = $anzahl_seiten;
                $row['News'] = $seite_anzeigen[$anzahl_seiten - 1];
            } else {
                $row['News'] = $seite_anzeigen[$_REQUEST['artpage'] - 1];
            }

            if ($anzahl_seiten > 1) {
                $article_pages = $this->__object('Navigation')->artpage($anzahl_seiten, $_REQUEST['artpage'], " <a class=\"page_navigation\" href=\"index.php?p=news&amp;area=" . AREA . "&amp;newsid={$newsid}&amp;name=" . $row['LinkTitel'] . "&amp;artpage={s}\">{t}</a> ");
                $this->_view->assign('article_pages', $article_pages);
            }
        }

        if ($row['Kommentare'] == 1) {
            // Подключаем вывод комментариев
            $comment_url = 'index.php?p=news&amp;newsid=' . $row['Id'] . '&amp;name=' . translit($row['Titel']);
            $this->__object('Comments')->load('news', $row['Id'], $comment_url);
        }

        if ($row['Bewertung'] == 1) {
            $row['Wertung'] = Tool::rating($row['Id'], 'news');
            $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $row['Id'] . '&where=news');
            $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
        }

        $this->included($row);

        if (!isset($_SESSION['nr'][$newsid])) {
            $this->_db->query("UPDATE " . PREFIX . "_news SET Hits = Hits+1 WHERE Id = '" . intval($newsid) . "'");
            $_SESSION['nr'][$newsid] = 1;
        }

        $this->_view->assign('row', $row);
        $headernav = !is_array($row) ? '' : '<a href="index.php?p=newsarchive&amp;area=' . AREA . '">' . $this->_lang['Newsarchive'] . '</a>' . $this->__object('Navigation')->path($row['Kategorie'], 'news_kategorie', 'newsarchive&amp;area=' . AREA, 'catid', 'Id', 'Name_' . $this->Lc, '');

        $seo_array = array(
            'headernav'     => $headernav,
            'pagetitle'     => $row['Titel'] . Tool::numPage('artpage') . $this->_lang['PageSep'] . $this->_lang['Newsarchive'],
            'tags_keywords' => $row['Tags'],
            'generate'      => $row['Tags'] . ' ' . $row['News'],
            'content'       => $this->_view->fetch(THEME . '/news/shownews.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Метод вывода связанных документов */
    protected function included($row = array()) {
        $included = array('IncludedArticles' => '', 'IncludedNews' => '', 'IncludedContent' => '', 'IncludedGalleries' => '');
        if (!empty($row['Galerien']) && get_active('gallery')) {
            $included['IncludedGalleries'] = $this->__object('Gallery')->includedGallery($row['Galerien']);
        }
        if (!empty($row['Tags'])) {
            $included['IncludedNews'] = $this->includedNews($row['Tags'], $row['Id']);
            if (get_active('News')) {
                $included['IncludedContent'] = $this->__object('Content')->includedContent($row['Tags']);
            }
            if (get_active('articles')) {
                $included['IncludedArticles'] = $this->__object('Articles')->includedArticles($row['Tags']);
            }
        }
        $this->_view->assign($included);
    }

    public function includedNews($tags, $newsid = 0) {
        $found_news = $where = array();
        $tags = explode(',', $tags);
        $tags = array_unique($tags);
        foreach ($tags as $word) {
            if (!empty($word)) {
                $where[] = "Tags LIKE '%" . $this->_db->escape(trim($word)) . "%'";
            }
        }
        if (!empty($where)) {
            $order_sql = Tool::randQuery(array('Id', 'Kategorie', 'Autor', 'Zeit', 'ZeitStart', 'ZeitEnde', 'Titel1', 'Hits'));
            $where_not = ($newsid != 0) ? "AND Id != '" . intval($newsid) . "'" : '';
            $res = $this->_db->query("SELECT
                    Id,
                    ZeitStart,
                    Kategorie,
                    Autor,
                    Bild{$this->Lc} AS Bild,
                    Titel{$this->Lc} AS Titel,
                    Intro{$this->Lc} AS Intro,
                    News{$this->Lc} AS News,
                    Titel1 AS DefTitel,
                    News1 AS DefNews,
                    Intro1 AS DefIntro
            FROM
                    " . PREFIX . "_news
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
                $row['Intro'] = empty($row['Intro']) ? $row['DefIntro'] : $row['Intro'];
                $row['News'] = empty($row['News']) ? $row['DefNews'] : $row['News'];
                $row['News'] = Tool::cleanTags($row['News'], array('screen', 'contact', 'audio', 'video'));
                $row['User'] = Tool::userName($row['Autor']);
                if (!in_array($row, $found_news)) {
                    $found_news[] = $row;
                }
            }
            $res->close();
            shuffle($found_news);
        }
        $this->_view->assign('externNews', $found_news);
        return $this->_view->fetch(THEME . '/news/news_extern.tpl');
    }

    protected function categs($id, $prefix, &$news_categ, &$area) {
        $query = $this->_db->query("SELECT *, Name_" . $this->Lc . " AS Name FROM " . PREFIX . "_news_kategorie WHERE Parent_Id = '" . intval($id) . "' AND Sektion = '" . intval($area) . "' ORDER BY POSI ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $item->HLink = 'index.php?p=newsarchive&amp;area=' . $item->Sektion . '&amp;catid=' . $item->Id . '&amp;name=' . translit($item->Name);
            $news_categ[] = $item;
            $this->categs($item->Id, $prefix . ' - ', $news_categ, $area);
        }
        $query->close();
        return $news_categ;
    }

    protected function listCategs($id, $prefix, &$list_categs, &$area) {
        $query = $this->_db->query("SELECT
            a.*,
            a.Name_" . $this->Lc . " AS Name,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_news_kategorie AS a,
            " . PREFIX . "_news AS b
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
            $item->HLink = 'index.php?p=newsarchive&amp;area=' . $item->Sektion . '&amp;catid=' . $item->Id . '&amp;name=' . translit($item->Name);
            $item->LinkCount += $this->count($item->Id);
            $list_categs[] = $item;
            $this->listCategs($item->Id, $prefix . ' - ', $list_categs, $area);
        }
        $query->close();
        return $list_categs;
    }

    protected function count($categ, &$count = 0) {
        $query = $this->_db->query("SELECT
            a.Id,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_news_kategorie AS a,
            " . PREFIX . "_news AS b
        WHERE
            a.Parent_Id = '" . intval($categ) . "'
        AND
            b.Kategorie = a.Id
        AND
            b.Aktiv = '1'
        GROUP BY a.Id");
        while ($item = $query->fetch_object()) {
            $count += $item->LinkCount;
            $this->count($item->Id, $count);
        }
        $query->close();
        return $count;
    }

}
