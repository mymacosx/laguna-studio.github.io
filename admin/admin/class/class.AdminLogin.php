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

class AdminLogin extends Magic {

    protected function listLanguages() {
        $languages = array();
        $sql = $this->_db->query("SELECT Id, Sprachcode FROM " . PREFIX . "_sprachen_admin WHERE Aktiv=1 ORDER BY Posi ASC");
        while ($row = $sql->fetch_object()) {
            if (is_file(LANG_DIR . '/' . $row->Sprachcode . '/admin.txt')) {
                $row->Sprache = $this->_lang['Lang_' . $row->Sprachcode];
                $languages[] = $row;
            }
        }
        $sql->close();
        return $languages;
    }

    protected function listSections() {
        $sections = $this->_db->fetch_object_all("SELECT Id, Name FROM " . PREFIX . "_sektionen ORDER BY ID ASC");
        return $sections;
    }

    protected function listThemes() {
        $files = glob(SX_DIR . '/admin/theme/*', GLOB_ONLYDIR);
        return array_map('basename', $files);
    }

    public function formLogin() {
        unset($_SESSION['admin_lang'], $_SESSION['admin_theme'], $_SESSION['a_area']);
        $this->_view->assign('themes', $this->listThemes());
        $this->_view->assign('sections', $this->listSections());
        $this->_view->assign('languages', $this->listLanguages());
        $this->_view->content('/login/login.tpl');
    }

    public function themeSwitch() {
        if (Arr::getSession('loggedin') == 1 && isset($_SESSION['user_group']) && $_SESSION['user_group'] != 2) {
            $theme = Arr::getGet('theme');
            if (!empty($theme) && in_array($theme, $this->listThemes())) {
                Arr::setSession('admin_theme', $theme);
            }
            $this->__object('Redir')->redirect();
        }
    }

    public function sectionSwitch() {
        if (Arr::getSession('loggedin') == 1 && isset($_SESSION['user_group']) && $_SESSION['user_group'] != 2) {
            $group = $_SESSION['user_group'];
            $id = intval(Arr::getGet('id'));
            $res = $this->_db->fetch_object("SELECT Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $group . "' AND Sektion = '" . $id . "' LIMIT 1");
            if (is_object($res)) {
                $perms = explode(',', $res->Rechte_Admin);
                if (in_array('all', $perms) || in_array('adminpanel', $perms)) {
                    $perms_arr = explode(',', $res->Rechte_Admin);
                    foreach ($perms_arr as $perm) {
                        $_SESSION['perm_admin'][$perm . $id] = 1;
                    }
                    unset($_SESSION['a_area']);
                    $_SESSION['loggedin'] = 1;
                    $_SESSION['a_area'] = $_SESSION['section_new'] = $id;
                }
            }
            $this->__object('Redir')->redirect();
        }
    }

    public function newLogin() {
        $admin_lang = !empty($_POST['lang']) ? Tool::cleanAllow($_POST['lang']) : 'ru';
        Arr::setCookie('pre_admin_lang', $admin_lang, 3600 * 24 * 365);
        $admin_theme = !empty($_POST['theme']) ? Tool::cleanAllow($_POST['theme']) : 'standard';
        Arr::setCookie('pre_admin_theme', $admin_theme, 3600 * 24 * 365);

        if (!empty($_REQUEST['login_email_a']) && !empty($_REQUEST['login_pass_a'])) {
            $login_email = Tool::cleanMail($_REQUEST['login_email_a']);
            $login_pass = Tool::getPass($_REQUEST['login_pass_a']);
            $row = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_benutzer WHERE Email = '{$this->_db->escape($login_email)}' AND Kennwort = '{$this->_db->escape($login_pass)}' AND Aktiv = '1' LIMIT 1");
            if (isset($row->Email, $row->Kennwort) && $row->Email == $login_email && $row->Kennwort == $login_pass) {
                $area = intval($_REQUEST['area']);
                $row_perm = $this->_db->fetch_object("SELECT Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $row->Gruppe . "' AND Sektion = '" . $area . "' LIMIT 1");
                if (is_object($row_perm)) {
                    $perms_arr = explode(',', $row_perm->Rechte_Admin);
                    foreach ($perms_arr as $perm) {
                        $_SESSION['perm_admin'][$perm . $area] = 1;
                    }
                }
                if ($row->Gruppe == 1) {
                    $_SESSION['perm']['adminpanel' . $area] = 1;
                }
                if (perm('adminpanel') || (isset($_SESSION['perm']['adminpanel' . $area]) && $_SESSION['perm']['adminpanel' . $area] == 1)) {
                    $cookie_time = 0;
                    if (Arr::getRequest('save_logindata') == 1) {
                        $cookie_time = 3600 * 24 * 7;
                        Arr::setCookie('admin_remember', 1, $cookie_time);
                    }
                    Arr::setCookie('admin_email', $row->Email, $cookie_time);
                    Arr::setCookie('admin_pass', $row->Kennwort, $cookie_time);
                    Arr::setCookie('admin_lang', $admin_lang, $cookie_time);
                    Arr::setCookie('admin_theme', $admin_theme, $cookie_time);

                    $_SESSION['admin_lang'] = $admin_lang;
                    $_SESSION['admin_theme'] = $admin_theme;
                    $_SESSION['loggedin'] = 1;
                    $_SESSION['a_area'] = $_SESSION['section_new'] = $area;
                    $_SESSION['benutzer_id'] = $row->Id;
                    $_SESSION['login_email'] = $row->Email;
                    $_SESSION['login_pass'] = $row->Kennwort;
                    $_SESSION['user_name'] = $row->Benutzername;
                    $_SESSION['user_group'] = $row->Gruppe;
                    SX::syslog('Панель управления, удачная авторизация: ' . $row->Email, '0', $row->Id);
                    $this->__object('Redir')->redirect('index.php?do=main');
                } else {
                    SX::syslog('Панель управления, неудачная авторизация: ' . $login_email . ' (Нет прав доступа в админ панель)', '0', $_SESSION['benutzer_id']);
                    $this->_view->assign('message', $this->_lang['NoPermSection']);
                }
            } else {
                SX::syslog('Панель управления, неудачная авторизация: ' . $login_email, '0', $_SESSION['benutzer_id']);
                unset($_SESSION['perm_admin'], $_SESSION['user_group']);
                $this->_view->assign('message', $this->_lang['LoginFalse']);
            }
        } else {
            $this->_view->assign('message', $this->_lang['LoginFalse']);
        }
        $this->_view->assign('themes', $this->listThemes());
        $this->_view->assign('sections', $this->listSections());
        $this->_view->assign('languages', $this->listLanguages());
        $this->_view->content('/login/login.tpl');
    }

}
