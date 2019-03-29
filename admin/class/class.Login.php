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

class Login extends Magic {

    protected $_tpl_file = 'login.tpl';
    protected $_user_tpl = 'userpanel.tpl';
    protected $_register_tpl = 'register.tpl';
    protected $_register_ok_tpl = 'register_ok.tpl';
    protected $_changepass_tpl = 'changepass.tpl';
    protected $settings;

    public function __construct() {
        $this->settings = SX::get('system');
    }

    /* Метод выводит форму авторизации в профиле */
    public function pageLogin() {
        $seo_array = array(
            'headernav' => $this->_lang['LoginExtern'],
            'pagetitle' => $this->_lang['LoginExtern'],
            'content'   => $this->_view->fetch(THEME . '/user/userloginpage.tpl'));
        $this->_view->finish($seo_array);
    }

    public function launch() {
        $user_login = get_active('Login') ? $this->userLogin() : NULL;
        $this->_view->assign('user_login', $user_login);
        $this->authorize();
    }

    public function authorize() {
        if ($_SESSION['user_group'] != '2' && isset($_SESSION['benutzer_id'])) {
            $this->user(Arr::getSession('area'));
        } else {
            $this->guest(Arr::getSession('area'));
        }
    }

    /* Пытаемся узнать пользователя, узнаем или назначаем гостем */
    protected function user($area) {
        unset($_SESSION['perm'], $_SESSION['perm_admin']);
        $array = array();
        $query = "SELECT * FROM " . PREFIX . "_benutzer_gruppen WHERE Id = '" . intval($_SESSION['user_group']) . "' LIMIT 1; ";
        $query .= "SELECT
            Kennwort,
            LandCode
        FROM
            " . PREFIX . "_benutzer
        WHERE
            Id = '" . intval($_SESSION['benutzer_id']) . "'
        AND
            Kennwort = '" . $this->_db->escape($_SESSION['login_pass']) . "'
        LIMIT 1";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $array = $result->fetch_assoc();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row = $result->fetch_assoc();
                $result->close();
            }
        }
        SX::set('user_group', $array);

        $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Zuletzt_Aktiv = '" . time() . "' WHERE Id = '" . intval($_SESSION['benutzer_id']) . "'");
        if (isset($row['Kennwort']) && $row['Kennwort'] == $_SESSION['login_pass']) {
            $_SESSION['user_country'] = $row['LandCode'];
            $row_perm = $this->_db->fetch_assoc("SELECT
                Rechte,
                Rechte_Admin
            FROM
                " . PREFIX . "_berechtigungen
            WHERE
                Gruppe = '" . intval($_SESSION['user_group']) . "'
            AND
                Sektion = '" . intval($area) . "'
            LIMIT 1");

            if (is_array($row_perm)) {
                $perms_arr = explode(',', $row_perm['Rechte']);
                foreach ($perms_arr as $perm) {
                    $_SESSION['perm'][$perm . $area] = 1;
                }

                $perms_arr = explode(',', $row_perm['Rechte_Admin']);
                foreach ($perms_arr as $perm) {
                    $_SESSION['perm_admin'][$perm . $area] = 1;
                }
            }
        } else {
            unset($_SESSION['benutzer_id'], $_SESSION['perm']);
            $_SESSION['user_country'] = $this->settings['Land'];
            $_SESSION['user_group'] = 2;
            $_SESSION['benutzer_id'] = 0;
            $_SESSION['loggedin'] = 0;
            $row_perm = $this->_db->fetch_assoc("SELECT Rechte FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '2' AND Sektion = '" . intval($area) . "' LIMIT 1");
            $perms_arr = explode(',', $row_perm['Rechte']);
            foreach ($perms_arr as $perm) {
                $_SESSION['perm'][$perm . $area] = 1;
            }
        }
    }

    /* Устанавливаем пользователя как гостя */
    protected function guest($area) {
        unset($_SESSION['benutzer_id'], $_SESSION['perm']);
        $_SESSION['user_country'] = $this->settings['Land'];
        $_SESSION['user_group'] = 2;
        $_SESSION['benutzer_id'] = 0;
        $_SESSION['loggedin'] = 0;

        $array = $this->_db->fetch_assoc("SELECT
                a.MaxAnlagen,
                a.MaxZeichenPost,
                c.Rechte
         FROM
                " . PREFIX . "_benutzer_gruppen AS a,
                " . PREFIX . "_berechtigungen AS c
         WHERE
                a.Id = '2'
         AND
                c.Gruppe = '2'
         AND
                c.Sektion = '" . intval($area) . "' LIMIT 1");

        $perms_arr = explode(',', $array['Rechte']);
        foreach ($perms_arr as $perm) {
            $_SESSION['perm'][$perm . $area] = 1;
        }
        unset($array['Rechte']);
        $array['MaxPn'] = 0;
        $array['MaxPn_Zeichen'] = 0;
        SX::set('user_group', $array);
    }

    /* Метод производит авторизацию */
    public function checkLogin($user, $pass, $md5 = false) {
        if (!empty($user) && !empty($pass)) {
            $mail = Tool::cleanMail($user);
            $user = Tool::cleanAllow($user);
            $pass = Tool::getPass($pass, $md5);

            $row = $this->_db->fetch_assoc("SELECT
                *
            FROM
                " . PREFIX . "_benutzer
            WHERE
                Kennwort = '" . $this->_db->escape($pass) . "'
            AND
                (Email = '" . $this->_db->escape($mail) . "' OR Benutzername = '" . $this->_db->escape($user) . "')
            AND
                Aktiv = '1'
            LIMIT 1");

            if (isset($row['Email'], $row['Kennwort']) && !Tool::lockedMail($row['Email']) && $row['Kennwort'] == $pass && ($row['Email'] == $mail || $row['Benutzername'] == $user)) {
                $array = array(
                    'loggedin'            => 1,
                    'login_email'         => $row['Email'],
                    'login_pass'          => $row['Kennwort'],
                    'benutzer_id'         => $row['Id'],
                    'user_group'          => $row['Gruppe'],
                    'user_name'           => $row['Benutzername'],
                    'unsichtbar'          => $row['Unsichtbar'],
                    'benutzer_vorname'    => $row['Vorname'],
                    'benutzer_nachname'   => $row['Nachname'],
                    'benutzer_middlename' => $row['MiddleName'],
                    'benutzer_bankname'   => $row['BankName'],
                    'benutzer_strasse'    => $row['Strasse_Nr'],
                    'benutzer_plz'        => $row['Postleitzahl'],
                    'benutzer_ort'        => $row['Ort'],
                    'benutzer_firma'      => $row['Firma'],
                    'benutzer_ustid'      => $row['UStId'],
                    'benutzer_fon'        => $row['Telefon'],
                    'benutzer_fax'        => $row['Telefax']
                );
                Arr::setSession($array);
                if (Arr::getSession('loggedin') == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    public function userLogin() {
        $tpl = $this->_tpl_file;
        if (Arr::getSession('loggedin') != 1) {
            if (!Arr::nilCookie('login_email') && !Arr::nilCookie('login_pass')) {
                if ($this->checkLogin(Arr::getCookie('login_email'), Arr::getCookie('login_pass'))) {
                    $tpl = $this->_user_tpl;
                } else {
                    $this->cleanCookie();
                }
            }
        } else {
            if ($this->checkLogin(Arr::getSession('login_email'), Arr::getSession('login_pass'))) {
                $tpl = $this->_user_tpl;
            } else {
                $this->cleanSession();
            }
        }
        $this->_view->assign('welcome', Tool::welcome());
        return $this->_view->fetch(THEME . '/user/' . $tpl);
    }

    /* Метод выхода пользователя */
    public function logout() {
        if (isset($_SESSION['user_name'])) {
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' вышел из профиля', '6', $_SESSION['benutzer_id']);
        }
        $this->cleanSession();
        $this->cleanCookie();
        $back = (isset($_REQUEST['backurl'])) ? base64_decode($_REQUEST['backurl']) : 'index.php';
        $this->__object('Redir')->seoRedirect($back);
    }

    /* Метод очистки кук */
    public function cleanCookie() {
        Arr::delCookie('login_email');
        Arr::delCookie('login_pass');
    }

    /* Метод очистки сессии */
    public function cleanSession() {
        unset($_SESSION['visitor_key']);
        unset($_SESSION['loggedin']);
        unset($_SESSION['benutzer_id']);
        unset($_SESSION['login_email']);
        unset($_SESSION['login_pass']);
        unset($_SESSION['user_group']);
        unset($_SESSION['perm_admin']);
        unset($_SESSION['benutzer_vorname']);
        unset($_SESSION['benutzer_nachname']);
        unset($_SESSION['benutzer_middlename']);
        unset($_SESSION['benutzer_bankname']);
        unset($_SESSION['benutzer_strasse']);
        unset($_SESSION['benutzer_plz']);
        unset($_SESSION['benutzer_ort']);
        unset($_SESSION['benutzer_firma']);
        unset($_SESSION['benutzer_ustid']);
        unset($_SESSION['benutzer_fon']);
        unset($_SESSION['benutzer_fax']);
        unset($_SESSION['user_name']);
        unset($_SESSION['r_vorname']);
        unset($_SESSION['r_nachname']);
        unset($_SESSION['r_strasse']);
        unset($_SESSION['r_plz']);
        unset($_SESSION['r_ort']);
        unset($_SESSION['r_telefon']);
        unset($_SESSION['products']);
        unset($_SESSION['gewicht']);
        unset($_SESSION['price']);
        unset($_SESSION['price_netto']);
        unset($_SESSION['currency']);
        unset($_SESSION['shopstep']);
        unset($_SESSION['r_email']);
        unset($_SESSION['r_fax']);
        unset($_SESSION['r_firma']);
        unset($_SESSION['r_ustid']);
        unset($_SESSION['r_land']);
        unset($_SESSION['prod_seen']);
        unset($_SESSION['payment_summ_extra']);
        unset($_SESSION['shipping_summ']);
    }

    public function ajaxLogin() {
        if (get_active('Login')) {
            $login_email = Arr::getRequest('login_email');
            if (!empty($login_email) && !Arr::nilRequest('login_pass')) {
                if ($this->checkLogin($login_email, Arr::getRequest('login_pass'), true)) {
                    $this->saveLogin();
                    $this->_view->assign('ajaxlogged', 1);
                    $out = "<script type=\"text/javascript\">
                    <!--
                        document.getElementById('ajlw').style.display='none';
                        location.href='" . $this->__object('Redir')->referer(true) . "';
                    -->
                    </script>";
                    $out .= $this->_view->fetch(THEME . '/user/userpanel_raw.tpl');

                    SX::output($out, true);
                } else {
                    SX::syslog('Пользователь ' . $login_email . ' неудачная авторизация (аякс окно)', '6', '');
                    $this->errorMail($login_email);
                }
            }
            $this->cleanSession();
            $this->cleanCookie();
            $this->_view->assign('login_error_true', 1);
            $out = $this->_view->fetch(THEME . '/user/login_raw.tpl');
            SX::output($out, true);
        }
    }

    public function newLogin($extern = 0) {
        if (get_active('Login')) {
            $login_email = Arr::getRequest('login_email');
            if (!empty($login_email) && !Arr::nilRequest('login_pass')) {
                if ($this->checkLogin($login_email, Arr::getRequest('login_pass'), true)) {
                    $this->saveLogin();
                    if ($extern == 1) {
                        $this->_view->assign('LoginSuccess', 1);
                        $this->__object('Redir')->seoRedirect('index.php?success=1&p=userlogin');
                    } else {
                        $this->__object('Redir')->seoRedirect(base64_decode($_REQUEST['backurl']));
                    }
                } else {
                    SX::syslog('Пользователь ' . $login_email . ' неудачная авторизация на странице профиля', '6', '');
                    $this->errorMail($login_email);
                }
            }
            $this->cleanSession();
            $this->cleanCookie();
            $this->loginError($extern);
        }
    }

    public function saveLogin() {
        $array = Arr::getSession(array('login_email', 'login_pass', 'user_name', 'benutzer_id' => 0));

        if (Arr::getRequest('staylogged') == 1) {
            Arr::setCookie('login_email', $array['login_email'], 3600 * 24 * 365);
            Arr::setCookie('login_pass', $array['login_pass'], 3600 * 24 * 365);
        }

        $this->_db->query("UPDATE " . PREFIX . "_benutzer
        SET
            Logins = Logins + 1
        WHERE
            Email = '" . $this->_db->escape($array['login_email']) . "'
        AND
            Kennwort = '" . $this->_db->escape($array['login_pass']) . "'
        AND
            Aktiv = '1'
        LIMIT 1");
        $insert_array = array(
            'Benutzer' => $array['benutzer_id'],
            'Datum'    => time(),
            'Datum_dt' => date('Y-m-d H:i:s'),
            'Ip'       => IP_USER,
            'Email'    => $array['login_email']);
        $this->_db->insert_query('benutzer_logins', $insert_array);
        SX::syslog('Пользователь ' . $array['user_name'] . ' авторизовался', '6', $array['benutzer_id']);
    }

    protected function loginError($extern) {
        $this->cleanSession();
        $_SESSION['user_group'] = 2;
        $this->cleanCookie();

        if ($extern == 1) {
            $this->_view->assign('LoginError', 1);
        } else {
            $this->__object('Core')->message('Error', 'WrongLoginData', base64_decode($_REQUEST['backurl']), 5);
        }
    }

    /* Метод отправки уведомления при неудачной авторизации */
    protected function errorMail($email) {
        if ($this->settings['Error_Email'] == 1 && Tool::isMail($email)) {
            if (!isset($_SESSION['error_email']) || $_SESSION['error_email'] != $email) {
                $row = $this->_db->fetch_object("SELECT Id FROM " . PREFIX . "_benutzer WHERE Email = '" . $this->_db->escape($email) . "' LIMIT 1");
                if (!is_object($row) && !empty($email)) {
                    $_SESSION['error_email'] = $email;
                    $mail_array = array('__URL__' => BASE_URL, '__MAIL__' => $email);
                    $message = $this->_text->replace($this->_lang['ErrorEmailSend'], $mail_array);
                    $subject = $this->_text->replace($this->_lang['ErrorEmailSendSubj'], '__URL__', BASE_URL);
                    SX::setMail(array(
                        'globs'     => '1',
                        'to'        => $email,
                        'to_name'   => '',
                        'text'      => $message,
                        'subject'   => $subject,
                        'fromemail' => $this->settings['Mail_Absender'],
                        'from'      => $this->settings['Mail_Name'],
                        'type'      => 'text',
                        'attach'    => '',
                        'html'      => '',
                        'prio'      => 3));
                }
            }
        }
    }

    /* Метод проверки при регистрации существует ли пользователь или мыло в базе */
    public function сheck() {
        $valid = 'true';
        if (get_active('Register')) {
            $valid = 'false';
            $mail = Tool::cleanMail(Arr::getRequest('reg_email'));
            if (!empty($mail)) {
                $res = $this->_db->fetch_object("SELECT Email FROM " . PREFIX . "_benutzer WHERE Email = '" . $this->_db->escape($mail) . "' LIMIT 1");
                $valid = (is_object($res)) ? 'false' : 'true';
            }
            if ($valid == 'false') {
                $name = Tool::cleanAllow(Arr::getRequest('reg_username'));
                if (!empty($name)) {
                    $res = $this->_db->fetch_object("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Benutzername = '" . $this->_db->escape($name) . "' LIMIT 1");
                    $valid = is_object($res) ? 'false' : 'true';
                }
            }
        }
        SX::output($valid, true);
    }

    /* Метод регистрации пользователя */
    public function register($shop = 0) {
        if (get_active('Register')) {
            if ($_SESSION['loggedin'] == 1) {
                $this->success(1);
            } else {
                $final = false;

                if (Arr::getRequest('send') == '1') {
                    $error = array();
                    if (empty($_POST['reg_email'])) {
                        $error[] = $this->_lang['Comment_NoEmail'];
                    }
                    if (!Tool::isMail($_POST['reg_email'])) {
                        $error[] = $this->_lang['RegE_wrongmail'];
                    }
                    $reg_email = Tool::cleanMail(Arr::getPost('reg_email'));
                    if (empty($_POST['reg_email2']) || $reg_email != Tool::cleanMail($_POST['reg_email2'])) {
                        $error[] = $this->_lang['RegE_wrongmail2'];
                    }

                    if ($this->settings['Reg_Pass'] == '1') {
                        if (empty($_POST['reg_pass'])) {
                            $error[] = $this->_lang['PassLostNoPass'];
                        }
                        if (empty($_POST['reg_pass2']) || $_POST['reg_pass'] != $_POST['reg_pass2']) {
                            $error[] = $this->_lang['PassLostNoMatch'];
                        }
                    }

                    if (empty($error)) {
                        $row_c = $this->_db->cache_fetch_object("SELECT Email FROM " . PREFIX . "_benutzer WHERE Email = '" . $this->_db->escape($reg_email) . "' LIMIT 1");
                        if (is_object($row_c) && !empty($row_c->Email)) {
                            $error[] = $this->_lang['RegE_mailinuse'];
                        }
                    }

                    if (empty($error)) {
                        if (Tool::lockedMail($reg_email)) {
                            $error[] = $this->_lang['RegE_maillocked'];
                        }
                    }

                    if (empty($_POST['reg_username'])) {
                        $error[] = $this->_lang['RegE_noUsername'];
                    }
                    if (!empty($_POST['reg_username']) && preg_match('/[^\w- ]/iu', $_POST['reg_username'])) {
                        $error[] = $this->_lang['RegE_wrongUsername'];
                    }
                    $reg_username = Tool::cleanAllow(Arr::getPost('reg_username'), ' ');
                    $reg_username = $this->_text->substr($reg_username, 0, 20);
                    $row_c = $this->_db->cache_fetch_object("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Benutzername = '" . $this->_db->escape($reg_username) . "' LIMIT 1");
                    if (is_object($row_c) && !empty($row_c->Benutzername)) {
                        $error[] = $this->_lang['RegE_usernameUsed'];
                    }
                    if (!Tool::checkSpam($reg_username)) {
                        $error[] = $this->_lang['Username'] . ': ' . $this->_lang['SpamUsed'];
                    }
                    if ($this->settings['Reg_DataPflichtFill'] == 1) {
                        if (empty($_POST['Vorname'])) {
                            $error[] = $this->_lang['Profile_NoFirstName'];
                        }
                        if (empty($_POST['Nachname'])) {
                            $error[] = $this->_lang['Profile_NoLastName'];
                        }
                    }
                    if (!empty($_POST['Vorname']) && !Tool::isAllow($_POST['Vorname'])) {
                        $error[] = $this->_lang['Profile_CheckFirstName'];
                    }
                    if (!empty($_POST['Nachname']) && !Tool::isAllow($_POST['Nachname'])) {
                        $error[] = $this->_lang['Profile_CheckLastName'];
                    }
                    if (!empty($_POST['MiddleName']) && !Tool::isAllow($_POST['MiddleName'])) {
                        $error[] = $this->_lang['Profile_CheckMiddleName'];
                    }
                    if ($this->settings['Reg_AddressFill'] == 1) {
                        if (empty($_POST['Strasse_Nr'])) {
                            $error[] = $this->_lang['Profile_NoStreet'];
                        }
                        if (empty($_POST['Postleitzahl'])) {
                            $error[] = $this->_lang['Profile_NoZip'];
                        }
                        if (empty($_POST['Ort'])) {
                            $error[] = $this->_lang['Profile_NoTown'];
                        }
                    }
                    if (!empty($_POST['Strasse_Nr']) && !Tool::isAddress($_POST['Strasse_Nr'])) {
                        $error[] = $this->_lang['Profile_WrongStreet'];
                    }
                    if (!empty($_POST['Postleitzahl']) && !Tool::isAllow($_POST['Postleitzahl'])) {
                        $error[] = $this->_lang['Profile_WrongZip'];
                    }
                    if (!empty($_POST['Ort']) && !Tool::isAllow($_POST['Ort'])) {
                        $error[] = $this->_lang['Profile_WrongTown'];
                    }
                    if (!empty($_POST['Firma']) && !Tool::isAllow($_POST['Firma'])) {
                        $error[] = $this->_lang['Profile_WrongCompany'];
                    }
                    if (!empty($_POST['UStId']) && !Tool::isAllow($_POST['UStId'])) {
                        $error[] = $this->_lang['Profile_WrongVat'];
                    }
                    if (!empty($_POST['Telefon']) && !Tool::isAllow($_POST['Telefon'])) {
                        $error[] = $this->_lang['Profile_WrongPhone'];
                    }
                    if (!empty($_POST['Telefax']) && !Tool::isAllow($_POST['Telefax'])) {
                        $error[] = $this->_lang['Profile_WrongFax'];
                    }
                    if ($this->settings['Reg_AgbPflicht'] == 1 && $_POST['agb_checked'] != 1) {
                        $error[] = $this->_lang['Reg_agb_failed'];
                    }
                    if (!empty($_POST['Postleitzahl']) && !preg_match('/[\d]/u', $_POST['Postleitzahl'])) {
                        $error[] = $this->_lang['Profile_WrongZip2'];
                    }
                    if (!empty($_POST['birth'])) {
                        $birth = Tool::formatDate($_POST['birth']);
                        if (!preg_match("/(^[\d]{2}).([\d]{2}).([\d]{4}$)/u", $birth)) {
                            $error[] = $this->_lang['RegE_wrongBirth'];
                        }
                        $year_d = substr($birth, 0, 2);
                        $year_m = substr($birth, 3, 2);
                        $year_b = substr($birth, 6);
                        if ($year_d < 1 || $year_d > 31) {
                            $error[] = $this->_lang['RegE_wrongBirthD'];
                        }
                        if ($year_m < 1 || $year_m > 12) {
                            $error[] = $this->_lang['RegE_wrongBirthM'];
                        }
                        if ($year_b < (date('Y') - 80) || $year_b > (date('Y') - 10)) {
                            $error[] = $this->_lang['RegE_wrongBirthY'];
                        }
                    }

                    if ($this->__object('Captcha')->check($error)) {
                        if ($this->settings['Reg_Pass'] == '1') {
                            $pass_nomd5 = Tool::getPass($_POST['reg_pass'], false);
                        } else {
                            $pass_nomd5 = Tool::getPass(Tool::random(8), false);
                        }
                        $pass = Tool::getPass($pass_nomd5);

                        $Landcode = Tool::cleanAllow($_POST['country']);
                        $row_l = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape($Landcode) . "' LIMIT 1");

                        if (is_object($row_l)) {
                            $Land = $row_l->Name;
                        } else {
                            $Landcode = $this->settings['Land'];
                            $row_l = $this->_db->cache_fetch_object("SELECT Name FROM " . PREFIX . "_laender WHERE Code = '" . $this->_db->escape($Landcode) . "' LIMIT 1");
                            $Land = $row_l->Name;
                        }

                        $RegCode = Tool::random(10);
                        $Regdatum = time();
                        $aktiv = $this->settings['Reg_Typ'] == 'norm' ? 1 : 0;

                        $insert_array = array(
                            'Regdatum'     => $Regdatum,
                            'RegCode'      => $RegCode,
                            'Email'        => $reg_email,
                            'Kennwort'     => $pass,
                            'Benutzername' => $reg_username,
                            'Geburtstag'   => $birth,
                            'Aktiv'        => $aktiv,
                            'Land'         => $Land,
                            'LandCode'     => strtolower($Landcode),
                            'Gruppe'       => 3,
                            'Vorname'      => sanitize(Arr::getPost('Vorname')),
                            'Nachname'     => sanitize(Arr::getPost('Nachname')),
                            'Strasse_Nr'   => sanitize(Arr::getPost('Strasse_Nr')),
                            'Postleitzahl' => sanitize(Arr::getPost('Postleitzahl')),
                            'Ort'          => sanitize(Arr::getPost('Ort')),
                            'Telefon'      => sanitize(Arr::getPost('Telefon')),
                            'Telefax'      => sanitize(Arr::getPost('Telefax')),
                            'Firma'        => sanitize(Arr::getPost('Firma')),
                            'UStId'        => sanitize(Arr::getPost('UStId')),
                            'MiddleName'   => sanitize(Arr::getPost('MiddleName')),
                            'BankName'     => sanitize(strip_tags(Arr::getPost('BankName'))));
                        $this->_db->insert_query('benutzer', $insert_array);
                        $iid = $this->_db->insert_id();
                        SX::syslog('Зарегистрировался новый пользователь: ' . $reg_username . ' (' . $reg_email . ')', '6', $iid);

                        if ($this->settings['Reg_Typ'] == 'norm') {
                            $_SESSION['loggedin'] = 1;
                            $_SESSION['login_email'] = $reg_email;
                            $_SESSION['login_pass'] = $pass;
                            $_SESSION['user_name'] = $reg_username;
                            $_SESSION['user_group'] = 3;
                            $_SESSION['user_country'] = $Landcode;
                            $_SESSION['benutzer_id'] = $iid;
                            $body_user = $this->_lang['Reg_Email_1'];
                        } else {
                            $_SESSION['user_group'] = 2;
                            $body_user = $this->_lang['Activate_Email'];
                            $body_user = str_replace('__LINK__', BASE_URL . '/index.php?p=register&do=activate&datum=' . $Regdatum . '&code=' . $RegCode, $body_user);
                        }

                        $mail_array = array(
                            '__USER__'     => $reg_username,
                            '__SITE__'     => BASE_URL,
                            '__MAIL__'     => $reg_email,
                            '__PASS__'     => $pass_nomd5,
                            '__SITENAME__' => $this->settings['Seitenname']);
                        $body_user = $this->_text->replace($body_user, $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $reg_email,
                            'to_name'   => $reg_username,
                            'text'      => $body_user,
                            'subject'   => $this->_lang['Reg_Email_Subject'],
                            'fromemail' => $this->settings['Mail_Absender'],
                            'from'      => $this->settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 1));

                        $mail_array = array(
                            '__USER__' => $reg_username,
                            '__TIME__' => date('d.m.Y H:i', $Regdatum),
                            '__MAIL__' => $reg_email,
                            '__IP__'   => IP_USER);
                        $body_admin = $this->_text->replace($this->_lang['Reg_Email_Admin'], $mail_array);
                        SX::setMail(array(
                            'globs'     => '1',
                            'to'        => $this->settings['Mail_Absender'],
                            'to_name'   => $this->settings['Mail_Name'],
                            'text'      => $body_admin,
                            'subject'   => $this->_lang['Reg_Email_Admin_Subject'],
                            'fromemail' => $this->settings['Mail_Absender'],
                            'from'      => $this->settings['Mail_Name'],
                            'type'      => 'text',
                            'attach'    => '',
                            'html'      => '',
                            'prio'      => 1));
                        $final = true;

                        if ($shop == 1) {
                            if ($this->settings['Reg_Typ'] == 'norm') {
                                $this->__object('Redir')->seoRedirect('index.php?subaction=step2&p=shop&area=' . $_REQUEST['area'] . '&action=shoporder');
                            } else {
                                $this->__object('Redir')->seoRedirect('index.php?inf=regcode&p=shop&area=' . $_REQUEST['area'] . '&action=shoporder&subaction=step2');
                            }
                        } else {
                            $regcode = $this->settings['Reg_Typ'] != 'norm' ? '&inf=regcode' : '';
                            $this->__object('Redir')->seoRedirect('index.php?lang=' . $_REQUEST['lang'] . '&p=register&sub=ok&area=' . $_REQUEST['area'] . $regcode);
                        }
                    }
                }
                $this->__object('Captcha')->start(); // Инициализация каптчи

                $tpl_array = array(
                    'countries' => Tool::countries(),
                    'startyear' => (date('Y') - 80),
                    'endyear'   => (date('Y') - 10));
                $this->_view->assign($tpl_array);

                $tplout = ($final === true) ? $this->_register_ok_tpl : $this->_register_tpl;

                $seo_array = array(
                    'headernav' => $this->_lang['RegNew'],
                    'pagetitle' => $this->_lang['RegNew'] . $this->_lang['PageSep'] . $this->_lang['LoginExtern'],
                    'content'   => $this->_view->fetch(THEME . '/user/' . $tplout));
                $this->_view->finish($seo_array);
            }
        } else {
            $this->__object('Redir')->redirect();
        }
    }

    public function success($allready = 0) {
        $this->_view->assign('allready', $allready);

        $seo_array = array(
            'headernav' => $this->_lang['RegNew'],
            'pagetitle' => $this->_lang['RegNew'] . $this->_lang['PageSep'] . $this->_lang['LoginExtern'],
            'content'   => $this->_view->fetch(THEME . '/user/' . $this->_register_ok_tpl));
        $this->_view->finish($seo_array);
    }

    /* Подтверждение регистрации по коду из письма */
    public function activate() {
        if (!empty($_REQUEST['code']) && !empty($_REQUEST['datum']) && $this->settings['Reg_Typ'] == 'email') {
            $activate = 0;
            $code = Tool::cleanAllow(Arr::getRequest('code'));
            $Regdatum = Tool::cleanDigit($_REQUEST['datum']);
            $row = $this->_db->fetch_object(" SELECT
                    Id,
                    Benutzername,
                    Email
            FROM
                    " . PREFIX . "_benutzer
            WHERE
                    Regdatum = '" . $this->_db->escape($Regdatum) . "'
            AND
                    RegCode = '" . $this->_db->escape($code) . "'
            AND
                    Aktiv != '1'
            AND
                    Geloescht != '1'
            LIMIT 1");

            if (is_object($row)) {
                $RegCode = Tool::random(10);
                $this->_db->query("UPDATE " . PREFIX . "_benutzer SET Aktiv = '1', RegCode = '" . $RegCode . "' WHERE Id = '" . $row->Id . "' AND RegCode = '" . $this->_db->escape($code) . "'");
                SX::syslog('Подверждена регистрация пользователя: ' . $row->Benutzername . ' (' . $row->Email . ')', '6', $row->Id);
                $activate = 1;
            }

            $this->_view->assign('activate', $activate);

            $seo_array = array(
                'headernav' => $this->_lang['RegNew'],
                'pagetitle' => $this->_lang['RegNew'] . $this->_lang['PageSep'] . $this->_lang['LoginExtern'],
                'content'   => $this->_view->fetch(THEME . '/user/activate.tpl'));
            $this->_view->finish($seo_array);
        } else {
            $this->__object('Redir')->redirect();
        }
    }

    /* Метод смены пароля */
    public function changepass() {
        if ($_SESSION['loggedin'] != 1) {
            $this->_view->assign('not_logged', 1);
        } else {
            if (Arr::getPost('send') == '1') {
                $error = array();
                if (empty($_POST['oldpass'])) {
                    $error[] = $this->_lang['ChangePass_E_NoPass'];
                }
                if (empty($_POST['newpass'])) {
                    $error[] = $this->_lang['ChangePass_E_NoNewPass'];
                }
                if (!empty($_POST['newpass']) && Tool::getPass($_POST['newpass'], false) != $_POST['newpass']) {
                    $error[] = $this->_lang['ChangePass_E_BadPass'];
                }
                if (!empty($_POST['newpass']) && strlen($_POST['newpass']) < 5) {
                    $error[] = $this->_lang['ChangePass_E_PassShort'];
                }
                if (empty($_POST['newpass2'])) {
                    $error[] = $this->_lang['ChangePass_E_NoNewPass2'];
                }
                if (!empty($_POST['newpass']) && !empty($_POST['newpass2']) && $_POST['newpass'] != $_POST['newpass2']) {
                    $error[] = $this->_lang['ChangePass_E_PassNotMatch'];
                }
                if (empty($error)) {
                    $num = $this->_db->num_rows("SELECT
                        Kennwort
                    FROM
                        " . PREFIX . "_benutzer
                    WHERE
                        Id = '" . intval($_SESSION['benutzer_id']) . "'
                    AND
                        Kennwort = '" . $this->_db->escape(Tool::getPass($_POST['oldpass'])) . "'
                    AND
                        Email = '" . $this->_db->escape($_SESSION['login_email']) . "'
                    LIMIT 1");
                    if ($num < 1) {
                        $error[] = $this->_lang['ChangePass_E_WrongPass'];
                    }
                }

                if (!empty($error)) {
                    $this->_view->assign('error', $error);
                    SX::syslog('Ошибка смены пароля пользователя: ' . $_SESSION['user_name'] . '.', '6', $_SESSION['benutzer_id']);
                } else {
                    $_SESSION['login_pass'] = Tool::getPass($_POST['newpass']);
                    $this->_db->query("UPDATE
                        " . PREFIX . "_benutzer
                    SET
                        Kennwort = '" . $this->_db->escape($_SESSION['login_pass']) . "'
                    WHERE
                        Id = '" . intval($_SESSION['benutzer_id']) . "'
                    AND
                        Kennwort = '" . $this->_db->escape(Tool::getPass($_POST['oldpass'])) . "'
                    AND
                        Email = '" . $this->_db->escape($_SESSION['login_email']) . "'");
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' изменил пароль', '6', $_SESSION['benutzer_id']);
                    $this->_view->assign('register_ok', 1);
                    $_POST['oldpass'] = $_POST['newpass'] = '';
                }
            }
        }

        $seo_array = array(
            'headernav' => '<a href="index.php?p=userlogin">' . $this->_lang['Login'] . '</a>' . $this->_lang['PageSep'] . $this->_lang['ChangePass'],
            'pagetitle' => $this->_lang['ChangePass'] . $this->_lang['PageSep'] . $this->_lang['LoginExtern'],
            'content'   => $this->_view->fetch(THEME . '/user/' . $this->_changepass_tpl));
        $this->_view->finish($seo_array);
    }

}
