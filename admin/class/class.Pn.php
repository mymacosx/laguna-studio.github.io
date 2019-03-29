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

class Pn extends Magic {

    protected $user_id;
    protected $max_pn;

    public function __construct() {
        $this->max_pn = SX::get('user_group.MaxPn');
        $this->user_id = $_SESSION['benutzer_id'];
    }

    /* Вывод уведомлений о личном сообщении */
    public function popup() {
        $seo_array = array(
            'headernav' => $this->_lang['PN_PeronalMessages'],
            'pagetitle' => $this->_lang['PN_PeronalMessages'],
            'content'   => $this->_view->fetch(THEME . '/forums/pn_newinfo.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Отключаем вывод всплывающих уведомлений о личном сообщении */
    public function cancel() {
        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET PnPopup = '0' WHERE Id = '" . $_SESSION['benutzer_id'] . "'");
        $this->_view->assign('cancel_popup', 1);

        $seo_array = array(
            'headernav' => $this->_lang['PN_PeronalMessages'],
            'pagetitle' => $this->_lang['PN_PeronalMessages'],
            'content'   => $this->_view->fetch(THEME . '/forums/pn_newinfo.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Поиск пользователя для отправки личных сообщений */
    public function search() {
        $usererg = '';
        if (Arr::getRequest('search') == '1' && !empty($_REQUEST['name'])) {
            $nametemp = Tool::cleanAllow($_REQUEST['name'], '. ');
            $sql = $this->_db->query("SELECT Benutzername AS uname FROM " . PREFIX . "_benutzer WHERE Benutzername LIKE '" . $this->_db->escape($nametemp) . "%' AND Aktiv='1'");
            while ($row = $sql->fetch_object()) {
                $usererg .= "<a href='javascript:userName(\"" . $row->uname . "\");'>$row->uname</a><br />";
            }
            $sql->close();
            if ($usererg) {
                $this->_view->assign('userfound', 1);
            }
        }

        $tpl_array = array(
            'userfound_t' => $this->_lang['PN_searchuser_found'],
            'usererg'     => $usererg,
            'searchuser'  => $this->_lang['PN_SearchUser']);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $this->_lang['PN_SearchUser'],
            'pagetitle' => $this->_lang['PN_SearchUser'],
            'content'   => $this->_view->fetch(THEME . '/forums/searchuser_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Выводим сообщение об ошибке */
    protected function emsg($text) {
        return '<li><span class="error">' . $this->_lang[$text] . '</span></li>';
    }

    /* Делаем редирект */
    protected function redirect() {
        $this->__object('Redir')->seoRedirect('index.php?p=pn&goto=' . Arr::getRequest('goto'));
    }

    /* Выводим статус сообщения, прочитано или нет */
    protected function isreaded($row) {
        $im = ($row->is_readed == 'yes') ? 'readed' : 'unreaded';
        if ($row->reply == 'yes') {
            $im = 'reply';
        }
        if ($row->forward == 'yes') {
            $im = 'forward';
        }
        $icon = '<img hspace="1" src="theme/' . SX::get('options.theme') . '/images/pn/' . $im . '.gif" border="0" alt="" />';
        return $icon;
    }

    /* Удаляем выбранное сообщение */
    protected function delete() {
        $id = intval(Arr::getGet('id'));
        switch (Arr::getGet('goto')) {
            case 'outbox':
                $check = $this->_db->cache_fetch_object("SELECT pnid FROM " . PREFIX . "_pn WHERE typ = 'outbox' AND pnid='$id' AND from_uid='" . $this->user_id . "' LIMIT 1");
                if (is_object($check)) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_pn WHERE pnid='" . $check->pnid . "'");
                }
                break;

            case 'inbox':
                $check = $this->_db->cache_fetch_object("SELECT pnid FROM " . PREFIX . "_pn WHERE typ = 'inbox' AND pnid='$id' AND to_uid='" . $this->user_id . "' LIMIT 1");
                if (is_object($check)) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_pn WHERE pnid='" . $check->pnid . "'");
                }
                break;
        }
        $this->redirect();
    }

    /* Удаляем все сообщения */
    protected function delall() {
        reset($_REQUEST);
        foreach ($_REQUEST as $key => $val) {
            if (substr($key, 0, 3) == 'pn_') {
                $aktid = str_replace('pn_', '', $key);
                $this->_db->query("DELETE FROM " . PREFIX . "_pn WHERE pnid='" . intval($aktid) . "'");
            }
        }
        $this->redirect();
    }

    /* Формирование текста сообщения при пересылке */
    protected function forward() {
        $fwre = Arr::getRequest('forward') == '1' ? 'Fw: ' : 'Re: ';
        $qtext = stripslashes(Arr::getRequest('text'));
        $aut = base64_decode(Arr::getRequest('aut'));
        $subject = base64_decode(Arr::getRequest('subject'));
        $qtext = PE . PE . "------------------------------------------------------------" . PE . $this->_lang['PN_originalmessage'] . PE . $this->_lang['from_t'] . ": " . $aut . PE . $this->_lang['GlobalTheme'] . ": " . $subject . PE . $this->_lang['Date'] . ": " . date('d.m.Y, H:i', base64_decode(Arr::getRequest('date'))) . PE . PE . $qtext;

        $tpl_array = array(
            'tofromname' => $aut,
            'title'      => $fwre . $subject,
            'text'       => $qtext);
        $this->_view->assign($tpl_array);
    }

    public function get() {
        $pnerror = false;
        $ok = $pnin = 1;
        $topic_sel = $outbox_uid = $inbox_uid = $readed_sel = $notreaded_sel = $disp = $thisselect = $pntime_sel = $pp_l = '';
        $limit = Tool::getLim(25);

        $_REQUEST['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        if (!permission('canpn') || $this->max_pn == 0 || $_SESSION['user_group'] == 2) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t');
        } else {
            if (Arr::getRequest('delete_once') == 1) {
                $this->delete();
            }
            if (!empty($_REQUEST['del'])) {
                $this->delall();
            }
            if (empty($_REQUEST['action'])) {
                $goto = (Arr::getRequest('goto') == 'outbox') ? 'outbox' : 'inbox';
                $tofrom = ($goto == 'inbox') ? 'to_uid' : 'from_uid';
                $send_recieve_text = ($goto == 'inbox') ? $this->_lang['recieve_dt'] : $this->_lang['send_dt'];
                $text_fromto = ($goto == 'inbox') ? $this->_lang['from_t'] : $this->_lang['Recipient'];
                $sort = (Arr::getRequest('sort') == 'ASC' || Arr::getRequest('sort') == 'DESC') ? Tool::cleanAllow(Arr::getRequest('sort')) : 'DESC';
                $porder = (!empty($_REQUEST['porder'])) ? Tool::cleanAllow(Arr::getRequest('porder')) : 'pntime';
                if (($porder != 'pntime') && ($porder != 'topic') && ($porder != 'uid') && ($porder != 'readed') && ($porder != 'notreaded')) {
                    $porder = 'pntime';
                }

                if (($goto == 'inbox') && ($porder == 'uid')) {
                    $porder = 'from_uid';
                    $inbox_uid = ' selected="selected"';
                }
                if (($goto == 'outbox') && ($porder == 'uid')) {
                    $porder = 'to_uid';
                    $outbox_uid = ' selected="selected"';
                }

                if ($porder == 'pntime') {
                    $porder = 'pntime';
                    $pntime_sel = ' selected="selected"';
                }

                if ($porder == 'topic') {
                    $porder = 'topic';
                    $topic_sel = ' selected="selected"';
                }

                if ($porder == 'readed') {
                    $porder = "is_readed='yes'";
                    $readed_sel = ' selected="selected"';
                }

                if ($porder == 'notreaded') {
                    $porder = "is_readed='no'";
                    $notreaded_sel = ' selected="selected"';
                }

                $sel_topic_read_unread = '<option value="pntime" ' . $pntime_sel . '>' . $this->_lang['bydate'] . '</option>';
                $sel_topic_read_unread .= '<option value="topic" ' . $topic_sel . '>' . $this->_lang['bytopic'] . '</option>';
                $sel_topic_read_unread .= '<option value="uid" ' . $outbox_uid . $inbox_uid . '>' . $this->_lang['byauthor'] . '</option>';
                $sel_topic_read_unread .= '<option value="readed" ' . $readed_sel . ' >' . $this->_lang['byreaded'] . '</option>';
                $sel_topic_read_unread .= '<option value="notreaded" ' . $notreaded_sel . '>' . $this->_lang['byunreaded'] . '</option>';
                $this->_view->assign('sel_topic_read_unread', $sel_topic_read_unread);

                $a = Tool::getLimit($limit);
                $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_pn WHERE $tofrom='" . $this->user_id . "' AND typ='" . $goto . "' ORDER BY $porder $sort LIMIT $a, $limit");
                $pnin = $this->_db->found_rows();
                $seiten = ceil($pnin / $limit);

                if (Arr::getCookie('listpn') == 'katalog') {
                    $listpn = 1;
                    Arr::setCookie('listpn', 'katalog', 365 * 24 * 3600);
                }

                switch (Arr::getRequest('switchto')) {
                    case 'katalog':
                        $listpn = 1;
                        Arr::setCookie('listpn', 'katalog', 365 * 24 * 3600);
                        $switchto = 'katalog';
                        break;
                    case 'norm':
                        $listpn = -1;
                        Arr::setCookie('listpn', 'norm', 365 * 24 * 3600);
                        $switchto = 'norm';
                        break;
                    default:
                        $switchto = '';
                        break;
                }

                $entry_array = array();
                $table_data = array();
                if (isset($listpn) && $listpn == 1) {
                    while ($row = $sql->fetch_object()) {
                        $row2 = $this->_db->cache_fetch_object("SELECT Benutzername AS uname, Id AS uid FROM " . PREFIX . "_benutzer WHERE Id='" . $row->from_uid . "' LIMIT 1");
                        if (is_object($row2)) {
                            if ($goto == 'inbox') {
                                $theuserid = $row2->uid;
                                $theusername = $row2->uname;
                            } else {
                                $row_emp = $this->_db->cache_fetch_object("SELECT Benutzername AS uname, Id AS uid FROM " . PREFIX . "_benutzer WHERE Id='" . $row->to_uid . "' LIMIT 1");
                                if (is_object($row_emp)) {
                                    $theuserid = $row->to_uid;
                                    $theusername = $row_emp->uname;
                                } else {
                                    $theuserid = '';
                                    $theusername = $this->_lang['PN_undefined'];
                                }
                            }
                        } else {
                            $theuserid = '';
                            $theusername = $this->_lang['PN_undefined'];
                        }

                        $entry_array[] = array('timestamp' => $row->pntime, 'data' => array('title' => $row->topic, 'pntime' => $row->pntime, 'pnday' => $row->pntime, 'von' => $theusername, 'goto' => $goto, 'pnid' => $row->pnid, 'icon' => $this->isreaded($row), 'uid' => $theuserid, 'toid' => 'index.php?p=user&amp;id=' . $theuserid, 'mlink' => 'index.php?p=pn&amp;action=message&amp;id=' . $row->pnid . '&amp;goto=' . $goto));
                    }

                    $last = 0;
                    $ts = array();
                    $ts[0] = array('anfang' => mktime(0, 0, 0, date('m'), date('d'), date('Y')), 'ende' => mktime(23, 59, 59, date('m'), date('d'), date('Y')));
                    $last = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    $wochentag = date('w') + 1;

                    for ($i = 1; $i < $wochentag; $i++) {
                        $a = $wochentag - $i;
                        if (date('d') - $i > 0) {
                            $last -= 86400;
                            $ts[$a] = array('anfang' => $last, 'ende' => $last + 86399);
                        }
                    }

                    $ts[-2] = array('anfang' => $last - (7 * 86400), 'ende' => $last);
                    $last -= 7 * 86400;
                    $ts[-1] = array('anfang' => 0, 'ende' => $last);
                    $wochentage = explode(',', $this->_lang['Calendar_DaysWeek']);
                    foreach ($ts as $key => $val) {
                        switch ($key) {
                            case 0:
                                $t = $this->_lang['today'];
                                $d = ', ' . date('d.m.Y', $val['anfang']);
                                break;

                            case -1:
                                $t = $this->_lang['later'];
                                $d = '';
                                break;

                            case -2:
                                $t = $this->_lang['lastweek'];
                                $d = '';
                                break;

                            default:
                                $t = $wochentage[$key - 2];
                                $d = ', ' . date('d.m.Y', $val['anfang']);
                                break;
                        }

                        $mys = 0;
                        reset($entry_array);
                        foreach ($entry_array as $k => $v) {
                            if ($v['timestamp'] > $val['anfang'] && $v['timestamp'] < $val['ende']) {
                                $mys++;
                            }
                        }
                        if ($mys > 0) {
                            $a = 0;
                            reset($entry_array);
                            foreach ($entry_array as $k => $v) {
                                if ($v['timestamp'] > $val['anfang'] && $v['timestamp'] < $val['ende']) {
                                    $a++;
                                    $v['data']['key'] = $goto . $key;
                                    if ($a == 1) {
                                        $v['data']['header'] = 1;
                                        $v['data']['time'] = $t;
                                        $v['data']['date'] = $d;
                                        if (Arr::getCookie('pn_' . $goto . $key) == 1) {
                                            $v['data']['image'] = 'spoiler_close.png';
                                            $v['data']['display'] = 'none';
                                        } else {
                                            $v['data']['image'] = 'spoiler_open.png';
                                            $v['data']['display'] = '';
                                        }
                                    }
                                    if ($a == $mys) {
                                        $v['data']['end'] = 1;
                                    }
                                    $table_data[] = $v['data'];
                                }
                            }
                        }
                    }
                } else {
                    while ($row = $sql->fetch_object()) {
                        $where_id = ($goto == 'inbox') ? $row->from_uid : $row->to_uid;
                        $row_emp = $this->_db->cache_fetch_object("SELECT Benutzername, Id FROM " . PREFIX . "_benutzer WHERE Id='" . $where_id . "' LIMIT 1");
                        $theuserid = isset($row_emp->Id) ? $row_emp->Id : '';
                        $theusername = isset($row_emp->Benutzername) ? $row_emp->Benutzername : '';
                        array_push($table_data, array('timestamp' => $row->pntime, 'title' => $row->topic, 'pntime' => $row->pntime, 'pnday' => $row->pntime, 'von' => $theusername, 'pnid' => $row->pnid, 'goto' => $goto, 'icon' => $this->isreaded($row), 'toid' => "index.php?p=user&amp;id=" . $theuserid, 'mlink' => "index.php?p=pn&amp;action=message&amp;id=" . $row->pnid . "&amp;goto=" . $goto));
                    }
                }

                switch (Arr::getRequest('porder')) {
                    case 'readed':
                        $porder = 'readed';
                        break;

                    case 'notreaded':
                        $porder = 'notreaded';
                        break;

                    case 'uid':
                        $porder = 'uid';
                        break;
                }

                if ($pnin > $limit) {
                    $nav = $this->__object('Navigation')->pagenav($seiten, " <a class=\"page_navigation\" href=\"index.php?switchto=" . $switchto . "&amp;p=pn&goto=" . $goto . "&amp;sort=" . $sort . "&amp;porder=" . $porder . "&amp;pp=" . $limit . "&amp;page={s}\">{t}</a> ");
                    $this->_view->assign('nav', $nav);
                }

                for ($i = 10; $i <= 50; $i += 10) {
                    if (Arr::getRequest('pp') == $i) {
                        unset($thisselect);
                        $thisselect = 'selected="selected"';
                    }
                    $pp_l .= '<option value="' . $i . '" ' . $thisselect . '>' . $this->_lang['GlobalShow'] . ' ' . $this->_lang['by_t'] . ' ' . $i . ' ' . $this->_lang['eachpage'] . '</option>';
                }

                $page = Arr::getRequest('page');
                switch (Arr::getRequest('sort')) {
                    case 'DESC':
                        $this->_view->assign(array('sel1' => 'selected="selected"', 'sel2' => ''));
                        break;

                    case 'ASC':
                        $this->_view->assign(array('sel2' => 'selected="selected"', 'sel1' => ''));
                        break;
                }

                $links = '&amp;p=pn&amp;goto=' . $goto . '&amp;sort=' . $sort . '&amp;porder=' . $porder . '&amp;pp=' . $limit . '&amp;page=' . $page;
                $tpl_array = array(
                    'pp_l'           => $pp_l,
                    'page'           => $page,
                    'normmodus_link' => 'index.php?switchto=norm' . $links,
                    'katmodus_link'  => 'index.php?switchto=katalog' . $links);
                $this->_view->assign($tpl_array);

                $onepn = 100 / $this->max_pn;
                $allpn = $onepn * $pnin;
                $warningpnfull = ($pnin >= $this->max_pn) ? $this->_lang['PN_warningpnfull'] : '';

                switch ($goto) {
                    case 'inbox':
                        $this->_view->assign(array('selin' => 'selected="selected"', 'view' => 'inbox'));
                        break;

                    case 'outbox':
                        $this->_view->assign(array('selout' => 'selected="selected"', 'view' => 'outbox'));
                        break;
                }
            }

            if (empty($_REQUEST['action'])) {
                $_REQUEST['pp'] = $pp = Arr::getRequest('pp');

                $tpl_array = array(
                    'send_recieve_text' => $send_recieve_text,
                    'title_t'           => $this->_lang['GlobalTitle'],
                    'from_t'            => $text_fromto,
                    'action'            => $this->_lang['Global_Action'],
                    'title'             => $this->_lang['PN_PeronalMessages'],
                    'delmarked'         => $this->_lang['PN_delmarked'],
                    'goto'              => $goto,
                    'pndel_confirm'     => $this->_lang['pndel_confirm'],
                    'inoutwidth'        => round($allpn / 1.005, 3),
                    'inoutpercent'      => round($allpn, 0),
                    'pnioutnall'        => $pnin,
                    'pnmax'             => str_replace('__MAXPN__', $this->max_pn, $this->_lang['pninoutstatus']),
                    'warningpnfull'     => $warningpnfull,
                    'sortdesc'          => 'index.php?p=pn&amp;goto=' . $goto . '&amp;sort=DESC&amp;pp=' . $pp . '&amp;page=' . Tool::prePage(),
                    'sortasc'           => 'index.php?p=pn&amp;goto=' . $goto . '&amp;sort=ASC&amp;pp=' . $pp . '&amp;page=' . Tool::prePage(),
                    'dlpnas'            => $this->_lang['PN_downloadas'],
                    'pndl_text'         => 'index.php?type=text&amp;p=pn&amp;goto=' . $goto . '&amp;download=1',
                    'pndl_html'         => 'index.php?type=html&amp;p=pn&amp;goto=' . $goto . '&amp;download=1',
                    'pndl_text_link'    => $this->_lang['GlobalText'],
                    'pndl_html_link'    => $this->_lang['GlobalHTML'],
                    'outin'             => 1,
                    'neu'               => 0);
                $this->_view->assign($tpl_array);

                if ($pnin) {
                    $this->_view->assign('table_data', $table_data);
                } else {
                    $tpl_array = array(
                        'nopns'      => $this->_lang['NotMessages'],
                        'nomessages' => 1,
                        'outin'      => 0);
                    $this->_view->assign($tpl_array);
                }

                if (Arr::getRequest('download') == 1) {
                    $this->download($goto, $sort, $tofrom, $theusername);
                }
            }

            if (Arr::getRequest('action') == 'message') {
                $pnid = intval(Arr::getRequest('id'));
                $goto = (Arr::getRequest('goto') == 'inbox') ? 'inbox' : 'outbox';
                $tofrom = ($goto == 'inbox') ? 'to_uid' : 'from_uid';
                $row = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_pn WHERE pnid='" . $pnid . "' AND $tofrom='" . $this->user_id . "' AND typ='" . $goto . "' LIMIT 1");
                if (!is_object($row) || empty($row->pnid)) {
                    $this->redirect();
                }
                if ($ok == 1) {
                    $pn_id = $pnid;
                }
                if (Arr::getRequest('do') == 'del') {
                    $this->_db->query("DELETE FROM " . PREFIX . "_pn WHERE pnid='" . $pn_id . "'");
                    $this->redirect();
                }

                if ($goto == 'inbox') {
                    $this->_db->query("UPDATE " . PREFIX . "_pn SET is_readed='yes' WHERE pnid='" . $pnid . "'");
                    $row_subid = $this->_db->cache_fetch_object("SELECT pntime, topic FROM " . PREFIX . "_pn WHERE pnid='" . $pnid . "' LIMIT 1");
                    $this->_db->query("UPDATE " . PREFIX . "_pn SET is_readed='yes' WHERE pntime='" . $row_subid->pntime . "' AND topic='" . $this->_db->escape($row_subid->topic) . "'");
                }

                switch (Arr::getRequest('goto')) {
                    case 'inbox':
                        $sqlid = $row->from_uid;
                        $tfrlink = 'index.php?p=user&amp;id=' . $this->user_id;
                        break;

                    case 'outbox':
                        $sqlid = $row->to_uid;
                        $tfrlink = 'index.php?p=user&amp;id=' . $row->to_uid;
                        break;

                    default:
                        $sqlid = $row->from_uid;
                        break;
                }

                $goto = !empty($_REQUEST['goto']) ? $_REQUEST['goto'] : 'inbox';
                $text_fromto = ($goto == 'inbox') ? $this->_lang['from_t'] : $this->_lang['Recipient'];
                $message = ($row->smilies == 'yes') ? $this->__object('Post')->smilies($this->__object('Post')->codes($row->message)) : $this->__object('Post')->codes($row->message);
                $row_u = $this->_db->cache_fetch_object("SELECT Id AS uid, Benutzername AS uname, Regdatum AS user_regdate, Beitraege AS user_posts, Gruppe AS ugroup FROM " . PREFIX . "_benutzer WHERE Id='" . $sqlid . "' LIMIT 1");

                if (is_object($row_u)) {
                    $row1 = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . $row_u->uid . "' LIMIT 1");
                }
                if (isset($row1) && is_object($row1)) {
                    $row_u2 = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer_gruppen WHERE Id = '" . $row1->Gruppe . "' LIMIT 1");
                }
                $forwardlink = 'index.php?p=pn&action=new&forward=1&id=' . $pn_id . '&subject=' . base64_encode($row->topic) . '&aut=' . base64_encode($row_u->uname) . '&date=' . base64_encode($row->pntime) . '&goto=' . $goto;
                $relink = 'index.php?p=pn&action=new&forward=2&id=' . $pn_id . '&subject=' . base64_encode($row->topic) . '&aut=' . base64_encode($row_u->uname) . '&date=' . base64_encode($row->pntime) . '&goto=' . $goto;

                $tfrlink = 'index.php?p=user&amp;id=' . $row_u->uid;
                if (Arr::getRequest('goto') == 'inbox') {
                    $group = Tool::userGroup($row_u->ugroup);
                } else {
                    $group = Tool::userGroup($row_u2->Id);
                }

                if ($goto == 'inbox') {
                    $this->_view->assign('answerok', 1);
                }

                $tpl_array = array(
                    'message'          => $message,
                    'forward'          => 2,
                    'pn_id'            => $pn_id,
                    'pn_subject'       => base64_encode($row->topic),
                    'pn_aut'           => base64_encode($row_u->uname),
                    'pn_date'          => base64_encode($row->pntime),
                    'pn_goto'          => $goto,
                    'pn_text'          => $row->message,
                    'delpn'            => 'index.php?delete_once=1&p=pn&action=message&id=' . $pn_id . '&goto=' . $goto,
                    'forwardlink'      => $forwardlink,
                    'relink'           => $relink,
                    'delpn_t'          => $this->_lang['delpn_t'],
                    'membersince_date' => $row_u->user_regdate,
                    'membersince'      => $this->_lang['Forums_Field_membersince'],
                    'posts_num'        => $row_u->user_posts,
                    'groupname'        => $group,
                    'posts'            => $this->_lang['Forums_Postings'],
                    'pntitle'          => sanitize($row->topic),
                    'send_dt'          => $this->_lang['send_dt'],
                    'pntime'           => $row->pntime,
                    'pn_t_once'        => $this->_lang['pn_t_once'],
                    'tofromname'       => $row_u->uname,
                    'tofromname_link'  => $tfrlink,
                    'PN_sendtime_t'    => $this->_lang['PN_sendtime_t'],
                    'pndate'           => $row->pntime,
                    'to_t'             => $text_fromto,
                    'showmessage'      => 1);
                $this->_view->assign($tpl_array);
            }

            if (Arr::getRequest('action') == 'new') {
                $num = $this->_db->cache_num_rows("SELECT typ FROM " . PREFIX . "_pn WHERE typ='outbox' and from_uid='" . $this->user_id . "'");
                if ($num == $this->max_pn || $this->max_pn >= MAXPN) {
                    $this->__object('Core')->message('Global_error', 'PN_warningpnfull2', BASE_URL . '/index.php?p=pn&goto=inbox');
                }
                if (Arr::getRequest('send') == '1') {
                    $message = $_REQUEST['text'];
                    if (Arr::getRequest('parseurl') == 'yes') {
                        $message = $this->__object('Post')->parseUrl($message);
                    }
                    if (Arr::getRequest('use_smilies') == 'yes') {
                        $this->_view->assign('preview_text', $this->__object('Post')->smilies($this->__object('Post')->codes($message)));
                    } else {
                        $this->_view->assign('preview_text', $this->__object('Post')->codes($message));
                    }
                    $text = '';
                    $tpl_array = array(
                        'tofromname' => sanitize($_REQUEST['tofromname']),
                        'title'      => sanitize($_REQUEST['title']),
                        'text'       => sanitize($_REQUEST['text']),
                        'preview'    => 1);
                    $this->_view->assign($tpl_array);
                }

                if (Arr::getRequest('send') == '2') {
                    if (empty($_REQUEST['tofromname'])) {
                        $pnerror .= $this->emsg('PN_error_selUser');
                    }
                    if (empty($_REQUEST['title'])) {
                        $pnerror .= $this->emsg('No_Subject');
                    }
                    if (empty($_REQUEST['text'])) {
                        $pnerror .= $this->emsg('PN_error_notext');
                    }
                    if ($this->_text->strlen($_REQUEST['text']) > SX::get('user_group.MaxPn_Zeichen')) {
                        $pnerror .= $this->emsg('PN_error_tomuchtext');
                    }

                    $tpl_array = array(
                        'tofromname' => sanitize($_REQUEST['tofromname']),
                        'title'      => sanitize($_REQUEST['title']),
                        'text'       => sanitize($_REQUEST['text']));
                    $this->_view->assign($tpl_array);

                    $row = $this->_db->fetch_object("SELECT Benutzername AS uname, Id AS uid, Pnempfang FROM " . PREFIX . "_benutzer WHERE Benutzername='" . $this->_db->escape($_REQUEST['tofromname']) . "' AND Aktiv='1' LIMIT 1");

                    if (!$pnerror) {
                        if (!is_object($row) && empty($row->uid)) {
                            $pnerror .= $this->emsg('Forums_User_NotExsist_T');
                        }
                    }

                    if (!$pnerror) {
                        if (Arr::getRequest('tofromname') == $_SESSION['user_name']) {
                            $pnerror .= $this->emsg('PN_error_usersameasuname');
                        }
                    }

                    if (is_object($row) && !$pnerror) {
                        $num_ignore = $this->_db->num_rows("SELECT IgnorierId, BenutzerId  FROM " . PREFIX . "_ignorierliste WHERE BenutzerId='" . $row->uid . "' AND IgnorierId='" . $this->user_id . "'");
                        if ($num_ignore) {
                            $pnerror .= $this->emsg('PN_error_blocked');
                        }
                    }

                    if (!$pnerror) {
                        $query = "SELECT * FROM " . PREFIX . "_pn WHERE to_uid='" . $row->uid . "' AND typ='inbox' ; ";
                        $query .= "SELECT *, b.Id, b.Gruppe, g.Id, g.MaxPn FROM " . PREFIX . "_benutzer AS b, " . PREFIX . "_benutzer_gruppen AS g WHERE b.Gruppe = g.Id AND b.Id = '" . $row->uid . "'";
                        if ($this->_db->multi_query($query)) {
                            if (($result = $this->_db->store_result())) {
                                $numuserpn = $result->num_rows();
                                $result->close();
                            }
                            if (($result = $this->_db->store_next_result())) {
                                $row1 = $result->fetch_object();
                                $result->close();
                            }
                        }
                        if ($numuserpn >= $row1->MaxPn) {
                            $pnerror .= $this->emsg('PN_error_mailbox_user_full');
                        }
                    }

                    if (!$pnerror) {
                        if ($row->Pnempfang != 1 || !permission('canpn')) {
                            $pnerror .= $this->emsg('PN_error_wants_no_pn');
                        }
                    }

                    $text = $this->_text->substr($_REQUEST['text'], 0, SX::get('user_group.MaxPn_Zeichen'));
                    if (Arr::getRequest('parseurl') == 'yes') {
                        $text = $this->__object('Post')->parseUrl($text);
                    }
                    if (!$pnerror) {
                        $use_smilies = Tool::cleanAllow(Arr::getRequest('use_smilies'));
                        $title = Tool::cleanTags(Arr::getRequest('title'), array('codewidget'));

                        $insert_array = array(
                            'smilies'   => $use_smilies,
                            'to_uid'    => $row->uid,
                            'from_uid'  => $this->user_id,
                            'topic'     => $title,
                            'message'   => Tool::cleanTags($text, array('codewidget')),
                            'is_readed' => 'no',
                            'pntime'    => time(),
                            'typ'       => 'inbox');
                        $this->_db->insert_query('pn', $insert_array);

                        if (Arr::getRequest('savecopy') == 'yes') {
                            $insert_array = array(
                                'smilies'   => $use_smilies,
                                'to_uid'    => $row->uid,
                                'from_uid'  => $this->user_id,
                                'topic'     => $title,
                                'message'   => Tool::cleanTags($text, array('codewidget')),
                                'is_readed' => 'no',
                                'pntime'    => time(),
                                'typ'       => 'outbox');
                            $this->_db->insert_query('pn', $insert_array);
                        }

                        if ($row1->Pnempfang == 1 && $row1->PnEmail == 1) {
                            $mail_array = array(
                                '__USER__'  => $row1->Benutzername,
                                '__AUTOR__' => Tool::fullName(),
                                '__LINK__'  => BASE_URL . '/index.php?p=pn&goto=inbox');
                            $body = $this->_text->replace($this->_lang['PN_new_pn_emailbody'], $mail_array);
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $row1->Email,
                                'to_name'   => $row1->Benutzername,
                                'text'      => $body,
                                'subject'   => $this->_lang['PN_subjectnewpnemail'],
                                'fromemail' => SX::get('system.Mail_Absender'),
                                'from'      => SX::get('system.Mail_Name'),
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 3));
                        }
                        $this->__object('Core')->message('PN_PeronalMessages', 'PN_PeronalMessages_ok_t', BASE_URL . '/index.php?p=pn&goto=outbox');
                    }
                }

                if ($pnerror) {
                    $tpl_array = array(
                        'title_error' => $this->_lang['Global_error'],
                        'iserror'     => 1,
                        'error'       => $pnerror);
                    $this->_view->assign($tpl_array);
                }

                if (!empty($_REQUEST['to'])) {
                    $this->_view->assign('tofromname', base64_decode(Arr::getRequest('to')));
                }
                if (!empty($_REQUEST['forward'])) {
                    $this->forward();
                }
                if (SX::get('system.SysCode_Smilies') == 1) {
                    $this->_view->assign('smilie', 1);
                }

                $tpl_array = array(
                    'listfonts'      => $this->__object('Post')->font(),
                    'sizedropdown'   => $this->__object('Post')->fontsize(),
                    'colordropdown'  => $this->__object('Post')->color(),
                    'maxlength_post' => SX::get('user_group.MaxPn_Zeichen'),
                    'listemos'       => $this->__object('Post')->listsmilies(),
                    'newpn_t'        => str_replace('__ZEICHEN__', SX::get('user_group.MaxPn_Zeichen'), $this->_lang['PN_newpn_t']),
                    'newpn_error'    => $pnerror,
                    'outin'          => 0,
                    'neu'            => 1);
                $this->_view->assign($tpl_array);
            }

            $seo_array = array(
                'headernav' => $this->_lang['PN_PeronalMessages'],
                'pagetitle' => $this->_lang['PN_PeronalMessages'] . Tool::numPage(),
                'content'   => $this->_view->fetch(THEME . '/forums/pn.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Скачиваем сообщения */
    protected function download($goto, $sort, $tofrom, $theusername) {
        $req_type = Arr::getRequest('type');
        if ($req_type == 'text') {
            $dlmessage = '';
            $end = '.txt';
        } else {
            $dlmessage  = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            $dlmessage .= '<style><!-- td{font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif;} --></style>';
            $end = '.html';
        }

        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_pn WHERE $tofrom='" . $this->user_id . "' AND typ='" . $goto . "'  ORDER BY pntime " . $sort);
        while ($row = $sql->fetch_object()) {
            if ($goto == 'inbox') {
                $pninout = $this->_lang['PN_inbox'];
                $fname = $theusername;
                $tname = Tool::fullName();
            } else {
                $pninout = $this->_lang['PN_outbox'];
                $fname = Tool::fullName();
                $tname = $theusername;
            }

            if ($req_type == 'text') {
                $dlmessage .= "===============================================================================" . PE . PE;
                $dlmessage .= $this->_lang['from_t'] . ": " . $fname . PE;
                $dlmessage .= $this->_lang['Recipient'] . ": " . $tname . PE;
                $dlmessage .= $this->_lang['Date'] . ": " . date('d-m-Y H:i', $row->pntime) . PE;
                $dlmessage .= $this->_lang['GlobalTheme'] . ": " . $row->topic . PE;
                $dlmessage .= "-------------------------------------------------------------------------------" . PE;
                $dlmessage .= $this->__object('Post')->clean($row->message) . PE . PE;
            } else {
                $dlmessage .= '<table width="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#333333"><tr><td bgcolor="#FFFF00">';
                $dlmessage .= "<strong>" . $this->_lang['from_t'] . "</strong>: " . $fname . "<br />";
                $dlmessage .= "<strong>" . $this->_lang['Recipient'] . "</strong>: " . $tname . "<br />";
                $dlmessage .= "<strong>" . $this->_lang['Date'] . "</strong>: " . date('d-m-Y H:i', $row->pntime) . "<br />";
                $dlmessage .= "<strong>" . $this->_lang['GlobalTheme'] . "</strong>: " . $row->topic . "<br />";
                $dlmessage .= '</td></tr><tr><td>';
                $dlmessage .= $this->codes($row->message);
                $dlmessage .= '</td></tr></table><br />';
            }
        }
        File::download($dlmessage, $pninout . '-' . $this->_lang['PN_PeronalMessages'] . date('d-m-Y') . $end);
    }

    protected function codes($text) {
        if (SX::get('system.SysCode_Aktiv') == 1) {
            $text = $this->__object('Post')->bbcode($text);
        }
        if (SX::get('system.SysCode_Smilies') == 1) {
            $text = $this->__object('Post')->smilies($text);
        }
        return $this->__object('Post')->clean($text);
    }

}
