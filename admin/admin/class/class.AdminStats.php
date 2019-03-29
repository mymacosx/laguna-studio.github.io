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

class AdminStats extends Magic {

    public function deleteSearchs($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_suche_log WHERE Id='" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showSearchs();
    }

    /* Метод сохранения в файл поисковых фраз сайта */
    public function exportSearchs() {
        $export = array();
        $sql = $this->_db->query("SELECT Suche FROM " . PREFIX . "_suche_log WHERE Suche != '' ORDER BY Suche ASC");
        while ($row = $sql->fetch_object()) {
            $export[] = trim($row->Suche);
        }
        $sql->close();
        $export = array_unique($export);
        $export = implode("\r\n", $export);
        File::download($export, 'Экспорт_фраз_поиска_сайта_от_' . date('d-m-Y') . '.txt');
    }

    public function cleanSearchs() {
        if (perm('settings')) {
            Tool::cleanTable('suche_log');
        }
        $this->showSearchs();
    }

    public function showSearchs() {
        $db_sort = " ORDER BY Datum ASC";
        $nav_sort = '&amp;sort=dat_asc';
        $datsort = $ortsort = $suchesort = $ipsort = $usersort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'dat_asc':
            default:
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=dat_asc';
                $datsort = 'dat_desc';
                break;
            case 'dat_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=dat_desc';
                $datsort = 'dat_asc';
                break;
            case 'ort_asc':
                $db_sort = 'ORDER BY Suchort ASC';
                $nav_sort = '&amp;sort=ort_asc';
                $ortsort = 'ort_desc';
                break;
            case 'ort_desc':
                $db_sort = 'ORDER BY Suchort DESC';
                $nav_sort = '&amp;sort=ort_desc';
                $ortsort = 'ort_asc';
                break;
            case 'suche_asc':
                $db_sort = 'ORDER BY Suche ASC';
                $nav_sort = '&amp;sort=suche_asc';
                $suchesort = 'suche_desc';
                break;
            case 'suche_desc':
                $db_sort = 'ORDER BY Suche DESC';
                $nav_sort = '&amp;sort=suche_desc';
                $suchesort = 'suche_asc';
                break;
            case 'ip_asc':
                $db_sort = 'ORDER BY Ip ASC';
                $nav_sort = '&amp;sort=ip_asc';
                $ipsort = 'ip_desc';
                break;
            case 'ip_desc':
                $db_sort = 'ORDER BY Ip DESC';
                $nav_sort = '&amp;sort=ip_desc';
                $ipsort = 'ip_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY UserId ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY UserId DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $pattern = Arr::getRequest('qs');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['qs'] = $pattern = Tool::cleanAllow($pattern, '., ');
            $def_search_n = "&amp;qs=" . urlencode($pattern);
            $def_search = " WHERE (Suche LIKE '%{$this->_db->escape($pattern)}%') ";
        }

        $limit = $this->__object('AdminCore')->limit(20);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_suche_log {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $row->UserNames = $row->UserId == 0 ? $this->_lang['Guest'] : Tool::userName($row->UserId);
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=stats&amp;sub=search{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('datsort', $datsort);
        $this->_view->assign('ortsort', $ortsort);
        $this->_view->assign('suchesort', $suchesort);
        $this->_view->assign('ipsort', $ipsort);
        $this->_view->assign('usersort', $usersort);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['StatSearch']);
        $this->_view->content('/stats/search.tpl');
    }

    public function deleteLogins($id) {
        if (!empty($id)) {
            $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_logins WHERE Id='" . intval($id) . "'");
            $this->__object('AdminCore')->script('save');
        }
        $this->showLogins();
    }

    public function cleanLogins() {
        if (perm('settings')) {
            Tool::cleanTable('benutzer_logins');
            $this->__object('AdminCore')->script('save');
        }
        $this->showLogins();
    }

    public function showLogins() {
        $db_sort = " ORDER BY Datum ASC";
        $nav_sort = '&amp;sort=dat_asc';
        $datsort = $idsort = $mailsort = $ipsort = $def_search_n = $def_search = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            case 'dat_asc':
            default:
                $db_sort = 'ORDER BY Datum ASC';
                $nav_sort = '&amp;sort=dat_asc';
                $datsort = 'dat_desc';
                break;
            case 'dat_desc':
                $db_sort = 'ORDER BY Datum DESC';
                $nav_sort = '&amp;sort=dat_desc';
                $datsort = 'dat_asc';
                break;
            case 'id_asc':
                $db_sort = 'ORDER BY Benutzer ASC';
                $nav_sort = '&amp;sort=id_asc';
                $idsort = 'id_desc';
                break;
            case 'id_desc':
                $db_sort = 'ORDER BY Benutzer DESC';
                $nav_sort = '&amp;sort=id_desc';
                $idsort = 'id_asc';
                break;
            case 'mail_asc':
                $db_sort = 'ORDER BY Email ASC';
                $nav_sort = '&amp;sort=mail_asc';
                $mailsort = 'mail_desc';
                break;
            case 'mail_desc':
                $db_sort = 'ORDER BY Email DESC';
                $nav_sort = '&amp;sort=mail_desc';
                $mailsort = 'mail_asc';
                break;
            case 'ip_asc':
                $db_sort = 'ORDER BY Ip ASC';
                $nav_sort = '&amp;sort=ip_asc';
                $ipsort = 'ip_desc';
                break;
            case 'ip_desc':
                $db_sort = 'ORDER BY Ip DESC';
                $nav_sort = '&amp;sort=ip_desc';
                $ipsort = 'ip_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 1) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, '.,@ ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $pattern = $this->_db->escape($pattern);
            $def_search = " WHERE (Benutzer LIKE '%{$pattern}%' OR Email LIKE '%{$pattern}%' OR Datum_dt LIKE '%{$pattern}%' OR Ip LIKE '%{$pattern}%') ";
        }

        $limit = $this->__object('AdminCore')->limit(20);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_benutzer_logins {$def_search} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $row->Name = Tool::userName($row->Benutzer);
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=stats&amp;sub=autorize{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('datsort', $datsort);
        $this->_view->assign('idsort', $idsort);
        $this->_view->assign('mailsort', $mailsort);
        $this->_view->assign('ipsort', $ipsort);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('items', $items);
        $this->_view->assign('title', $this->_lang['Stats_Autorize']);
        $this->_view->content('/stats/autorize.tpl');
    }

    public function showChart() {
        $jahr = Arr::getPost('jahr');
        $_POST['jahr'] = $jahr = !empty($jahr) ? intval($jahr) : date('Y');
        $monat = Arr::getPost('monat');
        $_POST['monat'] = $monat = !empty($monat) ? intval($monat) : date('m');
        $day_count = date('t', mktime(0, 0, 0, $monat, 1, $jahr));

        for ($y = 1; $y <= 12; $y++) {
            $res_mon[$y] = $this->_db->cache_fetch_assoc("SELECT SUM(Gesamt_Wert) AS Alle, SUM(Hits) AS Hits FROM  " . PREFIX . "_counter_werte WHERE Jahr='" . $jahr . "' AND Monat='" . $y . "'");
        }

        for ($x = 1; $x <= $day_count; $x++) {
            $res_day[$x] = $this->_db->cache_fetch_assoc("SELECT SUM(Gesamt_Wert) AS Alle, SUM(Hits) AS Hits FROM  " . PREFIX . "_counter_werte WHERE Jahr='" . $jahr . "' AND Tag='" . $x . "' AND Monat='" . $monat . "'");
        }

        $s = array();
        $s['w10'] = $s['w8_1'] = $s['w8'] = $s['w7'] = $s['wv'] = $s['w3'] = $s['w0'] = $s['wxp'] = $s['wnt'] = $s['w98'] = $s['w95'] = $s['uw'] = $s['mx'] = $s['im'] = $s['pm'] = $s['pp'] = $s['cy'] = $s['li'] = $s['de'] = $s['ov'] = $s['ss'] = $s['am'] = $s['bo'] = $s['ab'] = $s['fb'] = $s['nb'] = $s['bs'] = $s['ob'] = $s['ai'] = $s['ir'] = $s['do'] = $s['hu'] = $s['uu'] = 0;

        $sql = $this->_db->query("SELECT Os FROM  " . PREFIX . "_counter_referer");
        while ($row = $sql->fetch_object()) {
            switch ($row->Os) {
                case 'Windows 10': $s['w10'] ++;
                    break;
                case 'Windows 8.1': $s['w8_1'] ++;
                    break;
                case 'Windows 8': $s['w8'] ++;
                    break;
                case 'Windows 7': $s['w7'] ++;
                    break;
                case 'Windows Vista': $s['wv'] ++;
                    break;
                case 'Windows 2003': $s['w3'] ++;
                    break;
                case 'Windows 2000': $s['w0'] ++;
                    break;
                case 'Windows XP': $s['wxp'] ++;
                    break;
                case 'Windows NT': $s['wnt'] ++;
                    break;
                case 'windows 98': $s['w98'] ++;
                    break;
                case 'windows 95': $s['w95'] ++;
                    break;
                case 'Unknown Windows OS': $s['uw'] ++;
                    break;
                case 'Mac OS X': $s['mx'] ++;
                    break;
                case 'Intel Mac': $s['im'] ++;
                    break;
                case 'PowerPC Mac': $s['pm'] ++;
                    break;
                case 'PowerPC': $s['pp'] ++;
                    break;
                case 'Cygwin': $s['cy'] ++;
                    break;
                case 'Linux': $s['li'] ++;
                    break;
                case 'Debian': $s['de'] ++;
                    break;
                case 'OpenVMS': $s['ov'] ++;
                    break;
                case 'Sun Solaris': $s['ss'] ++;
                    break;
                case 'Amiga': $s['am'] ++;
                    break;
                case 'BeOS': $s['bo'] ++;
                    break;
                case 'ApacheBench': $s['ab'] ++;
                    break;
                case 'FreeBSD': $s['fb'] ++;
                    break;
                case 'NetBSD': $s['nb'] ++;
                    break;
                case 'BSDi': $s['bs'] ++;
                    break;
                case 'OpenBSD': $s['ob'] ++;
                    break;
                case 'AIX': $s['ai'] ++;
                    break;
                case 'Irix': $s['ir'] ++;
                    break;
                case 'DEC OSF': $s['do'] ++;
                    break;
                case 'HP-UX': $s['hu'] ++;
                    break;
                case 'Unknown Unix OS': $s['uu'] ++;
                    break;
            }
        }
        $sql->close();

        $this->_view->assign('s', $s);
        $this->_view->assign('res_mon', $res_mon);
        $this->_view->assign('res_day', $res_day);
        $this->_view->assign('start', date('Y') - 10);
        $this->_view->assign('end', date('Y'));
        $this->_view->assign('title', $this->_lang['Stats']);
        $this->_view->content('/stats/overview.tpl');
    }

    public function showReferer() {
        $db_sort = " ORDER BY Datum_Int ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $def_search_n = $def_search = $namesort = $ossort = $browsersort = $ipsort = $wsort = $usersort = '';

        if (Arr::getGet('del') == 1 && perm('referer_del')) {
            Tool::cleanTable('counter_referer');
        }

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'date_desc':
                $db_sort = 'ORDER BY Datum_Int DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum_Int ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'w_asc':
                $db_sort = 'ORDER BY Words ASC';
                $nav_sort = '&amp;sort=w_asc';
                $wsort = 'w_desc';
                break;
            case 'w_desc':
                $db_sort = 'ORDER BY Words DESC';
                $nav_sort = '&amp;sort=w_desc';
                $wsort = 'w_asc';
                break;
            case 'name_asc':
                $db_sort = 'ORDER BY Referer ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Referer DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'os_asc':
                $db_sort = 'ORDER BY Os ASC';
                $nav_sort = '&amp;sort=os_asc';
                $ossort = 'os_desc';
                break;
            case 'os_desc':
                $db_sort = 'ORDER BY Os DESC';
                $nav_sort = '&amp;sort=os_desc';
                $ossort = 'os_asc';
                break;
            case 'browser_asc':
                $db_sort = 'ORDER BY Ua ASC';
                $nav_sort = '&amp;sort=browser_asc';
                $browsersort = 'browser_desc';
                break;
            case 'browser_desc':
                $db_sort = 'ORDER BY Ua DESC';
                $nav_sort = '&amp;sort=browser_desc';
                $browsersort = 'browser_asc';
                break;
            case 'ip_asc':
                $db_sort = 'ORDER BY IPAdresse ASC';
                $nav_sort = '&amp;sort=ip_asc';
                $ipsort = 'ip_desc';
                break;
            case 'ip_desc':
                $db_sort = 'ORDER BY IPAdresse DESC';
                $nav_sort = '&amp;sort=ip_desc';
                $ipsort = 'ip_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY UserName ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY UserName DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $pattern = Arr::getRequest('q');
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ]');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $pattern = $this->_db->escape($pattern);
            $def_search = " AND (Ua='{$pattern}' OR Os='{$pattern}' OR Referer LIKE '%{$pattern}%' OR Words LIKE '%{$pattern}%' OR IPAdresse='{$pattern}' OR UserName='{$pattern}') ";
        }

        $where_not = str_replace(array('www.', 'http://', 'https://'), '', BASE_URL);
        $like = "Referer NOT LIKE '%{$where_not}%'";
        $noref = (Arr::getRequest('noref') == 1) ? " AND Referer != '' " : '';
        $words = (Arr::getRequest('words') == 1) ? " AND Words != '' " : '';

        $limit = $this->__object('AdminCore')->limit(25);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_counter_referer WHERE {$like} {$def_search} {$noref} {$words} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $referer = array();
        while ($row = $sql->fetch_object()) {
            $row->UserNames = ($row->UserName == 'UNAME') ? $this->_lang['Guest'] : $row->UserName;
            $referer[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=stats&amp;sub=referer{$def_search_n}{$nav_sort}&amp;pp={$limit}&amp;noref=" . Arr::getRequest('noref', 0) . "&amp;words=" . Arr::getRequest('words', 0) . "&amp;page={s}\">{t}</a> "));
        }
        $this->_view->assign('wsort', $wsort);
        $this->_view->assign('ipsort', $ipsort);
        $this->_view->assign('browsersort', $browsersort);
        $this->_view->assign('ossort', $ossort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('usersort', $usersort);
        $this->_view->assign('referer', $referer);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('def_sort_n', $nav_sort);
        $this->_view->assign('title', $this->_lang['Stats_Referer']);
        $this->_view->content('/stats/referer.tpl');
    }

    /* Ёкспорт поисковых фраз */
    public function exportSearch() {
        $export = array();
        $sql = $this->_db->query("SELECT Words FROM " . PREFIX . "_counter_referer WHERE Words != '' ORDER BY Words ASC");
        while ($row = $sql->fetch_object()) {
            $export[] = trim($row->Words);
        }
        $sql->close();
        $export = array_unique($export);
        $export = implode("\r\n", $export);
        File::download($export, 'Экспорт_поисковых_фраз_от_' . date('d-m-Y') . '.txt');
    }

    public function userMaps() {
        $where = array();
        $tpl = 'user_map_all.tpl';
        $db_sort = " ORDER BY Datum_Int ASC";
        $nav_sort = '&amp;sort=name_asc';
        $datesort = $def_search_n = $namesort = $ossort = $browsersort = $ipsort = $noframes = $usersort = $select = '';

        $_REQUEST['sort'] = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        switch ($_REQUEST['sort']) {
            default:
            case 'date_desc':
                $db_sort = 'ORDER BY Datum_Int DESC';
                $nav_sort = '&amp;sort=date_desc';
                $datesort = 'date_asc';
                break;
            case 'date_asc':
                $db_sort = 'ORDER BY Datum_Int ASC';
                $nav_sort = '&amp;sort=date_asc';
                $datesort = 'date_desc';
                break;
            case 'name_asc':
                $db_sort = 'ORDER BY Url ASC';
                $nav_sort = '&amp;sort=name_asc';
                $namesort = 'name_desc';
                break;
            case 'name_desc':
                $db_sort = 'ORDER BY Url DESC';
                $nav_sort = '&amp;sort=name_desc';
                $namesort = 'name_asc';
                break;
            case 'os_asc':
                $db_sort = 'ORDER BY Os ASC';
                $nav_sort = '&amp;sort=os_asc';
                $ossort = 'os_desc';
                break;
            case 'os_desc':
                $db_sort = 'ORDER BY Os DESC';
                $nav_sort = '&amp;sort=os_desc';
                $ossort = 'os_asc';
                break;
            case 'browser_asc':
                $db_sort = 'ORDER BY Ua ASC';
                $nav_sort = '&amp;sort=browser_asc';
                $browsersort = 'browser_desc';
                break;
            case 'browser_desc':
                $db_sort = 'ORDER BY Ua DESC';
                $nav_sort = '&amp;sort=browser_desc';
                $browsersort = 'browser_asc';
                break;
            case 'ip_asc':
                $db_sort = 'ORDER BY IPAdresse ASC';
                $nav_sort = '&amp;sort=ip_asc';
                $ipsort = 'ip_desc';
                break;
            case 'ip_desc':
                $db_sort = 'ORDER BY IPAdresse DESC';
                $nav_sort = '&amp;sort=ip_desc';
                $ipsort = 'ip_asc';
                break;
            case 'user_asc':
                $db_sort = 'ORDER BY UserName ASC';
                $nav_sort = '&amp;sort=user_asc';
                $usersort = 'user_desc';
                break;
            case 'user_desc':
                $db_sort = 'ORDER BY UserName DESC';
                $nav_sort = '&amp;sort=user_desc';
                $usersort = 'user_asc';
                break;
        }

        $r_user = Arr::getRequest('user');
        if (!empty($r_user) && is_numeric($r_user)) {
            $noframes = '&amp;user=' . $r_user . '&amp;noframes=1';
            $where[] = "UserId = " . intval($r_user);
            $tpl = 'user_map.tpl';
        }

        $pattern = trim(Arr::getRequest('q'));
        if (!empty($pattern) && $pattern != 'empty' && $this->_text->strlen($pattern) >= 3) {
            $_REQUEST['q'] = $pattern = Tool::cleanAllow($pattern, ',.:\/&=? ');
            $def_search_n = '&amp;q=' . urlencode($pattern);
            $pattern = $this->_db->escape($pattern);
            $where[] = "(Ua='{$pattern}' OR Os='{$pattern}' OR Url LIKE '%{$pattern}%' OR IPAdresse='{$pattern}' OR UserName='{$pattern}')";
        }

        if (!empty($where)) {
            $select = 'WHERE ' . implode(' AND ', $where);
        }
        $limit = $this->__object('AdminCore')->limit(25);
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_counter_referer {$select} {$db_sort} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $referer = array();
        while ($row = $sql->fetch_object()) {
            $row->UserNames = ($row->UserName == 'UNAME') ? $this->_lang['Guest'] : $row->UserName;
            $referer[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=stats&amp;sub=user_map{$def_search_n}{$nav_sort}&amp;pp={$limit}{$noframes}&amp;page={s}\">{t}</a> "));
        }

        $this->_view->assign('ipsort', $ipsort);
        $this->_view->assign('browsersort', $browsersort);
        $this->_view->assign('ossort', $ossort);
        $this->_view->assign('namesort', $namesort);
        $this->_view->assign('datesort', $datesort);
        $this->_view->assign('usersort', $usersort);
        $this->_view->assign('referer', $referer);
        $this->_view->assign('limit', $limit);
        $this->_view->assign('def_sort_n', $nav_sort);
        $this->_view->assign('noframes', $noframes);
        $this->_view->assign('title', $this->_lang['SiteMapUser']);
        $this->_view->content('/stats/' . $tpl);
    }

}
