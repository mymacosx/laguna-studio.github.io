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

class AdminCore extends Magic {

    protected $_settings = array();
    protected $_locales = array();

    public function __construct() {
        if (!empty($_POST)) {
            array_walk_recursive($_POST, array('Tool', 'patchPrefix'));
        }
        if (!empty($_GET)) {
            array_walk_recursive($_GET, array('Tool', 'patchPrefix'));
        }
        if (!empty($_REQUEST)) {
            array_walk_recursive($_REQUEST, array('Tool', 'patchPrefix'));
        }
        if (!empty($_FILES)) {
            array_walk_recursive($_FILES, array('Tool', 'patchPrefix'));
        }
    }

    public function noAccess() {
        if (Arr::getSession('loggedin') == 1) {
            if (Arr::getRequest('noout') == 1) {
                $out = SX::$lang['NoPermAction'];
            } else {
                $this->_view->content('/other/no_perm.tpl');
                $out = $this->_view->fetch(THEME . '/' . (Arr::getRequest('noframes') == 1 ? 'noframes.tpl' : 'main.tpl'));
            }
            SX::output($out, true);
        }
        $this->logout();
    }

    /* Устанавливаем код языка в сессии */
    public function sessionLang() {
        $lang = Arr::getRequest('lang');
        if (!empty($lang) && is_file(LANG_DIR . '/' . $lang . '/admin.txt')) {
            $_SESSION['admin_lang'] = $lang;
        } elseif (empty($_SESSION['admin_lang'])) {
            $_SESSION['admin_lang'] = $this->_settings['default'];
        }
        $_REQUEST['lang'] = $_SESSION['admin_lang'];
    }

    /* Устанавливаем номер языка в сессии */
    public function sessionLangcode() {
        $codes = array_flip($this->_settings['lang']);
        $_SESSION['admin_lang_num'] = $codes[$_SESSION['admin_lang']];
    }

    /* Устанавливаем код языка */
    public function getLangcode() {
        return !empty($_REQUEST['langcode']) ? intval($_REQUEST['langcode']) : 1;
    }

    /* Устанавливаем секцию */
    public function setSection() {
        if (!empty($_SESSION['section_new'])) {
            $area = intval($_SESSION['section_new']);
        } else {
            $_REQUEST['area'] = (!empty($_REQUEST['area']) && is_numeric($_REQUEST['area'])) ? intval($_REQUEST['area']) : '1';
            $area = !empty($_SESSION['a_area']) ? $_SESSION['a_area'] : $_REQUEST['area'];
        }
        $_SESSION['a_area'] = $area;
    }

    /* Устанавливаем шаблон админки */
    public function theme() {
        $theme = Arr::getSession('admin_theme');
        $theme = !empty($theme) && is_dir(SX_DIR . '/admin/theme/' . $theme) ? $theme : 'standard';
        SX::set('options.theme', $theme);
        SX::setDefine('THEME', SX_DIR . '/admin/theme/' . $theme);
    }

    /* Метод олучения списка шаблонов вывода на сайте */
    public function templates($dir) {
        $tpl = array();
        $files = glob(SX_DIR . '/theme/' . $dir . '/page/*.tpl');
        foreach ($files as $file) {
            $tpl[] = basename($file);
        }
        return $tpl;
    }

    /* Переключение шаблонов */
    public function switchTheme() {
        if (isset($_SESSION['user_group']) && $_SESSION['user_group'] != 2) {
            $themes = glob(SX_DIR . '/admin/theme/*', GLOB_ONLYDIR);
            $themes = array_map('basename', $themes);
            $this->_view->assign('themes', $themes);
            return $this->_view->fetch(THEME . '/navigation/theme_switch.tpl');
        }
        return '';
    }

    /* Переключение секции */
    public function switchSection() {
        if (isset($_SESSION['user_group']) && $_SESSION['user_group'] != 2) {
            $section = '';
            $sections = array();
            $sql = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_sektionen ORDER BY Id ASC");
            while ($row = $sql->fetch_object()) {
                $res_p = $this->_db->fetch_object("SELECT Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Sektion='$row->Id' AND Gruppe='" . $_SESSION['user_group'] . "' LIMIT 1");
                $perms = explode(',', $res_p->Rechte_Admin);
                if (in_array('all', $perms) || in_array('adminpanel', $perms)) {
                    $sections[] = $row;
                    $section++;
                }
            }
            $sql->close();
            $this->_view->assign('sections', $sections);
            return $this->_view->fetch(THEME . '/navigation/section_switch.tpl');
        }
        return '';
    }

    /* Загрузка меню и ленгов доступных модулей */
    public function naviModules() {
        $modul_navi = array();
        $files = glob(MODUL_DIR . '/*', GLOB_ONLYDIR);
        foreach ($files as $file) {
            $file = basename($file);
            $active = SX::get('admin_active.' . $file);
            if (!empty($active) && is_file(MODUL_DIR . '/' . $file . '/admin/templates/navielements.tpl')) {
                $this->loadLang($file);
                $tpl['Modul'] = MODUL_DIR . '/' . $file . '/admin/templates/navielements.tpl';
                $modul_navi[] = $tpl;
            }
        }
        $this->_view->assign('modul_navi', $modul_navi);
        $this->_view->content('/settings/navi_modul.tpl');
    }

    /* Загрузка ленгов доступных модулей */
    public function loadLang($name) {
        $file = MODUL_DIR . '/' . $name . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt';
        if (!is_file($file)) {
            $file = MODUL_DIR . '/' . $name . '/lang/' . $this->_settings['default'] . '/admin.txt';
        }
        SX::loadLang($file);
    }

    /* Проверка прав и подключение модуля */
    public function extensions() {
        if (!perm('adminpanel') || $_SESSION['benutzer_id'] == 0 || $_SESSION['user_group'] == 2 || $_SESSION['loggedin'] != 1) {
            $include = SX_DIR . '/admin/action/login.php';
        } else {
            $ext = !empty($_REQUEST['do']) ? Tool::cleanString(Arr::getRequest('do'), '_') : 'main';
            if (admin_active($ext) && is_file(MODUL_DIR . '/' . $ext . '/admin/action.php')) {
                $include = MODUL_DIR . '/' . $ext . '/admin/action.php';
            } else if (is_file(SX_DIR . '/admin/action/' . $ext . '.php')) {
                $include = SX_DIR . '/admin/action/' . $ext . '.php';
            } else {
                $include = SX_DIR . '/admin/action/main.php';
            }
        }
        include $include;
    }

    /* Отдаем в шаблон доступные языки */
    public function languages() {
        $array = array();
        $sql = $this->_db->query("SELECT Id, Sprache, Sprachcode FROM " . PREFIX . "_sprachen");
        while ($row = $sql->fetch_object()) {
            $array['code'][$row->Id] = $row->Sprachcode;
            $array['name'][$row->Id] = $row->Sprache;
        }
        $sql->close();
        $this->_view->assign('language', $array);
    }

    /* Выход из админ панели */
    public function logout() {
        if (Arr::getGet('logout') == 1) {
            Arr::delCookie('admin_email');
            Arr::delCookie('admin_pass');
            Arr::setCookie('admin_remember', '-1', 3600 * 24 * 7);
            unset($_SESSION['admin_lang'], $_SESSION['admin_lang_num'], $_SESSION['login_email'], $_SESSION['login_pass']);
            unset($_SESSION['benutzer_id'], $_SESSION['loggedin'], $_SESSION['all' . $_SESSION['a_area']], $_SESSION['a_area']);
            SX::object('Redir')->redirect('index.php?do=login');
        }
    }

    /* Проверка разрешен ли IP для доступа в админ панель */
    public function checkIp() {
        $ip = SX::get('admin.Login_Ip');
        if (!empty($ip)) {
            $admin_ip = explode(',', str_replace(array("\r\n", "\n"), ',', trim($ip)));
            if (!in_array(IP_USER, $admin_ip)) {
                $this->_view->assign('no_ip', 1);
                $this->_view->assign('message', SX::$lang['LoginNoIp']);
                $this->_view->content('/login/login.tpl');
                $out = $this->_view->fetch(THEME . '/login.tpl');
                SX::output($out, true);
            }
        }
    }

    /* Проверяем разрешен ли доступ системой бана */
    public function access() {
        SX::checkBanned();
        if ($_SESSION['banned'] == 1) {
            $this->_view->assign('no_ip', 1);
            $this->_view->assign('message', SX::$lang['NoGlobalAccess']);
            $this->_view->content('/login/login.tpl');
            $out = $this->_view->fetch(THEME . '/login.tpl');
            SX::output($out, true);
        }
    }

    /* Установка прав доступа */
    public function permisson() {
        if ($_SESSION['user_group'] != 2 && isset($_SESSION['benutzer_id']) && $_SESSION['loggedin'] == 1) {
            $area = $_SESSION['a_area'];
            unset($_SESSION['perm'], $_SESSION['perm_admin']);
            $row_perm = $this->_db->cache_fetch_object("SELECT Rechte, Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $_SESSION['user_group'] . "' AND Sektion = '$area' LIMIT 1");
            if (!is_object($row_perm)) {
                $row_perm = $this->_db->cache_fetch_object("SELECT Rechte, Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $_SESSION['user_group'] . "' AND Sektion = '1' LIMIT 1");
                $_SESSION['a_area'] = 1;
            }
            $perms_arr = explode(',', $row_perm->Rechte);
            foreach ($perms_arr as $perm) {
                $_SESSION['perm'][$perm . $area] = 1;
            }
            $perms_arr = explode(',', $row_perm->Rechte_Admin);
            foreach ($perms_arr as $perm) {
                $_SESSION['perm_admin'][$perm . $area] = 1;
            }
        }
    }

    public function backurl() {
        SX::object('Redir')->redirect(base64_decode($_REQUEST['backurl']));
    }

    public function mktime($value, $num = 0, $num2 = 0, $num3 = 0) {
        list($val, $val2, $val3) = explode('.', $value) + array(0 => 0, 1 => 0, 2 => 0);
        return mktime($num, $num2, $num3, $val2, $val, $val3);
    }

    public function script($action, $time = 3000, $message = '') {
        $start = '<script type="text/javascript">' . PE . '<!-- //' . PE;
        $finish = '//-->' . PE . '</script>' . PE;
        switch ($action) {
            case 'close':
                $text = $start . 'parent.location.href = parent.location;' . $finish;
                SX::output($text, true);
                break;
            case 'save':
                $result = '$(\'#com_loader\').hide();' . PE;
                $result .= 'showNotice(\'<h3>' . SX::$lang['GlobalOk'] . '</h3>\', ' . $time . ', false);' . PE;
                $this->_view->assign('mesage_save', $start . $result . $finish);
                break;
            case 'message':
                $result = '$(\'#com_loader\').hide();' . PE;
                $result .= 'showNotice(\'<h3 style="width:auto">' . $message . '</h3>\', ' . $time . ', false);' . PE;
                $this->_view->assign('mesage_save', $start . $result . $finish);
                break;
        }
    }

    /* Формируем имя для вывода справки по разделу */
    public function helpQuery() {
        $val = str_replace('=', '_', $_SERVER['QUERY_STRING']);
        $val = explode('&', $val);
        $val[1] = (!empty($val[1]) && strpos($val[1], 'sub') !== false) ? '_' . $val[1] : '_sub_default';
        return !empty($val[0]) ? Tool::cleanAllow($val[0] . $val[1]) : '';
    }

    public function getContents($a) {
        $query = $this->_db->fetch_object_all("SELECT Id, Sektion, Titel1 FROM " . PREFIX . "_content WHERE Sektion = '" . $this->_db->escape($a) . "' ORDER BY Titel1 ASC ");
        return $query;
    }

    /* Получаем список видео файлов */
    public function getVideos($a) {
        $query = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_videos WHERE Sektion = '" . $this->_db->escape($a) . "' ORDER BY Name ASC ");
        return $query;
    }

    /* Получаем список аудио файлов */
    public function getAudios($a) {
        $query = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_audios WHERE Sektion = '" . $this->_db->escape($a) . "' ORDER BY Name ASC ");
        return $query;
    }

    public function categsGallery($area) {
        $categs = array();
        $query = $this->_db->query("SELECT Id, Name_1 AS CategName FROM " . PREFIX . "_galerie_kategorien WHERE Sektion = '" . $this->_db->escape($area) . "' ORDER BY Name_1 ASC ");
        while ($row = $query->fetch_object()) {
            $row->Gals = $this->_db->fetch_object_all("SELECT Id AS GalId, Name_1 AS GalName FROM " . PREFIX . "_galerie WHERE Kategorie = '$row->Id' ORDER BY Name_1 ASC ");
            $categs[] = $row;
        }
        $query->close();
        return $categs;
    }

    public function getContactforms() {
        $cforms = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_kontakt_form WHERE Aktiv='1' ORDER BY Id ASC");
        return $cforms;
    }

    public function getNavigation($a = 1) {
        $navis = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_navi_cat WHERE Sektion='" . $this->_db->escape($a) . "' ORDER BY Name_1 ASC");
        while ($row = $sql->fetch_object()) {
            $row->Items = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_navi WHERE ParentId='0' AND NaviCat='$row->Id' ORDER BY Titel_1 ASC");
            $navis[] = $row;
        }
        $sql->close();
        return $navis;
    }

    public function limit($limit = 15) {
        $_REQUEST['pp'] = $limit = !empty($_REQUEST['pp']) ? $_REQUEST['pp'] : $limit;
        return intval($limit);
    }

    public function format($size, $round = 0) {
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        for ($i = 0, $count = count($sizes); $size > 1024 && $i < $count - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $round) . $sizes[$i];
    }

    public function sizeBytes($val) {
        $val = trim($val);
        $last = strtolower($val{strlen($val) - 1});
        $val = intval($val);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    public function postMaxsize() {
        $size = ini_get('post_max_size');
        return !empty($size) ? $this->sizeBytes($size) : 6 * 1024 * 1024;
    }

    public function postMaxsizeMb() {
        return $this->format($this->postMaxsize());
    }

    public function groups() {
        $groups = $this->_db->fetch_object_all("SELECT Id, Name_Intern, Name FROM " . PREFIX . "_benutzer_gruppen ORDER BY Id ASC");
        return $groups;
    }

    public function countComments($where, $object) {
        $res = $this->_db->cache_fetch_object("SELECT DISTINCT(Objekt_Id) AS CCount FROM " . PREFIX . "_kommentare WHERE Bereich='" . $this->_db->escape($where) . "' AND Objekt_Id='" . $this->_db->escape($object) . "'");
        return is_object($res) ? $res->CCount : 0;
    }

    public function pagination($anzahl_seiten, $tpl_off) {
        $nav = '';
        $aktuelle_seite = Tool::prePage();
        $tpl_on = Tool::aktPage();
        $seiten = array($aktuelle_seite - 4, $aktuelle_seite - 3, $aktuelle_seite - 2, $aktuelle_seite - 1, $aktuelle_seite, $aktuelle_seite + 1, $aktuelle_seite + 2, $aktuelle_seite + 3, $aktuelle_seite + 4);

        $seiten = array_unique($seiten);
        if ($anzahl_seiten > 1) {
            $nav = str_replace('{t}', SX::$lang['NavStart'], str_replace('{s}', 1, $tpl_off));
        }
        if ($aktuelle_seite > 1) {
            $nav .= str_replace('{t}', SX::$lang['NavBack'], str_replace('{s}', ($aktuelle_seite - 1), $tpl_off));
        }
        foreach ($seiten as $key => $val) {
            if ($val >= 1 && $val <= $anzahl_seiten) {
                if ($aktuelle_seite == $val) {
                    $nav .= str_replace(array('{s}', '{t}'), $val, '<span class="page_active">' . $tpl_on . '</span>');
                } else {
                    $nav .= str_replace(array('{s}', '{t}'), $val, $tpl_off);
                }
            }
        }

        if ($aktuelle_seite < $anzahl_seiten) {
            $nav .= str_replace('{t}', SX::$lang['NavNext'], str_replace('{s}', ($aktuelle_seite + 1), $tpl_off));
        }
        if ($anzahl_seiten > 1) {
            $nav .= str_replace('{t}', SX::$lang['NavEnd'] . '  (' . $anzahl_seiten . ')', str_replace('{s}', $anzahl_seiten, $tpl_off));
        }
        return $nav;
    }

    public function checkLogin() {
        if (!Arr::nilCookie('admin_email') && !Arr::nilCookie('admin_pass')) {
            $login_email = Tool::cleanMail(Arr::getCookie('admin_email'));
            $login_pass = Tool::getPass(Arr::getCookie('admin_pass'), false);
            $row = $this->_db->fetch_object("SELECT
                *
            FROM
                " . PREFIX . "_benutzer
            WHERE
                    Email = '" . $this->_db->escape($login_email) . "'
            AND
                    Kennwort = '" . $this->_db->escape($login_pass) . "'
            AND
                    Aktiv = '1' LIMIT 1");

            if (isset($row->Email, $row->Kennwort) && $row->Email == $login_email && $row->Kennwort == $login_pass) {
                $area = Arr::getRequest('area');
                if (isset($_SESSION['a_area']) && empty($area)) {
                    $area = $_SESSION['a_area'];
                }
                $area = intval($area);
                $row_perm = $this->_db->fetch_object("SELECT Rechte_Admin FROM " . PREFIX . "_berechtigungen WHERE Gruppe = '" . $row->Gruppe . "' AND Sektion = '$area' LIMIT 1");

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
                    $admin_lang = Tool::cleanAllow(Arr::getRequest('lang', Arr::getCookie('admin_lang', 'ru')));
                    $admin_theme = Tool::cleanAllow(Arr::getRequest('theme', Arr::getCookie('admin_theme', 'standard')));
                    $time = Arr::getCookie('admin_remember') == 1 ? 3600 * 24 * 7 : 0;
                    Arr::setCookie('admin_email', $row->Email, $time);
                    Arr::setCookie('admin_pass', $row->Kennwort, $time);
                    Arr::setCookie('admin_lang', $admin_lang, $time);
                    Arr::setCookie('admin_theme', $admin_theme, $time);

                    $_SESSION['admin_lang'] = $admin_lang;
                    $_SESSION['admin_theme'] = $admin_theme;
                    $_SESSION['loggedin'] = 1;
                    $_SESSION['a_area'] = $area;
                    $_SESSION['benutzer_id'] = $row->Id;
                    $_SESSION['login_email'] = $row->Email;
                    $_SESSION['login_pass'] = $row->Kennwort;
                    $_SESSION['user_name'] = $row->Benutzername;
                    $_SESSION['user_group'] = $row->Gruppe;
                }
            }
        }
    }

    public function settings() {
        $area = $_SESSION['a_area'];
        $query = "SELECT *, Aktiv_Section_" . $area . " AS Aktiv FROM " . PREFIX . "_bereiche ; ";
        $query .= "SELECT Id, Sprachcode, Locale FROM " . PREFIX . "_sprachen_admin WHERE Aktiv = 1 ORDER BY Posi ASC ; ";
        $query .= "SELECT Template FROM " . PREFIX . "_sektionen WHERE Id = '" . $area . "'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                while ($active = $result->fetch_assoc()) {
                    if ($active['Type'] == 'widget') {
                        $active['Name'] = 'widget_' . $active['Name'];
                    }
                    $this->_settings['active'][$active['Name']] = $active['Aktiv'];
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                while ($row = $result->fetch_assoc()) {
                    $this->_locales[$row['Sprachcode']] = $row['Locale'];
                    $this->_settings['lang'][$row['Id']] = $row['Sprachcode'];
                }
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $row = $result->fetch_assoc();
                $this->_settings['template'] = $row['Template'];
                $result->close();
            }
        }
        $this->_settings['default'] = current($this->_settings['lang']);
        SX::set('active', $this->_settings['active']);
        SX::set('admin_active', $this->_settings['active']);
        SX::set('options.template', $this->_settings['template']);
    }

    public function getLocale() {
        $value = $_SESSION['admin_lang'];
        return isset($this->_locales[$value]) ? $this->_locales[$value] : null;
    }

}