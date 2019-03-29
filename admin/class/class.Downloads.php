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

class Downloads extends Magic {

    protected $Lc;
    protected $_settings;
    protected $_allowed;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->_settings = SX::get('downloads');
        $this->_allowed = SX::get('system.allowed');
        $this->_view->assign('Downloadssettings', (object) $this->_settings);
    }

    public function newDownloads() {
        $this->_view->assign('NewDownloadsEntries', $this->newEntries(SX::get('section.LimitNewDownloads'), 1));
        return $this->_view->fetch(THEME . '/downloads/entries_new_start.tpl');
    }

    public function search($search) {
        $search_like = '';
        $search = urldecode($search);
        if (!empty($search) && $this->_text->strlen($search) >= 2) {
            $this->__object('Core')->monitor($search, 'downloads');
            $like = $this->_db->escape($search);
            $like2 = $this->_db->escape(sanitize($search));
            $search_like = "AND ((Name_{$this->Lc} LIKE '%{$like}%' OR Beschreibung_{$this->Lc} LIKE '%{$like}%') OR (Name_{$this->Lc} LIKE '%{$like2}%' OR Beschreibung_{$this->Lc} LIKE '%{$like2}%'))";
        }

        $limit = Tool::getLim($this->_settings['PageLimit']);
        $a = Tool::getLimit($limit);
        $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Kategorie, Name_{$this->Lc} AS Name, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_downloads WHERE Aktiv='1' {$search_like} AND Sektion = '" . AREA . "' ORDER BY Name_{$this->Lc} ASC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $entries = array();
        while ($row_items = $query_items->fetch_object()) {
            if ($this->_settings['Wertung'] == 1) {
                $row_items->Wertung = Tool::rating($row_items->Id, 'downloads');
            }
            $res_name = $this->_db->cache_fetch_object("SELECT Name_{$this->Lc} AS Name FROM " . PREFIX . "_downloads_kategorie WHERE Id='$row_items->Kategorie' LIMIT 1");
            $row_items->KategName = $res_name->Name;
            $row_items->Link_Categ = 'index.php?p=downloads&amp;area=' . AREA . '&amp;categ=' . $row_items->Kategorie . '&amp;name=' . translit($row_items->KategName);
            $row_items->CCount = Tool::countComments($row_items->Id, 'downloads');
            $row_items->Beschreibung = $this->_text->strlen($search) >= 2 ? Tool::highlight(strip_tags($row_items->Beschreibung, '<span>'), $search) : strip_tags($row_items->Beschreibung, $this->_allowed);
            $row_items->Link_Details = 'index.php?p=downloads&amp;action=showdetails&amp;area=' . AREA . '&amp;categ=' . $row_items->Kategorie . '&amp;id=' . $row_items->Id . '&amp;name=' . translit($row_items->Name);
            $entries[] = $row_items;
        }
        $query_items->close();

        if ($num > $limit) {
            $search = empty($search) ? '-' : $search;
            $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?ql=" . urlencode($search) . "&amp;action=search&amp;p=downloads&amp;area=" . AREA . "&amp;page={s}\">{t}</a> "));
        }

        $this->_view->assign('Entries', $entries);
        $this->_view->assign('Results', $this->_view->fetch(THEME . '/downloads/entries.tpl'));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=downloads&amp;area=' . AREA . '">' . $this->_lang['Downloads'] . '</a> ',
            'pagetitle' => $this->_lang['Search'] . $this->_lang['PageSep'] . $this->_lang['Downloads'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/downloads/search_result.tpl'));
        $this->_view->finish($seo_array);
    }

    public function get($id) {
        $id = intval($id);
        $res = $this->_db->cache_fetch_object("SELECT *, Kategorie, Name_{$this->Lc} AS Name,Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_downloads WHERE Id='$id' AND Aktiv='1' LIMIT 1");
        if (!is_object($res)) {
            $this->__object('Redir')->seoRedirect('index.php?p=downloads&area=' . AREA);
        }
        $headernav = '<a href="index.php?p=downloads&amp;area=' . AREA . '">' . $this->_lang['Downloads'] . '</a>' . $this->__object('Navigation')->path($res->Kategorie, 'downloads_kategorie', 'downloads&amp;area=' . AREA, 'categ', 'Id', 'Name_' . $this->Lc, '');
        $res->UserName = Tool::userName($res->Autor);

        if ($this->_settings['Kommentare'] == 1) {
            // Подключаем вывод комментариев
            $comment_url = 'index.php?p=downloads&amp;action=showdetails&amp;categ=' . $res->Kategorie . '&amp;id=' . $id . '&amp;name=' . translit($res->Name);
            $this->__object('Comments')->load('downloads', $id, $comment_url);
        }

        if ($this->_settings['Wertung'] == 1) {
            $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $id . '&where=downloads');
            $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
        }

        $res->Beschreibung = $this->__object('Glossar')->get($res->Beschreibung);
        $res->Wertung = Tool::rating($res->Id, 'downloads');
        if (!empty($res->Mirrors)) {
            $alternatives = array();
            $mirrors = explode("\r\n", $res->Mirrors);
            $i = 1;
            foreach ($mirrors as $m) {
                $mi = '';
                $det = explode(';', $m);
                $mi->Id = $i++;
                $mi->Link = $det[0];
                $mi->Name = $det[1];
                $alternatives[] = $mi;
            }
            $this->_view->assign('alternatives', $alternatives);
        }

        if (empty($res->Url_Direct)) {
            $res->Traffic = File::filesize(filesize(UPLOADS_DIR . '/downloads_files/' . $res->Url) * $res->Hits / 1024);
            $res->Size = File::filesize(filesize(UPLOADS_DIR . '/downloads_files/' . $res->Url) / 1024);
        } else {
            $res->Traffic = File::filesize($res->Size_Direct * $res->Hits * 1024);
            $res->Size = File::filesize($res->Size_Direct * 1024);
        }

        $tpl_array = array(
            'BrokenLinkSubmit' => 'index.php?action=brokenlink&p=downloads&id=' . $id,
            'link_res'         => $res);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $res->Name . $this->_lang['PageSep'] . $this->_lang['Downloads'],
            'generate'  => $this->_lang['Downloads'] . ' ' . $res->Beschreibung,
            'content'   => $this->_view->fetch(THEME . '/downloads/showdownload.tpl'));
        $this->_view->finish($seo_array);
    }

    public function categs() {
        $getid = !empty($_REQUEST['categ']) ? intval($_REQUEST['categ']) : 0;

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'datedesc':
                $req = 'datedesc';
                $defsort = "ORDER BY Sponsor DESC, Datum DESC";
                $defsort_n = '&amp;sort=datedesc';
                $this->_view->assign(array('img_date' => 'sorter_down', 'datesort' => 'dateasc'));
                break;

            case 'dateasc':
                $req = 'dateasc';
                $defsort = "ORDER BY Sponsor DESC, Datum ASC";
                $defsort_n = '&amp;sort=dateasc';
                $this->_view->assign(array('img_date' => 'sorter_up', 'datesort' => 'datedesc'));
                break;

            case 'namedesc':
                $req = 'namedesc';
                $defsort = "ORDER BY Sponsor DESC, Name_{$this->Lc} DESC";
                $defsort_n = '&amp;sort=namedesc';
                $this->_view->assign(array('img_name' => 'sorter_down', 'namesort' => 'nameasc'));
                break;

            case 'nameasc':
                $req = 'nameasc';
                $defsort = "ORDER BY Sponsor DESC, Name_{$this->Lc} ASC";
                $defsort_n = '&amp;sort=nameasc';
                $this->_view->assign(array('img_name' => 'sorter_up', 'namesort' => 'namedesc'));
                break;

            case 'hitsdesc':
                $req = 'hitsdesc';
                $defsort = "ORDER BY Sponsor DESC, Hits DESC";
                $defsort_n = '&amp;sort=hitsdesc';
                $this->_view->assign(array('img_hits' => 'sorter_down', 'hitssort' => 'hitsasc'));
                break;

            case 'hitsasc':
                $req = 'hitsasc';
                $defsort = "ORDER BY Sponsor DESC, Hits ASC";
                $defsort_n = '&amp;sort=hitsasc';
                $this->_view->assign(array('img_hits' => 'sorter_up', 'hitssort' => 'hitsdesc'));
                break;
        }

        $_REQUEST['sort'] = $req;
        $links_categ = array();
        $Categs = $this->listCategs($getid, '', $links_categ, $_SESSION['area']);

        if (!empty($getid)) {
            $res_name = $this->_db->fetch_object("SELECT Name_{$this->Lc} AS Name FROM " . PREFIX . "_downloads_kategorie WHERE Id='$getid' LIMIT 1");
            $limit = Tool::getLim($this->_settings['PageLimit']);
            $a = Tool::getLimit($limit);
            $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Kategorie, Name_{$this->Lc} AS Name,Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_downloads WHERE Kategorie='$getid' AND Aktiv='1' {$defsort} LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $entries = array();
            while ($row_items = $query_items->fetch_object()) {
                if ($this->_settings['Wertung'] == 1) {
                    $row_items->Wertung = Tool::rating($row_items->Id, 'downloads');
                }
                $row_items->CCount = Tool::countComments($row_items->Id, 'downloads');
                $row_items->KategName = $res_name->Name;
                $row_items->Beschreibung = strip_tags($row_items->Beschreibung, $this->_allowed);
                $row_items->Link_Details = 'index.php?p=downloads&amp;action=showdetails&amp;area=' . AREA . '&amp;categ=' . $getid . '&amp;id=' . $row_items->Id . '&amp;name=' . translit($row_items->Name);
                $entries[] = $row_items;
            }
            $query_items->close();

            if ($num > $limit) {
                $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=downloads&amp;area=" . AREA . "&amp;categ=" . $getid . "&amp;name=" . translit($res_name->Name) . "&amp;page={s}{$defsort_n}\">{t}</a> "));
            }
            $this->_view->assign('Entries', $entries);
        }

        if (!isset($_REQUEST['categ'])) {
            $this->_view->assign('Entries', $this->newEntries());
        }
        $this->_view->assign(array('Categs' => $Categs, 'CategName' => (isset($res_name->Name) ? translit($res_name->Name) : '')));

        if (!empty($res_name->Name)) {
            $pagetitle = $res_name->Name . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Downloads'];
        } else {
            $pagetitle = $this->_lang['Downloads'] . Tool::numPage();
        }

        $headernav = '<a href="index.php?p=downloads&amp;area=' . AREA . '">' . $this->_lang['Downloads'] . '</a>' . $this->__object('Navigation')->path($getid, 'downloads_kategorie', 'downloads&amp;area=' . AREA, 'categ', 'Id', 'Name_' . $this->Lc, '');
        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($pagetitle),
            'content'   => $this->_view->fetch(THEME . '/downloads/showcategs.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function newEntries($limit = 10, $ext = '') {
        $entries = array();
        $query_items = $this->_db->query("SELECT SQL_CACHE
		       a.*,
			a.Kategorie,
			a.Name_{$this->Lc} AS Name,
			a.Beschreibung_{$this->Lc} AS Beschreibung,
			b.Name_{$this->Lc} AS KategName
		FROM
			" . PREFIX . "_downloads AS a
		LEFT JOIN
			" . PREFIX . "_downloads_kategorie AS b
		ON
		        b.Id = a.Kategorie
		WHERE
			a.Sektion='" . AREA . "'
		AND
			a.Aktiv='1'
		ORDER BY a.Id DESC LIMIT " . intval($limit));
        while ($row_items = $query_items->fetch_object()) {
            if ($this->_settings['Wertung'] == 1) {
                $row_items->Wertung = Tool::rating($row_items->Id, 'downloads');
            }
            $allowed = ($ext == 1) ? '' : $this->_allowed;
            $row_items->CCount = Tool::countComments($row_items->Id, 'downloads');
            $row_items->Beschreibung = strip_tags($row_items->Beschreibung, $allowed);
            $row_items->Link_Categ = 'index.php?p=downloads&amp;area=' . AREA . '&amp;categ=' . $row_items->Kategorie . '&amp;name=' . translit($row_items->KategName);
            $row_items->Link_Details = 'index.php?p=downloads&amp;action=showdetails&amp;area=' . AREA . '&amp;categ=' . $row_items->Kategorie . '&amp;id=' . $row_items->Id . '&amp;name=' . translit($row_items->Name);
            $entries[] = $row_items;
        }
        $query_items->close();
        return $entries;
    }

    protected function listCategs($id, $prefix, &$list_categs, &$area) {
        $query = $this->_db->query("SELECT
            a.*,
            a.Name_" . $this->Lc . " AS Name,
            a.Beschreibung_" . $this->Lc . " AS Beschreibung,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_downloads_kategorie AS a,
            " . PREFIX . "_downloads AS b
        WHERE
            a.Parent_Id = '" . intval($id) . "'
        AND
            a.Sektion = '" . intval($area) . "'
        AND
            b.Kategorie = a.Id
        AND
            b.Aktiv = '1'
        GROUP BY a.Id
        ORDER BY Name ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $item->HLink = 'index.php?p=downloads&amp;area=' . AREA . '&amp;categ=' . $item->Id . '&amp;name=' . translit($item->Name);
            $item->LinkCount += $this->linkCount($item->Id);
            $list_categs[] = $item;
            $this->listCategs($item->Id, $prefix . ' - ', $list_categs, $area);
        }
        $query->close();
        return $list_categs;
    }

    protected function linkCount($categ, &$count = 0) {
        $query = $this->_db->query("SELECT
            a.Id,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_downloads_kategorie AS a,
            " . PREFIX . "_downloads AS b
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

    public function file($id) {
        if ($this->__object('Redir')->referer()) {
            if (permission('downloads_candownload')) {
                $id = intval($id);
                $res = $this->_db->cache_fetch_object("SELECT Url FROM " . PREFIX . "_downloads WHERE Id='$id' AND Aktiv='1' LIMIT 1");
                if (!is_object($res) || !is_file(UPLOADS_DIR . '/downloads_files/' . $res->Url)) {
                    $this->__object('Core')->message('Error', 'Downloads_NotFound', $this->__object('Redir')->referer(true), 5);
                } else {
                    $this->_db->query("UPDATE " . PREFIX . "_downloads SET Hits=Hits+1 WHERE Id='$id' AND Aktiv='1'");
                    File::filerange(UPLOADS_DIR . '/downloads_files/' . $res->Url, 'application/octet-stream');
                }
            } else {
                $this->__object('Redir')->seoRedirect('index.php?p=downloads&area=' . AREA);
            }
        } else {
            $this->__object('Core')->message('Global_error', 'ErrorReferer', BASE_URL . '/index.php?p=downloads&area=' . AREA);
        }
    }

    public function update($id) {
        if ($this->__object('Redir')->referer()) {
            $this->_db->query("UPDATE " . PREFIX . "_downloads SET Hits=Hits+1 WHERE Id='" . intval($id) . "'");
        }
    }

    public function deadlink($id) {
        if ($this->__object('Redir')->referer()) {
            $id = intval($id);
            $allowed = array('Links_Broken_dnserror', 'Links_Broken_noconnection', 'Links_Broken_auth', 'Links_Broken_notfound', 'Links_Broken_servererror', 'ActionOther');
            $res = $this->_db->cache_fetch_object("SELECT Id, DefektGemeldet FROM " . PREFIX . "_downloads WHERE Id='$id' LIMIT 1");
            if (is_object($res) && empty($res->DefektGemeldet) && (in_array($_REQUEST['BrokenReason'], $allowed))) {
                $Reason = $this->_lang[$_REQUEST['BrokenReason']];
                $Email = Tool::cleanMail($_REQUEST['email']);
                $Name = Tool::cleanAllow($_REQUEST['name'], ' ');
                $Page = str_replace('&amp;', '&', base64_decode(Tool::cleanUrl($_REQUEST['dpage'])));

                $array = array(
                    'DefektGemeldet' => $Reason,
                    'DEmail'         => $Email,
                    'DName'          => $Name,
                    'DDatum'         => time(),
                );
                $this->_db->update_query('downloads', $array, "Id = '" . $id . "'");

                $mail_array = array(
                    '__BENUTZER__' => $Name,
                    '__MAIL__'    => $Email,
                    '__LINK__'     => $Page,
                    '__GRUND__'    => $Reason);
                $Text = $this->_text->replace($this->_lang['Links_E_BrokenText'], $mail_array);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => SX::get('system.Mail_Absender'),
                    'to_name'   => SX::get('system.Mail_Name'),
                    'text'      => $Text,
                    'subject'   => $this->_lang['Links_E_BrokenSubject'],
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
                SX::output($this->_lang['Links_ErrorSendBrokenOk'], true);
            } else {
                SX::output($this->_lang['Links_ErrorSendBrokenAllready'], true);
            }
        } else {
            SX::output($this->_lang['ErrorReferer'], true);
        }
    }

}
