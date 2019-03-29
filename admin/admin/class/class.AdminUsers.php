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

class AdminUsers extends Magic {

    protected $UserLimit = 15;

    public function settings() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'UserGallery'     => Arr::getPost('UserGallery'),
                'LimitAlbom'      => Arr::getPost('LimitAlbom'),
                'LimitFotos'      => Arr::getPost('LimitFotos'),
                'LimitFotosStr'   => Arr::getPost('LimitFotosStr'),
                'WidthFotos'      => Arr::getPost('WidthFotos'),
                'UserFriends'     => Arr::getPost('UserFriends'),
                'UserVisits'      => Arr::getPost('UserVisits'),
                'LimitVisits'     => Arr::getPost('LimitVisits'),
                'LimitFriends'    => Arr::getPost('LimitFriends'),
                'LimitFriendsStr' => Arr::getPost('LimitFriendsStr'),
                'AvatarFriends'   => Arr::getPost('AvatarFriends'),
                'UserActions'     => Arr::getPost('UserActions'),
                'LimitActions'    => Arr::getPost('LimitActions'),
                'ImageCompres'    => Arr::getPost('ImageCompres'),
                'AvatarWidth'     => Arr::getPost('AvatarWidth'),
                'AvatarCompres'   => Arr::getPost('AvatarCompres'),
            );
            SX::save('users', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки модуля пользователи', '0', $this->UserId);
            $this->__object('AdminCore')->script('save');
            SX::load('users');
        }
        $row = SX::get('users');
        $this->_view->assign('row', $row);
        $this->_view->assign('title', $this->_lang['SettingsModule']);
        $this->_view->content('/user/settings.tpl');
    }

    protected function delete($user, $details) {
        $this->_db->query("DELETE FROM " . PREFIX . "_benutzer WHERE Id='" . intval($user) . "' AND Id!='1'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил пользователя: ' . $details, '0', $_SESSION['benutzer_id']);
    }

    public function show($new = '') {
        if (!perm('users_overview')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($_POST['uaction'])) {
            if (!perm('users_edit')) {
                $this->__object('AdminCore')->noAccess();
            }
            switch ($_POST['uaction']) {
                case 'activate':
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Aktiv='1' " . $_SESSION['UserSearch'] . " AND Id!='1'");
                    break;

                case 'deactivate':
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Aktiv='0' " . $_SESSION['UserSearch'] . " AND Id!='1'");
                    break;

                case 'changegroup':
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Gruppe='" . $this->_db->escape(Arr::getPost('newgroup')) . "' " . $_SESSION['UserSearch'] . " AND Id!='1'");
                    break;

                case 'delete':
                    $this->_db->query("DELETE FROM " . PREFIX . "_benutzer " . $_SESSION['UserSearch'] . " AND Id!='1'");
                    break;

                case 'sendmessage':
                    if (!empty($_POST['message_subject']) && !empty($_POST['message_text'])) {
                        $sql = $this->_db->query("SELECT Email, Benutzername FROM " . PREFIX . "_benutzer " . $_SESSION['UserSearch']);
                        while ($res = $sql->fetch_object()) {
                            $Text = $this->_text->replace($_POST['message_text'], '__USER__', $res->Benutzername);
                            SX::setMail(array(
                                'globs'     => '1',
                                'to'        => $res->Email,
                                'to_name'   => $res->Benutzername,
                                'text'      => $Text,
                                'subject'   => $_POST['message_subject'],
                                'fromemail' => SX::get('system.Mail_Absender'),
                                'from'      => SX::get('system.Mail_Name'),
                                'type'      => 'text',
                                'attach'    => '',
                                'html'      => '',
                                'prio'      => 1));
                        }
                        $sql->close();
                    }
                    break;
            }
            $this->__object('AdminCore')->script('save');
            $this->_view->assign('action_done', 1);
        } else {
            unset($_SESSION['UserSearch']);
        }

        if (Arr::getPost('quicksave') == 1) {
            foreach ($_POST['user'] as $uid => $u) {
                $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Gruppe = '" . $this->_db->escape($_POST['Gruppe'][$uid]) . "' WHERE Id = '" . intval($_POST['user'][$uid]) . "'");
                if (isset($_POST['del'][$uid])) {
                    $this->delete($_POST['user'][$uid], $_POST['deldetails'][$uid]);
                }
            }
            $this->__object('AdminCore')->script('save');
        }
        $this->_view->assign('user', $this->load($new));
        if ($new == 'new') {
            $this->_view->assign('all_user', $this->count());
            $out = $this->_view->fetch(THEME . '/user/user_overview_small.tpl');
            return $out;
        } else {
            $this->_view->assign('title', $this->_lang['Global_User']);
            $this->_view->content('/user/user_overview.tpl');
        }
    }

    protected function count() {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS ResCount FROM " . PREFIX . "_benutzer");
        return $res->ResCount;
    }

    protected function langcode($code) {
        $res = $this->_db->cache_fetch_object("SELECT Code FROM " . PREFIX . "_laender WHERE Name='" . $this->_db->escape($code) . "' LIMIT 1");
        return $res->Code;
    }

    protected function country($code) {
        $res = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code='" . $this->_db->escape(strtoupper($code)) . "' LIMIT 1");
        return $res->Name;
    }

    protected function load($new = '') {
        $def_order = 'ORDER BY Id ASC';
        $def_order_n = $def_search = $def_search_n = $def_group = $def_group_n = $def_regtime = $def_regtime_n = '';
        $def_lastonline = $def_lastonline_n = $def_aktive = $def_aktive_n = $db_shop_orders_n = '';

        if (!empty($_REQUEST['name']) && $this->_text->strlen($_REQUEST['name']) >= 1) {
            $_REQUEST['name'] = $name = Tool::cleanAllow($_REQUEST['name'], '.@ ');
            $name = $this->_db->escape($name);
            $def_search = " AND (
            Benutzername LIKE '$name%' OR
            Vorname LIKE '$name%' OR
            Nachname LIKE '$name%' OR
            Email LIKE '%$name' OR
            Id = '$name')";
            $def_search_n = "&name={$name}";
        }

        $aktiv = Arr::getRequest('aktiv', '');
        if ($aktiv == '1') {
            $def_aktive = " AND (Aktiv = '1') ";
            $def_aktive_n = "&amp;aktiv=1";
        } else if ($aktiv == '0') {
            $def_aktive = " AND (Aktiv = '0') ";
            $def_aktive_n = "&amp;aktiv=0";
        }

        if (!empty($_REQUEST['group'])) {
            $def_group = "AND (Gruppe='" . intval($_REQUEST['group']) . "')";
            $def_group_n = "&group=" . intval($_REQUEST['group']);
        }

        if (!empty($_REQUEST['regfrom']) && !empty($_REQUEST['regtill'])) {
            $rfrom = $this->__object('AdminCore')->mktime($_REQUEST['regfrom'], 0, 0, 1);
            $rtill = $this->__object('AdminCore')->mktime($_REQUEST['regtill'], 23, 59, 59);

            if ($rfrom < $rtill) {
                $def_regtime = " AND (Regdatum BETWEEN '" . intval($rfrom) . "' AND '" . intval($rtill) . "') ";
                $def_regtime_n = "&regfrom=" . Arr::getRequest('regfrom') . "&regtill=" . Arr::getRequest('regtill');
            }
        }

        if (!empty($_REQUEST['lastonlinefrom']) && !empty($_REQUEST['lastonlinetill'])) {
            $lafrom = $this->__object('AdminCore')->mktime($_REQUEST['lastonlinefrom'], 0, 0, 1);
            $latill = $this->__object('AdminCore')->mktime($_REQUEST['lastonlinetill'], 23, 59, 59);
            if ($lafrom < $latill) {
                $def_lastonline = " AND (Zuletzt_Aktiv BETWEEN '" . intval($lafrom) . "' AND '" . intval($latill) . "') ";
                $def_lastonline_n = "&lastonlinefrom=" . Arr::getRequest('lastonlinefrom') . "&lastonlinetill=" . Arr::getRequest('lastonlinetill');
            }
        }

        $curr_page = '&amp;page=' . Arr::getRequest('page', 1);
        switch (Arr::getRequest('sort')) {
            default:
            case 'regdate_asc':
                $def_order = ' ORDER BY Regdatum ASC';
                $def_order_n = '&sort=regdate_asc' . $curr_page;
                $def_order_ns = '&sort=regdate_desc' . $curr_page;
                $this->_view->assign('regdate_s', $def_order_ns);
                break;

            case 'regdate_desc':
                $def_order = ' ORDER BY Regdatum DESC';
                $def_order_n = '&sort=regdate_desc' . $curr_page;
                $def_order_ns = '&sort=regdate_asc' . $curr_page;
                $this->_view->assign('regdate_s', $def_order_ns);
                break;

            case 'name_asc':
                $def_order = ' ORDER BY Nachname ASC';
                $def_order_n = '&sort=name_asc' . $curr_page;
                $def_order_ns = '&sort=name_desc' . $curr_page;
                $this->_view->assign('name_s', $def_order_ns);
                break;

            case 'name_desc':
                $def_order = ' ORDER BY Nachname DESC';
                $def_order_n = '&sort=name_desc' . $curr_page;
                $def_order_ns = '&sort=name_asc' . $curr_page;
                $this->_view->assign('name_s', $def_order_ns);
                break;

            case 'username_asc':
                $def_order = ' ORDER BY Benutzername ASC';
                $def_order_n = '&sort=username_asc' . $curr_page;
                $def_order_ns = '&sort=username_desc' . $curr_page;
                $this->_view->assign('username_s', $def_order_ns);
                break;

            case 'username_desc':
                $def_order = ' ORDER BY Benutzername DESC';
                $def_order_n = '&sort=username_desc' . $curr_page;
                $def_order_ns = '&sort=username_asc' . $curr_page;
                $this->_view->assign('username_s', $def_order_ns);
                break;

            case 'usergroup_asc':
                $def_order = ' ORDER BY Gruppe ASC';
                $def_order_n = '&sort=usergroup_asc' . $curr_page;
                $def_order_ns = '&sort=usergroup_desc' . $curr_page;
                $this->_view->assign('usergroup_s', $def_order_ns);
                break;

            case 'usergroup_desc':
                $def_order = ' ORDER BY Gruppe DESC';
                $def_order_n = '&sort=usergroup_desc' . $curr_page;
                $def_order_ns = '&sort=usergroup_asc' . $curr_page;
                $this->_view->assign('usergroup_s', $def_order_ns);
                break;

            case 'lastonline_asc':
                $def_order = ' ORDER BY Zuletzt_Aktiv ASC';
                $def_order_n = '&sort=lastonline_asc' . $curr_page;
                $def_order_ns = '&sort=lastonline_desc' . $curr_page;
                $this->_view->assign('regdate_s', $def_order_ns);
                break;

            case 'lastonline_desc':
                $def_order = ' ORDER BY Zuletzt_Aktiv DESC';
                $def_order_n = '&sort=lastonline_desc' . $curr_page;
                $def_order_ns = '&sort=lastonline_asc' . $curr_page;
                $this->_view->assign('lastonline_s', $def_order_ns);
                break;
        }

        if (Arr::getPost('startsearch') == 1) {
            $_SESSION['UserSearch'] = "WHERE Id != '0'  {$def_search} {$def_group} {$def_regtime} {$def_lastonline} {$def_aktive}";
        }

        $limit = (Arr::getRequest('limit') >= 1) ? Tool::cleanDigit($_REQUEST['limit']) : $this->UserLimit;
        if ($new == 'new') {
            $limit = 5;
            $def_order = ' ORDER BY Regdatum DESC';
        }
        $a = Tool::getLimit($limit);
        $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS Id,Nachname,Benutzername,Email,Vorname,Gruppe,Aktiv,Zuletzt_Aktiv,Regdatum FROM " . PREFIX . "_benutzer WHERE Id!='0' {$def_search} {$def_group} {$def_regtime} {$def_lastonline} {$def_aktive} {$def_order} LIMIT $a, $limit");
        $num = $this->_db->found_rows();
        $seiten = ceil($num / $limit);
        $user = array();
        while ($row = $sql->fetch_object()) {
            $row->orders = $this->orders($row->Id);
            $row->downloads = $this->downloads($row->Id);
            $user[] = $row;
        }
        $sql->close();

        $ordstr = "index.php?do=user&sub=showusers{$def_search_n}{$def_group_n}{$def_regtime_n}{$def_lastonline_n}&amp;limit={$limit}{$def_aktive_n}{$db_shop_orders_n}";
        $nastr = "{$def_search_n}{$def_group_n}{$def_regtime_n}{$def_lastonline_n}&amp;limit={$limit}{$def_aktive_n}{$db_shop_orders_n}{$def_order_n}";
        $this->_view->assign('limit', $limit);
        $this->_view->assign('ordstr', $ordstr);
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        if ($num > $limit) {
            $this->_view->assign('pages', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=user&amp;sub=showusers{$nastr}&amp;limit={$limit}&amp;page={s}\">{t}</a> "));
        }
        return $user;
    }

    protected function orders($user) {
        $res = $this->_db->cache_fetch_object("SELECT Id AS OrderCount FROM " . PREFIX . "_shop_bestellungen WHERE Benutzer = '" . intval($user) . "' LIMIT 1");
        return is_object($res) ? $res->OrderCount : '';
    }

    public function add() {
        if (!perm('users_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $error = '';
            $name = Tool::cleanAllow(Arr::getPost('Benutzername'));
            $email = Tool::cleanMail(Arr::getPost('Email'));
            $CName = $this->country($_POST['Land']);
            $PassRaw = Tool::getPass($_POST['Kennwort'], false);
            $Pass = Tool::getPass($PassRaw);
            $check = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer WHERE Email='" . $this->_db->escape($email) . "' OR Benutzername='" . $this->_db->escape($name) . "' LIMIT 1");
            if (is_object($check) && $check->Email == $email) {
                $error[] = 'Такой E-Mail уже используется.';
            }
            if (is_object($check) && $check->Benutzername == $name) {
                $error[] = 'Такое имя пользователя уже используется.';
            }

            if ($error) {
                $this->_view->assign('error', $error);
            } else {
                $insert_array = array(
                    'Gruppe'        => Arr::getPost('Gruppe'),
                    'Team'          => Arr::getPost('Team'),
                    'Regdatum'      => time(),
                    'Email'         => Tool::cleanMail(Arr::getPost('Email')),
                    'Kennwort'      => $Pass,
                    'Benutzername'  => $name,
                    'Vorname'       => Arr::getPost('Vorname'),
                    'Nachname'      => Arr::getPost('Nachname'),
                    'Strasse_Nr'    => Arr::getPost('Strasse_Nr'),
                    'Postleitzahl'  => Arr::getPost('Postleitzahl'),
                    'Ort'           => Arr::getPost('Ort'),
                    'Firma'         => Arr::getPost('Firma'),
                    'UStId'         => Arr::getPost('UStId'),
                    'Telefon'       => Arr::getPost('Telefon'),
                    'Telefax'       => Arr::getPost('Telefax'),
                    'Land'          => $CName,
                    'LandCode'      => Arr::getPost('Land'),
                    'Aktiv'         => Arr::getPost('Aktiv'),
                    'Profil_public' => 0,
                    'Profil_Alle'   => 0,
                    'Emailempfang'  => 0,
                    'Pnempfang'     => 0,
                    'Gaestebuch'    => 0,
                    'Zuletzt_Aktiv' => time(),
                    'Fsk18'         => Arr::getPost('Fsk18'),
                    'Webseite'      => Arr::getPost('Webseite'),
                    'MiddleName'    => Arr::getPost('MiddleName'),
                    'BankName'      => Arr::getPost('BankName'),
                    'Geloescht'     => Arr::getPost('Geloescht'));
                $this->_db->insert_query('benutzer', $insert_array);
                $id = $this->_db->insert_id();

                if (Arr::getPost('send_mail') == 1 && !empty($_POST['Email'])) {
                    $mail_array = array(
                        '__WEBSITE__' => BASE_URL,
                        '__MAIL__'    => Arr::getPost('Email'),
                        '__PASS__'    => $PassRaw);
                    $Text = $this->_text->replace($_POST['mail_text'], $mail_array);
                    $Subject = $this->_text->replace($this->_lang['Shop_convertGuestMailSubject'], '__WEBSITE__', BASE_URL);
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $_POST['Email'],
                        'to_name'   => $name,
                        'text'      => $Text,
                        'subject'   => $Subject,
                        'fromemail' => SX::get('system.Mail_Absender'),
                        'from'      => SX::get('system.Mail_Name'),
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => '',
                        'prio'      => 1));
                }
                $this->_view->assign('done', 1);
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' создал пользователя: ' . $name . '(Id: ' . $id . ')', '0', $_SESSION['benutzer_id']);
                $this->__object('AdminCore')->script('save');
            }
        }
        $this->_view->assign('password', Tool::random());
        $this->_view->assign('countries', Tool::countries());
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('title', $this->_lang['User_Add']);
        $this->_view->content('/user/userform_new.tpl');
    }

    public function convert($order) {
        if (!perm('shop_guestconvert')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $CName = $this->country($_POST['Land']);
            $PassRaw = Tool::getPass($_POST['Kennwort'], false);
            $Pass = Tool::getPass($PassRaw);
            $name = Arr::getPost('Benutzername');
            $insert_array = array(
                'Gruppe'        => Arr::getPost('Gruppe'),
                'Team'          => Arr::getPost('Team'),
                'Regdatum'      => time(),
                'Email'         => Tool::cleanMail(Arr::getPost('Email')),
                'Kennwort'      => $Pass,
                'Benutzername'  => $name,
                'Vorname'       => Arr::getPost('Vorname'),
                'Nachname'      => Arr::getPost('Nachname'),
                'Strasse_Nr'    => Arr::getPost('Strasse_Nr'),
                'Postleitzahl'  => Arr::getPost('Postleitzahl'),
                'Ort'           => Arr::getPost('Ort'),
                'Firma'         => Arr::getPost('Firma'),
                'UStId'         => Arr::getPost('UStId'),
                'Telefon'       => Arr::getPost('Telefon'),
                'Telefax'       => Arr::getPost('Telefax'),
                'Land'          => $CName,
                'LandCode'      => Arr::getPost('Land'),
                'Aktiv'         => 1,
                'Profil_public' => 0,
                'Profil_Alle'   => 0,
                'Emailempfang'  => 0,
                'Pnempfang'     => 0,
                'Gaestebuch'    => 0,
                'Zuletzt_Aktiv' => time(),
                'Fsk18'         => Arr::getPost('Fsk18'),
                'Webseite'      => '',
                'MiddleName'    => Arr::getPost('MiddleName'),
                'BankName'      => Arr::getPost('BankName'));
            $this->_db->insert_query('benutzer', $insert_array);
            $UserId = $this->_db->insert_id();

            if (!empty($_REQUEST['order'])) {
                $this->_db->query("UPDATE " . PREFIX . "_shop_bestellungen SET Benutzer = '" . $UserId . "' WHERE Id = '" . intval(Arr::getRequest('order')) . "'");
            }

            if (Arr::getPost('send_mail') == 1 && !empty($_POST['Email'])) {
                $mail_array = array(
                    '__WEBSITE__' => BASE_URL,
                    '__MAIL__'    => Arr::getPost('Email'),
                    '__PASS__'    => $PassRaw);
                $Text = $this->_text->replace($_POST['mail_text'], $mail_array);
                $Subject = $this->_text->replace($this->_lang['Shop_convertGuestMailSubject'], '__WEBSITE__', BASE_URL);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $_POST['Email'],
                    'to_name'   => $name,
                    'text'      => $Text,
                    'subject'   => $Subject,
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' конвертировал пользователя: ' . $name . '(Id: ' . $UserId . ') из гостя', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('close');
        }
        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_shop_bestellungen WHERE Id='" . intval($order) . "' LIMIT 1");
        $this->_view->assign('password', Tool::random());
        $this->_view->assign('countries', Tool::countries());
        $this->_view->assign('country_short', $this->langcode($res->Rng_Land));
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['Global_User']);
        $this->_view->content('/user/guesttouser.tpl');
    }

    public function edit($user) {
        if (!perm('users_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $user = intval($user);
        if (Arr::getPost('save') == 1) {
            $CName = $this->country($_POST['Land']);
            $PassRaw = '';
            $name = Arr::getPost('Benutzername');
            $array = array(
                'Gruppe'       => ($user == 1 ? 1 : Arr::getPost('Gruppe')),
                'Team'         => Arr::getPost('Team'),
                'Email'        => Tool::cleanMail(Arr::getPost('Email')),
                'Benutzername' => $name,
                'Vorname'      => Arr::getPost('Vorname'),
                'Nachname'     => Arr::getPost('Nachname'),
                'Strasse_Nr'   => Arr::getPost('Strasse_Nr'),
                'Postleitzahl' => Arr::getPost('Postleitzahl'),
                'Ort'          => Arr::getPost('Ort'),
                'Firma'        => Arr::getPost('Firma'),
                'UStId'        => Arr::getPost('UStId'),
                'Telefon'      => Arr::getPost('Telefon'),
                'Telefax'      => Arr::getPost('Telefax'),
                'Land'         => $CName,
                'LandCode'     => Arr::getPost('Land'),
                'Aktiv'        => ($user == 1 ? 1 : Arr::getPost('Aktiv')),
                'Geburtstag'   => Tool::formatDate(Arr::getPost('Geburtstag')),
                'Fsk18'        => Arr::getPost('Fsk18'),
                'Webseite'     => Arr::getPost('Webseite'),
                'MiddleName'   => Arr::getPost('MiddleName'),
                'BankName'     => Arr::getPost('BankName'),
                'Geloescht'    => Arr::getPost('Geloescht'),
            );
            if (!empty($_POST['Kennwort'])) {
                $PassRaw = Tool::getPass($_POST['Kennwort'], false);
                $array['Kennwort'] = Tool::getPass($PassRaw);
            }
            $this->_db->update_query('benutzer', $array, "Id = '" . $user . "'");

            if (Arr::getPost('send_mail') == 1 && !empty($_POST['Email'])) {
                $mail_array = array(
                    '__WEBSITE__' => BASE_URL,
                    '__MAIL__'    => Arr::getPost('Email'),
                    '__PASS__'    => (!empty($_POST['Kennwort']) ? $PassRaw : $this->_lang['User_profileChangePassUnd']));
                $Text = $this->_text->replace($_POST['mail_text'], $mail_array);
                $Subject = $this->_lang['User_profileChangeSubject'];
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $_POST['Email'],
                    'to_name'   => $name,
                    'text'      => $Text,
                    'subject'   => $Subject,
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал пользователя: ' . $name . '(Id: ' . $user . ')', '0', $_SESSION['benutzer_id']);
            $this->__object('AdminCore')->script('save');
        }

        $res = $this->_db->cache_fetch_object("SELECT * FROM " . PREFIX . "_benutzer WHERE Id='" . $user . "' LIMIT 1");
        if (!is_object($res)) {
            exit;
        }
        $this->_view->assign('mindate', date('Y') - 50);
        $this->_view->assign('countries', Tool::countries());
        $this->_view->assign('country_short', $this->langcode($res->Land));
        $this->_view->assign('groups', $this->__object('AdminCore')->groups());
        $this->_view->assign('res', $res);
        $this->_view->assign('title', $this->_lang['User_edit']);
        $this->_view->content('/user/userform_newedit.tpl');
    }

    protected function downloads($user) {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS Numcount FROM " . PREFIX . "_shop_downloads_user WHERE Benutzer = '" . intval($user) . "'");
        return $res->Numcount >= 1 ? true : false;
    }

    public function active($w, $user) {
        if (perm('users_edit')) {
            $user = intval($user);
            switch ($w) {
                case 'open':
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Aktiv='1' WHERE Id='" . $user . "'");
                    break;
                case 'close':
                    $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Aktiv='0', RegCode='' WHERE Id='" . $user . "' AND Id!='1'");
                    break;
            }
            $this->__object('AdminCore')->backurl();
        }
    }

    /* Метод проверки на существование пользователя или мыла */
    public function сheck($oext = '') {
        $valid = 'true';
        if (perm('shop_guestconvert')) {
            $valid = 'false';
            $mail = Tool::cleanMail(Arr::getRequest('Email'));
            if (!empty($mail)) {
                $mailex = (!empty($oext)) ? " AND Email != '" . $this->_db->escape($oext) . "'" : '';
                $res = $this->_db->cache_fetch_object("SELECT Email FROM " . PREFIX . "_benutzer WHERE Email = '" . $this->_db->escape($mail) . "' {$mailex} LIMIT 1");
                $valid = is_object($res) ? 'false' : 'true';
            }
            if ($valid == 'false') {
                $uname = Tool::cleanAllow(Arr::getRequest('Benutzername'));
                if (!empty($uname)) {
                    $userex = (!empty($oext)) ? " AND Benutzername != '" . $this->_db->escape($oext) . "'" : '';
                    $res = $this->_db->cache_fetch_object("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Benutzername = '" . $this->_db->escape($uname) . "' {$userex} LIMIT 1");
                    $valid = is_object($res) ? 'false' : 'true';
                }
            }
        }
        SX::output($valid, true);
    }

}
