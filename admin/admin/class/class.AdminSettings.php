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

class AdminSettings extends Magic {

    protected $area;

    public function __construct() {
        $this->area = $_SESSION['a_area'];
    }

    public function settingsSecure() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'active'    => $_POST['active'],
                'gd'        => $_POST['gd'],
                'ttf_font'  => $_POST['ttf_font'],
                'max_calc1' => $_POST['max_calc1'],
                'max_calc2' => $_POST['max_calc2'],
                'min_text'  => $_POST['min_text'],
                'max_text'  => $_POST['max_text'],
                'type'      => $_POST['type'],
                'text'      => $_POST['text'],
            );
            SX::save('secure', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки капчи', '0', $this->UserId);

            $this->__object('AdminCore')->script('save');
            SX::load('secure');
        }
        $this->_view->assign('row', SX::get('secure'));
        $this->_view->assign('title', $this->_lang['SecureSettings']);
        $this->_view->content('/settings/secure_settings.tpl');
    }

    public function emailCheck() {
        SX::setMail(array(
            'globs'     => '1',
            'to'        => $_REQUEST['f'],
            'to_name'   => SX::get('system.Mail_Name'),
            'text'      => 'Настройки системы правильные!',
            'subject'   => 'Тестирование настроек системы отправки писем',
            'fromemail' => $_REQUEST['f'],
            'from'      => 'Панель управления SX CMS',
            'type'      => 'text',
            'attach'    => '',
            'html'      => '',
            'prio'      => 1));
        $m = '<div style="padding:5px"><h3 style="color:green">Для проверки настроек на Ваш ящик оправлено письмо!</h3><br />Проверьте Вашу почту, чтобы убедится что тестовое письмо действительно доставлено<br /><br /><input type="button" class="button" value="Закрыть окно" onclick="window.close();"> </div>';
        $this->_view->assign('content', $m);
    }

    public function extModul($array) {
        $modul = array();
        if (($handle = opendir(MODUL_DIR . '/'))) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $array) && !in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_dir(MODUL_DIR . '/' . $file)) {
                    if (is_file(MODUL_DIR . '/' . $file . '/admin/action.php')) {
                        SX::loadLang(MODUL_DIR . '/' . $file . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt');
                        $row = array();
                        $row['BName'] = SX::$lang['module_' . $file];
                        $row['ModulInf'] = SX::$lang['module_inf_' . $file];
                        $row['Typ'] = SX::$lang['ExtModul'];
                        $row['Type'] = 'extmodul';
                        $row['Modul'] = $file;
                        $modul[] = $row;
                    }
                }
            }
            closedir($handle);
        }
        return $modul;
    }

    /* Обновляем корневой .htaccess при следующим обращении к сайту */
    protected function setHtaccess() {
        SX::save('system', array('Seo_Sprachen' => ''));
    }

    public function saveModul() {
        foreach (array_keys($_POST['Aktiv']) as $aktiv) {
            $array = array(
                'Aktiv_Section_' . $this->area => $_POST['Aktiv'][$aktiv]
            );
            $this->_db->update_query('bereiche', $array, "Id='" . intval($aktiv) . "'");
        }
        $this->setHtaccess();
        $this->__object('Redir')->redirect('index.php?do=settings&sub=sectionsettings');
    }

    public function installModul($modul) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($modul)) {
            $modul = Tool::cleanAllow($modul);
            if (is_file(MODUL_DIR . '/' . $modul . '/admin/install.php')) {
                SX::setDefine('INSTALL', 'install');
                include (MODUL_DIR . '/' . $modul . '/admin/install.php');
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' успешно инсталировал модуль: ' . $modul, '0', $_SESSION['benutzer_id']);
                $this->setHtaccess();
            } else {
                SX::syslog('Возникла ошибка при установке модуля: ' . $modul . ' Не найден установочный файл', '0', $_SESSION['benutzer_id']);
            }
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=sectionsettings');
    }

    public function delModul($modul) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($modul)) {
            $modul = Tool::cleanAllow($modul);
            if (is_file(MODUL_DIR . '/' . $modul . '/admin/install.php')) {
                SX::setDefine('INSTALL', 'uninstall');
                include (MODUL_DIR . '/' . $modul . '/admin/install.php');
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' успешно деинсталировал модуль: ' . $modul, '0', $_SESSION['benutzer_id']);
                $this->setHtaccess();
            } else {
                SX::syslog('Возникла ошибка при удалении модуля: ' . $modul . ' Не найден установочный файл', '0', $_SESSION['benutzer_id']);
            }
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=sectionsettings');
    }

    public function settingsWidgets() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $this->saveWidgets();
        }
        $file = array();
        $widgets = array();
        $query = $this->_db->query("SELECT *, Aktiv_Section_" . $this->area . " AS Aktiv FROM " . PREFIX . "_bereiche WHERE Type = 'widget' ORDER BY Id ASC");
        while ($row = $query->fetch_assoc()) {
            if (is_file(WIDGET_DIR . '/' . $row['Name'] . '/install/install.php')) {
                SX::loadLang(WIDGET_DIR . '/' . $row['Name'] . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt');
                $row['BName'] = SX::$lang['widget_' . $row['Name']];
                $row['WidgetInf'] = SX::$lang['widget_inf_' . $row['Name']];
                $row['Install'] = 'ok';
                $file[] = $row['Name'];
                $widgets[] = $row;
            }
        }
        $query->close();
        $this->_view->assign('widgets', array_merge($widgets, $this->loadWidgets($file)));
        $this->_view->assign('title', $this->_lang['Global_Widgets']);
        $this->_view->content('/settings/widgets_settings.tpl');
    }

    public function saveWidgets() {
        foreach (array_keys($_POST['Aktiv']) as $aktiv) {
            $array = array(
                'Aktiv_Section_' . $this->area => $_POST['Aktiv'][$aktiv]
            );
            $this->_db->update_query('bereiche', $array, "Id='" . intval($aktiv) . "'");
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=widgets');
    }

    public function loadWidgets($array) {
        $widgets = array();
        if (($handle = opendir(WIDGET_DIR . '/'))) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $array) && !in_array($file, array('.', '..', '.htaccess', 'index.php'))) {
                    if (is_file(WIDGET_DIR . '/' . $file . '/install/install.php')) {
                        SX::loadLang(WIDGET_DIR . '/' . $file . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt');
                        $row = array();
                        $row['BName'] = isset(SX::$lang['widget_' . $file]) ? SX::$lang['widget_' . $file] : '';
                        $row['WidgetInf'] = isset(SX::$lang['widget_inf_' . $file]) ? SX::$lang['widget_inf_' . $file] : '';
                        $row['Widget'] = $file;
                        $widgets[] = $row;
                    }
                }
            }
            closedir($handle);
        }
        return $widgets;
    }

    public function installWidget($widget) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($widget)) {
            $widget = Tool::cleanAllow($_REQUEST['widget']);
            if (is_file(WIDGET_DIR . '/' . $widget . '/install/install.php')) {
                SX::setDefine('INSTALL', 'install');
                include_once WIDGET_DIR . '/' . $widget . '/install/install.php';
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' успешно инсталировал виджет: ' . $widget, '0', $_SESSION['benutzer_id']);
            } else {
                SX::syslog('Возникла ошибка при установке виджета: ' . $widget . ' Не найден установочный файл', '0', $_SESSION['benutzer_id']);
            }
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=widgets');
    }

    public function delWidget($id) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $widget = Tool::cleanAllow($_REQUEST['name']);
            $this->_db->query("DELETE FROM " . PREFIX . "_bereiche WHERE Type = 'widget' AND Id = '" . intval($id) . "'");
            if (is_file(WIDGET_DIR . '/' . $widget . '/install/install.php')) {
                SX::setDefine('INSTALL', 'uninstall');
                include (WIDGET_DIR . '/' . $widget . '/install/install.php');
            }
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' деинсталировал виджет: ' . $widget, '0', $_SESSION['benutzer_id']);
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=widgets');
    }

    public function editWidget($id) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($id)) {
            $id = intval($id);
            if (Arr::getPost('save') == 1) {
                $this->saveWidget($id);
            }
            $row = $this->_db->fetch_assoc("SELECT *, Aktiv_Section_" . $this->area . " AS Aktiv FROM " . PREFIX . "_bereiche WHERE Type = 'widget' AND Id='" . $id . "' LIMIT 1");
            if (!is_file(WIDGET_DIR . '/' . $row['Name'] . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt')) {
                SX::syslog('Возникла ошибка при редактировании виджета: ' . $row['Name'] . ' Не найден языковой файл', '0', $_SESSION['benutzer_id']);
                $this->__object('Redir')->redirect('index.php?do=settings&sub=widgets');
            }

            SX::loadLang(WIDGET_DIR . '/' . $row['Name'] . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt');
            $row['BName'] = SX::$lang['widget_' . $row['Name']];

            $array = unserialize($row['Settings']);

            $settings = array();
            foreach ((array) $array as $key => $value) {
                $settings[$key] = $this->fieldWidget($row['Name'], $key, $value);
            }

            $this->_view->assign('widget', $row);
            $this->_view->assign('witget_settings', $settings);
            $this->_view->assign('title', SX::$lang['WidgetEdit']);
            $this->changeWidget($row['Name']);
        }
    }

    /* Метод формирует значения полей настроек */
    protected function fieldWidget($name, $key, $value) {
        $array = array();
        $sett = 'widget_' . $name . '_' . $key;
        $sett_inf = 'widget_inf_' . $name . '_' . $key;
        $array['widget'] = isset(SX::$lang[$sett]) ? SX::$lang[$sett] : SX::$lang['Global_Error'];
        $array['widget_inf'] = isset(SX::$lang[$sett_inf]) ? SX::$lang[$sett_inf] : '';
        $array['key'] = $key;
        $array['value'] = $value;
        return $array;
    }

    /* Метод подключает альтернативный шаблон редактирования фиджета */
    protected function changeWidget($name) {
        $tpl = WIDGET_DIR . '/' . $name . '/tpl/' . $name . '_edit.tpl';
        if (!is_file($tpl)) {
            $tpl = THEME . '/settings/widget_edit.tpl';
        }
        return $this->_view->assign('content', $this->_view->fetch($tpl));
    }

    public function saveWidget($id) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (!empty($_POST['witget_settings'])) {
            $array = array(
                'Result'                       => $_POST['Result'],
                'Settings'                     => serialize($_POST['witget_settings']),
                'Aktiv_Section_' . $this->area => $_POST['Aktiv'],
            );
            $this->_db->update_query('bereiche', $array, "Id='" . intval($id) . "'");
        }
        $this->__object('AdminCore')->script('save');
    }

    public function settingsSection() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $this->saveModul();
        }
        $file = $bereiche = array();
        $query = $this->_db->query("SELECT *, Aktiv_Section_" . $this->area . " AS Aktiv FROM " . PREFIX . "_bereiche WHERE Type = 'modul' OR Type = 'extmodul' ORDER BY Id ASC");
        while ($row = $query->fetch_assoc()) {
            if (!empty($row['Link'])) {
                $row['Link'] = str_replace('&amp;', '&', $row['Link']);
                $row['Link'] = str_replace('&', '&amp;', $row['Link']);
                $row['Link'] = str_replace('__SECTION__', $this->area, $row['Link']);
            }
            if ($row['Type'] == 'modul') {
                $row['BName'] = $this->_lang['Sections_' . $row['Name']];
                $row['Typ'] = $this->_lang['IntModul'];
                $row['Install'] = 'ok';
                $bereiche[] = $row;
            } elseif ($row['Type'] == 'extmodul') {
                if (is_file(MODUL_DIR . '/' . $row['Name'] . '/admin/action.php')) {
                    SX::loadLang(MODUL_DIR . '/' . $row['Name'] . '/lang/' . $_SESSION['admin_lang'] . '/admin.txt');
                    $row['BName'] = SX::$lang['module_' . $row['Name']];
                    $row['ModulInf'] = SX::$lang['module_inf_' . $row['Name']];
                    $row['Typ'] = SX::$lang['ExtModul'];
                    $row['Install'] = 'ok';
                    $file[] = $row['Name'];
                    $bereiche[] = $row;
                }
            }
        }
        $query->close();
        $this->_view->assign('bereiche', array_merge($bereiche, $this->extModul($file)));
        $this->_view->assign('title', $this->_lang['Global_SettingsSections']);
        $this->_view->content('/settings/section_settings.tpl');
    }

    public function addSection($name) {
        if (perm('settings')) {
            if (!empty($name)) {
                $name = Tool::cleanAllow($name, ' ');
                $erg = $this->_db->fetch_assoc("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '1' LIMIT 1");

                $insert_array = array();
                foreach ($erg as $i => $fid) {
                    if ($i == 'Name' || $i == 'Name_2' || $i == 'Name_3') {
                        $fid = $name;
                    }
                    if ($i != 'Id') {
                        $insert_array[$i] = $fid;
                    }
                }
                $this->_db->insert_query('sektionen', $insert_array);
                $Iid = $this->_db->insert_id();

                $this->_db->insert_query('shop_eigenschaften', array('Sektion' => $Iid));

                $this->_db->query("ALTER TABLE " . PREFIX . "_bereiche ADD Aktiv_Section_{$Iid} ENUM('0','1') NOT NULL DEFAULT '1'");
                $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_berechtigungen WHERE Sektion = '1'");
                while ($row = $sql->fetch_object()) {
                    $insert_array = array(
                        'Gruppe'       => $row->Gruppe,
                        'Sektion'      => $Iid,
                        'Rechte'       => $row->Rechte,
                        'Rechte_Admin' => $row->Rechte_Admin);
                    $this->_db->insert_query('berechtigungen', $insert_array);
                }
                $sql->close();
            }
            $this->__object('Redir')->redirect('index.php?do=settings&sub=sectionsdisplay');
        }
    }

    public function deleteSection($id) {
        if ($id != 1) {
            $id = intval($id);
            $sql = $this->_db->query("SELECT Id FROM " . PREFIX . "_galerie WHERE Sektion = '" . $id . "'");
            while ($row = $sql->fetch_object()) {
                $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilder WHERE Galerie_Id = '" . $row->Id . "'");
                $this->_db->query("DELETE FROM " . PREFIX . "_galerie_bilderfavoriten WHERE Galerie_Id = '" . $row->Id . "'");
            }
            $sql->close();
            $this->_db->query("ALTER TABLE " . PREFIX . "_bereiche DROP COLUMN Aktiv_Section_{$id}");
            $this->_db->query("DELETE FROM " . PREFIX . "_galerie WHERE Sektion = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_berechtigungen WHERE Sektion = '" . $id . "'");
            $this->_db->query("ALTER TABLE " . PREFIX . "_berechtigungen AUTO_INCREMENT = 1");
            $this->_db->query("DELETE FROM " . PREFIX . "_quicknavi WHERE Sektion = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_navi WHERE Id = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_navi_cat WHERE Id = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_sektionen WHERE Id = '" . $id . "'");
            $this->_db->query("ALTER TABLE " . PREFIX . "_sektionen AUTO_INCREMENT = 1");
            $this->_db->query("DELETE FROM " . PREFIX . "_shop_eigenschaften WHERE Sektion = '" . $id . "'");
            $this->_db->query("ALTER TABLE " . PREFIX . "_shop_eigenschaften AUTO_INCREMENT = 1");
            $this->_db->query("DELETE FROM " . PREFIX . "_news WHERE Sektion = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_news_kategorie WHERE Sektion = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_roadmap WHERE Sektion = '" . $id . "'");
            $this->_db->query("DELETE FROM " . PREFIX . "_roadmap_tickets WHERE Sektion = '" . $id . "'");
        }
        $this->__object('Redir')->redirect('index.php?do=settings&sub=sectionsdisplay');
    }

    public function showSection($sec) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $sec = is_numeric($sec) ? intval($sec) : 1;
        if (Arr::getRequest('secaction') == 'edit') {
            if (Arr::getPost('save') == 1) {
                $array = array(
                    'Name'              => Arr::getPost('Name'),
                    'Aktiv'             => Arr::getPost('Aktiv'),
                    'Meldung'           => Arr::getPost('Meldung'),
                    'LimitNews'         => Arr::getPost('LimitNews'),
                    'LimitNewsArchive'  => Arr::getPost('LimitNewsArchive'),
                    'LimitNewlinks'     => Arr::getPost('LimitNewlinks'),
                    'LimitNewDownloads' => Arr::getPost('LimitNewDownloads'),
                    'LimitNewProducts'  => Arr::getPost('LimitNewProducts'),
                    'LimitNewCheats'    => Arr::getPost('LimitNewCheats'),
                    'LimitNewGalleries' => Arr::getPost('LimitNewGalleries'),
                    'LimitLastPosts'    => Arr::getPost('LimitLastPosts'),
                    'LimitLastThreads'  => Arr::getPost('LimitLastThreads'),
                    'LimitTopArticles'  => Arr::getPost('LimitTopArticles'),
                    'LimitTopcontent'   => Arr::getPost('LimitTopcontent'),
                    'Tpl_shop'          => Arr::getPost('Tpl_shop'),
                    'Tpl_content'       => Arr::getPost('Tpl_content'),
                    'Tpl_news'          => Arr::getPost('Tpl_news'),
                    'Tpl_newsletter'    => Arr::getPost('Tpl_newsletter'),
                    'Tpl_newsarchive'   => Arr::getPost('Tpl_newsarchive'),
                    'Tpl_index'         => Arr::getPost('Tpl_index'),
                    'Tpl_sitemap'       => Arr::getPost('Tpl_sitemap'),
                    'Tpl_useraction'    => Arr::getPost('Tpl_useraction'),
                    'Tpl_calendar'      => Arr::getPost('Tpl_calendar'),
                    'Tpl_faq'           => Arr::getPost('Tpl_faq'),
                    'Tpl_gallery'       => Arr::getPost('Tpl_gallery'),
                    'Tpl_articles'      => Arr::getPost('Tpl_articles'),
                    'Tpl_products'      => Arr::getPost('Tpl_products'),
                    'Tpl_downloads'     => Arr::getPost('Tpl_downloads'),
                    'Tpl_links'         => Arr::getPost('Tpl_links'),
                    'Tpl_register'      => Arr::getPost('Tpl_register'),
                    'Tpl_misc'          => Arr::getPost('Tpl_misc'),
                    'Tpl_forums'        => Arr::getPost('Tpl_forums'),
                    'Tpl_members'       => Arr::getPost('Tpl_members'),
                    'Tpl_pn'            => Arr::getPost('Tpl_pn'),
                    'Tpl_pwlost'        => Arr::getPost('Tpl_pwlost'),
                    'Tpl_manufacturer'  => Arr::getPost('Tpl_manufacturer'),
                    'Tpl_cheats'        => Arr::getPost('Tpl_cheats'),
                    'Tpl_poll'          => Arr::getPost('Tpl_polls'),
                    'Tpl_guestbook'     => Arr::getPost('Tpl_guestbook'),
                    'Tpl_imprint'       => Arr::getPost('Tpl_imprint'),
                    'Tpl_search'        => Arr::getPost('Tpl_search'),
                    'Template'          => Arr::getPost('Template'),
                    'CSS_Theme'         => Arr::getPost('CSS_Theme'),
                    'Passwort'          => Arr::getPost('Passwort'),
                    'StartText'         => Arr::getPost('StartText'),
                    'ZeigeStartText'    => Arr::getPost('ZeigeStartText'),
                    'ZeigeStartTextNur' => Arr::getPost('ZeigeStartTextNur'),
                    'Domains'           => Arr::getPost('Domains'),
                );
                $this->_db->update_query('sektionen', $array, "Id = '" . $sec . "'");
                $this->__object('AdminCore')->script('save');
            }

            $res = $this->_db->fetch_object("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '" . $sec . "' LIMIT 1");

            $folders = array();
            $d = SX_DIR . '/theme/';
            $handle = opendir($d);
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_dir($d . $file)) {
                    $f = new stdClass;
                    $f->Name = $file;
                    $folders[] = $f;
                }
            }
            closedir($handle);

            $this->_view->assign('StartText', $this->__object('Editor')->load('admin', $res->StartText, 'StartText', 450, 'Settings'));
            $this->_view->assign('folders', $folders);
            $this->_view->assign('templates', $this->__object('AdminCore')->templates($res->Template));
            $this->_view->assign('res', $res);
            $this->_view->assign('title', $this->_lang['Sections_Edit']);
            $this->_view->content('/settings/sections.tpl');
        } else {
            $sections = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_sektionen ORDER BY Id ASC");

            $this->_view->assign('sections', $sections);
            $this->_view->assign('title', $this->_lang['Sections']);
            $this->_view->content('/settings/sectionsdisplay.tpl');
        }
    }

    public function settingsAdmin() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Ahelp'         => Arr::getPost('Ahelp'),
                'Type_Redaktor' => Arr::getPost('Type_Redaktor'),
                'EditArea'      => Arr::getPost('EditArea'),
                'Aktiv_Modul'   => Arr::getPost('Aktiv_Modul'),
                'Aktiv_Notes'   => Arr::getPost('Aktiv_Notes'),
                'Login_Ip'      => Arr::getPost('Login_Ip'),
                'Navi_Anime'    => Arr::getPost('Navi_Anime'),
                'Navi'          => Arr::getPost('Navi'));
            SX::save('admin', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('admin');
        }
        $row = SX::get('admin');
        $this->_view->assign('row', $row);
        $this->_view->assign('your_ip', IP_USER);
        $this->_view->content('/settings/admin_general.tpl');
    }

    public function settingsSystem() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'Land'                  => strtolower($_POST['Land']),
                'Logging'               => Arr::getPost('Logging'),
                'Firma'                 => Arr::getPost('Firma'),
                'Impressum'             => Arr::getPost('Impressum'),
                'Seitenname'            => Arr::getPost('Seitenname'),
                'Seitenbetreiber'       => Arr::getPost('Seitenbetreiber'),
                'Strasse'               => Arr::getPost('Strasse'),
                'Stadt'                 => Arr::getPost('Stadt'),
                'Mail_Typ'              => Arr::getPost('Mail_Typ'),
                'Mail_Port'             => Arr::getPost('Mail_Port'),
                'Mail_Host'             => Arr::getPost('Mail_Host'),
                'Mail_Auth'             => Arr::getPost('Mail_Auth', 0),
                'Mail_Type_Auth'        => Arr::getPost('Mail_Type_Auth'),
                'Mail_Username'         => Arr::getPost('Mail_Username'),
                'Mail_Passwort'         => Arr::getPost('Mail_Passwort'),
                'Mail_Sendmailpfad'     => Arr::getPost('Mail_Sendmailpfad'),
                'Mail_WordWrap'         => 80,
                'Mail_Absender'         => Arr::getPost('Mail_Absender'),
                'Mail_Name'             => Arr::getPost('Mail_Name'),
                'Mail_Fuss'             => Arr::getPost('Mail_Fuss'),
                'Mail_Header'           => Arr::getPost('Mail_Header'),
                'Mail_Fuss_HTML'        => Arr::getPost('Mail_Fuss_HTML'),
                'Spamwoerter'           => Arr::getPost('Spamwoerter'),
                'SpamRegEx'             => Arr::getPost('SpamRegEx'),
                'SysCode_Aktiv'         => Arr::getPost('SysCode_Aktiv'),
                'SysCode_Smilies'       => Arr::getPost('SysCode_Smilies'),
                'KommentarFormat'       => Arr::getPost('KommentarFormat'),
                'SysCode_Bild'          => Arr::getPost('SysCode_Bild'),
                'SysCode_Links'         => Arr::getPost('SysCode_Links'),
                'SysCode_Email'         => Arr::getPost('SysCode_Email'),
                'Kommentar_Laenge'      => Arr::getPost('Kommentar_Laenge'),
                'Kommentar_Moderiert'   => Arr::getPost('Kommentar_Moderiert'),
                'Kommentare_Seite'      => Arr::getPost('Kommentare_Seite'),
                'Kommentare_Icon'       => Arr::getPost('Kommentare_Icon'),
                'Kommentare_IconBreite' => Arr::getPost('Kommentare_IconBreite'),
                'shop_is_startpage'     => Arr::getPost('shop_is_startpage'),
                'Reg_Typ'               => Arr::getPost('Reg_Typ'),
                'Reg_Firma'             => Arr::getPost('Reg_Firma'),
                'Reg_Ust'               => Arr::getPost('Reg_Ust'),
                'Reg_Fon'               => Arr::getPost('Reg_Fon'),
                'Reg_Fax'               => Arr::getPost('Reg_Fax'),
                'Reg_Birth'             => Arr::getPost('Reg_Birth'),
                'use_seo'               => Arr::getPost('use_seo'),
                'Loesch_Gruende'        => Arr::getPost('Loesch_Gruende'),
                'Reg_Agb'               => Arr::getPost('Reg_Agb'),
                'Reg_DataPflicht'       => Arr::getPost('Reg_DataPflicht'),
                'Reg_AgbPflicht'        => Arr::getPost('Reg_AgbPflicht'),
                'meta_yandex'           => Arr::getPost('meta_yandex'),
                'code_yandex'           => Arr::getPost('code_yandex'),
                'meta_google'           => Arr::getPost('meta_google'),
                'code_google'           => Arr::getPost('code_google'),
                'analytics'             => Arr::getPost('analytics'),
                'analytics_code'        => Arr::getPost('analytics_code'),
                'birthdays_mail'        => Arr::getPost('birthdays_mail'),
                'min_page'              => Arr::getPost('min_page'),
                'gzip_page'             => Arr::getPost('gzip_page'),
                'comb_js'               => Arr::getPost('comb_js'),
                'min_js'                => Arr::getPost('min_js'),
                'gzip_js'               => Arr::getPost('gzip_js'),
                'expires_js'            => Arr::getPost('expires_js'),
                'comb_css'              => Arr::getPost('comb_css'),
                'min_css'               => Arr::getPost('min_css'),
                'gzip_css'              => Arr::getPost('gzip_css'),
                'expires_css'           => Arr::getPost('expires_css'),
                'cleanup'               => Arr::getPost('cleanup'),
                'ignore_list'           => preg_replace('!\s+!u', '', Arr::getPost('ignore_list')),
                'Reg_Bank'              => Arr::getPost('Reg_Bank'),
                'Inn'                   => Arr::getPost('Inn'),
                'Kpp'                   => Arr::getPost('Kpp'),
                'Bik'                   => Arr::getPost('Bik'),
                'Bank'                  => Arr::getPost('Bank'),
                'Kschet'                => Arr::getPost('Kschet'),
                'Rschet'                => Arr::getPost('Rschet'),
                'Zip'                   => Arr::getPost('Zip'),
                'Telefon'               => Arr::getPost('Telefon'),
                'Fax'                   => Arr::getPost('Fax'),
                'Buh'                   => Arr::getPost('Buh'),
                'Domains'               => Arr::getPost('Domains'),
                'SiteEditor'            => Arr::getPost('SiteEditor'),
                'timezone'              => Arr::getPost('timezone'),
                'Reg_Pass'              => Arr::getPost('Reg_Pass', 0),
                'Reg_Address'           => Arr::getPost('Reg_Address', 0),
                'Error_Email'           => Arr::getPost('Error_Email', 0),
                'CountTitle'            => Arr::getPost('CountTitle'),
                'CountKeywords'         => Arr::getPost('CountKeywords'),
                'CountDescription'      => Arr::getPost('CountDescription'),
                'allowed'               => Arr::getPost('allowed'),
                'Reg_AddressFill'       => (Arr::getPost('Reg_Address') == '1' ? Arr::getPost('Reg_AddressFill', 0) : '0'),
                'Reg_DataPflichtFill'   => (Arr::getPost('Reg_DataPflicht') == '1' ? Arr::getPost('Reg_DataPflichtFill', 0) : '0'));
            SX::save('system', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил глобальные настройки системы', '0', $this->UserId);

            if (Arr::getPost('use_seo') == 1) {
                $this->_view->assign('iframe_index', '<iframe width="1" height="1" style="display:none" src="../index.php"></iframe>');
            }
            $this->__object('AdminCore')->script('save');
            SX::load('system');
        }
        $row = SX::get('system');
        $file_check = SX_DIR . '/.htaccess';
        if (!is_file($file_check)) {
            $this->_view->assign('rewrite_error1', $this->_lang['RwEHtacces_ne'] . '<br />');
        }
        if (!is_writable($file_check)) {
            $this->_view->assign('rewrite_error2', $this->_lang['RwEHtacces_nw']);
        }
        if (!Tool::apacheModul('mod_rewrite')) {
            $this->_view->assign('rewrite_error1', $this->_lang['RwEHtacces_nomod']);
        }
        $this->_view->assign('row', $row);
        $this->_view->assign('timezone', $this->timezone());
        $this->_view->assign('Impressum', $this->__object('Editor')->load('admin', $row['Impressum'], 'Impressum', 350, 'Settings'));
        $this->_view->assign('Reg_Agb', $this->__object('Editor')->load('admin', $row['Reg_Agb'], 'Reg_Agb', 350, 'Basic'));
        $this->_view->assign('title', $this->_lang['Settings_general']);
        $this->_view->content('/settings/general.tpl');
    }

    /* Загружаем данные из файла со списком временных зон */
    public function timezone() {
        $file = LANG_DIR . '/' . $_SESSION['admin_lang'] . '/time.txt';
        $file = is_file($file) ? $file : LANG_DIR . '/ru/time.txt';
        return File::parse($file);
    }

    /* Метод получения имени и фамилии пользователя */
    public function user($id) {
        $row = $this->_db->cache_fetch_object("SELECT Vorname, Nachname FROM " . PREFIX . "_benutzer WHERE Id='" . intval($id) . "' LIMIT 1");
        return !empty($row) ? $row->Vorname . ' ' . $row->Nachname : '';
    }

    public function logs() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $limit = 30;

        $_REQUEST['prior'] = !empty($_REQUEST['prior']) ? $_REQUEST['prior'] : '';
        switch ($_REQUEST['prior']) {
            case 'admin':
                $ex = "WHERE Typ = '0'";
                break;
            case 'adminshop':
                $ex = "WHERE Typ = '1'";
                break;
            case 'payment':
                $ex = "WHERE Typ = '2'";
                break;
            case 'sys':
                $ex = "WHERE Typ = '3'";
                break;
            case 'mysql':
                $ex = "WHERE Typ = '4'";
                break;
            case 'erphp':
                $ex = "WHERE Typ = '5'";
                break;
            case 'seite':
                $ex = "WHERE Typ = '6'";
                break;
            default:
            case 'all':
                $ex = '';
                break;
        }

        $_REQUEST['action'] = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
        switch ($_REQUEST['action']) {
            case 'del':
                $sql = $this->_db->query("DELETE FROM " . PREFIX . "_log $ex");
                if (empty($ex)) {
                    $this->_db->query("ALTER TABLE `" . PREFIX . "_log` AUTO_INCREMENT = 1");
                }
                $this->__object('AdminCore')->script('save');
                break;

            case 'download':
                $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_log $ex ORDER BY Id DESC");
                $dlmessage = '';
                while ($row = $sql->fetch_object()) {
                    $dlmessage .= "=======================================CMS_STATUS-X========================================\r\n";
                    $dlmessage .= "Событие № $row->Id\r\n";
                    $dlmessage .= "Дата и время: " . date("d-m-Y, H:i:s", $row->Datum) . "\r\n";
                    $dlmessage .= "Пользователь: " . $this->user($row->Benutzer) . "\r\n";
                    $dlmessage .= "IP: $row->Ip\r\n";
                    $dlmessage .= "Доп. Инфо: $row->Agent\r\n\r\n";
                    $dlmessage .= str_replace("\n", "\r\n", $row->Aktion) . "\r\n";
                }
                $sql->close();
                File::download($dlmessage, 'Системные_события_от_' . date('d-m-Y') . '.txt');
                break;

            default:
                $a = Tool::getLimit($limit);
                $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_log $ex ORDER BY Id DESC LIMIT $a, $limit");
                $num = $this->_db->found_rows();
                $seiten = ceil($num / $limit);
                $errors = array();
                while ($row = $sql->fetch_object()) {
                    $row->User = $this->user($row->Benutzer);
                    $errors[] = $row;
                }
                $sql->close();
                if ($num > $limit) {
                    $this->_view->assign('navi', $this->__object('AdminCore')->pagination($seiten, " <a class=\"page_navigation\" href=\"index.php?do=settings&amp;sub=logs&amp;prior=$_REQUEST[prior]&page={s}\">{t}</a> "));
                }
                $this->_view->assign('errors', $errors);
                break;
        }

        $this->_view->assign('title', $this->_lang['Admin_Logs']);
        $this->_view->content('/settings/logs.tpl');
    }

    public function languages($table = 'sprachen', $header = 'languages') {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            foreach (array_keys($_POST['Aktiv']) as $id) {
                if ($header != 'languages') {
                    if (empty($_POST['Sprachcode'][$id])) {
                        $_POST['Aktiv'][$id] = '0';
                    }
                }
                $array = array(
                    'Aktiv'  => $_POST['Aktiv'][$id],
                    'Posi'   => $_POST['Posi'][$id],
                    'Locale' => $_POST['Locale'][$id],
                );
                if ($header == 'languages') {
                    $array['Sprache'] = $_POST['Sprache'][$id];
                    $array['Sprachcode'] = $_POST['Sprachcode'][$id];
                }
                $this->_db->update_query($table, $array, "Id = '" . intval($id) . "'");
            }
            $this->__object('Redir')->redirect('index.php?do=settings&sub=' . $header);
        }

        $d = LANG_DIR . '/';
        $languages = array();
        $sql = $this->_db->query("SELECT * FROM " . PREFIX . "_{$table} ORDER BY Posi ASC");
        while ($row = $sql->fetch_object()) {
            $row->Exists = (is_file($d . $row->Sprachcode . '/admin.txt')) ? 1 : 0;
            $languages[] = $row;
        }
        $sql->close();

        $handle = opendir($d);
        $folders = array();
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_dir($d . $file)) {
                $f = new stdClass;
                $f->Name = $file;
                $f->Long = isset($this->_lang['Lang_' . strtolower($file)]) ? $this->_lang['Lang_' . strtolower($file)] : $file;
                $f->Exists = is_file($d . $file . '/admin.txt') ? 1 : 0;
                $folders[] = $f;
            }
        }
        closedir($handle);

        if ($header == 'languages') {
            $la = 'Settings_languages';
            $tp = 'languages.tpl';
        } else {
            $la = 'Settings_languages_a';
            $tp = 'languages_admin.tpl';
        }

        $this->_view->assign('folders', $folders);
        $this->_view->assign('languages', $languages);
        $this->_view->assign('title', $this->_lang[$la]);
        $this->_view->content('/settings/' . $tp);
    }

    public function money() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'sape'           => Arr::getPost('sape'),
                'code_sape'      => Arr::getPost('code_sape'),
                'linkfeed'       => Arr::getPost('linkfeed'),
                'code_linkfeed'  => Arr::getPost('code_linkfeed'),
                'setlinks'       => Arr::getPost('setlinks'),
                'code_setlinks'  => Arr::getPost('code_setlinks'),
                'mainlink'       => Arr::getPost('mainlink'),
                'code_mainlink'  => Arr::getPost('code_mainlink'),
                'trustlink'      => Arr::getPost('trustlink'),
                'code_trustlink' => Arr::getPost('code_trustlink')
            );
            SX::save('system', $array);
            $this->__object('AdminCore')->script('save');
            SX::load('system');
        }
        $row = SX::get('system');
        $this->_view->assign('row', $row);
        $this->_view->assign('title', $this->_lang['MoneySite']);
        $this->_view->content('/settings/money.tpl');
    }

    protected function support($value) {
        return base64_decode('aHR0cDovL3d3dy5zdGF0dXMteC5ydQ==') . '/supports/' . $value . '/';
    }

    /* Отправка сообщения об ошибке разработчику */
    public function error($id) {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $items = $this->_db->fetch_object("SELECT Aktion, Agent FROM " . PREFIX . "_log WHERE Id = '" . intval($id) . "' LIMIT 1");
        $this->_view->assign('flink', $this->support('error'));
        $this->_view->assign('items', $items);
        $this->_view->content('/settings/send_error.tpl');
    }

    /* Отправка заказа */
    public function order() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $this->_view->assign('flink', $this->support('order'));
        $this->_view->content('/settings/send_order.tpl');
    }

}