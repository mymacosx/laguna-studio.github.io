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

class Poll extends Magic {

    protected $Lc;
    protected $stime;

    public function __construct() {
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->stime = time();
    }

    public function show() {
        $this->_view->assign('out', $this->load());
        $this->_view->assign('PollOutSmall', $this->_view->fetch(THEME . '/poll/pollout_small.tpl'));
    }

    public function archive() {
        $limit = Tool::getLim();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * , Titel_1 AS Titel FROM " . PREFIX . "_umfrage WHERE Sektion='" . AREA . "' AND Start<='" . $this->stime . "' ORDER BY Aktiv DESC, Id DESC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $polls = array();
        while ($row = $sql->fetch_object()) {
            $PollItems = array();
            $query = "SELECT SUM(Hits) AS HitCount FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$row->Id' ; ";
            $query .= "SELECT * , Frage_{$this->Lc} AS Frage FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$row->Id' ORDER BY Position ASC";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $pollcount = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    while ($row_answ = $result->fetch_object()) {
                        $row_answ->Perc = ($row_answ->Hits == 0) ? 1 : round(100 / $pollcount->HitCount * $row_answ->Hits, 2);
                        $PollItems[] = $row_answ;
                    }
                    $result->close();
                }
            }
            $row->HitsAll = $pollcount->HitCount;
            $row->PollItems = $PollItems;
            $polls[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('pollNavi', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\" href=\"index.php?p=poll&amp;action=archive&amp;page={s}&amp;area=" . AREA . "\">{t}</a> "));
        }
        $this->_view->assign('polls', $polls);

        $seo_array = array(
            'headernav' => $this->_lang['Poll_Archive'],
            'pagetitle' => $this->_lang['Poll_Archive'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/poll/pollarchive.tpl'));
        $this->_view->finish($seo_array);
    }

    public function current($id = '') {
        $id = intval($id);
        if (empty($id)) {
            $poll = $this->result(Arr::getRequest('polloption'), 1, '', '');
        } else {
            $PollItemsOnce = array();
            $query = "SELECT SUM(Hits) AS HitCount FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='" . $id . "' ; ";
            $query .= "SELECT Titel_{$this->Lc} AS Titel FROM " . PREFIX . "_umfrage WHERE Id='" . $id . "' ; ";
            $query .= "SELECT * , Frage_{$this->Lc} AS Frage FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='" . $id . "' ORDER BY Position ASC";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $pollcount = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    $Question = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    while ($row_answ = $result->fetch_object()) {
                        $row_answ->Perc = ($row_answ->Hits == 0) ? 1 : round(100 / $pollcount->HitCount * $row_answ->Hits, 2);
                        $PollItemsOnce[] = $row_answ;
                    }
                    $result->close();
                }
            }

            $tpl_array = array(
                'Question'   => $Question->Titel,
                'polls_once' => $PollItemsOnce,
                'Extern'     => 1);
            $this->_view->assign($tpl_array);
            $poll = $this->_view->fetch(THEME . '/poll/polldisplay_result.tpl');
        }

        $where = !empty($id) ? " WHERE Id = '$id' AND Start<='" . $this->stime . "'" : " WHERE Aktiv='1' AND Start<='" . $this->stime . "' AND Ende>='" . $this->stime . "'";
        $res = $this->_db->cache_fetch_object("SELECT Id, Titel_{$this->Lc} AS Titel, Start, Ende, Kommentare, Aktiv, IpLog FROM " . PREFIX . "_umfrage {$where} AND Sektion='" . AREA . "' LIMIT 1");

        if (is_object($res)) {
            $pollhits = $this->_db->cache_fetch_object("SELECT SUM(Hits) AS HitCount FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$res->Id' LIMIT 1");
            $res->HitsAll = $pollhits->HitCount;

            if ($res->Ende < $this->stime || $res->Start > $this->stime) {
                $res->Aktiv = '0';
                $comment_new = false;
            } else {
                $comment_new = true;
            }

            if ($res->Kommentare == 1) {
                // Подключаем вывод комментариев
                $comment_url = 'index.php?p=poll&amp;id=' . $res->Id . '&amp;name=' . translit($res->Titel);
                $this->__object('Comments')->load('poll', $res->Id, $comment_url, $comment_new);
            }
        } else {
            $this->_view->assign('Inactive', 1);
        }
        $res->Titel = isset($res->Titel) ? $res->Titel : '';
        $this->_view->assign(array('PollRes' => $res, 'CPoll' => $poll));

        $seo_array = array(
            'headernav' => '<a href="index.php?p=poll&amp;action=archive&amp;area=' . AREA . '">' . $this->_lang['Poll_Name'] . '</a>',
            'pagetitle' => sanitize($res->Titel . $this->_lang['PageSep'] . $this->_lang['Poll_Archive']),
            'content'   => $this->_view->fetch(THEME . '/poll/showpoll.tpl'));
        $this->_view->finish($seo_array);
    }

    public function load() {
        $q = "SELECT * , Titel_{$this->Lc} AS Titel FROM " . PREFIX . "_umfrage WHERE Aktiv='1' AND Start<='" . $this->stime . "' AND Ende>='" . $this->stime . "' AND Sektion='" . AREA . "' LIMIT 1";
        $res = $this->_db->cache_fetch_object($q);

        if (is_object($res)) {
            $GroupC = explode(',', $res->Gruppen);
            $IPC = explode(',', $res->IpLog);
            $PollItems = array();

            $query = "SELECT SUM(Hits) AS HitCount FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$res->Id' ; ";
            $query .= "SELECT * , Frage_{$this->Lc} AS Frage FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$res->Id' ORDER BY Position ASC";
            if ($this->_db->multi_query($query)) {
                if (($result = $this->_db->store_result())) {
                    $pollcount = $result->fetch_object();
                    $result->close();
                }
                if (($result = $this->_db->store_next_result())) {
                    while ($row_answ = $result->fetch_object()) {
                        $row_answ->Perc = ($row_answ->Hits == 0) ? 1 : round(100 / $pollcount->HitCount * $row_answ->Hits, 2);
                        $PollItems[] = $row_answ;
                    }
                    $result->close();
                }
            }
            $tpl_array = array(
                'PollResultsSmall'  => $PollItems,
                'PollAllreadySmall' => (in_array(IP_USER, $IPC) ? 1 : 0),
                'PollPermSmall'     => (in_array($_SESSION['user_group'], $GroupC) ? 1 : 0),
                'PollAnswersSmall'  => $PollItems,
                'PollTitleSmall'    => sanitize($res->Titel),
                'PollRes'           => $res);
            $this->_view->assign($tpl_array);
        }
    }

    public function result($pollanswerid, $intern = 0, $extern = 0, $id = '') {
        $Entry = true;
        if ($pollanswerid == 0) {
            if ($intern == 1) {
                $this->_view->assign('Extern', 1);
                return $this->_view->fetch(THEME . '/poll/pollout_small_raw.tpl');
            } else {
                if ($extern == 1) {
                    $this->_view->assign('Extern', 1);
                }
                SX::output($this->_view->fetch(THEME . '/poll/pollout_small_raw.tpl'));
            }
        } else {
            $where = (!empty($id)) ? " WHERE Id = '$id' AND Start<='" . $this->stime . "'" : " WHERE Aktiv='1' AND Start<='" . $this->stime . "' AND Ende>='" . $this->stime . "'";
            $res = $this->_db->cache_fetch_object("SELECT * , Titel_{$this->Lc} AS Titel FROM " . PREFIX . "_umfrage {$where} AND Sektion = '" . AREA . "' LIMIT 1");

            if (in_array(IP_USER, explode(',', $res->IpLog))) {
                $Entry = false;
            }
            if (!in_array($_SESSION['user_group'], explode(',', $res->Gruppen))) {
                $Entry = false;
            }

            if ($Entry) {
                $uupdate = ($_SESSION['benutzer_id'] > 0) ? ", UserLog = CONCAT(UserLog, ',', '" . $_SESSION['benutzer_id'] . "')" : '';

                if ($res->Multi == 1) {
                    foreach ($_POST['polloption'] as $pollval => $pollopt) {
                        $this->_db->query("UPDATE " . PREFIX . "_umfrage_fragen SET Hits=Hits+1 WHERE Id='" . intval($_POST['polloption'][$pollopt]) . "' AND UmfrageId='$res->Id'");
                    }
                } else {
                    $this->_db->query("UPDATE " . PREFIX . "_umfrage_fragen SET Hits=Hits+1 WHERE Id='" . intval($pollanswerid) . "' AND UmfrageId='$res->Id'");
                }

                $this->_db->query("UPDATE " . PREFIX . "_umfrage SET IpLog = CONCAT(IpLog, ',', '" . IP_USER . "') {$uupdate} WHERE Id='$res->Id'");
                $PollItems = array();

                $query = "SELECT SUM(Hits) AS HitCount FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$res->Id' ; ";
                $query .= "SELECT * , Frage_{$this->Lc} AS Frage FROM " . PREFIX . "_umfrage_fragen WHERE UmfrageId='$res->Id' ORDER BY Position ASC";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $pollcount = $result->fetch_object();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        while ($row_answ = $result->fetch_object()) {
                            $row_answ->Perc = ($row_answ->Hits == 0) ? 1 : round(100 / $pollcount->HitCount * $row_answ->Hits);
                            $PollItems[] = $row_answ;
                        }
                        $result->close();
                    }
                }

                if ($extern == 1) {
                    $this->_view->assign('Extern', 1);
                }

                $tpl_array = array(
                    'PollRes'           => $res,
                    'PollResultsSmall'  => $PollItems,
                    'PollAllreadySmall' => 1);
                $this->_view->assign($tpl_array);
                $out = $this->_view->fetch(THEME . '/poll/pollout_small_raw.tpl');
                if ($intern != 1) {
                    SX::output($out);
                }
            } else {
                SX::output('Попытка нападения: ' . IP_USER);
            }
        }
    }

}
