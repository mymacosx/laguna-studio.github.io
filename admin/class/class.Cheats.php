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

class Cheats extends Magic {

    protected $Lc;
    protected $_settings;
    protected $_allowed;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->_settings = SX::get('cheats');
        $this->_allowed = SX::get('system.allowed');
        $this->_view->assign('CheatSettings', (object) $this->_settings);
    }

    public function recent() {
        $this->_view->assign('NewCheatsEntries', $this->load(SX::get('section.LimitNewCheats'), 1));
        return $this->_view->fetch(THEME . '/cheats/new_start.tpl');
    }

    protected function manufacturer($id) {
        $res = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_hersteller WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? '<a href="index.php?p=manufacturer&amp;area=' . AREA . '&amp;action=showdetails&amp;id=' . $res->Id . '&amp;name=' . translit($res->Name) . '">' . sanitize($res->Name) . '</a>' : '';
    }

    protected function plattform($id) {
        $res = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Id='" . intval($id) . "' LIMIT 1");
        return is_object($res) ? '<a href="index.php?p=cheats&amp;area=' . AREA . '&amp;plattform=' . $res->Id . '&amp;name=' . translit($res->Name) . '">' . sanitize($res->Name) . '</a>' : '';
    }

    public function search($search) {
        $search_like = '';
        $search = urldecode($search);
        if (!empty($search) && $this->_text->strlen($search) >= 2) {
            $this->__object('Core')->monitor($search, 'cheats');
            $like = $this->_db->escape($search);
            $like2 = $this->_db->escape(sanitize($search));
            $search_like = "AND ((Name_{$this->Lc} LIKE '%{$like}%' OR Beschreibung_{$this->Lc} LIKE '%{$like}%') OR (Name_{$this->Lc} LIKE '%{$like2}%' OR Beschreibung_{$this->Lc} LIKE '%{$like2}%'))";
        }
        $limit = Tool::getLim($this->_settings['PageLimit']);
        $a = Tool::getLimit($limit);
        $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Name_{$this->Lc} AS Name, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_cheats WHERE Aktiv='1' {$search_like} AND Sektion = '" . AREA . "' ORDER BY Name_{$this->Lc} ASC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $entries = array();
        while ($row_items = $query_items->fetch_object()) {
            if ($this->_settings['Wertung'] == 1) {
                $row_items->Wertung = Tool::rating($row_items->Id, 'cheats');
            }
            $res_name = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Id='$row_items->Plattform' LIMIT 1");
            $row_items->KategName = $res_name->Name;
            $row_items->Link_Categ = "index.php?p=cheats&amp;area=" . AREA . "&amp;plattform=" . $row_items->Plattform . "&amp;name=" . translit($res_name->Name);
            $row_items->CCount = Tool::countComments($row_items->Id, 'cheats');
            $row_items->Beschreibung = $this->_text->strlen($search) >= 2 ? Tool::highlight(strip_tags($row_items->Beschreibung, '<span>'), $search) : strip_tags($row_items->Beschreibung, $this->_allowed);
            $row_items->Link_Details = "index.php?p=cheats&amp;action=showcheat&amp;area=" . AREA . "&amp;plattform=" . $row_items->Plattform . "&amp;id=" . $row_items->Id . "&amp;name=" . translit($row_items->Name);
            $entries[] = $row_items;
        }
        $query_items->close();

        if ($num > $limit) {
            $search = empty($search) ? '-' : $search;
            $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?ql=" . urlencode($search) . "&amp;action=search&amp;p=cheats&amp;area=" . AREA . "&amp;page={s}\">{t}</a> "));
        }

        $this->_view->assign('Entries', $entries);
        $this->_view->assign('Results', $this->_view->fetch(THEME . '/cheats/items.tpl'));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=cheats&amp;area=' . AREA . '">' . $this->_lang['Gaming_cheats'] . '</a> ',
            'pagetitle' => $this->_lang['Gaming_cheats'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/cheats/search_results.tpl'));
        $this->_view->finish($seo_array);
    }

    public function get($id) {
        $id = ($id);
        $res = $this->_db->cache_fetch_object("SELECT *, Name_{$this->Lc} AS Name, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_cheats WHERE Id='$id' AND Aktiv='1' LIMIT 1");
        if (!is_object($res)) {
            $this->__object('Redir')->seoRedirect('index.php?p=cheats&area=' . AREA);
        }
        if (!isset($_SESSION['cheat_read_' . $id])) {
            $this->_db->query("UPDATE " . PREFIX . "_cheats SET Hits=Hits+1 WHERE Id='{$id}'");
            $_SESSION['cheat_read_' . $id] = 1;
        }

        $gc = $this->plattform($res->Plattform);
        $res->UserName = Tool::userName($res->Benutzer);

        if (!empty($res->Galerien) && get_active('gallery')) {
            $this->_view->assign('IncludedGalleries', $this->__object('Gallery')->includedGallery($res->Galerien));
        }

        if ($this->_settings['Kommentare'] == 1) {
            // Подключаем вывод комментариев
            $comment_url = 'index.php?p=cheats&amp;action=showcheat&amp;plattform=' . $res->Plattform . '&amp;id=' . $id . '&amp;name=' . translit($res->Name);
            $this->__object('Comments')->load('cheats', $id, $comment_url);
        }

        if ($this->_settings['Wertung'] == 1) {
            $this->_view->assign('RatingUrl', 'index.php?p=rating&action=rate&id=' . $id . '&where=cheats');
            $this->_view->assign('RatingForm', $this->_view->fetch(THEME . '/other/rating.tpl'));
        }

        $res->Beschreibung = $this->__object('Glossar')->get($res->Beschreibung);
        $res->Pf = $this->plattform($res->Plattform);
        $res->Wertung = Tool::rating($res->Id, 'cheats');
        $res->Mf = $this->manufacturer($res->Hersteller);

        if (!empty($res->CheatLinks)) {
            $alternatives = array();
            $mirrors = explode("\r\n", $res->CheatLinks);
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
        } else {
            $res->Traffic = File::filesize(filesize(UPLOADS_DIR . '/cheats_files/' . $res->Download) * $res->DownloadHits / 1024);
            $res->Size = File::filesize(filesize(UPLOADS_DIR . '/cheats_files/' . $res->Download) / 1024);
        }

        $this->_view->assign('BrokenLinkSubmit', 'index.php?action=brokenlink&p=cheats&id=' . $id);
        $this->_view->assign('cheat_res', $res);

        $seo_array = array(
            'headernav' => '<a href="index.php?p=cheats&amp;area=' . AREA . '">' . $this->_lang['Gaming_cheats'] . '</a>' . $this->_lang['PageSep'] . $gc,
            'pagetitle' => sanitize($res->Name . $this->_lang['PageSep'] . $this->_lang['Gaming_cheats']),
            'generate'  => $this->_lang['Gaming_cheats'] . ' ' . $res->Beschreibung,
            'content'   => $this->_view->fetch(THEME . '/cheats/show.tpl'));
        $this->_view->finish($seo_array);
    }

    public function show() {
        $getid = !empty($_REQUEST['plattform']) ? intval($_REQUEST['plattform']) : 0;

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'datedesc':
                $req = 'datedesc';
                $defsort = "ORDER BY DatumUpdate DESC";
                $defsort_n = '&amp;sort=datedesc';
                $this->_view->assign(array('img_date' => 'sorter_down', 'datesort' => 'dateasc'));
                break;

            case 'dateasc':
                $req = 'dateasc';
                $defsort = "ORDER BY DatumUpdate ASC";
                $defsort_n = '&amp;sort=dateasc';
                $this->_view->assign(array('img_date' => 'sorter_up', 'datesort' => 'datedesc'));
                break;

            case 'namedesc':
                $req = 'namedesc';
                $defsort = "ORDER BY Name_1 DESC";
                $defsort_n = '&amp;sort=namedesc';
                $this->_view->assign(array('img_name' => 'sorter_down', 'namesort' => 'nameasc'));
                break;

            case 'nameasc':
                $req = 'nameasc';
                $defsort = "ORDER BY Name_1 ASC";
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
        $prefix = $news_categ = '';
        $area = AREA;
        $news_categ = array();
        $Categs = $this->categs($prefix, $news_categ, $area);

        if (!empty($getid)) {
            $res_name = $this->_db->cache_fetch_object("SELECT Id, Name FROM " . PREFIX . "_plattformen WHERE Id='$getid' LIMIT 1");
            $limit = Tool::getLim($this->_settings['PageLimit']);
            $a = Tool::getLimit($limit);
            $query_items = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS *, Plattform, Name_{$this->Lc} AS Name, Beschreibung_{$this->Lc} AS Beschreibung FROM " . PREFIX . "_cheats WHERE Plattform='$getid' AND Aktiv='1' {$defsort} LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $entries = array();
            while ($row_items = $query_items->fetch_object()) {
                if ($this->_settings['Wertung'] == 1) {
                    $row_items->Wertung = Tool::rating($row_items->Id, 'cheats');
                }
                $row_items->CCount = Tool::countComments($row_items->Id, 'cheats');
                $row_items->KategName = $res_name->Name;
                $row_items->Beschreibung = strip_tags($row_items->Beschreibung, $this->_allowed);
                $row_items->Link_Details = 'index.php?p=cheats&amp;action=showcheat&amp;area=' . AREA . '&amp;plattform=' . $getid . '&amp;id=' . $row_items->Id . '&amp;name=' . translit($row_items->Name);
                $entries[] = $row_items;
            }
            $query_items->close();

            if ($num > $limit) {
                $this->_view->assign('Navi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=cheats&amp;area=" . AREA . "&amp;plattform=" . $getid . "&amp;name=" . translit($res_name->Name) . "&amp;page={s}{$defsort_n}\">{t}</a> "));
            }
            $this->_view->assign('Entries', $entries);
        }

        if (empty($getid)) {
            $this->_view->assign('Entries', $this->load());
        }
        $this->_view->assign(array('Categs' => $Categs, 'CategName' => (!empty($res_name->Name) ? translit($res_name->Name) : '')));

        if (!empty($res_name->Name)) {
            $pagetitle = $res_name->Name . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['Gaming_cheats'];
        } else {
            $pagetitle = $this->_lang['Gaming_cheats'] . Tool::numPage();
        }

        $headernav = '<a href="index.php?p=cheats&amp;area=' . AREA . '">' . $this->_lang['Gaming_cheats'] . '</a>' . (($getid > 0) ? $this->_lang['PageSep'] . $this->plattform($getid) : '');
        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($pagetitle),
            'content'   => $this->_view->fetch(THEME . '/cheats/plattforms.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function load($limit = 10, $ext = 0) {
        $entries = array();
        $query_items = $this->_db->query("SELECT SQL_CACHE
		        a.*,
			a.Plattform,
			a.Name_{$this->Lc} AS Name,
			a.Beschreibung_{$this->Lc} AS Beschreibung,
			b.Name AS KategName
		FROM
			" . PREFIX . "_cheats AS a
		LEFT JOIN
			" . PREFIX . "_plattformen AS b
		ON
		        b.Id = a.Plattform
		WHERE
			a.Sektion='" . AREA . "'
		AND
			a.Aktiv='1'
		ORDER BY a.Id DESC LIMIT " . intval($limit));
        while ($row_items = $query_items->fetch_object()) {
            if ($ext != 1) {
                if ($this->_settings['Wertung'] == 1) {
                    $row_items->Wertung = Tool::rating($row_items->Id, 'cheats');
                }
                $row_items->CCount = Tool::countComments($row_items->Id, 'cheats');
                $row_items->Link_Categ = 'index.php?p=cheats&amp;area=' . AREA . '&amp;plattform=' . $row_items->Plattform . '&amp;name=' . translit($row_items->KategName);
                $row_items->Beschreibung = strip_tags($row_items->Beschreibung, $this->_allowed);
            } else {
                $row_items->Beschreibung = strip_tags($row_items->Beschreibung);
            }
            $row_items->Link_Details = 'index.php?p=cheats&amp;action=showcheat&amp;area=' . AREA . '&amp;plattform=' . $row_items->Plattform . '&amp;id=' . $row_items->Id . '&amp;name=' . translit($row_items->Name);
            $entries[] = $row_items;
        }
        $query_items->close();
        return $entries;
    }

    protected function categs($prefix, &$list_categs, &$area) {
        $query = $this->_db->query("SELECT
            a.*,
            COUNT(b.Id) AS LinkCount
        FROM
            " . PREFIX . "_plattformen AS a,
            " . PREFIX . "_cheats AS b
        WHERE
            a.Sektion = '" . intval($area) . "'
        AND
            b.Plattform = a.Id
        AND
            b.Aktiv = '1'
        GROUP BY a.Id
        ORDER BY a.Name ASC");
        while ($item = $query->fetch_object()) {
            $item->visible_title = $prefix . ' ' . $item->Name;
            $item->HLink = 'index.php?p=cheats&amp;area=' . $item->Sektion . '&amp;plattform=' . $item->Id . '&amp;name=' . translit($item->Name);
            $list_categs[] = $item;
        }
        $query->close();
        return $list_categs;
    }

    public function file($id) {
        if ($this->__object('Redir')->referer()) {
            if (permission('cheats_candownload')) {
                $id = intval($id);
                $res = $this->_db->cache_fetch_object("SELECT Download AS Url FROM " . PREFIX . "_cheats WHERE Id='$id' AND Aktiv='1' LIMIT 1");
                if (!is_object($res) || !is_file(UPLOADS_DIR . '/cheats_files/' . $res->Url)) {
                    $this->__object('Core')->message('Error', 'Downloads_NotFound', $this->__object('Redir')->referer(true), 5);
                } else {
                    $this->_db->query("UPDATE " . PREFIX . "_cheats SET DownloadHits=DownloadHits+1 WHERE Id='$id' AND Aktiv='1'");
                    File::filerange(UPLOADS_DIR . '/cheats_files/' . $res->Url, 'application/octet-stream');
                }
            } else {
                $this->__object('Redir')->seoRedirect('index.php?p=cheats&area=' . AREA);
            }
        } else {
            $this->__object('Core')->message('Global_error', 'ErrorReferer', BASE_URL . '/index.php?p=cheats&area=' . AREA);
        }
    }

    public function deadlink($id) {
        if ($this->__object('Redir')->referer()) {
            $id = intval($id);
            $allowed = array('Links_Broken_dnserror', 'Links_Broken_noconnection', 'Links_Broken_auth', 'Links_Broken_notfound', 'Links_Broken_servererror', 'ActionOther');
            $res = $this->_db->cache_fetch_object("SELECT Id, DefektGemeldet FROM " . PREFIX . "_cheats WHERE Id='$id' LIMIT 1");
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
                $this->_db->update_query('cheats', $array, "Id = '" . $id . "'");

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
