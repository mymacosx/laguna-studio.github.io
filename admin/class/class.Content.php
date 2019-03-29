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

class Content extends Magic {

    protected $Lc;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
    }

    public function top($lim = 10) {
        $contentitems = array();
        $sql = $this->_db->query("SELECT SQL_CACHE
                Id,
                Datum,
                Sektion,
                Titel{$this->Lc} AS Titel,
                Titel1 AS DefTitel,
                Inhalt{$this->Lc} AS Inhalt,
                Inhalt1 AS DefInhalt,
                Bild1 AS Bild,
                Topcontent_Bild_{$this->Lc} AS TopcontentBild,
                BildAusrichtung
        FROM
                " . PREFIX . "_content
        WHERE
                Sektion='" . AREA . "'
        AND
                Topcontent='1'
        AND
                Kennwort=''
        AND
                Aktiv='1'
        ORDER BY Id DESC LIMIT " . $this->_db->escape($lim));
        while ($row = $sql->fetch_assoc()) {
            $row['Inhalt'] = empty($row['Inhalt']) ? $row['DefInhalt'] : $row['Inhalt'];
            $row['Inhalt'] = Tool::cleanTags($row['Inhalt'], array('screen', 'contact', 'audio', 'video', 'neu'));
            $row['Inhalt'] = strip_tags($row['Inhalt'], SX::get('system.allowed'));
            $contentitems[] = $row;
        }
        $sql->close();
        $this->_view->assign('contentitems', $contentitems);
    }

    public function load($id = 0) {
        $cex = $this->_db->cache_fetch_assoc("SELECT Id, Datum, Sektion, Titel{$this->Lc} AS Titel, Titel1 AS DefTitel, Inhalt{$this->Lc} AS Inhalt, Inhalt1 AS DefInhalt, Bild1 AS Bild, Topcontent_Bild_{$this->Lc} AS TopcontentBild, BildAusrichtung FROM " . PREFIX . "_content WHERE Sektion='" . AREA . "' AND Kennwort='' AND Id='" . intval($id) . "' LIMIT 1");
        $this->_view->assign('content_ex_res', $cex);
        $out = $this->_view->fetch(THEME . '/content/getcontent.tpl');
        SX::output($out);
    }

    public function includedContent($tags, $newsid = 0) {
        $found_content = $where = array();
        $tags = explode(',', $tags);
        $tags = array_unique($tags);
        foreach ($tags as $tag => $word) {
            if (!empty($word)) {
                $where[] = "Tags LIKE '%" . $this->_db->escape(trim($word)) . "%'";
            }
        }
        if (!empty($where)) {
            $order_sql = Tool::randQuery(array('Id', 'Datum', 'Autor', 'Kategorie', 'Titel1', 'Hits'));
            $where_not = ($newsid != 0) ? "AND Id != '" . intval($newsid) . "'" : '';
            $where_gro = ($_SESSION['user_group'] == 1) ? '' : "Gruppen='' AND ";
            $res = $this->_db->query("SELECT
                    Id,
                    Autor,
                    Bild{$this->Lc} AS Bild,
                    Titel{$this->Lc} AS Titel,
                    Inhalt{$this->Lc} AS Inhalt,
                    Titel1 AS DefTitel
                    FROM
                    " . PREFIX . "_content
            WHERE
                    {$where_gro} Sektion='" . AREA . "'
            AND
                    Aktiv='1'
            AND
                    (" . implode(' OR ', $where) . ") {$where_not}
            ORDER BY " . $order_sql . " LIMIT 20");
            while ($row = $res->fetch_assoc()) {
                $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
                $row['Inhalt'] = Tool::cleanTags($row['Inhalt'], array('screen', 'contact', 'audio', 'video'));
                $row['User'] = Tool::userName($row['Autor']);
                if (!in_array($row, $found_content)) {
                    $found_content[] = $row;
                }
            }
            $res->close();
            shuffle($found_content);
        }
        $this->_view->assign('externContent', $found_content);
        return $this->_view->fetch(THEME . '/content/content_extern.tpl');
    }

    public function get($id) {
        $row = $this->_db->cache_fetch_assoc("SELECT *, Bild{$this->Lc} AS Bild, Titel{$this->Lc} AS Titel, Inhalt{$this->Lc} AS Inhalt, Titel1 AS DefTitel, Inhalt1 AS DefInhalt FROM " . PREFIX . "_content WHERE Sektion='" . AREA . "' AND Id='" . intval($id) . "' AND Aktiv='1' LIMIT 1");
        if (is_array($row)) {
            $allowed_groups = explode(',', $row['Gruppen']);
            if (!empty($row['Gruppen']) && !in_array($_SESSION['user_group'], $allowed_groups) && $_SESSION['user_group'] != 1) {
                $seo_array = array(
                    'headernav' => $this->_lang['Error'],
                    'pagetitle' => $this->_lang['Error'],
                    'content'   => $this->_view->fetch(THEME . '/content/showcontent_noperm.tpl'));
                $this->_view->finish($seo_array);
            } else {
                if (!isset($_SESSION['content_read_' . $id])) {
                    $this->_db->query("UPDATE " . PREFIX . "_content SET Hits=Hits+1 WHERE Id='" . intval($id) . "'");
                    $_SESSION['content_read_' . $id] = 1;
                }

                if (isset($_POST['Content_Kennwort_' . $row['Id']]) && $_POST['Content_Kennwort_' . $row['Id']] == $row['Kennwort']) {
                    $_SESSION['Content_Kennwort_' . $row['Id']] = $row['Kennwort'];
                }
                if (!empty($row['Kennwort']) && ($_SESSION['Content_Kennwort_' . $row['Id']] != $row['Kennwort'])) {
                    $this->_view->assign('res', $row);

                    $seo_array = array(
                        'headernav' => $this->_lang['Content_passwordProtected'],
                        'pagetitle' => $this->_lang['Content_passwordProtected'],
                        'content'   => $this->_view->fetch(THEME . '/content/showcontent_login.tpl'));
                    $this->_view->finish($seo_array);
                } else {
                    $row['Titel'] = empty($row['Titel']) ? $row['DefTitel'] : $row['Titel'];
                    $row['Inhalt'] = empty($row['Inhalt']) ? $row['DefInhalt'] : $row['Inhalt'];
                    $row['Inhalt'] = $this->__object('Glossar')->get($row['Inhalt']);
                    $row['Inhalt'] = !empty($row['Textbilder' . $this->Lc]) ? Tool::screens($row['Textbilder' . $this->Lc], $row['Inhalt']) : $row['Inhalt'];
                    $row['Inhalt'] = $this->__object('Media')->get($row['Inhalt']);
                    $row['Inhalt'] = $this->__object('Contactform')->get($row['Inhalt']);
                    $row['Inhalt'] = Tool::cleanTags($row['Inhalt'], array('screen', 'contact', 'audio', 'video'));
                    $_REQUEST['artpage'] = (!empty($_REQUEST['artpage']) && $_REQUEST['artpage'] >= 1) ? intval($_REQUEST['artpage']) : 1;
                    $seite_anzeigen = explode('[--NEU--]', $row['Inhalt']);
                    $anzahl_seiten = count($seite_anzeigen);

                    if ($_REQUEST['artpage'] > $anzahl_seiten) {
                        $_REQUEST['artpage'] = $anzahl_seiten;
                        $row['Inhalt'] = $seite_anzeigen[$anzahl_seiten - 1];
                    } else {
                        $row['Inhalt'] = $seite_anzeigen[$_REQUEST['artpage'] - 1];
                    }

                    $this->included($row);

                    if ($row['Kommentare'] == 1) { // Подключаем вывод комментариев
                        $comment_url = 'index.php?p=content&amp;id=' . $row['Id'] . '&amp;name=' . translit($row['Titel']);
                        $this->__object('Comments')->load('content', $row['Id'], $comment_url);
                    }

                    if ($anzahl_seiten > 1) {
                        $article_pages = $this->__object('Navigation')->artpage($anzahl_seiten, $_REQUEST['artpage'], " <a class=\"page_navigation\" href=\"index.php?p=content&amp;id={$id}&amp;name=" . translit($row['Titel']) . "&amp;area=" . AREA . "&amp;artpage={s}\">{t}</a> ");
                        $this->_view->assign('article_pages', $article_pages);
                    }

                    if ($row['Bewertung'] == 1) {
                        $row['Wertung'] = Tool::rating($row['Id'], 'content');
                        $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $row['Id'] . '&where=content');
                        $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
                    }
                    $this->_view->assign('res', $row);

                    $seo_array = array(
                        'headernav'     => $row['Titel'],
                        'pagetitle'     => $row['Titel'] . Tool::numPage('artpage'),
                        'generate'      => $row['Tags'] . ' ' . $row['Inhalt'],
                        'tags_keywords' => $row['Tags'],
                        'content'       => $this->_view->fetch(THEME . '/content/showcontent.tpl'));
                    $this->_view->finish($seo_array);
                }
            }
        } else {
            $this->__object('Core')->message('Error', 'Error_notFound', $this->__object('Redir')->referer(true), 5);
        }
    }

    /* Метод вывода связанных документов */
    protected function included($row = array()) {
        $included = array('IncludedArticles' => '', 'IncludedNews' => '', 'IncludedContent' => '', 'IncludedGalleries' => '');
        if (!empty($row['Galerien']) && get_active('gallery')) {
            $included['IncludedGalleries'] = $this->__object('Gallery')->includedGallery($row['Galerien']);
        }
        if (!empty($row['Tags'])) {
            $included['IncludedContent'] = $this->includedContent($row['Tags'], $row['Id']);
            if (get_active('News')) {
                $included['IncludedNews'] = $this->__object('News')->includedNews($row['Tags']);
            }
            if (get_active('articles')) {
                $included['IncludedArticles'] = $this->__object('Articles')->includedArticles($row['Tags']);
            }
        }
        $this->_view->assign($included);
    }

}
