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

class Calendar extends Magic {

    protected $_names;
    protected $_day = 1;
    protected $_month = 1;
    protected $_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    public function __construct() {
        $this->checkUrl();
    }

    /* Метод вывода аякс календаря */
    public function ajax($limit = 5) {
        $array = $this->getDate();
        $events = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_kalender WHERE Typ='public' AND Start >= " . time() . " ORDER BY Start ASC LIMIT " . intval($limit));
        while ($row = $sql->fetch_object()) {
            $row->Day = date('d', $row->Start);
            $row->Year = date('Y', $row->Start);
            $row->Month = date('m', $row->Start);
            $row->EventLink = 'index.php?p=calendar&amp;action=events&amp;show=public&amp;month=' . $row->Month . '&amp;year=' . $row->Year . '&amp;day=' . $row->Day . '&amp;area=' . AREA . '#' . $row->Id;
            $row->Beschreibung = preg_replace('#\[(.*?)\]#iu', '', $row->Beschreibung);
            $events[] = $row;
        }
        $sql->close();
        $this->_view->assign('NewCalEvents', $events);
        $this->_view->assign('SmallCalendarNewEvents', $this->_view->fetch(THEME . '/calendar/calendar_small_newevents.tpl'));
        $this->_view->assign('SmallCalendar', $this->viewMonth($array['month'], $array['year'], THEME . '/calendar/calendar_small.tpl', '1', 'small', 1));
    }

    public function checkUrl() {
        if (!empty($_SERVER['REQUEST_URI'])) {
            $array = array('calendar/weekview//' => 'calendar/weekview/public/', 'p=calendar&show=&action=week' => 'p=calendar&show=public&action=week');
            foreach ($array as $key => $value) {
                if (stripos($_SERVER['REQUEST_URI'], $key) !== false) {
                    return SX::object('Redir')->redirect(SHEME_URL . $_SERVER['HTTP_HOST'] . str_replace($key, $value, $_SERVER['REQUEST_URI']));
                }
            }
        }
    }

    public function defParam() {
        $year = Arr::getRequest('year');
        $_REQUEST['year'] = (is_numeric($year) && strlen($year) == 4 && $year <= 2050) ? intval($year) : date('Y');
        $day = Arr::getRequest('day');
        $_REQUEST['day'] = (is_numeric($day) && strlen($day) <= 2 && $day <= 31) ? intval($day) : date('d');
        $month = Arr::getRequest('month');
        $_REQUEST['month'] = (is_numeric($month) && strlen($month) <= 2 && $month <= 12) ? intval($month) : date('m');
    }

    public function switches() {
        $array = $this->getDate();
        $erg = $this->viewMonth($array['month'], $array['year'], THEME . '/calendar/calendar_small_raw.tpl', '1', 'small', 1);
        SX::setDefine('AJAX_OUTPUT', 1);
        SX::output($erg);
    }

    public function myevents() {
        $find = $this->_db->escape(Arr::getGet('qc'));
        if ($this->_text->strlen($find) >= 3) {
            $search = "(Titel LIKE '%$find%' OR Beschreibung LIKE '%$find%' AND Typ = 'public' AND Benutzer = '" . $_SESSION['benutzer_id'] . "') OR (Titel LIKE '%$find%' OR Beschreibung LIKE '%$find%' AND Typ = 'private' AND Benutzer = '" . $_SESSION['benutzer_id'] . "')";
        } else {
            $search = "(Benutzer = '" . $_SESSION['benutzer_id'] . "')";
        }
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_kalender WHERE {$search} ORDER BY Start DESC");
        $results = array();
        while ($row = $sql->fetch_object()) {
            $row->month = date('n', $row->Start);
            $row->year = date('Y', $row->Start);
            $row->day = date('j', $row->Start);
            $results[] = $row;
        }
        $sql->close();
        $this->_view->assign(array('search' => 1, 'results' => $results));
        $headernav = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_MyEvents'];

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $this->_lang['Calendar_MyEvents'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'generate'  => $this->_lang['Calendar_MyEvents'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_myevents.tpl'));
        $this->_view->finish($seo_array);
    }

    public function search($find) {
        $results = array();
        $find = urldecode($find);
        if (!empty($find) && $this->_text->strlen($find) >= 2) {
            $this->__object('Core')->monitor($find, 'calendar');
            $find = $this->_db->escape($find);
            $fq = "SELECT * FROM " . PREFIX . "_kalender WHERE (Titel LIKE '%$find%' OR Beschreibung LIKE '%$find%' AND Typ = 'public') OR (Titel LIKE '%$find%' OR Beschreibung LIKE '%$find%' AND Typ = 'private' AND Benutzer = '" . $_SESSION['benutzer_id'] . "') ORDER BY Start DESC";
            $sql = $this->_db->query($fq);
            while ($row = $sql->fetch_object()) {
                $row->month = date('n', $row->Start);
                $row->year = date('Y', $row->Start);
                $row->day = date('j', $row->Start);
                $results[] = $row;
            }
            $sql->close();
        } else {
            $this->_view->assign('warning', $this->_lang['Calendar_search_noinsert']);
        }
        $this->_view->assign(array('search' => 1, 'results' => $results));
        $headernav = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_search'];

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $this->_lang['Search'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'generate'  => $this->_lang['Search'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_searchresults.tpl'));
        $this->_view->finish($seo_array);
    }

    public function birthdays() {
        $sname = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Birthdays_Today'];
        $birthdays = $this->dayBirthdays($_REQUEST['day'], $_REQUEST['month'], $_REQUEST['year']);

        $this->_view->assign(array('birthdays' => $birthdays));
        $seo_array = array(
            'headernav' => $sname,
            'pagetitle' => $_REQUEST['day'] . '.' . $_REQUEST['month'] . '.' . $_REQUEST['year'] . $this->_lang['PageSep'] . $this->_lang['Birthdays_Today'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_birthdays.tpl'));
        $this->_view->finish($seo_array);
    }

    public function dayBirthdays($day, $month, $year) {
        $time = $this->mktime(0, 0, 0, $month, $day, $year);
        $where = " (Day = " . date('d', $time) . " AND Month = " . date('m', $time) . ") ";

        $array = array();
        if (!empty($where)) {
            $sql = $this->queryBirthdays($where, $year);
            while ($row = $sql->fetch_assoc()) {
                $array[] = $row;
            }
        }
        return $array;
    }

    public function weekBirthdays($start, $month, $year) {
        $where = array();
        for ($i = 0; $i < 7; $i++) {
            $time = $this->mktime(0, 0, 0, $month, $start + $i, $year);
            $where[] = " (Day = " . date('d', $time) . " AND Month = " . date('m', $time) . ") ";
        }

        $array = array();
        if (!empty($where)) {
            $sql = $this->queryBirthdays(implode(' OR ', $where), $year);
            $theme = SX::get('options.theme');
            while ($row = $sql->fetch_assoc()) {
                $day = intval($row['Day']);
                $count[$day] = !isset($count[$day]) ? 1 : $count[$day] + 1;
                if (!isset($array[$day])) {
                    $name = $this->_text->chars($row['Benutzername'], 20, '...', false);
                    $array[$day] = '<img class="absmiddle" src="theme/' . $theme . '/images/calendar/birthday.png" alt="" /> <a class="calendarEventLink" href="index.php?p=user&amp;id=' . $row['Id'] . '&amp;area=' . AREA . '">' . $name . ' (' . $row['Age'] . ')</a>';
                } else {
                    $array[$day] = '<img class="absmiddle" src="theme/' . $theme . '/images/calendar/birthday.png" alt="" /> <a href="index.php?p=calendar&amp;action=birthdays&amp;show=public&amp;month=' . $row['Month'] . '&amp;year=' . $year . '&amp;day=' . $row['Day'] . '&amp;area=' . AREA . '">' . $this->_lang['Birthdays_Today'] . ' (' . $count[$day] . ')</a>';
                }
            }
        }
        return $array;
    }

    public function monthBirthdays($start, $end, $month, $year) {
        $where = array();
        for ($i = $start; $i < $end; $i++) {
            $time = $this->mktime(0, 0, 0, $month, $i, $year);
            $where[] = " (Day = " . date('d', $time) . " AND Month = " . date('m', $time) . ") ";
        }

        $array = array();
        if (!empty($where)) {
            $sql = $this->queryBirthdays(implode(' OR ', $where), $year);
            $count = array();
            $theme = SX::get('options.theme');
            while ($row = $sql->fetch_assoc()) {
                $day = intval($row['Day']);
                $month = intval($row['Month']);
                $count[$month][$day] = !isset($count[$month][$day]) ? 1 : $count[$month][$day] + 1;
                if (!isset($array[$month][$day])) {
                    $name = $this->_text->chars($row['Benutzername'], 20, '...', false);
                    $array[$month][$day] = '<img class="absmiddle" src="theme/' . $theme . '/images/calendar/birthday.png" alt="" /> <a class="calendarEventLink" href="index.php?p=user&amp;id=' . $row['Id'] . '&amp;area=' . AREA . '">' . $name . ' (' . $row['Age'] . ')</a>';
                } else {
                    $array[$month][$day] = '<img class="absmiddle" src="theme/' . $theme . '/images/calendar/birthday.png" alt="" /> <a href="index.php?p=calendar&amp;action=birthdays&amp;show=public&amp;month=' . $row['Month'] . '&amp;year=' . $year . '&amp;day=' . $row['Day'] . '&amp;area=' . AREA . '">' . $this->_lang['Birthdays_Today'] . ' (' . $count[$month][$day] . ')</a>';
                }
            }
        }
        return $array;
    }

    protected function queryBirthdays($where, $year) {
        return $this->_db->query("SELECT
                Id,
                Geburtstag,
                Benutzername,
                LEFT(Geburtstag, 2) AS Day,
                LEFT(RIGHT(Geburtstag, 7), 2) AS Month,
                " . intval($year) . " - RIGHT(Geburtstag, 4) AS Age
            FROM
                " . PREFIX . "_benutzer
            WHERE
                Geburtstag != ''
            HAVING
                Age > 0
            AND
                " . $where . "
            ORDER BY Age DESC");
    }

    public function editevent() {
        $row = $this->fetchData();
        if ((!permission('calendar_event')) || ($row->Benutzer != $_SESSION['benutzer_id'] && !permission('edit_all_events'))) {
            $this->__object('Core')->noAccess();
        }

        $_REQUEST['subaction'] = !empty($_REQUEST['subaction']) ? $_REQUEST['subaction'] : '';
        switch ($_REQUEST['subaction']) {
            default:
                $even_date = ' ' . intval($_REQUEST['day']) . '.' . intval($_REQUEST['month']) . '.' . intval($_REQUEST['year']);
                $header = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . '<a href="index.php?p=calendar&amp;action=events&amp;show=' . $_REQUEST['show'] . '&amp;month=' . $_REQUEST['month'] . '&amp;year=' . $_REQUEST['year'] . '&amp;day=' . $_REQUEST['day'] . '&amp;area=' . $_REQUEST['area'] . '#' . Arr::getRequest('id') . '">' . $this->_lang['Calendar_EventsOn'] . $even_date . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_editEvent'];

                $tpl_array = array(
                    'row'       => $row,
                    'startYear' => $this->startYear(),
                    'month'     => $this->displayMonth(),
                    'weight'    => explode(',', $this->_lang['Calendar_Weight']));
                $this->_view->assign($tpl_array);

                $seo_array = array(
                    'headernav' => $header,
                    'pagetitle' => $this->_lang['Calendar_editEvent'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
                    'content'   => $this->_view->fetch(THEME . '/calendar/calendar_event_form.tpl'));
                $this->_view->finish($seo_array);
                break;

            case 'save':
                $start = $this->mktime($_REQUEST['s_std'], $_REQUEST['s_min'], 0, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
                $ende = $this->mktime($_REQUEST['e_std'], $_REQUEST['e_min'], 0, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
                $wday = (Arr::getRequest('s_wday') == 1) ? 1 : 0;
                $array = array(
                    'Titel'        => strip_tags(Tool::cleanTags($_REQUEST['name'], array('codewidget'))),
                    'Beschreibung' => Tool::cleanTags($_REQUEST['text'], array('codewidget')),
                    'Gewicht'      => Arr::getRequest('weight'),
                    'Start'        => $start,
                    'Ende'         => $ende,
                    'wd'           => $wday,
                    'Erledigt'     => $_REQUEST['done'],
                    'Typ'          => Arr::getRequest('show'),
                );
                $this->_db->update_query('kalender', $array, "Id='" . intval(Arr::getRequest('id')) . "'");
                $redir = 'index.php?p=calendar&amp;action=events&amp;show=' . $_REQUEST['show'] . '&amp;month=' . $_REQUEST['month'] . '&amp;year=' . $_REQUEST['year'] . '&amp;day=' . $_REQUEST['day'] . '&amp;area=' . $_REQUEST['area'] . '#' . Arr::getRequest('id');
                $this->__object('Core')->message('Calendar_editEvent', 'Calendar_editEventM', BASE_URL . '/' . $redir);
                break;
        }
    }

    public function delevent() {
        $row = $this->fetchData();
        if ($row->Benutzer != $_SESSION['benutzer_id'] && !permission('edit_all_events')) {
            $this->__object('Core')->noAccess();
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_kalender WHERE Id='" . intval(Arr::getRequest('id')) . "'");
        $this->__object('Core')->message('Calendar_eventDel', 'Calendar_eventDelM', BASE_URL . '/index.php?p=calendar&show=' . $_REQUEST['show'] . '&area=' . $_REQUEST['area']);
    }

    public function events() {
        $even_date = ' ' . intval($_REQUEST['day']) . '.' . intval($_REQUEST['month']) . '.' . intval($_REQUEST['year']);
        $sname = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_EventsOn'] . $even_date . ' (' . $this->dispType() . ')';
        $this->showEvents();
        $this->_view->assign('weight', explode(',', $this->_lang['Calendar_Weight']));

        $seo_array = array(
            'headernav' => $sname,
            'pagetitle' => $this->_lang['Calendar_EventsOn'] . $even_date . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_display_events.tpl'));
        $this->_view->finish($seo_array);
    }

    public function insertevent() {
        if ($_REQUEST['newevent'] == 1 && $_SESSION['user_group'] != 2) {
            if (Arr::getRequest('s_wday') == 1) {
                $start = $this->mktime(0, 0, 1, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
                $ende = $this->mktime(23, 59, 59, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
            } else {
                $start = $this->mktime($_REQUEST['s_std'], $_REQUEST['s_min'], 0, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
                $ende = $this->mktime($_REQUEST['e_std'], $_REQUEST['e_min'], 0, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']);
            }
            $datum = intval($_REQUEST['day']) . '-' . intval($_REQUEST['month']) . '-' . intval($_REQUEST['year']);
            $tdays = ($_REQUEST['days'] > 1) ? $this->mktime(23, 59, 59, $_REQUEST['month'], $_REQUEST['day'] + ($_REQUEST['days'] - 1), $_REQUEST['year']) : 0;
            $pb = $this->_db->escape(Arr::getRequest('show'));
            $pb = ($pb == 'public' && !permission('calendar_event_new')) ? 'private' : $pb;

            if (!empty($_REQUEST['name'])) {
                $insert_array = array(
                    'Benutzer'     => $_SESSION['benutzer_id'],
                    'Datum'        => $datum,
                    'Titel'        => sanitize(Tool::cleanTags(Arr::getRequest('name'), array('codewidget'))),
                    'Beschreibung' => Tool::cleanTags(Arr::getRequest('text'), array('codewidget')),
                    'Start'        => $start,
                    'Ende'         => $ende,
                    'Typ'          => $pb,
                    'wd'           => intval(Arr::getRequest('s_wday')),
                    'Gewicht'      => intval(Arr::getRequest('weight')),
                    'tdays'        => intval($tdays));
                $this->_db->insert_query('kalender', $insert_array);
            }
        }
        $this->__object('Core')->message('Calendar_newEvent', 'Calendar_editEventNewM', BASE_URL . '/index.php?p=calendar&show=' . $_REQUEST['show'] . '&area=' . $_REQUEST['area']);
    }

    public function newevent() {
        $array = $this->getDate();
        $header = '<a href="index.php?p=calendar&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_newEvent'];

        $tpl_array = array(
            'currentmonth' => $array['month'],
            'startYear'    => $this->startYear(),
            'month'        => $this->displayMonth(),
            'weight'       => explode(',', $this->_lang['Calendar_Weight']));
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $header,
            'pagetitle' => $this->_lang['Calendar_newEvent'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'generate'  => $this->_lang['Calendar'] . $this->_lang['PageSep'] . $this->_lang['Calendar_newEvent'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_event_form.tpl'));
        $this->_view->finish($seo_array);
    }

    public function displayyear() {
        $array = $this->getDate();
        $sname = $array['year'] . $this->_lang['PageSep'] . $this->_lang['Calendar_yearView'] . $this->_lang['PageSep'] . $this->_lang['Calendar'];
        $header = '<a href="index.php?p=calendar&amp;month=' . date('m') . '&amp;year=' . date('Y') . '&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_yearView'] . ' ' . $array['year'];

        $tpl_array = array(
            'showtype'  => $this->privatePublic(),
            'startYear' => $this->startYear(),
            'jahre'     => $this->currentYear(),
            'year_prev' => ($array['year'] - 1),
            'year_next' => ($array['year'] + 1),
            'Year'      => $array['year']);
        $this->_view->assign($tpl_array);
        $this->_view->assign('years', $this->_view->fetch(THEME . '/calendar/calendar_year_small.tpl'));

        $seo_array = array(
            'headernav' => $header,
            'pagetitle' => $sname,
            'generate'  => $sname,
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_year.tpl'));
        $this->_view->finish($seo_array);
    }

    public function week() {
        $array = $this->getDate();
        $start = intval($_REQUEST['weekstart']);
        $end = intval($_REQUEST['weekend']);
        $f_t = (intval($_REQUEST['weekstart']) && intval($_REQUEST['weekend'])) ? date('d.m.Y', $start) . ' - ' . date('d.m.Y', $end) : '';
        $smonth = date('m', $start);
        $sday = date('d', $start);
        $syear = date('Y', $start);
        $smonth_end = date('m', $end);
        $syear_end = date('Y', $end);
        for ($i = 0; $i < 7; $i++) {
            $dates_array[$i] = $this->mktime(0, 0, 0, $smonth, $sday + $i, $syear);
        }

        $tpl_array = array(
            'birthdays'       => $this->weekBirthdays($sday, $smonth, $syear),
            'week_pref_start' => $this->mktime(0, 0, 0, $smonth, $sday - 7, $syear),
            'week_pref_end'   => $this->mktime(0, 0, 0, $smonth, $sday, $syear),
            'week_next_start' => $this->mktime(0, 0, 0, $smonth, $sday + 7, $syear),
            'week_next_end'   => $this->mktime(0, 0, 0, $smonth, $sday + 14, $syear),
            'second_month'    => explode('-', date('m-Y', $end)),
            'days_inmonth'    => $this->_days,
            'month_array'     => $this->displayMonth(),
            'dates_array'     => $dates_array,
            'items'           => $this->displayWeek($start, $end),
            'days'            => $this->namesWeek(),
            'showtype'        => $this->privatePublic(),
            'privatePublic'   => $this->privatePublic(),
            'month'           => $this->displayMonth(),
            'currentmonth'    => $array['month'],
            'Year'            => $array['year'],
            'startYear'       => $this->startYear());
        $this->_view->assign($tpl_array);

        if ($smonth != $smonth_end) {
            $this->_view->assign('calendar_next', $this->viewMonth($smonth_end, $syear_end, THEME . '/calendar/calendar_small_prev_next.tpl', 1, 'small'));
        }

        $headernav = '<a href="index.php?p=calendar&amp;month=' . date('m') . '&amp;year=' . date('Y') . '&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Calendar_weekview'];

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => $f_t . $this->_lang['PageSep'] . $this->_lang['Calendar_weekview'] . $this->_lang['PageSep'] . $this->_lang['Calendar'],
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar_week.tpl'));
        $this->_view->finish($seo_array);
    }

    public function load() {
        $array = $this->getDate();
        $m_p = date('m', $this->mktime(0, 0, 1, $array['month'] - 1, 1, $array['year']));
        $y_p = date('Y', $this->mktime(0, 0, 1, $array['month'] - 1, 1, $array['year']));
        $m_n = date('m', $this->mktime(0, 0, 1, $array['month'] + 1, 1, $array['year']));
        $y_n = date('Y', $this->mktime(0, 0, 1, $array['month'] + 1, 1, $array['year']));

        $tpl_array = array(
            'mp'            => $m_p,
            'yp'            => $y_p,
            'mn'            => $m_n,
            'yn'            => $y_n,
            'text_prev_m'   => $this->monthSimple($m_p) . ' ' . $y_p,
            'text_next_m'   => $this->monthSimple($m_n) . ' ' . $y_n,
            'Year'          => $array['year'],
            'showtype'      => $this->privatePublic(),
            'month'         => $this->displayMonth(),
            'currentmonth'  => $array['month'],
            'startYear'     => $this->startYear(),
            'privatePublic' => $this->privatePublic());
        $this->_view->assign($tpl_array);

        $tpl_array = array(
            'calendar'      => $this->viewMonth($array['month'], $array['year'], THEME . '/calendar/calendar_big.tpl', '1', 'big'),
            'calendar_prev' => $this->viewMonth($m_p, $y_p, THEME . '/calendar/calendar_small_prev_next.tpl', 1, 'small'),
            'calendar_next' => $this->viewMonth($m_n, $y_n, THEME . '/calendar/calendar_small_prev_next.tpl', 1, 'small'));
        $this->_view->assign($tpl_array);

        $headernav = '<a href="index.php?p=calendar&amp;month=' . date('m') . '&amp;year=' . date('Y') . '&amp;area=' . AREA . '">' . $this->_lang['Calendar'] . '</a>';

        $seo_array = array(
            'headernav' => $headernav,
            'pagetitle' => sanitize($this->monthSimple($array['month']) . ' ' . $array['year'] . $this->_lang['PageSep'] . $this->_lang['Calendar']),
            'content'   => $this->_view->fetch(THEME . '/calendar/calendar.tpl'));
        $this->_view->finish($seo_array);
    }

    public function getDate() {
        $d = $this->stime();
        $array['year'] = (!empty($_REQUEST['year'])) ? intval($_REQUEST['year']) : $d['year'];
        $array['month'] = (!empty($_REQUEST['month'])) ? intval($_REQUEST['month']) : $d['mon'];
        return $array;
    }

    protected function stime() {
        return getdate(time());
    }

    protected function dayNames() {
        return explode(',', $this->_lang['Calendar_Days']);
    }

    protected function namesShort() {
        return explode(',', $this->_lang['Calendar_DaysShort']);
    }

    protected function namesWeek() {
        return explode(',', $this->_lang['Calendar_DaysWeek']);
    }

    protected function months() {
        return explode(',', $this->_lang['Calendar_Months']);
    }

    protected function dispWeight($weight) {
        $ws = explode(',', $this->_lang['Calendar_Weight']);
        return $ws[$weight - 1];
    }

    public function monthSimple($month) {
        $this->_names = $this->months();
        return $this->_names[$month - 1];
    }

    public function dispType() {
        return $_REQUEST['show'] == 'private' ? $this->_lang['Calendar_private'] : $this->_lang['Calendar_public'];
    }

    public function displayMonth() {
        $this->_names = $this->months();
        return $this->_names;
    }

    public function startYear() {
        return date('Y') - 5;
    }

    public function privatePublic() {
        $_REQUEST['show'] = Arr::getRequest('show') == 'private' ? 'private' : 'public';
        return $_REQUEST['show'];
    }

    public function currentYear() {
        $this->_view->assign('showtype', $this->privatePublic());
        $the_tpl = THEME . '/calendar/calendar_small_years.tpl';
        $d = $this->stime();
        $year = !empty($_REQUEST['year']) ? intval($_REQUEST['year']) : $d['year'];
        for ($i = 0; $i < 12; $i++) {
            $this->_view->assign('cal_' . $i, $this->viewMonth($i + $this->_month, $year, $the_tpl, '2', 'small'));
        }
    }

    protected function daysInMonth($month, $year) {
        if ($month < 1 || $month > 12) {
            return 0;
        }
        $d = $this->_days[$month - 1];
        if ($month == 2) {
            if ($year % 4 == 0) {
                if ($year % 100 == 0) {
                    if ($year % 400 == 0) {
                        $d = 29;
                    }
                } else {
                    $d = 29;
                }
            }
        }
        return $d;
    }

    protected function daysMonth($day_start, $day_end, $month, $year) {
        $day_end = $day_end - 1;
        $dbextra = (Arr::getRequest('show') == 'private') ? " AND Typ = 'private' AND Benutzer = '" . $_SESSION['benutzer_id'] . ";' " : " AND Typ = 'public'";
        $db_start = " WHERE ((Start BETWEEN '" . $this->mktime(0, 0, 0, $month, $day_start, $year) . "' AND '" . $this->mktime(23, 59, 59, $month, $day_end, $year) . "')";
        $db_start .= " OR ((tdays != 0) AND (tdays >= '" . $this->mktime(0, 0, 0, $month, $day_start, $year) . "') AND (Start <= '" . $this->mktime(23, 59, 59, $month, $day_start - 1, $year) . "')))";
        $cal = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_kalender $db_start $dbextra ORDER BY Start ASC");
        return $cal;
    }

    protected function viewMonth($m, $y, $template, $showYear, $SmallBig, $Ajax = '') {
        $link = $birthdays = $items = '';
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
        $daysInMonth = $this->daysInMonth($month, $year);
        $date = getdate($this->mktime(12, 0, 0, $month, 1, $year));
        $first = $date['wday'];
        $this->_names = $this->months();
        $monthName = $this->_names[$month - 1];
        $prev = $this->adjustDate($month - 1, $year);
        $next = $this->adjustDate($month + 1, $year);
        if ($showYear == 1) {
            if ($Ajax == 1) {
                $this->_view->assign('prevMonth', BASE_URL . '/index.php?p=calendar&action=switch&month=' . $prev[0] . '&year=' . $prev[1] . '&area=' . AREA);
                $this->_view->assign('nextMonth', BASE_URL . '/index.php?p=calendar&action=switch&month=' . $next[0] . '&year=' . $next[1] . '&area=' . AREA);
            } else {
                $this->_view->assign('prevMonth', $this->link($prev[0], $prev[1]));
                $this->_view->assign('nextMonth', $this->link($next[0], $next[1]));
            }
        }

        $this->_view->assign('header', $monthName . (($showYear > 0) ? ' ' . $year : ''));
        $namesShort = $this->namesShort();
        $this->_view->assign('DayNamesShortArray', array($namesShort[($this->_day) % 7], $namesShort[($this->_day + 1) % 7], $namesShort[($this->_day + 2) % 7], $namesShort[($this->_day + 3) % 7], $namesShort[($this->_day + 4) % 7], $namesShort[($this->_day + 5) % 7], $namesShort[($this->_day + 6) % 7]));
        $dayNames = $this->dayNames();
        $this->_view->assign('DayNamesArray', array($dayNames[($this->_day) % 7], $dayNames[($this->_day + 1) % 7], $dayNames[($this->_day + 2) % 7], $dayNames[($this->_day + 3) % 7], $dayNames[($this->_day + 4) % 7], $dayNames[($this->_day + 5) % 7], $dayNames[($this->_day + 6) % 7]));

        $d = $this->_day + 1 - $first;
        while ($d > 1) {
            $d -= 7;
        }
        $today = $this->stime();
        $cal_data = array();
        $nd = $d;
        while ($nd <= $daysInMonth) {
            for ($k = 0; $k < 7; $k++) {
                $nd++;
            }
        }
        $sql = $this->daysMonth($d, $nd, $month, $year);

        if (strpos($template, 'calendar_big.tpl') !== false) {
            $birthdays = $this->monthBirthdays($d, $nd, $month, $year);
        }

        $show_type = (Arr::getRequest('show') == 'private') ? 'private' : 'public';
        while ($d <= $daysInMonth) {
            $cal_data_days = array();
            for ($i = 0; $i < 7; $i++) {
                $link = '';
                $items = array();
                foreach ($sql as $row) {
                    if (($row->Start >= $this->mktime(0, 0, 0, $month, $d, $year) && $row->Start <= $this->mktime(23, 59, 59, $month, $d, $year)) || ($row->tdays != 0 && $row->tdays >= $this->mktime(0, 0, 0, $month, $d, $year) && $row->Start <= $this->mktime(23, 59, 59, $month, $d - 1, $year))) {
                        if (!empty($row->Titel)) {
                            $link = BASE_URL . '/index.php?p=calendar&amp;action=events&amp;show=' . $show_type . '&amp;month=' . date('m', $row->Start) . '&amp;year=' . date('Y', $row->Start) . '&amp;day=' . date('d', $row->Start) . '&amp;area=' . AREA;
                            $row->link_event_only = BASE_URL . '/index.php?p=calendar&amp;action=events&amp;show=' . $show_type . '&amp;month=' . date('m', $row->Start) . '&amp;year=' . date('Y', $row->Start) . '&amp;day=' . date('d', $row->Start) . '&amp;area=' . AREA . '#' . $row->Id;
                            $row->day_event_link = $link;
                            if (date('Y', $row->Start) != $year) {
                                $row->is_not_inyear = 1;
                            }
                            $items[] = $row;
                        }
                    }
                }
                if ($SmallBig == 'small') {
                    $class = ($year == $today['year'] && $month == $today['mon'] && $d == $today['mday']) ? 'calendarToday' : 'calendar';
                } else {
                    $class = ($year == $today['year'] && $month == $today['mon'] && $d == $today['mday']) ? 'calendarTodayBig' : 'calendarBig';
                }
                $CalDataInner = new stdClass;
                $currday = intval(date('d', $this->mktime(0, 0, 0, $month, $d, $year)));
                $currmonth = intval(date('m', $this->mktime(0, 0, 0, $month, $d, $year)));
                $CalDataInner->Geburtstage = isset($birthdays[$currmonth][$currday]) ? $birthdays[$currmonth][$currday] : '';
                $CalDataInner->countitems = count($items);
                $CalDataInner->Ereignisse = $items;

                if ($d > 0 && $d <= $daysInMonth) {
                    $CalDataInner->thelink = ((empty($link)) ? (($SmallBig == 'big') ? '<strong>' . $d . '</strong>' : $d) : (($SmallBig != 'big' && ($year == $today['year'] && $month == $today['mon'] && $d == $today['mday'])) ? '<a class="calendarLinkSmall" href="' . $link . '"><strong>' . $d . '</strong></a>' : '<a class="calendarLink" href="' . $link . '"><strong>' . $d . '</strong></a>'));
                    $CalDataInner->tdclass = $class;
                    $CalDataInner->packed_events_link = $link;
                } else {
                    $CalDataInner->thelink = '&nbsp;<span class="calendarInactiveDay">' . $currday . '</span>';
                    $CalDataInner->tdclass = 'calendarBlanc';
                    $CalDataInner->packed_events_link = $link;
                    $CalDataInner->packed_events_niy = 1;
                }
                $cal_data_days[] = $CalDataInner;
                $d++;
            }
            $row = new stdClass;
            $row->StartWeek = $this->mktime(0, 0, 0, $month, $d - 7, $year);
            $row->EndWeek = $this->mktime(23, 59, 59, $month, $d - 1, $year);
            $row->CalDataInner = $cal_data_days;
            $cal_data[] = $row;
        }

        $tpl_array = array(
            'linkmonth' => date('m', $this->mktime(0, 0, 1, $month, 1, $year)),
            'linkyear'  => date('Y', $this->mktime(0, 0, 1, $month, 1, $year)),
            'cal_data'  => $cal_data);
        $this->_view->assign($tpl_array);

        $s = $this->_view->fetch($template);
        return $s;
    }

    protected function mktime($hour, $minute, $second, $month, $day, $year) {
        $value = mktime($hour, $minute, $second, $month, $day, $year);
        return $value === false ? 0 : $value;
    }

    public function displayWeek($start, $end) {
        if ($_SESSION['user_group'] == 2 && $_REQUEST['show'] == 'private') {
            $this->__object('Core')->noAccess();
        }

        $dbextra = $_REQUEST['show'] == 'private' ? " AND Typ = 'private' AND Benutzer = '" . $_SESSION['benutzer_id'] . ";' " : " AND Typ = 'public'";
        $sql = $this->_db->query("SELECT
            *
        FROM
            " . PREFIX . "_kalender
        WHERE
            Start >= '" . $this->_db->escape($start) . "'
        AND
            Start <= '" . $this->_db->escape($end) . "'
            " . $dbextra . "
        ORDER BY start ASC");
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();
        return $items;
    }

    protected function adjustDate($month, $year) {
        $a = array();
        $a[0] = $month;
        $a[1] = $year;
        while ($a[0] > 12) {
            $a[0] -= 12;
            $a[1] ++;
        }
        while ($a[0] <= 0) {
            $a[0] += 12;
            $a[1] --;
        }
        return $a;
    }

    public function showEvents() {
        $_REQUEST['id'] = (isset($_REQUEST['id'])) ? intval(Arr::getRequest('id')) : '';
        $month = $this->_db->escape(Arr::getRequest('month'));
        $day = $this->_db->escape(Arr::getRequest('day'));
        $year = $this->_db->escape(Arr::getRequest('year'));
        $dbextra = ($_REQUEST['show'] == 'private') ? " AND Typ = 'private' AND Benutzer = '" . intval($_SESSION['benutzer_id']) . ";' " : " AND Typ = 'public'";
        $db_start = " WHERE ((Start between '" . $this->mktime(0, 0, 0, $month, $day, $year) . "' AND '" . $this->mktime(23, 59, 59, $month, $day, $year) . "')";
        $db_start .= " OR (( tdays != 0) AND ( tdays >= '" . $this->mktime(0, 0, 0, $month, $day, $year) . "' ) AND (Start <= '" . $this->mktime(23, 59, 59, $month, $day - 1, $year) . "')))";
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_kalender $db_start $dbextra AND Id != '" . intval(Arr::getRequest('id')) . "' ORDER BY start ASC");
        $events = array();
        while ($row = $sql->fetch_object()) {
            $row->weight = $this->dispWeight($row->Gewicht);
            $row->descr = Tool::cleanTags($row->Beschreibung, array('codewidget'));

            $row->descr = $this->__object('Post')->codes($row->descr);
            $row->descr = $this->__object('Post')->smilies($row->descr);
            $row->UserName = Tool::userName($row->Benutzer);
            $events[] = $row;
        }
        $sql->close();
        $this->_view->assign('events', $events);
    }

    public function fetchData() {
        $row = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_kalender WHERE Id='" . intval(Arr::getRequest('id')) . "' LIMIT 1");
        $row->Beschreibung = Tool::cleanTags($row->Beschreibung, array('codewidget'));
        return $row;
    }

    protected function link($month, $year) {
        return 'index.php?month=' . $month . '&amp;year=' . $year . '&amp;area=' . AREA;
    }

}
