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

class AdminNewsletter extends Magic {

    protected $_req = array();
    protected $_cache = array();
    protected $_attach = array();
    protected $_groups = array();
    protected $_url;
    protected $_path;
    protected $_limit = 20;
    protected $_separator = "\r\n============================================================\r\n";

    public function __construct() {
        $this->_url = BASE_URL;
    }

    public function delCateg($id) {
        $id = intval($id);
        $this->_db->query("DELETE FROM " . PREFIX . "_newsletter WHERE Id='" . $id . "'");
        $this->_db->query("DELETE FROM " . PREFIX . "_newsletter_abos WHERE Newsletter_Id='" . $id . "'");
        $this->__object('Redir')->redirect('index.php?do=newsletter&sub=categs');
    }

    public function attachment($att) {
        $att = Tool::cleanAllow($att, '. ');
        File::filerange(UPLOADS_DIR . '/attachments/' . $att, 'application/octet-stream');
    }

    public function subscribers() {
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Format']) as $format) {
                $array = array(
                    'Format' => $_POST['Format'][$format],
                    'Aktiv'  => $_POST['Aktiv'][$format],
                );
                $this->_db->update_query('newsletter_abos', $array, "Id = '" . intval($format) . "'");
                if (!empty($_POST['del'][$format]) && $_POST['del'][$format] == 1) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_newsletter_abos WHERE Id='" . intval($format) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $def_search = $def_search_n = '';
        $def_order = ' ORDER BY a.Datum DESC';

        $pattern = Arr::getRequest('q');
        if (!empty($pattern)) {
            $_REQUEST['q'] = $pattern = trim($pattern);
            $def_search = " AND a.Email='" . $this->_db->escape($pattern) . "'";
            $def_search_n = "&amp;q=" . $pattern;
        }

        if (isset($_REQUEST['sort'])) {
            $curr_page = '&amp;page=' . Arr::getRequest('page', 1);

            switch ($_REQUEST['sort']) {
                default:
                case 'email_asc':
                    $def_order = ' ORDER BY a.Email ASC';
                    $def_order_ns = '&sort=email_desc' . $curr_page;
                    $this->_view->assign('email_s', $def_order_ns);
                    break;

                case 'email_desc':
                    $def_order = ' ORDER BY a.Email DESC';
                    $def_order_ns = '&sort=email_asc' . $curr_page;
                    $this->_view->assign('email_s', $def_order_ns);
                    break;

                case 'format_asc':
                    $def_order = ' ORDER BY a.Format ASC';
                    $def_order_ns = '&sort=format_desc' . $curr_page;
                    $this->_view->assign('format_s', $def_order_ns);
                    break;

                case 'format_desc':
                    $def_order = ' ORDER BY a.Format DESC';
                    $def_order_ns = '&sort=format_asc' . $curr_page;
                    $this->_view->assign('format_s', $def_order_ns);
                    break;

                case 'date_asc':
                    $def_order = ' ORDER BY a.Datum ASC';
                    $def_order_ns = '&sort=datum_desc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'date_desc':
                    $def_order = ' ORDER BY a.Datum DESC';
                    $def_order_ns = '&sort=datum_asc' . $curr_page;
                    $this->_view->assign('date_s', $def_order_ns);
                    break;

                case 'active_asc':
                    $def_order = ' ORDER BY a.Aktiv ASC';
                    $def_order_ns = '&sort=active_desc' . $curr_page;
                    $this->_view->assign('active_s', $def_order_ns);
                    break;

                case 'active_desc':
                    $def_order = ' ORDER BY a.Aktiv DESC';
                    $def_order_ns = '&sort=active_asc' . $curr_page;
                    $this->_view->assign('active_s', $def_order_ns);
                    break;

                case 'newsletter_asc':
                    $def_order = ' ORDER BY a.Newsletter_Id ASC';
                    $def_order_ns = '&sort=newsletter_desc' . $curr_page;
                    $this->_view->assign('newsletter_s', $def_order_ns);
                    break;

                case 'newsletter_desc':
                    $def_order = ' ORDER BY a.Newsletter_Id DESC';
                    $def_order_ns = '&sort=newsletter_asc' . $curr_page;
                    $this->_view->assign('newsletter_s', $def_order_ns);
                    break;
            }
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS
            a.*,
                    b.Name
            FROM
                    " . PREFIX . "_newsletter_abos AS a,
                    " . PREFIX . "_newsletter AS b
            WHERE
                b.Id = a.Newsletter_Id
            AND
                    a.Sektion = '" . AREA . "'
                    $def_search
                    $def_order
            LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }

        $ordstr = 'index.php?do=newsletter&amp;sub=showabos' . $def_search_n . '&amp;pp=' . $limit;
        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"{$ordstr}&page={s}\">{t}</a> "));
        }
        $tpl_array = array(
            'ordstr' => $ordstr,
            'limit'  => $limit,
            'items'  => $items,
            'title'  => $this->_lang['Newsletter_Categs']);
        $this->_view->assign($tpl_array);
        $this->_view->content('/newsletter/abos.tpl');
    }

    public function getCategs() {
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Name']) as $Nname) {
                if (!empty($_POST['Name'][$Nname])) {
                    $array = array(
                        'Name' => $_POST['Name'][$Nname],
                        'Info' => $_POST['Info'][$Nname],
                    );
                    $this->_db->update_query('newsletter', $array, "Id='" . intval($Nname) . "'");
                }
            }
            $this->__object('AdminCore')->script('save');
        }

        if (Arr::getPost('new') == 1) {
            if (!empty($_POST['Name'])) {
                $insert_array = array(
                    'Name'    => Arr::getPost('Name'),
                    'Info'    => Arr::getPost('Info'),
                    'Sektion' => AREA);
                $this->_db->insert_query('newsletter', $insert_array);
                $this->__object('AdminCore')->script('save');
            }
        }
        $this->_view->assign('Categs', $this->categs());
        $this->_view->assign('title', $this->_lang['Newsletter_Categs']);
        $this->_view->content('/newsletter/categs.tpl');
    }

    public function archive() {
        $db_where = $nav = '';
        if (Arr::getPost('delete') == 1 && perm('newsletter')) {
            if (isset($_POST['del'])) {
                foreach (array_keys($_POST['del']) as $did) {
                    $this->_db->query("DELETE FROM " . PREFIX . "_newsletter_archiv WHERE Id='" . intval($did) . "'");
                }
            }
        }

        $_REQUEST['typ'] = !empty($_REQUEST['typ']) ? $_REQUEST['typ'] : 'all';
        switch ($_REQUEST['typ']) {
            case 'groups':
                $db_where .= " AND Typ='groups'";
                $nav = '&amp;typ=groups';
                break;

            case 'abos':
                $db_where .= " AND Typ='abos'";
                $nav = '&amp;typ=abos';
                break;
        }

        switch (Arr::getRequest('sys')) {
            default:
            case 'one':
                $db_where .= " AND Sys='one'";
                $sys = '&amp;sys=one';
                break;

            case 'later':
                $db_where .= " AND Sys='later'";
                $sys = '&amp;sys=later';
                break;

            case 'more':
                $db_where .= " AND Sys='more'";
                $sys = '&amp;sys=more';
                break;
        }

        $limit = $this->__object('AdminCore')->limit();
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_newsletter_archiv WHERE Sektion='" . AREA . "' {$db_where} ORDER BY Id DESC LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $items = array();
        while ($row = $sql->fetch_object()) {
            $items[] = $row;
        }
        $sql->close();

        if ($num > $limit) {
            $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=newsletter&sub=archive{$sys}&amp;page={s}{$nav}&pp={$limit}\">{t}</a> "));
        }
        $tpl_array = array(
            'limit' => $limit,
            'items' => $items,
            'title' => $this->_lang['Newsletter_archive']);
        $this->_view->assign($tpl_array);
        $this->_view->content('/newsletter/archive.tpl');
    }

    /* Метод просмотра информации о рассылке в архиве */
    public function show($id) {
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_newsletter_archiv WHERE Sektion='" . AREA . "' AND Id='" . intval($id) . "' LIMIT 1");
        if (Arr::getRequest('noout') == 1) {
            if ($res->Noheader == 1) {
                SX::output(SX::get('system.Mail_Header'));
                SX::output($res->Newsletter);
                SX::output(SX::get('system.Mail_Fuss_HTML'));
            } else {
                SX::output($res->Newsletter);
            }
        } else {
            $names = array();
            if (!empty($res->Gruppen)) {
                $base = $res->Typ == 'abos' ? '_newsletter' : '_benutzer_gruppen';
                $sql = $this->_db->query("SELECT Name AS Name_Intern FROM " . PREFIX . $base . " WHERE Id IN(" . $res->Gruppen . ")");
                while ($row = $sql->fetch_object()) {
                    $names[] = $row->Name_Intern;
                }
            }
            $tpl_array = array(
                'names' => $names,
                'att'   => (!empty($res->Anlagen) ? explode(',', $res->Anlagen) : array()),
                'HtmlV' => $this->__object('Editor')->load('admin', $res->Code, 'htmlversion', 450, 'Nothing'),
                'res'   => $res,
                'title' => $this->_lang['Global_Overview']);
            $this->_view->assign($tpl_array);
            $this->_view->content('/newsletter/view.tpl');
        }
    }

    /* Метод проверки основных полей */
    protected function check() {
        if (empty($_REQUEST['htmlversion']) || $this->_text->strlen($_REQUEST['htmlversion']) < 5) {
            $this->redir();
        } else {
            if (empty($_REQUEST['ToCateg'])) {
                $_SESSION['Newsletter_text'] = $_REQUEST['htmlversion'];
                $this->redir();
            }
        }
    }

    /* Метод редиректа на стартовую страницу рассылки */
    protected function redir() {
        $link = 'index.php?do=newsletter&sub=new&sys=' . Arr::getRequest('sys') . '&to=' . Arr::getRequest('to') . '&area=' . AREA . '&noframes=1';
        $this->__object('Redir')->redirect($link);
    }

    /* Метод загрузки вложений */
    protected function loadAttach() {
        $this->_path = UPLOADS_DIR . '/attachments/';
        $_SESSION['delattach'] = intval($this->_req['delattach']);
        if ($this->_req['start'] >= 1) {
            $this->addAttach($_SESSION['datas']);
        } else {
            $options = array(
                'rand'   => true,
                'type'   => 'file',
                'result' => 'data',
                'upload' => '/uploads/attachments/',
                'input'  => 'files',
            );
            $result = SX::object('Upload')->load($options);
            $_SESSION['datas'] = !empty($result) ? $result : '';
        }
    }

    /* Метод вложения файлов */
    protected function addAttach($datas) {
        if (!empty($datas)) {
            $Anlagen = explode(',', $datas);
            foreach ($Anlagen as $Anlage) {
                $cnt = File::get($this->_path . $Anlage);
                $this->_attach[] = $Anlage;
                array_push($this->_attach, array('filename' => $Anlage, 'content' => $cnt, 'type' => ''));
            }
        }
    }

    /* Метод получения путей для вложений */
    protected function patchAttach($text, $self) {
        $start_media = str_replace('admin/index.php', '', $self);
        return str_replace('src="' . $start_media . 'uploads/', 'src="' . $this->_url . '/uploads/', $text);
    }

    /* Метод получения групп рассылки */
    protected function groups() {
        foreach (array_keys($this->_req['ToCateg']) as $ToC) {
            $this->_groups[] = intval($ToC);
        }
    }

    /* Метод выбора типа рассылки */
    protected function type() {
        switch ($this->_req['sys']) {
            default:
            case 'one':
                $this->addOne();
                break;

            case 'later':
                $this->addLater();
                break;

            case 'more':
                $this->addMore();
                break;
        }
    }

    /* Метод добавления рассылки */
    public function add() {
        if (Arr::getRequest('send') == 1) {
            $this->check();

            $this->_req = Arr::getRequest(array(
                        'htmlversion' => '',
                        'betreff'     => '',
                        'absname'     => '',
                        'absmail'     => '',
                        'to'          => '',
                        'start'       => '',
                        'noheader'    => 1,
                        'delattach'   => '',
                        'area'        => 1,
                        'nltype'      => '',
                        'sys'         => '',
                        'ToCateg'     => ''));

            $this->loadAttach();
            $text = $this->parse($this->_req['htmlversion']);
            $this->_cache['html'] = $this->patchAttach($text, $_SERVER['PHP_SELF']);
            $this->groups();
            $this->type();
        } else {
            $tpl_array = array(
                'Typs'             => explode(',', $this->_lang['NewsletterTyps']),
                'UserGroups'       => $this->__object('AdminCore')->groups(),
                'title'            => $this->_lang['NewsletterNew'],
                'DefSubject'       => $this->_lang['NewsletterDefS'] . SX::get('system.Seitenname'),
                'NewsletterCategs' => $this->categs(),
                'HtmlV'            => $this->__object('Editor')->load('admin', Arr::getSession('Newsletter_text'), 'htmlversion', 400, 'Content'));
            $this->_view->assign($tpl_array);
            $this->_view->content('/newsletter/new.tpl');
        }
    }

    /* Метод выполняет задание по крону */
    public function send($obj) {
        $val = unserialize($obj->Options);
        $this->_url = $val['url'];
        switch ($val['type']) {
            case 'later':
                $this->sendLater($obj, $val);
                break;

            case 'more':
                $this->sendMore($obj, $val);
                break;
        }
    }

    /* Вычисляем время следующего запуска при пропуске выполнений */
    protected function newTime($ntime, $datum, $time) {
        $val = $time - $datum;
        if ($val < $ntime) {
            return $datum + $ntime;
        } else {
            $var = floor($val / $ntime) + 1;
            return $datum + ($var * $ntime);
        }
    }

    /* Вычисляем старта рассылки */
    protected function startTime($post) {
        $post['datum'] = !empty($post['datum']) ? $this->__object('AdminCore')->mktime($post['datum']) : (time() + 600);
        $post['s_hour'] = $post['s_hour'] * 3600;
        $post['s_minut'] = $post['s_minut'] * 60;
        $post['datum'] = $post['datum'] + $post['s_hour'] + $post['s_minut'];
        return $post;
    }

    /* Метод реализует отложенную отправку */
    protected function sendLater($obj, $val) {
        $id = intval($val['id']);
        $row = $this->_db->fetch_object("SELECT * FROM  " . PREFIX . "_newsletter_archiv WHERE Id = '" . $id . "' LIMIT 1");
        if (is_object($row)) {
            $this->_req = array(
                'htmlversion' => $row->Newsletter,
                'betreff'     => $row->Titel,
                'absname'     => $row->Absender,
                'absmail'     => $row->Email,
                'to'          => $row->Typ,
                'start'       => $val['start'],
                'noheader'    => $row->Noheader,
                'delattach'   => $val['del'],
                'area'        => $row->Sektion,
                'nltype'      => $row->Typ,
                'sys'         => $row->Sys);

            $this->addAttach($row->Anlagen);
            $this->_cache['html'] = $row->Newsletter;
            $this->_groups = explode(',', $row->Gruppen);
            $this->mails($this->_req['start'], $val['limit']);

            if ($this->_req['start'] >= $this->_req['count']) {
                $this->_db->query("UPDATE " . PREFIX . "_newsletter_archiv SET Sys = 'one' WHERE Id = '" . $id . "'");
                $this->_db->query("DELETE FROM " . PREFIX . "_schedule WHERE Id='" . $obj->Id . "'");
                SX::syslog('Завершена отложенная рассылка (' . $row->Titel . ')', '0', $_SESSION['benutzer_id']);
                if ($val['del'] == 1) {
                    $this->deleteFiles($row->Anlagen);
                }
            } else {
                $time = time();
                $data = $this->newTime($obj->NextTime, $obj->Datum, $time);
                $val['start'] = $val['start'] + $val['limit'];
                $array = array(
                    'Datum'    => $data,
                    'PrevTime' => $time,
                    'Options'  => serialize($val),
                );
                $this->_db->update_query('schedule', $array, "Id = '" . $obj->Id . "'");
            }
        }
    }

    /* Метод создает отложенное задание на рассылку */
    protected function addLater() {
        $post = Arr::getPost(array('datum' => '', 's_hour' => 0, 's_minut' => 0, 'limits' => 0, 'interval' => 0));
        $post = $this->startTime($post);
        $this->save($post['datum'], 'later');

        $options = array(
            'type'  => 'later',
            'id'    => $this->_db->insert_id(),
            'start' => 0,
            'limit' => (is_numeric($post['limits']) ? $post['limits'] : $this->_limit),
            'url'   => $this->_url,
            'self'  => $_SERVER['PHP_SELF'],
            'del'   => $_SESSION['delattach']);

        $schedule = array(
            'datum'    => $post['datum'],
            'nexttime' => (is_numeric($post['interval']) ? $post['interval'] : 600),
            'type'     => 'sys',
            'modul'    => 'newsletter',
            'title'    => $this->_lang['NewsletterLater'] . ' :: ' . $this->_req['betreff'],
            'options'  => serialize($options),
            'aktiv'    => 1);
        $this->__object('Cron')->add($schedule);
        $this->_view->assign('ok', 1);
        $this->_view->content('/newsletter/status.tpl');
    }

    /* Метод реализует отложенную регулярную отправку */
    protected function sendMore($obj, $val) {
        $id = intval($val['id']);
        $row = $this->_db->fetch_object("SELECT * FROM  " . PREFIX . "_newsletter_archiv WHERE Id = '" . $id . "' LIMIT 1");
        if (is_object($row)) {
            $this->_req = array(
                'htmlversion' => $row->Newsletter,
                'betreff'     => $row->Titel,
                'absname'     => $row->Absender,
                'absmail'     => $row->Email,
                'to'          => $row->Typ,
                'start'       => $val['start'],
                'noheader'    => $row->Noheader,
                'delattach'   => $val['del'],
                'area'        => $row->Sektion,
                'nltype'      => $row->Typ,
                'sys'         => $row->Sys);

            $this->addAttach($row->Anlagen);
            if ($val['start'] < 1) {
                $text = $this->parse($row->Code);
                $this->_cache['html'] = $this->patchAttach($text, $val['self']);
                $this->_db->query("UPDATE " . PREFIX . "_newsletter_archiv SET
				        Newsletter = '" . $this->_db->escape($this->_cache['html']) . "'
					WHERE
						Id = '" . $id . "'");
            } else {
                $this->_cache['html'] = $row->Newsletter;
            }
            $this->_groups = explode(',', $row->Gruppen);
            $this->mails($this->_req['start'], $val['limit']);

            $time = time();
            if ($this->_req['start'] >= $this->_req['count']) {
                $insert_array = array(
                    'Datum'      => $time,
                    'Typ'        => $row->Typ,
                    'Titel'      => $row->Titel,
                    'Newsletter' => $this->_cache['html'],
                    'Email'      => $row->Email,
                    'Absender'   => $row->Absender,
                    'Autor'      => $row->Autor,
                    'Anlagen'    => $row->Anlagen,
                    'Gruppen'    => $row->Gruppen,
                    'Sektion'    => AREA,
                    'Code'       => $row->Code,
                    'Sys'        => 'one',
                    'Noheader'   => $row->Noheader);
                $this->_db->insert_query('newsletter_archiv', $insert_array);

                $data = $this->newTime($val['now'], $val['temp'], $time);
                $val['start'] = 0;
                SX::syslog('Завершена регулярная рассылка (' . $row->Titel . ')', '0', $_SESSION['benutzer_id']);
            } else {
                $data = $this->newTime($obj->NextTime, $obj->Datum, $time);
                $val['start'] = $val['start'] + $val['limit'];
            }
            $array = array(
                'Datum'    => $data,
                'PrevTime' => $time,
                'Options'  => serialize($val),
            );
            $this->_db->update_query('schedule', $array, "Id = '" . $obj->Id . "'");
        }
    }

    /* Метод создает регулярное задание на рассылку */
    protected function addMore() {
        $post = Arr::getPost(array('datum' => '', 's_hour' => 0, 's_minut' => 0, 'limits' => 0, 'interval' => 0, 'now' => 1, 'now_typ' => 86400));
        $post = $this->startTime($post);
        $this->save($post['datum'], 'more');

        $options = array(
            'type'  => 'more',
            'id'    => $this->_db->insert_id(),
            'start' => 0,
            'limit' => (is_numeric($post['limits']) ? $post['limits'] : $this->_limit),
            'url'   => $this->_url,
            'self'  => $_SERVER['PHP_SELF'],
            'del'   => $_SESSION['delattach'],
            'temp'  => $post['datum'],
            'now'   => $post['now'] * $post['now_typ']);

        $schedule = array(
            'datum'    => $post['datum'],
            'nexttime' => (is_numeric($post['interval']) ? $post['interval'] : 600),
            'type'     => 'sys',
            'modul'    => 'newsletter',
            'title'    => $this->_lang['NewsletterMore'] . ' :: ' . $this->_req['betreff'],
            'options'  => serialize($options),
            'aktiv'    => 1);
        $this->__object('Cron')->add($schedule);
        $this->_view->assign('ok', 1);
        $this->_view->content('/newsletter/status.tpl');
    }

    /* Метод выполняет обычную рассылку */
    protected function addOne() {
        $this->mails($this->_req['start'], $this->_limit);
        if ($this->_req['start'] < $this->_req['count']) {
            $this->reload();
        } else {
            if ($this->_req['start'] > 0) {
                $this->save(time(), 'one');
                $this->deleteAttach($this->_db->insert_id());
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отправил рассылку (' . $this->_req['betreff'] . ')', '0', $_SESSION['benutzer_id']);
                unset($_SESSION['Newsletter_text']);
            } else {
                $_SESSION['Newsletter_text'] = $this->_req['htmlversion'];
                $this->_view->assign('not_send', 1);
            }
            $this->_view->assign('done', 100);
            $this->_view->content('/newsletter/status.tpl');
        }
    }

    /* Метод формирования писем расылки */
    protected function mails($start, $limit) {
        $sethtml = 1;
        $mail = SX::object('Mail');
        $header = $this->_req['noheader'] == '1' ? SX::get('system.Mail_Header') : '';
        $order = "ORDER BY Id ASC LIMIT " . intval($start) . "," . intval($limit);
        if ($this->_req['to'] == 'abos') {
            $sql = "_newsletter_abos WHERE Sektion='" . AREA . "' AND Aktiv='1' AND Newsletter_Id IN(" . implode(',', $this->_groups) . ") GROUP BY Email " . $order;
        } else {
            $sql = "_benutzer WHERE Newsletter='1' AND Aktiv='1' AND Gruppe IN(" . implode(',', $this->_groups) . ") " . $order;
        }
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . $sql);
        $this->_req['count'] = $this->_db->found_rows();
        while ($row = $sql->fetch_object()) {
            if ($this->_req['to'] == 'abos') {
                $row->Benutzername = '';
                $inf = $this->header($row);
                if ($row->Format == 'html') {
                    $text = $header . $inf . $this->_cache['html'];
                } else {
                    $text = $this->text($inf, $header);
                    $sethtml = 0;
                }
            } else {
                $text = $header . $this->users($row->Benutzername);
            }
            $mail->send($this->_req['noheader'], $row->Email, $row->Benutzername, $text, $this->_req['betreff'], $this->_req['absmail'], $this->_req['absname'], 'text', $this->_attach, $sethtml, 3);
            $this->_req['start'] ++;
        }
        $sql->close();
    }

    /* Метод формирует информацию приветствия */
    protected function header($row) {
        $inf = str_replace('__WEBSEITE__', '<a href="' . $this->_url . '">' . $this->_url . '</a>', $this->_lang['Newsletter_UnsubscribeInf_html']);
        $inf = str_replace('__LINK__', '<a href="' . $this->_url . '/index.php?p=newsletter&action=unsubscribe&email=' . $row->Email . '&code=' . $row->Code . '&area=' . $row->Sektion . '">' . $this->_url . '/index.php?p=newsletter&action=unsubscribe&email=' . $row->Email . '&code=' . $row->Code . '&area=' . $row->Sektion . '</a>', $inf);
        $inf .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
        return $inf;
    }

    /* Метод возвращает текстовое письмо */
    protected function text($inf, $header) {
        if (!isset($this->_cache['text_set'])) {
            $this->_cache['text_set'] = 1;
            $this->_cache['header'] = $this->html2text($header);
            $this->_cache['text'] = $this->html2text($this->_cache['html']);
        }
        return $this->_cache['header'] . $this->html2text($inf) . $this->_cache['text'];
    }

    /* Метод возвращает письмо для рассылки пользователям */
    protected function users($name) {
        $mail_array = array(
            '__WEBSEITE__' => '<a href="' . $this->_url . '">' . $this->_url . '</a>',
            '__BENUTZER__' => '<strong>' . $name . '</strong>');
        $inf = $this->_text->replace($this->_lang['Newsletter_UnsubscribeInf_html2'], $mail_array);
        $inf .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
        return $inf . $this->_cache['html'];
    }

    /* Метод этапа отправки */
    protected function reload() {
        $forms = '<form action="index.php?do=newsletter&amp;sub=new&amp;to=' . $this->_req['to'] . '&amp;area=' . $this->_req['area'] . '&amp;noframes=1" method="post" name="nextform" id="nextform">';
        $forms .= '<input type="hidden" name="send" value="1" />';
        $forms .= '<input type="hidden" name="start" value="' . $this->_req['start'] . '" />';
        $forms .= '<input type="hidden" name="betreff" value="' . $this->_req['betreff'] . '" />';
        $forms .= '<input type="hidden" name="absname" value="' . $this->_req['absname'] . '" />';
        $forms .= '<input type="hidden" name="absmail" value="' . $this->_req['absmail'] . '" />';
        $forms .= '<input type="hidden" name="nltype" value="' . $this->_req['to'] . '" />';
        $forms .= '<input type="hidden" name="delattach" value="' . $this->_req['delattach'] . '" />';

        foreach (array_keys($this->_req['ToCateg']) as $ToC) {
            $forms .= '<input type="hidden" name="ToCateg[' . $ToC . ']" value="true">' . "\n";
        }
        $forms .= '<textarea style="width:1;height:1;visibility:hidden" name="htmlversion">' . sanitize($this->_req['htmlversion']) . '</textarea>';
        $forms .= '</form>';
        $prozent = $this->_req['start'] / ($this->_req['count'] / 100);
        $jsdata = '<script type="text/javascript">
		<!--
		function nexts() {
		    document.nextform.submit();
		}
		setTimeout("nexts();", 1000);
		//-->
		</script>
		';
        $tpl_array = array(
            'forms'   => $forms,
            'done_nl' => round($prozent),
            'jsdata'  => $jsdata);
        $this->_view->assign($tpl_array);
        $this->_view->content('/newsletter/status.tpl');
    }

    /* Метод записи в архив рассылки */
    protected function save($date, $sys) {
        $insert_array = array(
            'Datum'      => $date,
            'Typ'        => $this->_req['nltype'],
            'Titel'      => $this->_req['betreff'],
            'Newsletter' => $this->_cache['html'],
            'Email'      => $this->_req['absmail'],
            'Absender'   => $this->_req['absname'],
            'Autor'      => $_SESSION['benutzer_id'],
            'Anlagen'    => implode(',', $this->_attach),
            'Gruppen'    => implode(',', $this->_groups),
            'Sektion'    => AREA,
            'Code'       => $this->_req['htmlversion'],
            'Sys'        => $sys,
            'Noheader'   => $this->_req['noheader']);
        $this->_db->insert_query('newsletter_archiv', $insert_array);
    }

    /* Метод удаления вложений */
    protected function deleteAttach($id) {
        if (Arr::getSession('delattach') == 1) {
            $row = $this->_db->fetch_object("SELECT Anlagen FROM  " . PREFIX . "_newsletter_archiv WHERE Id = '" . intval($id) . "' LIMIT 1");
            if (is_object($row)) {
                $this->deleteFiles($row->Anlagen);
                $_SESSION['delattach'] = '';
            }
        }
    }

    /* Метод удаления файлов */
    protected function deleteFiles($dels) {
        $dels = explode(',', trim($dels));
        foreach ($dels as $del) {
            File::delete($this->_path . $del);
        }
    }

    /* Метод получает список рассылок */
    protected function categs() {
        $items = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_newsletter WHERE Sektion='" . AREA . "' ORDER BY Name ASC");
        return $items;
    }

    /* Метод получения текста из html */
    protected function html2text($text) {
        $text = str_replace(array('<br />', '<hr style="clear:both" noshade="noshade" size="1" />'), array("\r\n", $this->_separator), $text);
        $text = preg_replace(array("!\t!", '!<script[^>]*?>.*?</script>!is', '!<style[^>]*?>.*?</style>!isu'), array('', ' ', ' '), $text);
        $text = strip_tags($text);
        $text = preg_replace("!\r\n\s+!u", "\r\n", $text);
        $text = str_replace(array('&amp;', '&nbsp;', '&lt;', '&gt;', '&quot;', '&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;', '&bull;'), array('&', ' ', '<', '>', '"', 'Ђ', '»', '«', '©', '®', '™', '„', '“', '•'), $text);
        return $text;
    }

    /* Метод парсинга на вставку последних новостей, товаров, галлерей */
    protected function parse($text) {
        $text = preg_replace_callback('!\[NEWS:([0-9]*)\]!iu', array($this, 'news'), $text);
        $text = preg_replace_callback('!\[ARTICLES:([0-9]*)\]!iu', array($this, 'articles'), $text);
        $text = preg_replace_callback('!\[SHOP:([0-9]*)\]!iu', array($this, 'shop'), $text);
        $text = preg_replace_callback('!\[GALLERY:([0-9]*)\]!iu', array($this, 'gallery'), $text);
        return Tool::cleanTags($text, array('screen', 'contact', 'audio', 'video', 'neu', 'codewidget'));
    }

    /* Метод обрезки текста по количеству символов по целому слову */
    protected function correct($text, $count = 350, $char = '...') {
        if ($this->_text->strlen($text) > $count) {
            $text = $this->_text->substr($text, 0, $count);
            $text = explode(' ', $text);
            array_pop($text);
            $text = implode(' ', $text) . $char;
        }
        return $text;
    }

    /* Метод вывода последних новостей */
    protected function news($match) {
        $text = '';
        if (!empty($match[1])) {
            $text .= '<h3>' . $this->_lang['Newsletter_NewsTitle'] . '</h3>';
            $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            $q = $this->_db->query("SELECT * FROM " . PREFIX . "_news
			WHERE Sektion = '" . AREA . "' AND Aktiv = '1' AND ZeitStart <= '" . time() . "'
			ORDER BY Id DESC LIMIT " . intval($match[1]));
            while ($row = $q->fetch_object()) {
                $link = $this->_url . '/index.php?p=news&area=' . AREA . '&newsid=' . $row->Id . '&name=' . translit($row->Titel1);
                $text .=!empty($row->Bild1) ? '<img src="' . $this->_url . '/' . Tool::prefixPatch($row->Bild1, true) . '" alt="" border="0" align="right" />' : '';
                $text .= '<h3><a href="' . $link . '">' . $this->correct($row->Titel1, 200) . '</a></h3><br />';
                $text .=!empty($row->News1) ? $this->correct(strip_tags($row->News1), 350) . '<br />' : '';
                $text .= $this->_lang['Newsletter_ReadMore'] . ' <a href="' . $link . '">' . $link . '</a>';
                $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            }
            $q->close();
        }
        return $text;
    }

    /* Метод вывода последних статей */
    protected function articles($match) {
        $text = '';
        if (!empty($match[1])) {
            $text .= '<h3>' . $this->_lang['NewsletterNewArticles'] . '</h3>';
            $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            $q = $this->_db->query("SELECT * FROM " . PREFIX . "_artikel
			WHERE Sektion = '" . AREA . "' AND Aktiv = '1' AND ZeitStart <= '" . time() . "'
			ORDER BY Id DESC LIMIT " . intval($match[1]));
            while ($row = $q->fetch_object()) {
                $link = $this->_url . '/index.php?p=articles&area=' . AREA . '&action=displayarticle&id=' . $row->Id . '&name=' . translit($row->Titel_1);
                $patch = '/uploads/articles/' . $row->Bild_1;
                $text .=!empty($row->Bild_1) && is_file(SX_DIR . $patch) ? '<img src="' . $this->_url . $patch . '" alt="" border="0" align="right" />' : '';
                $text .= '<h3><a href="' . $link . '">' . $this->correct($row->Titel_1, 200) . '</a></h3><br />';
                $text .=!empty($row->Inhalt_1) ? $this->correct(strip_tags($row->Inhalt_1), 350) . '<br />' : '';
                $text .= $this->_lang['Newsletter_ReadMore'] . ' <a href="' . $link . '">' . $link . '</a>';
                $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            }
            $q->close();
        }
        return $text;
    }

    /* Метод вывода последних товаров магазина */
    protected function shop($match) {
        $text = '';
        if (!empty($match[1])) {
            $width = SX::get('shop.thumb_width_middle');
            $text .= '<h3>' . $this->_lang['Newsletter_ShopTitle'] . '</h3>';
            $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            $q = $this->_db->query("SELECT * FROM " . PREFIX . "_shop_produkte WHERE Aktiv = '1' ORDER BY Id DESC LIMIT " . intval($match[1]));
            while ($row = $q->fetch_object()) {
                $link = $this->_url . '/index.php?p=shop&action=showproduct&id=' . $row->Id . '&cid=' . $row->Kategorie . '&name=' . translit($row->Titel_1);
                if (!empty($row->Bild)) {
                    $text .= '<img src="' . $this->_url . '/lib/image.php?action=shop&amp;width=' . $width . '&image=' . $row->Bild . '" alt="" border="0" align="right" />';
                }
                $text .= '<h3><a href="' . $link . '">' . $this->correct($row->Titel_1, 200) . '</a></h3><br />';
                $text .=!empty($row->Beschreibung_1) ? $this->correct(strip_tags($row->Beschreibung_1), 350) . '<br />' : '';
                $text .= $this->_lang['Newsletter_ReadMore'] . ' <a href="' . $link . '">' . $link . '</a>';
                $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            }
            $q->close();
        }
        return $text;
    }

    /* Метод вывода последних галерей */
    protected function gallery($match) {
        $text = '';
        if (!empty($match[1])) {
            $text .= '<h3>' . $this->_lang['Newsletter_GalTitle'] . '</h3>';
            $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            $q = $this->_db->query("SELECT * FROM " . PREFIX . "_galerie WHERE Sektion = '" . AREA . "' AND Aktiv = '1' ORDER BY Id DESC LIMIT " . intval($match[1]));
            while ($row = $q->fetch_object()) {
                $link = $this->_url . '/index.php?p=gallery&action=showgallery&id=' . $row->Id . '&categ=' . $row->Kategorie . '&name' . translit($row->Name_1) . '&area=' . AREA;
                $res = $this->_db->fetch_object("SELECT Id FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $row->Id . "' ORDER BY Id DESC LIMIT 1");
                if (isset($res->Id)) {
                    $text .= '<img src="' . $this->_url . '/lib/image.php?action=gallery&amp;width=' . SX::get('galerie.Bilder_Mittel') . '&amp;image=' . $res->Id . '" alt="" border="0" align="right" />';
                }
                $text .= '<h3><a href="' . $link . '">' . $this->correct($row->Name_1, 200) . '</a></h3><br />';
                $text .=!empty($row->Beschreibung_1) ? $this->correct(strip_tags($row->Beschreibung_1), 350) . '<br />' : '';
                $text .= $this->_lang['Newsletter_ShowGal'] . ' <a href="' . $link . '">' . $link . '</a>';
                $text .= '<hr style="clear:both" noshade="noshade" size="1" /><br />';
            }
            $q->close();
        }
        return $text;
    }

}
