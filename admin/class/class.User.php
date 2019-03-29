<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404);
    exit;
}

class User extends Magic {

    public $_changepass_tpl = 'changepass.tpl';
    protected $UserId;
    protected $error;

    public function __construct() {
        $this->UserId = $_SESSION['benutzer_id'];
    }

    /* Метод выводит модуль новые пользователи */
    public function show() {
        $sql = $this->_db->query("SELECT SQL_CACHE Id, Gruppe, Avatar, Avatar_Default, Benutzername, Status, Email, Gravatar FROM " . PREFIX . "_benutzer WHERE Profil_public='1' AND Aktiv='1' AND Gruppe!='2' ORDER BY Id DESC LIMIT 5");
        $array = array();
        while ($row = $sql->fetch_object()) {
            $row->userlink = 'index.php?p=user&amp;id=' . $row->Id . '&amp;area=' . AREA;
            $row->avatar = $this->__object('Avatar')->load($row->Gravatar, $row->Email, $row->Gruppe, $row->Avatar, $row->Avatar_Default);
            $row->name = $row->Benutzername;
            $array[] = $row;
        }
        $sql->close();
        $this->_view->assign('NewUsersData', $array);
        $this->_view->assign('NewUsers', $this->_view->fetch(THEME . '/user/newusers.tpl'));
    }

    /* Метод получения количества видеороликов пользователя */
    protected function count($id) {
        $res = $this->_db->cache_fetch_object("SELECT COUNT(Id) AS ResCount FROM " . PREFIX . "_benutzer_videos WHERE Benutzer='" . intval($id) . "'");
        return $res->ResCount;
    }

    public function profile() {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 1) {
            $this->__object('Core')->message('Global_NoPermission', 'Global_NoPermission_t');
        } else {
            $settings = SX::get('system');
            if (Arr::getPost('send') == '1') {
                $update_email = $update_username = $update_firstname = $update_lastname = $update_street = $update_zip = $update_town = $update_phone = $update_fax = '';

                if (!empty($_POST['newmail']) && !empty($_POST['newmail2']) && permission('email_change')) {
                    if (!Tool::isMail($_POST['newmail'])) {
                        $this->error[] = $this->_lang['RegE_wrongmail'];
                    }
                    $newmail = Tool::cleanMail($_POST['newmail']);
                    if ($newmail != Tool::cleanMail($_POST['newmail2'])) {
                        $this->error[] = $this->_lang['RegE_wrongmail2'];
                    }

                    if (empty($this->error)) {
                        $row_c = $this->_db->cache_fetch_object("SELECT Email FROM " . PREFIX . "_benutzer WHERE Email = '" . $this->_db->escape($newmail) . "' AND Email != '" . $_SESSION['login_email'] . "' LIMIT 1");
                        if (is_object($row_c) && !empty($row_c->Email)) {
                            $this->error[] = $this->_lang['RegE_mailinuse'];
                        }
                    }

                    if (empty($this->error)) {
                        if (Tool::lockedMail($newmail)) {
                            $this->error[] = $this->_lang['RegE_maillocked'];
                        }
                    }

                    if (empty($this->error)) {
                        $update_email = "Email = '" . $this->_db->escape($newmail) . "',";
                    }
                } else {
                    $_POST['newmail'] = $_POST['newmail2'] = '';
                }

                $_POST['msn'] = Tool::cleanAllow(Arr::getPost('msn'), ' :@|');
                $_POST['aim'] = Tool::cleanAllow(Arr::getPost('aim'), ' :@|');
                $_POST['icq'] = Tool::cleanAllow(Arr::getPost('icq'), ' :@|');
                $_POST['Webseite'] = Tool::cleanUrl($_POST['Webseite']);
                $_POST['Webseite'] = !empty($_POST['Webseite']) ? Tool::checkSheme($_POST['Webseite']) : '';
                $_POST['Firma'] = Tool::cleanAllow($_POST['Firma'], ' @&)(|_:');
                $_POST['UStId'] = Tool::cleanAllow($_POST['UStId'], ' @|\/_');
                $Landcode = Tool::cleanAllow($_POST['country']);
                $row_l = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape($Landcode) . "' LIMIT 1");

                if (is_object($row_l)) {
                    $Land = $row_l->Name;
                } else {
                    $Landcode = $settings['Land'];
                    $row_l = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code = '" . $Landcode . "' LIMIT 1");
                    $Land = $row_l->Name;
                }

                if (!empty($_POST['birth'])) {
                    $birth = Tool::formatDate($_POST['birth']);
                    if (!preg_match('/(^[\d]{2}).([\d]{2}).([\d]{4}$)/u', $birth)) {
                        $this->error[] = $this->_lang['RegE_wrongBirth'];
                    }
                    $year_d = substr($birth, 0, 2);
                    $year_m = substr($birth, 3, 2);
                    $year_b = substr($birth, 6);
                    if ($year_d < 1 || $year_d > 31) {
                        $this->error[] = $this->_lang['RegE_wrongBirthD'];
                    }
                    if ($year_m < 1 || $year_m > 12) {
                        $this->error[] = $this->_lang['RegE_wrongBirthM'];
                    }
                    if ($year_b < (date('Y') - 80) || $year_b > (date('Y') - 10)) {
                        $this->error[] = $this->_lang['RegE_wrongBirthY'];
                    }
                }

                if (permission('username')) {
                    if (empty($_POST['reg_username'])) {
                        $this->error[] = $this->_lang['RegE_noUsername'];
                    }
                    if (!empty($_POST['reg_username']) && !Tool::isAllow($_POST['reg_username'])) {
                        $this->error[] = $this->_lang['RegE_wrongUsername'];
                    }
                    $_POST['reg_username'] = Tool::cleanAllow($_POST['reg_username'], ' ');
                    $row_c = $this->_db->cache_fetch_object("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Benutzername = '" . $this->_db->escape(Arr::getPost('reg_username')) . "' AND Benutzername != '" . $_SESSION['user_name'] . "' LIMIT 1");
                    if (is_object($row_c) && !empty($row_c->Benutzername)) {
                        $this->error[] = $this->_lang['RegE_usernameUsed'];
                    }
                    if (!empty($_POST['reg_username']) && !Tool::checkSpam($_POST['reg_username'])) {
                        $this->error[] = $this->_lang['Username'] . ': ' . $this->_lang['SpamUsed'];
                    }

                    if (empty($this->error)) {
                        $new_name = Tool::cleanAllow(substr($_POST['reg_username'], 0, 20), '. ');
                        $update_username = "Benutzername = '" . $new_name . "',";
                        $_SESSION['user_name'] = $new_name;
                    }
                }

                if (empty($_POST['Vorname']) && $settings['Reg_DataPflichtFill'] == 1) {
                    $this->error[] = $this->_lang['Profile_NoFirstName'];
                }
                if (!empty($_POST['Vorname']) && !Tool::isAllow($_POST['Vorname'])) {
                    $this->error[] = $this->_lang['Profile_CheckFirstName'];
                }
                if (empty($this->error)) {
                    $update_firstname = "Vorname = '" . $this->_db->escape(trim(Arr::getPost('Vorname'))) . "',";
                }

                if (empty($_POST['Nachname']) && $settings['Reg_DataPflichtFill'] == 1) {
                    $this->error[] = $this->_lang['Profile_NoLastName'];
                }
                if (!empty($_POST['Nachname']) && !Tool::isAllow($_POST['Nachname'])) {
                    $this->error[] = $this->_lang['Profile_CheckLastName'];
                }
                if (empty($this->error)) {
                    $update_lastname = "Nachname = '" . $this->_db->escape(trim(Arr::getPost('Nachname'))) . "',";
                }

                if (empty($_POST['Strasse_Nr']) && $settings['Reg_AddressFill'] == 1) {
                    $this->error[] = $this->_lang['Profile_NoStreet'];
                }
                if (!empty($_POST['Strasse_Nr']) && !Tool::isAddress($_POST['Strasse_Nr'])) {
                    $this->error[] = $this->_lang['Profile_WrongStreet'];
                }
                if (empty($this->error)) {
                    $update_street = "Strasse_Nr = '" . $this->_db->escape(trim(Arr::getPost('Strasse_Nr'))) . "',";
                }

                if (empty($_POST['Postleitzahl']) && $settings['Reg_AddressFill'] == 1) {
                    $this->error[] = $this->_lang['Profile_NoZip'];
                }
                if (!empty($_POST['Postleitzahl']) && !Tool::isAllow($_POST['Postleitzahl'])) {
                    $this->error[] = $this->_lang['Profile_WrongZip'];
                }
                if (empty($this->error)) {
                    $update_zip = "Postleitzahl = '" . $this->_db->escape(trim(Arr::getPost('Postleitzahl'))) . "',";
                }

                if (empty($_POST['Ort']) && $settings['Reg_AddressFill'] == 1) {
                    $this->error[] = $this->_lang['Profile_NoTown'];
                }
                if (!empty($_POST['Ort']) && !Tool::isAllow($_POST['Ort'])) {
                    $this->error[] = $this->_lang['Profile_WrongTown'];
                }
                if (empty($this->error)) {
                    $update_town = "Ort = '" . $this->_db->escape(trim(Arr::getPost('Ort'))) . "',";
                }

                if (!empty($_POST['Telefon']) && !Tool::isAllow($_POST['Telefon'])) {
                    $this->error[] = $this->_lang['Profile_WrongPhone'];
                }
                if (empty($this->error)) {
                    $update_phone = "Telefon = '" . $this->_db->escape(trim(Arr::getPost('Telefon'))) . "',";
                }

                if (!empty($_POST['Telefax']) && !Tool::isAllow($_POST['Telefax'])) {
                    $this->error[] = $this->_lang['Profile_WrongFax'];
                }
                if (empty($this->error)) {
                    $update_fax = "Telefax = '" . $this->_db->escape(trim(Arr::getPost('Telefax'))) . "',";
                }

                if (!empty($_POST['icq']) && !Tool::checkSpam($_POST['icq'])) {
                    $this->error[] = $this->_lang['Profile_ICQ'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['msn']) && !Tool::checkSpam($_POST['msn'])) {
                    $this->error[] = $this->_lang['Profile_MSN'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['aim']) && !Tool::checkSpam($_POST['aim'])) {
                    $this->error[] = $this->_lang['Profile_AIM'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['Webseite']) && !Tool::checkSpam($_POST['Webseite'])) {
                    $this->error[] = $this->_lang['Web'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['Signatur']) && !Tool::checkSpam($_POST['Signatur'])) {
                    $this->error[] = $this->_lang['Profile_Sig'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['Interessen']) && !Tool::checkSpam($_POST['Interessen'])) {
                    $this->error[] = $this->_lang['Profile_Int'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['Firma']) && !Tool::checkSpam($_POST['Firma'])) {
                    $this->error[] = $this->_lang['Profile_company'] . ': ' . $this->_lang['SpamUsed'];
                }
                if (!empty($_POST['UStId']) && !Tool::checkSpam($_POST['UStId'])) {
                    $this->error[] = $this->_lang['Profile_vatnum'] . ': ' . $this->_lang['SpamUsed'];
                }

                $update_vkontakte = $this->social('Vkontakte', 'vk.com');
                $update_odnoklassniki = $this->social('Odnoklassniki', 'ok.ru');
                $update_mymail = $this->social('My.mail');
                $update_google = $this->social('Google');
                $update_facebook = $this->social('Facebook');
                $update_twitter = $this->social('Twitter');

                $avatar_db = isset($_POST['Avatar_Default']) ? "Avatar_Default = '" . $this->_db->escape(Arr::getPost('Avatar_Default')) . "'," : '';
                if (Arr::getPost('Avatar_Del') == 1) {
                    $avatar_del = "Avatar = '',";
                    File::delete(UPLOADS_DIR . '/avatars/' . Tool::userSettings('Avatar'));
                } else {
                    $avatar_del = '';
                }

                if (permission('own_avatar')) {
                    if (Arr::getPost('Avatar_Del') == 1) {
                        $avatar_db = "Avatar='',";
                    }
                    $avatar_std_db = "Avatar_Default = '" . intval($_POST['Avatar_Default']) . "',";

                    if (!empty($_POST['newAvatar'])) {
                        $avatar_db = "Avatar='" . $this->_db->escape(Tool::cleanAllow($_POST['newAvatar'], '.')) . "',";
                        $avatar_std_db = "Avatar_Default = '',";
                    }
                }

                if (!empty($this->error)) {
                    $this->_view->assign('error', $this->error);
                } else {
                    if (get_active('user_videos')) {
                        if (!empty($_POST['VideoSource'])) {
                            foreach ($_POST['VideoSource'] as $vid => $video) {
                                $VideoSrc = Tool::cleanAllow($_POST['VideoSource'][$vid]);
                                $Video = Tool::cleanAllow($_POST['Video'][$vid]);
                                $Name = Tool::cleanAllow($_POST['Name'][$vid]);
                                $q = "UPDATE " . PREFIX . "_benutzer_videos SET VideoSource = '" . $VideoSrc . "', Video = '" . $this->_db->escape($Video) . "', Name = '" . $Name . "', Position = '" . intval($_POST['Position'][$vid]) . "' WHERE Id = '" . intval($vid) . "' AND Benutzer = '" . $this->UserId . "'";
                                $this->_db->query($q);

                                if ($_POST['DelVideo'][$vid] == 1) {
                                    $this->_db->query("DELETE FROM " . PREFIX . "_benutzer_videos WHERE Id='" . intval($vid) . "' AND Benutzer = '" . $this->UserId . "'");
                                }
                            }
                        }

                        $VideoCount = $this->count($this->UserId);
                        if (!empty($_POST['VideoNeu']) && $VideoCount < 4) {
                            foreach ($_POST['VideoNeu'] as $vid => $video) {
                                $VideoCount = $this->count($this->UserId);
                                if ($VideoCount < 5 && !empty($_POST['VideoNeu'][$vid])) {
                                    $VideoSrc = Tool::cleanAllow($_POST['VideoSourceNeu'][$vid]);
                                    $Video = Tool::cleanAllow($_POST['VideoNeu'][$vid]);
                                    $Name = Tool::cleanAllow($_POST['NameNeu'][$vid]);

                                    $insert_array = array(
                                        'Benutzer'    => $this->UserId,
                                        'VideoSource' => $VideoSrc,
                                        'Video'       => $Video,
                                        'Name'        => $Name,
                                        'Position'    => intval($_POST['PositionNeu'][$vid]));
                                    $this->_db->insert_query('benutzer_videos', $insert_array);
                                }
                            }
                        }
                    }

                    $_SESSION['benutzer_vorname'] = Tool::cleanAllow($_POST['Vorname']);
                    $_SESSION['benutzer_nachname'] = Tool::cleanAllow($_POST['Nachname']);
                    $query = "UPDATE " . PREFIX . "_benutzer SET
                           $avatar_std_db
                            $avatar_del
                            $avatar_db
                            $update_fax
                            $update_phone
                            $update_town
                            $update_zip
                            $update_street
                            $update_lastname
                            $update_firstname
                            $update_username
                            $update_email
                            $update_vkontakte
                            $update_odnoklassniki
                            $update_facebook
                            $update_twitter
                            $update_google
                            $update_mymail
                            LandCode = '" . strtolower($Landcode) . "',
                            Land = '" . $Land . "',
                            Profil_public = '" . $this->_db->escape(Arr::getPost('Profil_public')) . "',
                            Geburtstag_public = '" . $this->_db->escape(Arr::getPost('Geburtstag_public')) . "',
                            Geburtstag = '" . $this->_db->escape($birth) . "',
                            Unsichtbar = '" . $this->_db->escape(Arr::getPost('Unsichtbar')) . "',
                            Newsletter = '" . $this->_db->escape(Arr::getPost('Newsletter')) . "',
                            Emailempfang = '" . $this->_db->escape(Arr::getPost('Emailempfang')) . "',
                            Pnempfang = '" . intval($_POST['Pnempfang']) . "',
                            PnEmail = '" . intval($_POST['PnEmail']) . "',
                            Gaestebuch = '" . $this->_db->escape(Arr::getPost('Gaestebuch')) . "',
                            msn = '" . $this->_db->escape(Arr::getPost('msn')) . "',
                            aim = '" . $this->_db->escape($_POST['aim']) . "',
                            icq = '" . $this->_db->escape(Arr::getPost('icq')) . "',
                            skype = '" . $this->_db->escape(Arr::getPost('skype')) . "',
                            Profil_Alle = '" . $this->_db->escape(Arr::getPost('Profil_Alle')) . "',
                            Webseite = '" . $this->_db->escape(Arr::getPost('Webseite')) . "',
                            Signatur = '" . $this->_db->escape(substr($_POST['Signatur'], 0, SX::get('user_group.Signatur_Laenge'))) . "',
                            Firma = '" . $this->_db->escape($_POST['Firma']) . "',
                            UStId = '" . $this->_db->escape(Arr::getPost('UStId')) . "',
                            Geschlecht = '" . $this->_db->escape(Arr::getPost('Geschlecht')) . "',
                            PnPopup = '" . $this->_db->escape(Arr::getPost('PnPopup')) . "',
                            Ort_Public = '" . $this->_db->escape(Arr::getPost('Ort_Public')) . "',
                            Gaestebuch_Moderiert = '" . $this->_db->escape(Arr::getPost('Gaestebuch_Moderiert')) . "',
                            Gaestebuch_KeineGaeste = '" . $this->_db->escape(Arr::getPost('Gaestebuch_KeineGaeste')) . "',
                            Gaestebuch_Zeichen = '" . $this->_db->escape(Arr::getPost('Gaestebuch_Zeichen')) . "',
                            Gaestebuch_smilies = '" . $this->_db->escape(Arr::getPost('Gaestebuch_smilies')) . "',
                            Gaestebuch_bbcode = '" . $this->_db->escape(Arr::getPost('Gaestebuch_bbcode')) . "',
                            Gaestebuch_imgcode = '" . $this->_db->escape(Arr::getPost('Gaestebuch_imgcode')) . "',
                            Forum_Beitraege_Limit = '" . $this->_db->escape(Arr::getPost('Forum_Beitraege_Limit')) . "',
                            Forum_Themen_Limit = '" . $this->_db->escape(Arr::getPost('Forum_Themen_Limit')) . "',
                            MiddleName = '" . $this->_db->escape(Tool::cleanAllow($_POST['MiddleName'])) . "',
                            Interessen = '" . $this->_db->escape(sanitize($_POST['Interessen'])) . "',
                            Beruf = '" . $this->_db->escape(sanitize($_POST['Beruf'])) . "',
                            Hobbys = '" . $this->_db->escape(sanitize($_POST['Hobbys'])) . "',
                            Essen = '" . $this->_db->escape(sanitize($_POST['Essen'])) . "',
                            Musik = '" . $this->_db->escape(sanitize($_POST['Musik'])) . "',
                            BankName = '" . $this->_db->escape(sanitize($_POST['BankName'])) . "',
                            Films = '" . $this->_db->escape(sanitize($_POST['Films'])) . "',
                            Tele = '" . $this->_db->escape(sanitize($_POST['Tele'])) . "',
                            Book = '" . $this->_db->escape(sanitize($_POST['Book'])) . "',
                            Game = '" . $this->_db->escape(sanitize($_POST['Game'])) . "',
                            Citat = '" . $this->_db->escape(sanitize($_POST['Citat'])) . "',
                            Other = '" . $this->_db->escape(sanitize($_POST['Other'])) . "',
                            Status = '" . $this->_db->escape(sanitize($_POST['Status'])) . "',
                            Gravatar = '" . intval($_POST['Gravatar']) . "'
                    WHERE Kennwort = '" . $_SESSION['login_pass'] . "' AND Id = '" . $this->UserId . "'";
                    $this->_db->query($query);
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' изменил свой профиль', '6', $this->UserId);
                    if (!empty($update_email)) {
                        $_SESSION['login_email'] = $newmail;
                    }
                    $this->__object('Redir')->seoRedirect('index.php?p=useraction&action=profile');
                }
            }

            $data = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . $this->UserId . "' AND Kennwort = '" . $_SESSION['login_pass'] . "' LIMIT 1");
            $data['Av_Start'] = 40;
            $data['Av_End'] = SX::get('user_group.Avatar_B', 100);
            $VideoCount = $this->count($this->UserId);
            $VideoCountLoop = 4 - $VideoCount;

            $tpl_array = array(
                'avatar'            => $this->__object('Avatar')->load($data['Gravatar'], $data['Email'], $data['Gruppe'], $data['Avatar'], $data['Avatar_Default']),
                'data'              => $data,
                'countries'         => Tool::countries(),
                'startyear'         => (date('Y') - 80),
                'endyear'           => (date('Y') - 10),
                'signatur_erlaubt'  => SX::get('user_group.Signatur_Erlaubt'),
                'signatur_laenge'   => SX::get('user_group.Signatur_Laenge'),
                'signatur_syscode'  => SX::get('user_group.SysCode_Signatur'),
                'myvideos'          => $this->videos($this->UserId),
                'myvideosCount'     => $VideoCount,
                'myvideosCountLoop' => $VideoCountLoop);
            $this->_view->assign($tpl_array);

            $seo_array = array(
                'headernav' => '<a href="index.php?p=userlogin">' . $this->_lang['Login'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['Login'],
                'pagetitle' => $this->_lang['Login'],
                'content'   => $this->_view->fetch(THEME . '/user/profile.tpl'));
            $this->_view->finish($seo_array);
        }
    }

    /* Метод проверки на коррестность заполнения данных с социальными сетями пользователя */
    protected function social($val, $check = NULL) {
        $lang = str_replace('.', '', $val);
        if (!empty($val) && !empty($_POST[$lang])) {
            $url = $this->_text->lower($_POST[$lang]);
            if ($this->_text->strpos($url, $this->_text->lower(!empty($check) ? $check : $val)) !== false) {
                return $lang . " = '" . $this->_db->escape(Tool::checkSheme($url)) . "', ";
            }
            $this->error[] = $this->_lang[$lang] . ': ' . $this->_lang['SocialError'];
        }
        return $lang . " = '', ";
    }

    protected function videos($id) {
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
        return $videos;
    }

    /* Метод самоудаления пользователя */
    public function delete() {
        if ($_SESSION['user_group'] != 1 && Arr::getRequest('subaction') == 'delfinal') {
            if (Tool::getPass(Arr::getRequest('PassCurr')) != Arr::getSession('login_pass')) {
                $this->_view->assign('CurrPassWrong', 1);
            } else {
                $this->_db->query("DELETE FROM " . PREFIX . "_benutzer WHERE Id = '" . $_SESSION['benutzer_id'] . "'");
                $user = Tool::fullName();
                $mail_array = array(
                    '__USER__'   => $user,
                    '__REASON__' => substr(Arr::getPost('DelReason'), 0, 200),
                    '__TEXT__'   => substr(Arr::getPost('ReasonMessage'), 0, 5000),
                    '__PE__'     => "\r\n");
                $Text = $this->_text->replace($this->_lang['AccountDelEmailText'], $mail_array);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => SX::get('system.Mail_Absender'),
                    'to_name'   => $_SESSION['user_name'],
                    'text'      => $Text,
                    'subject'   => $this->_lang['AccountDel'],
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
                $this->__object('Login')->cleanSession();
                $this->__object('Login')->cleanCookie();
                $_SESSION['user_group'] = 2;

                $this->__object('Core')->message('AccountDel', 'AccountDelOk');
            }
        }
        $reasons = explode("\r\n", SX::get('system.Loesch_Gruende'));
        $this->_view->assign('DelReasons', $reasons);

        $seo_array = array(
            'headernav' => $this->_lang['AccountDel'],
            'pagetitle' => $this->_lang['AccountDel'] . $this->_lang['PageSep'] . $this->_lang['Login'],
            'content'   => $this->_view->fetch(THEME . '/user/delaccount.tpl'));
        $this->_view->finish($seo_array);
    }

}