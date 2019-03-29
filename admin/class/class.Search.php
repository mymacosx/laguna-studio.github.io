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

class Search extends Magic {

    protected $Limit = 15;
    protected $len = 400;
    protected $count = 5;

    protected function clean($text) {
        return Tool::cleanTags($text, array('codewidget', 'screen', 'contact', 'audio', 'video', 'neu'));
    }

    protected function replace($q, $content) {
        $i = 0;
        $result = '';
        $array = array();
        preg_match_all('/.{0,32}' . $q . '.{0,32}/iu', $content, $array);
        foreach($array[0] as $val) {
            $result .= '<strong>...' . $val . '...</strong><br />';
            $i++;
            if ($i == $this->count) {
                break;
            }
        }
        $result = preg_replace('/(' . quotemeta($q) . ')/iu', '<span class="highlight">\1</span>', $result);
        return $result;
    }

    public function show($q) {
        $q = urldecode($q);
        if (!empty($q) && $this->_text->strlen($q) >= 2) {
            $pattern_or = str_ireplace(array(' или ', ' и '), array(' or ', ' and '), $q);
            $pattern_or = explode(' or ', $pattern_or);
            $type = 'LIKE';
            $prefix = '%';
            $LC = Arr::getSession('Langcode', 1);

            $where = Arr::getRequest('where');
            if ($where == 'all') {
                $this->__object('Core')->monitor($q, 'page');
            }

            $countall = 0;
            foreach ($pattern_or as $part) {
                $pattern_and = explode(' and ', $part);
                $sub_pattern = array();
                foreach ($pattern_and as $sub_part) {
                    $sub_part = $this->_db->escape(trim($sub_part));

                    if (get_active('News') && permission('news')) {
                        $sub_pattern[] = "(Titel{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Titel{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Intro{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Intro{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR News{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR News{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp = implode(' AND ', $sub_pattern);
                        $p_and_array[] = $pattern_tmp;
                        $pattern = implode(' OR ', $p_and_array);
                        $db_search = " AND ((Suche='1') AND $pattern )";
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_news WHERE ((Sektion='" . AREA . "' OR AlleSektionen='1') AND ((ZeitEnde>=" . time() . ") OR (ZeitEnde='1') OR (ZeitEnde='0')) AND (((Aktiv='1')) AND (Suche='1') AND (ZeitStart<=" . time() . ") $db_search)) ORDER BY Zeit DESC");
                        $this->_view->assign('count_news', $count);
                        $countall += $count;

                        if ($where == 'news') {
                            $this->__object('Core')->monitor($q, 'news');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Titel{$LC} AS Titel, Intro{$LC} AS Intro, News{$LC} AS News, ZeitStart, Zeit FROM " . PREFIX . "_news WHERE ((Sektion='" . AREA . "' OR AlleSektionen='1') AND ((ZeitEnde>=" . time() . ") OR (ZeitEnde='1') OR (ZeitEnde='0')) AND (((Aktiv='1')) AND (Suche='1') AND (ZeitStart<=" . time() . ") $db_search)) ORDER BY Zeit DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $newsitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->News = $this->clean(strip_tags($row->Intro . ' ' . $row->News));
                                $orte = $this->replace($q, $row->News);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->News, 0, $this->len) . '...';
                                $newsitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=news\">{t}</a> "));
                            }
                            $this->_view->assign('newsitems', $newsitems);
                        }
                    }

                    if (get_active('articles') && permission('articles')) {
                        $sub_pattern_b[] = "(Titel_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Titel_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Inhalt_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Inhalt_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp_b = implode(' AND ', $sub_pattern_b);
                        $p_and_array_b[] = $pattern_tmp_b;
                        $pattern_b = implode(' OR ', $p_and_array_b);
                        $db_search = " AND ((Suche='1') AND $pattern_b )";
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_artikel WHERE ((Sektion='" . AREA . "' OR AlleSektionen='1') AND ((ZeitEnde>=" . time() . ") OR (ZeitEnde='1') OR (ZeitEnde='0')) AND (((Aktiv='1')) AND (Kennwort='') AND (Suche='1') AND (ZeitStart<=" . time() . ") $db_search)) ORDER BY Zeit DESC");
                        $this->_view->assign('count_articles', $count);
                        $countall += $count;

                        if ($where == 'articles') {
                            $this->__object('Core')->monitor($q, 'articles');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Titel_{$LC} AS Titel, Inhalt_{$LC} AS Inhalt, ZeitStart, Zeit FROM " . PREFIX . "_artikel WHERE ((Sektion='" . AREA . "' OR AlleSektionen='1') AND ((ZeitEnde>=" . time() . ") OR (ZeitEnde='1') OR (ZeitEnde='0')) AND (((Aktiv='1')) AND (Kennwort='') AND (Suche='1') AND (ZeitStart<=" . time() . ") $db_search)) ORDER BY Zeit DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $articleitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $articleitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=articles\">{t}</a> "));
                            }
                            $this->_view->assign('articleitems', $articleitems);
                        }
                    }

                    if (get_active('content')) {
                        $sub_pattern__c[] = "(Titel{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Titel{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Inhalt{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Inhalt{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__c = implode(' AND ', $sub_pattern__c);
                        $p_and_array__c[] = $pattern_tmp__c;
                        $pattern__c = implode(' OR ', $p_and_array__c);
                        $where_gro = ($_SESSION['user_group'] == 1) ? '' : " AND (Gruppen='') ";
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_content WHERE Sektion='" . AREA . "' {$where_gro} AND (Suche='1') AND (Aktiv='1') AND (Kennwort='') AND $pattern__c");
                        $this->_view->assign('count_content', $count);
                        $countall += $count;

                        if ($where == 'content') {
                            $this->__object('Core')->monitor($q, 'content');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Titel{$LC} AS Titel, Inhalt{$LC} AS Inhalt, Datum FROM " . PREFIX . "_content WHERE Sektion='" . AREA . "'{$where_gro} AND (Suche='1') AND (Aktiv='1') AND (Kennwort='') AND $pattern__c ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $contentitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $contentitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=content\">{t}</a> "));
                            }
                            $this->_view->assign('contentitems', $contentitems);
                        }
                    }

                    if (get_active('faq') && permission('faq')) {
                        $sub_pattern__d[] = "(Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Antwort_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Antwort_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__d = implode(' AND ', $sub_pattern__d);
                        $p_and_array__d[] = $pattern_tmp__d;
                        $pattern__d = implode(' OR ', $p_and_array__d);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_faq WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__d ");
                        $this->_view->assign('count_faq', $count);
                        $countall += $count;

                        if ($where == 'faq') {
                            $this->__object('Core')->monitor($q, 'faq');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Benutzer, Id, Kategorie, Name_{$LC} AS Titel, Antwort_{$LC} AS Inhalt, Datum FROM " . PREFIX . "_faq WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__d ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $faqitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $faqitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=faq\">{t}</a> "));
                            }
                            $this->_view->assign('faqitems', $faqitems);
                        }
                    }

                    if (get_active('downloads') && permission('downloads')) {
                        $sub_pattern__e[] = "(Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__e = implode(' AND ', $sub_pattern__e);
                        $p_and_array__e[] = $pattern_tmp__e;
                        $pattern__e = implode(' OR ', $p_and_array__e);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_downloads WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__e ");
                        $this->_view->assign('count_downloads', $count);
                        $countall += $count;

                        if ($where == 'downloads') {
                            $this->__object('Core')->monitor($q, 'downloads');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Name_{$LC} AS Titel, Beschreibung_{$LC} AS Inhalt, Datum FROM " . PREFIX . "_downloads WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__e ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $downloaditems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $downloaditems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=downloads\">{t}</a> "));
                            }
                            $this->_view->assign('downloaditems', $downloaditems);
                        }
                    }

                    if (get_active('links') && permission('links')) {
                        $sub_pattern__f[] = "(Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__f = implode(' AND ', $sub_pattern__f);
                        $p_and_array__f[] = $pattern_tmp__f;
                        $pattern__f = implode(' OR ', $p_and_array__f);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_links WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__f ");
                        $this->_view->assign('count_links', $count);
                        $countall += $count;

                        if ($where == 'links') {
                            $this->__object('Core')->monitor($q, 'links');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Name_{$LC} AS Titel, Beschreibung_{$LC} AS Inhalt, Datum FROM " . PREFIX . "_links WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__f ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $linkitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $linkitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=links\">{t}</a> "));
                            }
                            $this->_view->assign('linkitems', $linkitems);
                        }
                    }

                    if (get_active('gallery') && permission('gallery')) {
                        $sub_pattern__g[] = "(Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__g = implode(' AND ', $sub_pattern__g);
                        $p_and_array__g[] = $pattern_tmp__g;
                        $pattern__g = implode(' OR ', $p_and_array__g);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_galerie WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__g ");
                        $this->_view->assign('count_galleries', $count);
                        $countall += $count;

                        if ($where == 'gallery') {
                            $this->__object('Core')->monitor($q, 'gallery');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Kategorie, Autor, Id, Name_{$LC} AS Titel, Beschreibung_{$LC} AS Inhalt, Datum FROM " . PREFIX . "_galerie WHERE Sektion='" . AREA . "' AND (Aktiv='1') AND $pattern__g ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $galleryitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $galleryitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=gallery\">{t}</a> "));
                            }
                            $this->_view->assign('galleryitems', $galleryitems);
                        }
                    }

                    if (get_active('shop')) {
                        $sub_pattern__h[] = "(Titel_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Titel_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Artikelnummer $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_lang_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_lang_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__h = implode(' AND ', $sub_pattern__h);
                        $p_and_array__h[] = $pattern_tmp__h;
                        $pattern__h = implode(' OR ', $p_and_array__h);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_shop_produkte WHERE (Aktiv='1') AND $pattern__h ");
                        $this->_view->assign('count_shoparticles', $count);
                        $countall += $count;

                        if ($where == 'shop') {
                            $this->__object('Core')->monitor($q, 'shop');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Id, Kategorie, Titel_{$LC} AS Titel, Beschreibung_{$LC} AS Inhalt, Beschreibung_lang_{$LC} AS Inhalt_Lang, Erstellt AS Datum FROM " . PREFIX . "_shop_produkte WHERE (Aktiv='1') AND $pattern__h ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $shopitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt . ' ' . $row->Inhalt_Lang));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $shopitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=shop\">{t}</a> "));
                            }
                            $this->_view->assign('shopitems', $shopitems);
                        }
                    }

                    if (get_active('products') && permission('products')) {
                        $sub_pattern__i[] = "(Name{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__i = implode(' AND ', $sub_pattern__i);
                        $p_and_array__i[] = $pattern_tmp__i;
                        $pattern__i = implode(' OR ', $p_and_array__i);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_produkte WHERE (Sektion='" . AREA . "') AND (Aktiv='1') AND $pattern__i ");
                        $this->_view->assign('count_products', $count);
                        $countall += $count;

                        if ($where == 'products') {
                            $this->__object('Core')->monitor($q, 'products');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Genre, Benutzer AS Autor, Id, Name{$LC} AS Titel, Beschreibung{$LC} AS Inhalt, Datum FROM " . PREFIX . "_produkte WHERE (Sektion='" . AREA . "') AND (Aktiv='1') AND $pattern__i ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $productitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $productitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=products\">{t}</a> "));
                            }
                            $this->_view->assign('productitems', $productitems);
                        }
                    }

                    if (get_active('manufacturer') && permission('manufacturer')) {
                        $sub_pattern__j[] = "(Name $type ('" . $prefix . $sub_part . $prefix . "') OR Name $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__j = implode(' AND ', $sub_pattern__j);
                        $p_and_array__j[] = $pattern_tmp__j;
                        $pattern__j = implode(' OR ', $p_and_array__j);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_hersteller WHERE (Sektion='" . AREA . "') AND $pattern__j ");
                        $this->_view->assign('count_manufacturer', $count);
                        $countall += $count;

                        if ($where == 'manufacturer') {
                            $this->__object('Core')->monitor($q, 'manufacturer');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Benutzer AS Autor, Id, Name AS Titel, Beschreibung_{$LC} AS Inhalt, Datum FROM " . PREFIX . "_hersteller WHERE (Sektion='" . AREA . "') AND $pattern__j ORDER BY Datum DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $manufactureritems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $manufactureritems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=manufacturer\">{t}</a> "));
                            }
                            $this->_view->assign('manufactureritems', $manufactureritems);
                        }
                    }

                    if (get_active('cheats') && permission('cheats')) {
                        $sub_pattern__k[] = "(Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Name_{$LC} $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC}  $type ('" . $prefix . $sub_part . $prefix . "') OR Beschreibung_{$LC} $type ('" . $prefix . $sub_part . $prefix . "'))";
                        $pattern_tmp__k = implode(' AND ', $sub_pattern__k);
                        $p_and_array__k[] = $pattern_tmp__k;
                        $pattern__k = implode(' OR ', $p_and_array__k);
                        $count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_cheats WHERE (Aktiv='1') AND (Sektion='" . AREA . "') AND $pattern__k ");
                        $this->_view->assign('count_cheats', $count);
                        $countall += $count;

                        if ($where == 'cheats') {
                            $this->__object('Core')->monitor($q, 'cheats');
                            $limit = Tool::getLim($this->Limit);
                            $seiten = ceil($count / $limit);
                            $a = Tool::getLimit($limit);
                            $sql = "SELECT Sektion, Benutzer AS Autor, Id, Plattform, Name_{$LC} AS Titel, Beschreibung_{$LC} AS Inhalt, DatumUpdate FROM " . PREFIX . "_cheats WHERE (Aktiv='1') AND (Sektion='" . AREA . "') AND $pattern__k ORDER BY DatumUpdate  DESC LIMIT $a, $limit";
                            $res = $this->_db->query($sql);
                            $cheatitems = array();
                            $temp_count = $a + 1;
                            while ($row = $res->fetch_object()) {
                                $row->num = $temp_count++;
                                $row->Inhalt = $this->clean(strip_tags($row->Inhalt));
                                $orte = $this->replace($q, $row->Inhalt);
                                $row->words = (!empty($orte)) ? $orte : '';
                                $row->erg = $this->_text->substr($row->Inhalt, 0, $this->len) . '...';
                                $cheatitems[] = $row;
                            }
                            $res->close();
                            if ($count > $limit) {
                                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?q=" . urlencode($q) . "&amp;p=search&amp;pp=" . $limit . "&amp;page={s}&amp;where=cheats\">{t}</a> "));
                            }
                            $this->_view->assign('cheatitems', $cheatitems);
                        }
                    }
                }
            }

            Arr::setRequest('id', $q);
            $countall_text = str_replace('__MATCHES__', $countall, $this->_lang['Page_Search_ResInf']);

            $tpl_array = array(
                'numall'        => $countall,
                'countall_text' => $countall_text);
            $this->_view->assign($tpl_array);
            $this->_view->assign('Results', $this->_view->fetch(THEME . '/search/results.tpl'));
        }

        $seo_array = array(
            'headernav' => $this->_lang['Search'],
            'pagetitle' => $this->_lang['Search'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/search/searchform.tpl'));
        $this->_view->finish($seo_array);
    }

}
