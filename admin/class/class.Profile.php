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

class Profile extends Magic {

    protected $autoritet = 0;
    protected $settings;
    protected $users;
    protected $UserId;

    public function __construct() {
        $this->users = SX::get('users');
        $this->settings = SX::get('system');
        $this->UserId = $_SESSION['benutzer_id'];
    }

    public function skype() {
        $title = $this->_lang['Profile_ScypeOpt'];
        $row = $this->_db->cache_fetch_object("SELECT skype FROM " . PREFIX . "_benutzer WHERE Id = '" . intval($_REQUEST['uid']) . "' LIMIT 1");

        $tpl_array = array(
            'skype'      => $row->skype,
            'title_html' => $title);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $title,
            'pagetitle' => sanitize($row->skype . $this->_lang['PageSep'] . $title . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
            'content'   => $this->_view->fetch(THEME . '/user/skype_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    public function icq() {
        $title = $this->_lang['Profile_ICQ'];
        $row = $this->_db->cache_fetch_object("SELECT icq FROM " . PREFIX . "_benutzer WHERE Id = '" . intval($_REQUEST['uid']) . "' LIMIT 1");

        $tpl_array = array(
            'icq'        => $row->icq,
            'title_html' => $title);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $title,
            'pagetitle' => sanitize($row->icq . $this->_lang['PageSep'] . $title . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
            'content'   => $this->_view->fetch(THEME . '/user/icq_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    public function email() {
        $ignore = array();
        $sname = $this->_lang['SendEmail_Inf'];
        $query = "SELECT Emailempfang, Email, Benutzername FROM " . PREFIX . "_benutzer WHERE Id='" . intval($_REQUEST['uid']) . "' ; ";
        $query .= "SELECT IgnorierId FROM " . PREFIX . "_ignorierliste WHERE BenutzerId='" . intval($_REQUEST['uid']) . "'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $row = $result->fetch_object();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row_2 = $result->fetch_object()) {
                    $ignore[] = $row_2->IgnorierId;
                }
                $result->close();
            }
        }
        if (Arr::getRequest('mailsend') == 1) {
            $error = array();
            if ($row->Emailempfang != 1) {
                $error[] = $this->_lang['SendEmail_NotWanted'];
            }
            if (in_array($_SESSION['benutzer_id'], $ignore)) {
                $error[] = $this->_lang['SendEmail_Blocked'];
            }
            if (!Tool::isMail($_POST['email'])) {
                $error[] = $this->_lang['RegE_wrongmail'];
            }
            if (empty($_REQUEST['subject'])) {
                $error[] = $this->_lang['No_Subject'];
            }
            if (empty($_REQUEST['body'])) {
                $error[] = $this->_lang['No_Message'];
            }

            if ($this->__object('Captcha')->check($error)) {
                $message = Tool::censored($_REQUEST['body']);
                $mail_array = array('__HOMEPAGEURL__' => BASE_URL, '__USER__' => Tool::fullName());
                $footer = $this->_text->replace($this->_lang['emailform_homepage_footer'], $mail_array);
                $from = $this->_text->replace($this->_lang['SendEmail_SubjectEmail'], '__HOMEPAGE__', $this->settings['Seitenname']);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $row->Email,
                    'to_name'   => $row->Benutzername,
                    'text'      => $message . "\r\n" . $footer,
                    'subject'   => $_REQUEST['subject'],
                    'fromemail' => $_POST['email'],
                    'from'      => $from,
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 3));
            }
        }

        $this->__object('Captcha')->start(); // Инициализация каптчи

        $tpl_array = array(
            'sname' => $sname,
            'uid'   => $_REQUEST['uid']);
        $this->_view->assign($tpl_array);

        $seo_array = array(
            'headernav' => $sname,
            'pagetitle' => sanitize($sname . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
            'content'   => $this->_view->fetch(THEME . '/user/email_popup.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function user_set($id) {
        if (empty($id)) {
            $this->__object('Redir')->seoRedirect('index.php?p=index&area=' . AREA);
        }
        $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . intval($id) . "' AND Aktiv=1 LIMIT 1");
        if (!is_array($row)) {
            $this->__object('Redir')->seoRedirect('index.php?p=index&area=' . AREA);
        }
        if ($row['Profil_Alle'] != 1 && $_SESSION['user_group'] == 2) {
            $this->__object('Core')->message('Forums_UserProfile', 'Profile_PublicAllNo', BASE_URL . '/index.php?p=index&amp;area=' . AREA);
        }
    }

    protected function user_thanks($id) {
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS thanks FROM " . PREFIX . "_f_post WHERE uid = '" . intval($id) . "' AND thanks != ''");
        $num_post = $this->_db->found_rows();
        if ($num_post < 1) {
            return '';
        }
        while ($row = $sql->fetch_assoc()) {
            $thanks[] = $row['thanks'] . ';';
        }
        $sql->close();
        $thanks = explode(';', implode('', $thanks));
        array_pop($thanks);
        $num_user = count(array_unique($thanks));
        $num_thanks = count($thanks);
        $this->setAutoritet($num_thanks, 20); // за 20 благодарностей очко в авторитет
        return array('num_post' => $num_post, 'num_user' => $num_user, 'num_thanks' => $num_thanks);
    }

    protected function user_activity($id) {
        if (empty($id)) {
            return;
        }
        $limit = $this->users['LimitActions'];
        $lc = Arr::getSession('Langcode', 1);
        $limit_page = Tool::userSettings('Forum_Beitraege_Limit', 15);
        $posts = array();

        if (get_active('forums')) {
            $sql = $this->_db->query("SELECT
                    a.*,
                    b.forum_id,
                    b.title AS forum_title,
                    c.group_id AS forum_group_id
            FROM
                    " . PREFIX . "_f_post AS a,
                    " . PREFIX . "_f_topic AS b,
                    " . PREFIX . "_f_forum AS c
            WHERE
                    a.uid = '" . intval($id) . "'
            AND
                    b.id = a.topic_id
            AND
                    c.id = b.forum_id
            ORDER BY a.id DESC LIMIT {$limit}");
            while ($row = $sql->fetch_assoc()) {
                if (in_array($_SESSION['user_group'], explode(',', $row['forum_group_id']))) {
                    $row['enc'] = '1';
                }
                $row['title'] = $this->_text->chars(Tool::censored($row['title']), 60, '');
                $row['message'] = $this->_text->chars(Tool::censored($this->__object('Post')->clean($row['message'])), 250, '');
                $row['date'] = strtotime($row['datum']);
                $page = Tool::countPost($row['id'], $row['topic_id'], $limit_page);
                $row['link'] = 'index.php?p=showtopic&amp;toid=' . $row['topic_id'] . '&amp;pp=' . $limit_page . '&amp;page=' . $page . '#pid_' . $row['id'];
                $posts[] = $row;
            }
            $sql->close();
        }

        if (get_active('comments')) {
            $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_kommentare WHERE Autor_Id = '" . intval($id) . "' AND Aktiv = '1' ORDER BY Id Desc LIMIT {$limit}");
            $this->setAutoritet($this->_db->found_rows(), 20); // за 20 комментариев очко в авторитет
            while ($row = $sql->fetch_assoc()) {
                switch ($row['Bereich']) {
                    case 'articles':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id, Titel_{$lc} AS Name FROM " . PREFIX . "_artikel WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Gaming_articles'];
                        $row['sec_link'] = 'index.php?p=articles&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=articles&amp;area=' . AREA . '&amp;action=displayarticle&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'news':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id, Titel{$lc} AS Name FROM " . PREFIX . "_news WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Newsarchive'];
                        $row['sec_link'] = 'index.php?p=newsarchive&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=news&amp;area=' . AREA . '&amp;newsid=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'galerie':
                        $item = $this->_db->cache_fetch_assoc("SELECT b.Id, b.Galerie_Id, b.Name_{$lc} AS Name, g.Kategorie FROM " . PREFIX . "_galerie_bilder AS b, " . PREFIX . "_galerie AS g WHERE b.Galerie_Id = g.Id AND b.Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        if (empty($item['Name'])) {
                            $item['Name'] = $this->_lang['GlobalNoName'];
                        }
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Gallery_Name'];
                        $row['sec_link'] = 'index.php?p=gallery&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=gallery&amp;action=showimage&amp;id=' . $row['Objekt_Id'] . '&amp;galid=' . $item['Galerie_Id'] . '&amp;ascdesc=desc&amp;categ=' . $item['Kategorie'] . '&amp;area=' . AREA . '#comments';
                        break;

                    case 'links':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id, Kategorie, Name_{$lc} AS Name FROM " . PREFIX . "_links WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Links'];
                        $row['sec_link'] = 'index.php?p=links&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=links&amp;action=showdetails&amp;area=' . AREA . '&amp;categ=' . $item['Kategorie'] . '&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'downloads':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id,Kategorie,Name_{$lc} AS Name FROM " . PREFIX . "_downloads WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Downloads'];
                        $row['sec_link'] = 'index.php?p=downloads&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=downloads&amp;action=showdetails&amp;area=' . AREA . '&amp;categ=' . $item['Kategorie'] . '&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'products':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id,Name{$lc} AS Name FROM " . PREFIX . "_produkte WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Products'];
                        $row['sec_link'] = 'index.php?p=products&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=products&amp;area=' . AREA . '&amp;action=showproduct&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'poll':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id, Titel_{$lc} AS Name FROM " . PREFIX . "_umfrage WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Poll_Name'];
                        $row['sec_link'] = 'index.php?p=poll&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=poll&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '&amp;area=' . AREA . '#comments';
                        break;

                    case 'cheats':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id,Plattform,Name_{$lc} AS Name FROM " . PREFIX . "_cheats WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = $this->_lang['Gaming_cheats'];
                        $row['sec_link'] = 'index.php?p=cheats&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=cheats&amp;action=showcheat&amp;area=' . AREA . '&amp;plattform=' . $item['Plattform'] . '&amp;id=' . $row['Objekt_Id'] . '&amp;name=' . translit($item['Name']) . '#comments';
                        break;

                    case 'content':
                        $item = $this->_db->cache_fetch_assoc("SELECT Id, Titel{$lc} AS Name FROM " . PREFIX . "_content WHERE Id = '" . $row['Objekt_Id'] . "' LIMIT 1");
                        $row['sec_title'] = $item['Name'];
                        $row['sec_name'] = '';
                        $row['sec_link'] = '';
                        $row['link'] = 'index.php?p=content&amp;id=' . $item['Id'] . '&amp;name=' . translit($item['Name']) . '&amp;area=' . AREA . '#comments';
                        break;

                    case 'guestbook':
                        $row['sec_title'] = $this->_text->substr($this->__object('Post')->clean($row['Eintrag']), 0, 50);
                        $row['sec_name'] = $this->_lang['Guestbook_t'];
                        $row['sec_link'] = 'index.php?p=guestbook&amp;area=' . AREA;
                        $row['link'] = 'index.php?p=guestbook&amp;area=' . AREA;
                        break;

                    default:
                        unset($row);
                        break;
                }
                $row['Eintrag'] = $this->_text->substr($this->__object('Post')->clean($row['Eintrag']), 0, 150);
                $row['date'] = $row['Datum'];
                $posts[] = $row;
            }
            $sql->close();
        }
        function cmp($a, $b) {
            return strcmp($b['date'], $a['date']);
        }

        usort($posts, 'cmp');

        if ($limit > count($posts)) {
            $limit = count($posts);
            $this->_view->assign('posts', $posts);
        } else {
            for ($i = 0; $i <= $limit - 1; $i++) {
                $last_posts[] = $posts[$i];
            }
            $this->_view->assign('posts', $last_posts);
        }
        return $this->_view->fetch(THEME . '/user/activity.tpl');
    }

    protected function user_visits($id) {
        if (!empty($id) && $_SESSION['loggedin'] == 1 && $id != $this->UserId) {
            $id = intval($id);
            $row = $this->_db->cache_fetch_assoc("SELECT Besucher FROM " . PREFIX . "_user_values WHERE BenutzerId = '" . $id . "' LIMIT 1");
            if (!is_array($row)) {
                $this->_db->insert_query('user_values', array('BenutzerId' => $id));
            }
            if (Tool::userSettings('Unsichtbar') == '0') {
                $visits = array_reverse(explode(';', $row['Besucher']));
                $limit = '40';
                for ($i = 1; $i <= $limit - 2; $i++) {
                    if (isset($visits[$i]) && $visits[$i] != $this->UserId) {
                        $visits_new[] = $visits[$i];
                    }
                }
                if (isset($visits_new)) {
                    $visits_new = array_reverse($visits_new);
                }
                $visits_new[] = $this->UserId . ';';
                $visits_new = implode(';', $visits_new);
                $this->_db->query("UPDATE " . PREFIX . "_user_values SET Besucher = '" . $visits_new . "' WHERE BenutzerId = '" . $id . "'");
            }
        }
    }

    protected function user_visits_profile($id) {
        if (empty($id)) {
            return '';
        }
        $items = array();
        $row = $this->_db->cache_fetch_assoc("SELECT Besucher FROM " . PREFIX . "_user_values WHERE BenutzerId = '" . intval($id) . "' LIMIT 1");
        if (!is_array($row)) {
            return '';
        }
        $visits = array_reverse(explode(';', $row['Besucher']));
        $limit = $this->users['LimitVisits'];
        for ($i = 0; $i <= $limit; $i++) {
            if (!empty($visits[$i])) {
                $item['Id'] = $visits[$i];
                $item['Name'] = Tool::userName($visits[$i]);
                $item['Avatar'] = $this->__object('Avatar')->get($visits[$i], $this->users['AvatarFriends']);
                $items[] = $item;
            }
        }

        $tpl_array = array(
            'items'         => $items,
            'avatar_line'   => $this->users['LimitFriendsStr'],
            'avatar_visits' => $this->users['AvatarFriends']);
        $this->_view->assign($tpl_array);
        if (!empty($items)) {
            return $this->_view->fetch(THEME . '/user/visits.tpl');
        }
    }

    protected function user_friends($id) {
        if (empty($id)) {
            return '';
        }
        $id = intval($id);
        $input = $friends = array();

        $query = "SELECT * FROM " . PREFIX . "_user_friends WHERE (BenutzerId = '" . $id . "' OR FreundId = '" . $id . "') AND Aktiv = '1' ; ";
        $query .= "SELECT FreundId FROM " . PREFIX . "_user_friends WHERE BenutzerId = '" . $id . "' AND Aktiv = '1' ; ";
        $query .= "SELECT BenutzerId FROM " . PREFIX . "_user_friends WHERE FreundId = '" . $id . "' AND Aktiv = '1'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $num = $result->num_rows();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_assoc()) {
                    $input[] = $row['FreundId'];
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_assoc()) {
                    $input[] = $row['BenutzerId'];
                }
                $result->close();
            }
        }

        $this->setAutoritet($num, 10); // за 10 друзей очко в авторитет
        $limit = (Arr::getRequest('friends') == 'all') ? $num : $this->users['LimitFriends'];

        if ($num < $limit) {
            $limit = $num;
        }
        if ($limit == 1) {
            $f['FreundId'] = $input[0];
            $f['Avatar'] = $this->__object('Avatar')->get($f['FreundId'], $this->users['AvatarFriends']);
            $f['Freundname'] = Tool::userName($f['FreundId']);
            if (!empty($f['Freundname'])) {
                $friends[] = $f;
            } else {
                $num = $limit = 0;
                $this->_db->query("DELETE FROM " . PREFIX . "_user_friends WHERE BenutzerId = '" . $f['FreundId'] . "' OR FreundId = '" . $f['FreundId'] . "'");
            }
        } else {
            $rand_keys = ($limit != 0) ? array_rand($input, $limit) : '';
            for ($i = 0, $x = -1, $max = $limit + $x; $i <= $max; $i++) {
                $f['FreundId'] = $input[$rand_keys[$i]];
                $f['Avatar'] = $this->__object('Avatar')->get($f['FreundId'], $this->users['AvatarFriends']);
                $f['Freundname'] = Tool::userName($f['FreundId']);
                if (!empty($f['Freundname'])) {
                    $friends[] = $f;
                } else {
                    $x = $x + 2;
                    $num--;
                    $limit--;
                    $this->_db->query("DELETE FROM " . PREFIX . "_user_friends WHERE (BenutzerId = '" . $f['FreundId'] . "' OR FreundId = '" . $f['FreundId'] . "')");
                }
            }
        }

        if (!in_array($this->UserId, $input)) {
            $row = $this->_db->cache_fetch_assoc("SELECT Id FROM " . PREFIX . "_user_friends WHERE (BenutzerId = '" . $id . "' AND FreundId ='" . $this->UserId . "') OR (BenutzerId = '" . $this->UserId . "' AND FreundId ='" . $id . "') LIMIT 1");
            $this->_view->assign('isf', (is_array($row)) ? '0' : '1');
        }
        $newfriends = array();
        $newsql = $this->_db->query("SELECT * FROM " . PREFIX . "_user_friends WHERE FreundId = '" . $id . "' AND Aktiv = '0'");
        while ($newrow = $newsql->fetch_assoc()) {
            $newrow['Freundname'] = Tool::userName($newrow['BenutzerId']);
            $newrow['Avatar'] = $this->__object('Avatar')->get($newrow['BenutzerId'], $this->users['AvatarFriends']);
            $newfriends[] = $newrow;
        }
        $newsql->close();

        $tpl_array = array(
            'newfriends' => $newfriends,
            'friends'    => $friends,
            'num'        => $limit,
            'num_all'    => $num,
            'NLine'      => $this->users['LimitFriendsStr'],
            'avatar'     => $this->users['AvatarFriends']);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/user/friends.tpl');
    }

    public function friends($id) {
        $id = intval($id);
        $this->user_set($id);
        switch ($_REQUEST['do']) {
            case 'add':
                $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_user_friends WHERE (BenutzerId = '" . $id . "' AND FreundId ='" . $this->UserId . "') OR (BenutzerId = '" . $this->UserId . "' AND FreundId ='" . $id . "') LIMIT 1");
                if ($id == $this->UserId || is_array($row) || $this->UserId == 0) {
                    $this->__object('Core')->message('Profile_Friends', 'Profile_Friends_AddFalse', BASE_URL . '/index.php?p=user&id=' . $id . '&area=' . AREA);
                }

                $insert_array = array(
                    'BenutzerId' => $this->UserId,
                    'FreundId'   => $id,
                    'Aktiv'      => 0);
                $this->_db->insert_query('user_friends', $insert_array);

                $user = $this->_db->cache_fetch_assoc("SELECT Benutzername, Email, PnEmail FROM " . PREFIX . "_benutzer WHERE Id = '" . $id . "' LIMIT 1");

                if ($user['PnEmail'] == 1) {
                    $mail_array = array(
                        '__USER__'  => $user['Benutzername'],
                        '__AUTOR__' => Tool::fullName(),
                        '__LINK__'  => BASE_URL . '/index.php?p=user&id=' . $this->UserId,
                        '__LINK2__' => BASE_URL . '/index.php?p=user&id=' . $id);
                    $body = $this->_text->replace($this->_lang['Friends_Emailbody'], $mail_array);
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $user['Email'],
                        'to_name'   => $user['Benutzername'],
                        'text'      => $body,
                        'subject'   => $this->_lang['Friends_Email'],
                        'fromemail' => $this->settings['Mail_Absender'],
                        'from'      => $this->settings['Mail_Name'],
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => '',
                        'prio'      => 3));
                }
                $this->__object('Core')->message('Profile_Friends', 'Profile_Friends_AddOk', BASE_URL . '/index.php?p=user&id=' . $id . '&area=' . AREA);
                break;

            case 'del':
                $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_user_friends WHERE (BenutzerId = '" . $id . "' AND FreundId ='" . $this->UserId . "') OR (BenutzerId = '" . $this->UserId . "' AND FreundId ='" . $id . "') LIMIT 1");
                if (!is_array($row)) {
                    $this->__object('Core')->message('Profile_Friends', 'NoFriends', BASE_URL . '/index.php?p=user&id=' . $this->UserId . '&area=' . AREA);
                }
                $this->_db->query("DELETE FROM " . PREFIX . "_user_friends WHERE Id = '" . $row['Id'] . "'");
                $this->__object('Core')->message('Profile_Friends', 'Profile_Friends_DelOk', BASE_URL . '/index.php?p=user&id=' . $this->UserId . '&area=' . AREA);
                break;

            case 'confirm':
                $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_user_friends WHERE FreundId = '" . $this->UserId . "' AND Id ='" . $id . "' LIMIT 1");
                if (!is_array($row)) {
                    $this->__object('Core')->message('Profile_Friends', 'Profile_Friends_ConfirmFalse', BASE_URL . '/index.php?p=user&id=' . $this->UserId . '&area=' . AREA);
                }
                $this->_db->query("UPDATE " . PREFIX . "_user_friends SET Aktiv=1 WHERE Id = '" . $id . "'");
                $this->__object('Core')->message('Profile_Friends', 'Profile_Friends_ConfirmOk', BASE_URL . '/index.php?p=user&id=' . $this->UserId . '&area=' . AREA);
                break;
        }
    }

    protected function user_gallery_profile($id) {
        if ($this->UserId != $id) {
            return '';
        }
        $items = array();
        $gal_count = $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . intval($id) . "'");

        $this->setAutoritet($gal_count, 5); // за 5 галлерей очко в авторитет
        $tpl_array = array(
            'gallery'   => $items,
            'gal_con'   => $this->users['LimitAlbom'],
            'gal_count' => $gal_count);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/user/gallery_profile.tpl');
    }

    protected function user_gallery($id) {
        $items = array();
        $num = 0;
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . intval($id) . "'");
        $gal = $this->_db->found_rows();
        while ($row = $sql->fetch_assoc()) {
            $sql_g = $this->_db->query("SELECT * FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $row['Id'] . "'");
            $input = array();
            while ($g = $sql_g->fetch_assoc()) {
                $input[$g['Id']] = $g['Datei'];
                $num = $num + 1;
            }
            if (!empty($input)) {
                $row['Bild'] = array_rand($input);
                $row['Datei'] = $input[$row['Bild']];
                $row['Image'] = Tool::thumb('ugallery', $row['Datei'], 72);
                $items[] = $row;
            }
        }
        $sql->close();

        $this->setAutoritet($num, 25); // за 25 картинок очко в авторитет

        $tpl_array = array(
            'gallery' => $items,
            'num_img' => $num,
            'gal'     => $gal,
            'width'   => ($this->users['WidthFotos'] + 80),
            'height'  => ($this->users['WidthFotos'] + 50),
            'NLines'  => $this->users['LimitFotosStr']);
        $this->_view->assign($tpl_array);
        return $this->_view->fetch(THEME . '/user/gallery.tpl');
    }

    public function gallery($id) {
        $id = intval($id);
        switch ($_REQUEST['do']) {
            case 'images':
                SX::setDefine('OUT_TPL', 'popup.tpl');
                $img = (isset($_REQUEST['image'])) ? "AND Id ='" . intval(Arr::getRequest('image')) . "'" : '';
                $query = "SELECT * FROM " . PREFIX . "_user_gallery WHERE Id = '" . $id . "' ; ";
                $query .= "SELECT * FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $id . "' {$img} LIMIT 1";
                if ($this->_db->multi_query($query)) {
                    if (($result = $this->_db->store_result())) {
                        $row = $result->fetch_assoc();
                        $result->close();
                    }
                    if (($result = $this->_db->store_next_result())) {
                        $item = $result->fetch_object();
                        $item->Link = Tool::thumb('ugallery', $item->Datei, $this->users['WidthFotos']);
                        $this->_view->assign('item', $item);
                        $result->close();
                    }
                }
                $this->_view->assign('row', $row);

                $seo_array = array(
                    'headernav' => $row['Name'],
                    'pagetitle' => sanitize($row['Name'] . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'] . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
                    'content'   => $this->_view->fetch(THEME . '/user/images.tpl'));
                $this->_view->finish($seo_array);
                break;

            case 'ajax':
                SX::setDefine('AJAX_OUTPUT', 1);
                $first = max(0, intval(Arr::getGet('first')) - 1);
                $last = max($first + 1, intval(Arr::getGet('last')) - 1);
                $length = $last - $first + 1;
                $more = array();
                $sql_more = $this->_db->query("SELECT * FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $id . "' ORDER BY Id");
                while ($row_more = $sql_more->fetch_object()) {
                    $imgage_link = 'javascript:getIMG(' . $row_more->Id . ');';
                    $images[] = "&lt;a href='$imgage_link'&gt; &lt;img src='" . Tool::thumb('ugallery', $row_more->Datei, 80) . "' alt='' border='0' /&gt;&lt;/a&gt;";
                    $row_more->ImageText = (!empty($row_more->Name)) ? '<strong>' . sanitize($row_more->Name) . '</strong>' : '';
                    $more[] = $row_more;
                }

                $total = count($images);
                $selected = array_slice($images, $first, $length);
                header('Content-Type: text/xml');
                SX::output('<data>');
                SX::output('<total>' . $total . '</total>');
                foreach ($selected as $img) {
                    SX::output('<image>' . $img . '</image>');
                }
                SX::output('</data>');
                break;

            case 'ajax_img':
                SX::setDefine('AJAX_OUTPUT', 1);
                $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_user_images WHERE Id = '" . $id . "' LIMIT 1");
                if (isset($_REQUEST['img'])) {
                    $src = Tool::thumb('ugallery', $row['Datei'], $this->users['WidthFotos']);
                    $src = str_replace('&amp;', '&', $src);
                    header('Content-type: text/javascript');
                    SX::output($src);
                } else {
                    header('Content-type: text/javascript');
                    SX::output($row['Name']);
                }
                break;
        }
    }

    public function launch() {
        if ($_SESSION['loggedin'] != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t');
        } else {
            switch ($_REQUEST['do']) {
                case 'new':
                    $num = 0;
                    $error = array();
                    $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "'");
                    $gal = $this->_db->found_rows();
                    while ($row = $sql->fetch_assoc()) {
                        $num += $this->_db->cache_num_rows("SELECT * FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $row['Id'] . "'");
                    }
                    $sql->close();

                    $gal_con = $this->users['LimitAlbom'];
                    $num_con = $this->users['LimitFotos'];
                    if ($gal_con != 0 && ($gal_con == $gal || $gal_con < $gal)) {
                        $error[] = $this->_lang['MaxAlbums'];
                    }
                    $loop = $num_con == 0 ? 10 : $num_con - $num;
                    if ($loop < 1) {
                        $error[] = $this->_lang['MaxImages'];
                    }

                    if (Arr::getPost('save') == 1) {
                        if (isset($_POST['feld_file'])) {
                            $stime = time();
                            $title = (!empty($_POST['title'])) ? sanitize($_POST['title']) : $this->_lang['GlobalNoName'];

                            $insert_array = array(
                                'BenutzerId' => $this->UserId,
                                'Datum'      => $stime,
                                'Name'       => Tool::cleanTags($title, array('codewidget')));
                            $this->_db->insert_query('user_gallery', $insert_array);

                            $row = $this->_db->cache_fetch_assoc("SELECT Id FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "' ORDER BY Id DESC LIMIT 1");
                            foreach ($_POST['feld_file'] as $id => $Feld) {
                                if (!empty($_POST['feld_file'][$id])) {
                                    $insert_array = array(
                                        'GalerieId' => $row['Id'],
                                        'Datum'     => $stime,
                                        'Name'      => sanitize(Tool::cleanTags($_POST['feld_title'][$id], array('codewidget'))),
                                        'Datei'     => $_POST['feld_file'][$id]);
                                    $this->_db->insert_query('user_images', $insert_array);
                                }
                            }
                            SX::output('<script type="text/javascript"> self.close(); window.opener.location.reload();</script>', true);
                        } else {
                            foreach ($_POST['feld_file'] as $id => $Feld) {
                                $title[$id] = $_POST['feld_title'][$id];
                                $file[$id] = $_POST['feld_file'][$id];
                                if (!empty($_POST['feld_file'][$id])) {
                                    $pic[$id] = '<img src=' . BASE_URL . '/lib/image.php?action=ugallery&amp;width=71&amp;image=' . $_POST['feld_file'][$id] . ' />';
                                }
                            }

                            $tpl_array = array(
                                'title' => $title,
                                'file'  => $file,
                                'pic'   => $pic,
                                'next'  => (count($pic) + 1));
                            $this->_view->assign($tpl_array);
                        }
                    }
                    SX::setDefine('OUT_TPL', 'popup.tpl');
                    $this->_view->assign(array('error' => $error, 'loop' => $loop));

                    $seo_array = array(
                        'headernav' => $this->_lang['NewAlbum'],
                        'pagetitle' => sanitize($this->_lang['NewAlbum'] . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'] . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
                        'content'   => $this->_view->fetch(THEME . '/user/gallery_new.tpl'));
                    $this->_view->finish($seo_array);
                    break;

                case 'edit':
                    if (!empty($_REQUEST['id'])) {
                        $num = 0;
                        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "'");
                        while ($row = $sql->fetch_assoc()) {
                            $num += $this->_db->cache_num_rows("SELECT Id FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $row['Id'] . "'");
                            if ($row['Id'] == $_REQUEST['id']) {
                                $ok = 1;
                                $item = $row;
                            }
                        }

                        if (!isset($ok)) {
                            exit;
                        }
                        $num_con = $this->users['LimitFotos'];
                        if ($num_con == '0') {
                            $loop = 10;
                        } else {
                            $loop = $num_con > $num ? $num_con - $num : 0;
                        }
                        if (Arr::getPost('save') == 1) {
                            if (isset($_POST['feld_file']) && !empty($_POST['title'])) {
                                $this->_db->query("UPDATE " . PREFIX . "_user_gallery SET Name = '" . $this->_db->escape(sanitize(Tool::cleanTags($_POST['title'], array('codewidget')))) . "' WHERE Id = '" . intval(Arr::getRequest('id')) . "'");
                                foreach ($_POST['feld_file'] as $id => $Feld) {
                                    if (!empty($_POST['feld_file'][$id])) {
                                        $insert_array = array(
                                            'GalerieId' => intval(Arr::getRequest('id')),
                                            'Datum'     => time(),
                                            'Name'      => sanitize(Tool::cleanTags($_POST['feld_title'][$id], array('codewidget'))),
                                            'Datei'     => $_POST['feld_file'][$id]);
                                        $this->_db->insert_query('user_images', $insert_array);
                                    }
                                }
                            } else {
                                foreach ($_POST['feld_file'] as $id => $Feld) {
                                    $title[$id] = $_POST['feld_title'][$id];
                                    $file[$id] = $_POST['feld_file'][$id];
                                    if (!empty($_POST['feld_file'][$id])) {
                                        $pic[$id] = '<img src=' . BASE_URL . '/lib/image.php?action=ugallery&amp;width=71&amp;image=' . $_POST['feld_file'][$id] . ' />';
                                    }
                                }

                                $tpl_array = array(
                                    'title' => $title,
                                    'file'  => $file,
                                    'pic'   => $pic);
                                $this->_view->assign($tpl_array);
                            }
                        }

                        $this->_view->assign(array('item' => $item, 'loop' => $loop));
                        $headernav = $item['Name'];
                        $pagetitle = $item['Name'] . $this->_lang['PageSep'] . $this->_lang['EditAlbum'] . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'] . $this->_lang['PageSep'] . $this->_lang['MyAccount'];
                    } else {
                        $headernav = $this->_lang['EditAlbum'];
                        $pagetitle = $this->_lang['EditAlbum'] . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'] . $this->_lang['PageSep'] . $this->_lang['MyAccount'];
                    }

                    $albums = $this->_db->fetch_assoc_all("SELECT * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "'");

                    SX::setDefine('OUT_TPL', 'popup.tpl');
                    $this->_view->assign('albums', $albums);

                    $seo_array = array(
                        'headernav' => $headernav,
                        'pagetitle' => sanitize($pagetitle),
                        'content'   => $this->_view->fetch(THEME . '/user/gallery_edit.tpl'));
                    $this->_view->finish($seo_array);
                    break;

                case 'del':
                    if (empty($_REQUEST['id'])) {

                        $albums = $this->_db->fetch_assoc_all("SELECT * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "'");

                        SX::setDefine('OUT_TPL', 'popup.tpl');
                        $this->_view->assign('albums', $albums);

                        $seo_array = array(
                            'headernav' => $this->_lang['DelAlbum'],
                            'pagetitle' => $this->_lang['DelAlbum'] . $this->_lang['PageSep'] . $this->_lang['Gallery_Name'] . $this->_lang['PageSep'] . $this->_lang['MyAccount'],
                            'content'   => $this->_view->fetch(THEME . '/user/gallery_del.tpl'));
                        $this->_view->finish($seo_array);
                    } else {
                        $query = "SELECT Id FROM " . PREFIX . "_user_gallery WHERE Id = '" . intval($_REQUEST['id']) . "' AND BenutzerId = '" . $this->UserId . "' ; ";
                        $query .= "SELECT * FROM " . PREFIX . "_user_gallery WHERE BenutzerId = '" . $this->UserId . "'";
                        if ($this->_db->multi_query($query)) {
                            if (($result = $this->_db->store_result())) {
                                $row = $result->fetch_assoc();
                                $result->close();
                            }
                            if (($result = $this->_db->store_next_result())) {
                                $gal = $result->num_rows();
                                $result->close();
                            }
                        }

                        if (isset($row) && is_array($row)) {
                            $sql = $this->_db->query("SELECT Datei FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $row['Id'] . "'");
                            while ($g = $sql->fetch_assoc()) {
                                File::delete(UPLOADS_DIR . '/user/gallery/' . $g['Datei']);
                            }
                            $sql->close();

                            $this->_db->query("DELETE FROM " . PREFIX . "_user_gallery WHERE Id = '" . $row['Id'] . "'");
                            $this->_db->query("DELETE FROM " . PREFIX . "_user_images WHERE GalerieId = '" . $row['Id'] . "'");
                        }

                        if ($gal == 0) {
                            SX::output('<script type="text/javascript"> self.close(); window.opener.location.reload();</script>', true);
                        } else {
                            $this->__object('Redir')->seoRedirect('index.php?p=user&action=gal&do=del&area=' . AREA);
                        }
                    }
                    break;

                case 'ajax':
                    $row = $this->_db->cache_fetch_assoc("SELECT Id, GalerieId, Datei FROM " . PREFIX . "_user_images WHERE Id = '" . intval(Arr::getRequest('id')) . "' LIMIT 1");
                    $check = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_user_gallery WHERE Id = '" . $row['GalerieId'] . "' AND BenutzerId = '" . $this->UserId . "' LIMIT 1");
                    if (is_array($check)) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_user_images WHERE Id = '" . $row['Id'] . "'");
                        File::delete(UPLOADS_DIR . '/user/gallery/' . $row['Datei']);
                    }
                    exit;
                    break;
            }
        }
    }

    public function upload() {
        if ($_SESSION['user_group'] != '2' && $this->users['UserGallery'] == '1') {
            $options = array(
                'type'   => 'image',
                'result' => 'ajax',
                'upload' => '/uploads/user/gallery/',
                'input'  => 'fileToUpload_' . Arr::getRequest('divid'),
                'resize' => $this->users['WidthFotos'],
            );
            SX::object('Upload')->load($options);
        }
    }

    protected function userVideos($id) {
        $videos = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_benutzer_videos WHERE Benutzer='" . intval($id) . "' ORDER BY Position ASC LIMIT 4");
        while ($row = $sql->fetch_object()) {
            switch ($row->VideoSource) {
                case 'youtube':
                    $row->VideoData = '<object width="200" height="173"><param name="movie" value="http://www.youtube.com/v/' . $row->Video . '"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="opaque"><embed src="http://www.youtube.com/v/' . $row->Video . '" type="application/x-shockwave-flash" allowfullscreen="true" width="200" height="173" wmode="opaque"></embed></object>';
                    break;
            }
            $videos[] = $row;
        }
        $sql->close();

        $this->setAutoritet(count($videos), 1); // за 1 видео очко в авторитет
        return $videos;
    }

    /* Метод добавление сообщения в гостевую пользователя */
    protected function addGuestbook($id, $row) {
        if (Arr::getPost('Eintrag') == 1) {
            if ($row['Gaestebuch_KeineGaeste'] == 1 && $_SESSION['user_group'] == 2) {
                $this->__object('Core')->message('Profile_GuestbookUser', 'Profile_Guestbook_NoGuests', BASE_URL . '/index.php?p=user&amp;id=' . $id);
            }

            $error = array();
            if (empty($_POST['Autor'])) {
                $error[] = $this->_lang['Comment_NoAuthor'];
            }
            if (empty($_POST['Titel'])) {
                $error[] = $this->_lang['Profile_Guestbook_NoTitle'];
            }
            if (empty($_POST['text'])) {
                $error[] = $this->_lang['Profile_Guestbook_NoComment'];
            }
            if ($this->__object('Captcha')->check($error)) {
                $insert_array = array(
                    'BenutzerId'     => $id,
                    'Titel'          => Tool::cleanAllow($_POST['Titel'], ' '),
                    'Eintrag'        => $this->_text->substr(sanitize(Tool::cleanTags($_POST['text'], array('codewidget'))), 0, $row['Gaestebuch_Zeichen']),
                    'Datum'          => time(),
                    'Autor'          => Tool::cleanAllow($_POST['Autor'], ' '),
                    'Autor_Web'      => Tool::cleanUrl($_POST['Webseite']),
                    'Autor_Herkunft' => Tool::cleanAllow($_POST['Herkunft'], ' '),
                    'Autor_Ip'       => IP_USER,
                    'Aktiv'          => $this->getModerateGB($id, $row));
                $this->_db->insert_query('benutzer_gaestebuch', $insert_array);

                $msg = $this->getModerateGB($id, $row) != 1 ? 'Profile_GuestbookUser_EntryOk_Moderated' : 'Profile_GuestbookUser_EntryOk';
                $this->__object('Core')->message('Profile_GuestbookUser', $msg, BASE_URL . '/index.php?p=user&amp;id=' . $_POST['id'] . '&amp;area=' . AREA);
            }
        }
        $this->__object('Captcha')->start(); // »нициализаци€ каптчи
    }

    /* Метод проверки необходимости модерации собщения в гостевой пользователя */
    public function getModerateGB($id, $row) {
        if ($this->UserId == $id) {
            return 1;
        }
        return $row['Gaestebuch_Moderiert'] == 1 ? 0 : 1;
    }

    public function load($id) {
        if (empty($id)) {
            $this->__object('Redir')->seoRedirect('index.php?p=index&area=' . AREA);
        }
        $id = intval($id);
        $query = "SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . $id . "' AND Aktiv=1 ; ";
        $query .= "SELECT COUNT(id) AS Beitraege FROM " . PREFIX . "_f_post WHERE uid = '" . $id . "'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $row = $result->fetch_assoc();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $beitraege_obj = $result->fetch_object();
                $result->close();
            }
        }
        if (!is_array($row)) {
            $this->__object('Redir')->seoRedirect('index.php?p=index&area=' . AREA);
        }
        if ($row['Beitraege'] != $beitraege_obj->Beitraege) {
            $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Beitraege = '" . $beitraege_obj->Beitraege . "' WHERE Id = '" . $id . "'");
        }
        if ($row['Profil_Alle'] != 1 && $_SESSION['user_group'] == 2) {
            $this->__object('Core')->message('Forums_UserProfile', 'Profile_PublicAllNo', BASE_URL . '/index.php?p=index&amp;area=' . AREA);
        }
        if ($this->UserId != $id) {
            $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Profil_Hits=Profil_Hits+1 WHERE Id = '" . $id . "'");
        }
        $this->setAutoritet($beitraege_obj->Beitraege, 100); // за 100 сообщений на форуме очко в авторитет

        $theme = SX::get('options.theme');
        $rank_obj = $this->_db->cache_fetch_object("SELECT title FROM " . PREFIX . "_f_rank WHERE count <= '" . ($beitraege_obj->Beitraege > 0 ? $beitraege_obj->Beitraege : 1) . "' ORDER BY count DESC LIMIT 1");
        $row['TeamName'] = $this->_lang['WebTeam'] . $this->settings['Seitenname'];
        $row['Beitraege'] = $beitraege_obj->Beitraege;
        $row['Rang'] = (is_object($rank_obj)) ? $rank_obj->title : '';
        $row['Avatar'] = $this->__object('Avatar')->load($row['Gravatar'], $row['Email'], $row['Gruppe'], $row['Avatar'], $row['Avatar_Default']);
        $row['Icq_User'] = (!empty($row['icq'])) ? '<a class="user_pop" href="index.php?p=misc&amp;do=icq&amp;uid=' . $row['Id'] . '"><img border="0" src="theme/' . $theme . '/images/forums/icq.png" alt="" /></a>' : '';
        $row['Pn_User'] = ($row['Pnempfang'] == 1 && $_SESSION['user_group'] != 2) ? '<a href="index.php?p=pn&amp;action=new&amp;to=' . base64_encode($row['Benutzername']) . '"><img border="0" src="theme/' . $theme . '/images/forums/pn.png" alt="" /></a>' : '';
        $row['Email_User'] = ($row['Emailempfang'] == 1 && $_SESSION['user_group'] != 2) ? '<a class="user_pop" href="index.php?p=misc&amp;do=email&amp;uid=' . $row['Id'] . '"><img border="0" src="theme/' . $theme . '/images/forums/mail.png" alt="" /></a>' : '';
        $row['Skype_User'] = (!empty($row['skype'])) ? '<a class="user_pop" href="index.php?p=misc&amp;do=skype&amp;uid=' . $row['Id'] . '"><img border="0" src="theme/' . $theme . '/images/forums/skype.png" alt="Ђвонок через Чкайп" /></a>' : '';
        $row['Webseite'] = !empty($row['Webseite']) ? Tool::checkSheme($row['Webseite']) : '';

        if (!get_active('user_guestbook')) {
            $row['Gaestebuch'] = 0;
        }
        if ($row['Gaestebuch'] == 1) {
            if (!empty($_REQUEST['do']) && ($this->UserId == $id)) {
                switch ($_REQUEST['do']) {
                    case 'set_active':
                        $this->_db->query("UPDATE " . PREFIX . "_benutzer_gaestebuch SET Aktiv = 1 WHERE BenutzerId = '" . $id . "' AND Id = '" . intval(Arr::getGet('gb_entry')) . "'");
                        $this->__object('Redir')->seoRedirect($this->__object('Redir')->referer(true) . '#eintraege');
                        break;

                    case 'delete':
                        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_gaestebuch WHERE BenutzerId = '" . $id . "' AND Id = '" . intval(Arr::getGet('gb_entry')) . "'");
                        $this->__object('Redir')->seoRedirect($this->__object('Redir')->referer(true) . '#eintraege');
                        break;

                    case 'delete_all':
                        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_gaestebuch WHERE BenutzerId = '" . $id . "'");
                        $this->__object('Redir')->seoRedirect('index.php?p=user&id=' . $id);
                        break;

                    case 'edit':
                        $array = array(
                            'Eintrag'        => $this->_text->substr(sanitize(Tool::cleanTags($_POST['E_Eintrag'], array('codewidget'))), 0, 5000),
                            'Titel'          => Tool::cleanAllow($_POST['E_Titel'], ' '),
                            'Autor'          => Tool::cleanAllow($_POST['E_Autor'], ' '),
                            'Autor_Web'      => Tool::cleanUrl($_POST['E_Webseite']),
                            'Autor_Herkunft' => Tool::cleanAllow($_POST['E_Herkunft'], ' '),
                        );
                        $this->_db->update_query('benutzer_gaestebuch', $array, "BenutzerId = '" . $id . "' AND Id = '" . intval(Arr::getRequest('gb_entry')) . "'");
                        $this->__object('Redir')->seoRedirect('index.php?p=user&id=' . $id . '&page=' . $_REQUEST['page'] . '#' . $_REQUEST['gb_entry']);
                        break;
                }
            }

            $this->addGuestbook($id, $row);

            $db_aktiv = ($id == $this->UserId) ? '' : 'AND Aktiv = 1';
            $limit = 15;
            $a = Tool::getLimit($limit);
            $gb_sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_benutzer_gaestebuch WHERE BenutzerId = '" . $id . "' {$db_aktiv} ORDER BY Id ASC LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $eintrag = array();
            while ($gb = $gb_sql->fetch_assoc()) {
                $gb['Eintrag_Raw'] = $gb['Eintrag'];
                if ($row['Gaestebuch_bbcode'] == 1 && $this->settings['SysCode_Aktiv'] == 1) {
                    $gb['Eintrag'] = $this->__object('Post')->bbcode($gb['Eintrag'], 'user_guestbook', $row['Gaestebuch_imgcode']);
                } else {
                    $gb['Eintrag'] = sanitize($gb['Eintrag']);
                    $gb['Eintrag'] = nl2br($gb['Eintrag']);
                }

                if ($row['Gaestebuch_smilies'] == 1 && $this->settings['SysCode_Smilies'] == 1) {
                    $gb['Eintrag'] = $this->__object('Post')->smilies($gb['Eintrag']);
                }
                $gb['Eintrag'] = Tool::censored($gb['Eintrag']);
                $gb['Eintrag'] = $this->__object('Glossar')->get($gb['Eintrag']);
                $gb['Autor_Web'] = !empty($gb['Autor_Web']) ? Tool::checkSheme($gb['Autor_Web']) : '';
                $eintrag[] = $gb;
            }
            $gb_sql->close();

            $this->setAutoritet($num, 20); // за 20 сообщений в гостевой пользовател€ очко в авторитет

            if ($num > $limit) {
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" href=\"index.php?p=user&amp;id=" . $id . "&amp;area=" . AREA . "&amp;page={s}#eintraege\">{t}</a> "));
            }
            if ($row['Gaestebuch_KeineGaeste'] == 1 && $_SESSION['user_group'] == 2) {
                $this->_view->assign('KeineGaeste', 1);
            }
            $this->_view->assign('eintrag', $eintrag);
        }

        if ($row['Gaestebuch_smilies'] == 1 && $this->settings['SysCode_Smilies'] == 1) {
            $this->_view->assign(array('smilie' => 1, 'listemos' => $this->__object('Post')->listsmilies()));
        }

        if ($row['Gaestebuch_bbcode'] == 1 && $this->settings['SysCode_Aktiv'] == 1) {
            $tpl_array = array(
                'format'        => 1,
                'listfonts'     => $this->__object('Post')->font(),
                'sizedropdown'  => $this->__object('Post')->fontsize(),
                'colordropdown' => $this->__object('Post')->color());
            $this->_view->assign($tpl_array);
        }

        if ($row['Gaestebuch_imgcode'] != 1) {
            $this->_view->assign('NoImgCode', 1);
        }

        $this->_view->assign('user_thanks', $this->user_thanks($id));
        if ($this->users['UserActions'] == '1') {
            $this->_view->assign('user_activity', $this->user_activity($id));
        }
        if ($this->users['UserFriends'] == '1') {
            $this->_view->assign('user_friends', $this->user_friends($id));
        }
        if ($this->users['UserVisits'] == '1') {
            $this->user_visits($id);
            $this->_view->assign('user_visits', $this->user_visits_profile($id));
        }
        if ($this->users['UserGallery'] == '1') {
            $this->_view->assign(array('user_gallery' => $this->user_gallery($id), 'user_gallery_profile' => $this->user_gallery_profile($id)));
        }

        $this->_view->assign(array('user' => $row, 'user_videos' => $this->userVideos($row['Id'])));
        $this->autoritet($row);

        $seo_array = array(
            'headernav' => $this->_lang['MyAccount'],
            'pagetitle' => sanitize($row['Benutzername'] . Tool::numPage() . $this->_lang['PageSep'] . $this->_lang['MyAccount']),
            'content'   => $this->_view->fetch(THEME . '/user/user_profile.tpl'));
        $this->_view->finish($seo_array);
    }

    /* Метод добавления баллов за заполненные поля */
    protected function autoritet($row) {
        $array = array(
            'Vorname'           => 3,
            'Nachname'          => 3,
            'Strasse_Nr'        => 3,
            'Postleitzahl'      => 3,
            'Ort'               => 3,
            'Firma'             => 3,
            'Telefon'           => 2,
            'Telefax'           => 2,
            'Geburtstag'        => 3,
            'Profil_public'     => 2,
            'Profil_Alle'       => 2,
            'Geburtstag_public' => 2,
            'Ort_Public'        => 2,
            'msn'               => 2,
            'aim'               => 2,
            'icq'               => 2,
            'skype'             => 2,
            'Webseite'          => 2,
            'Signatur'          => 2,
            'Interessen'        => 2,
            'Avatar'            => 2,
            'Beruf'             => 2,
            'Hobbys'            => 2,
            'Essen'             => 2,
            'Musik'             => 2,
            'Films'             => 2,
            'Tele'              => 2,
            'Book'              => 2,
            'Game'              => 2,
            'Citat'             => 2,
            'Other'             => 2,
            'Status'            => 2,
            'Vkontakte'         => 2,
            'Odnoklassniki'     => 2,
            'Facebook'          => 2,
            'Twitter'           => 2,
            'Mymail'            => 2,
            'Google'            => 2);

        foreach ($array as $key => $val) {
            if (!empty($row[$key])) {
                $this->setAutoritet($val);
            }
        }
        $autoritet = round($this->autoritet);
        $autoritet_bar = $autoritet <= 100 ? $autoritet : 100;

        $tpl_array = array(
            'autoritet'     => $autoritet,
            'autoritet_bar' => $autoritet_bar);
        $this->_view->assign($tpl_array);
    }

    /* Метод суммирования баллов авторитетности */
    protected function setAutoritet($num, $ball = NULL) {
        $this->autoritet += is_null($ball) ? $num : $num / $ball;
    }

}
