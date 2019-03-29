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

class Core extends Magic {

    /* Выгрузка в глобаль активных модулей */
    public function modules($area = 0) {
        if (empty($area)) {
            $area = $_SESSION['area'];
        }
        $array = array('modules' => array(), 'widgets' => array(), 'active' => array());
        $sql = $this->_db->query("SELECT SQL_CACHE Name, Settings, Type, Aktiv_Section_" . $area . " AS Aktiv FROM " . PREFIX . "_bereiche");
        while ($row = $sql->fetch_assoc()) {
            $array['active'][$row['Name']] = $row['Aktiv'];
            if ($row['Type'] == 'widget') {
                $name = !empty($row['Result']) ? $row['Result'] : $row['Name'];
                $array['widgets'][$name] = $row['Settings'];
            }
            if ($row['Type'] == 'extmodul' && $row['Aktiv'] == 1) {
                $array['modules'][] = $row['Name'];
            }
        }
        $sql->close();
        SX::set('modules', $array['modules']);
        SX::set('widgets', $array['widgets']);
        SX::set('active', $array['active']);
    }

    /* Подключение модуля */
    public function extensions() {
        $ext = $_SESSION['banned'] == 1 ? 'banned' : Tool::cleanString($_REQUEST['p'], '_');
        if (get_active($ext) && is_file(MODUL_DIR . '/' . $ext . '/main/action.php')) {
            $include = MODUL_DIR . '/' . $ext . '/main/action.php';
        } elseif (get_active($ext) && is_file(WIDGET_DIR . '/' . $ext . '/main/action.php')) {
            $include = WIDGET_DIR . '/' . $ext . '/main/action.php';
        } else {
            if (!is_file(SX_DIR . '/action/' . $ext . '.php')) {
                $_REQUEST['p'] = 'index';
                $include = SX_DIR . '/action/index.php';
            } else {
                $include = SX_DIR . '/action/' . $ext . '.php';
            }
        }
        include $include;
    }

    /* Подключаем запароленную секцию или подключаем дефолтную секцию */
    public function section() {
        $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '" . $_SESSION['area'] . "' AND Aktiv = '1' LIMIT 1");
        if (!is_array($row)) {
            $row_p = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_sektionen WHERE Id = '" . $_SESSION['area'] . "' LIMIT 1");
            if (!empty($row_p['Passwort']) && Arr::getGet('pass') == $row_p['Passwort']) {
                $_SESSION['secpass'][$row_p['Id']] = $row_p['Passwort'];
            }
            if (is_array($row_p) && $row_p['Aktiv'] != 1) {
                if (isset($_SESSION['secpass'][$row_p['Id']]) && $_SESSION['secpass'][$row_p['Id']] == $row_p['Passwort']) {
                    $row = $row_p;
                } else {
                    $row_p['Meldung'] = (empty($row_p['Meldung'])) ? 'Секция не активна' : $row_p['Meldung'];
                    SX::output('<pre>' . $row_p['Meldung'] . '</pre>', true);
                }
            } else {
                $row = $this->_db->cache_fetch_assoc("SELECT * FROM " . PREFIX . "_sektionen WHERE Aktiv = '1' ORDER BY Id ASC LIMIT 1");
                if (is_array($row)) {
                    $_REQUEST['area'] = $_SESSION['area'] = $row['Id'];
                } else {
                    SX::output('<pre>Секция не активна</pre>', true);
                }
            }
        }
        SX::set('section', $row);
    }

    /* Установка активной секции и привязка домена */
    public function getSection() {
        if (SX::get('system.Domains') == '1') {
            $host = !empty($_SERVER['HTTP_HOST']) ? Tool::cleanAllow($_SERVER['HTTP_HOST'], '.') : Tool::cleanAllow(getenv('HTTP_HOST'), '.');
            $sql = $this->_db->fetch_object("SELECT SQL_CACHE Id FROM " . PREFIX . "_sektionen WHERE Domains = '" . $this->_db->escape(trim($host)) . "' AND Aktiv='1' LIMIT 1");
        }
        if (isset($sql) && is_object($sql)) {
            $_REQUEST['area'] = $_SESSION['area'] = $sql->Id;
        } elseif (isset($_SESSION['area']) && is_numeric($_SESSION['area']) && $_SESSION['area'] >= 1 && !isset($_REQUEST['area'])) {
            $_REQUEST['area'] = $_SESSION['area'] = intval($_SESSION['area']);
        } elseif (isset($_REQUEST['area']) && is_numeric($_REQUEST['area']) && $_REQUEST['area'] >= 1) {
            $_REQUEST['area'] = $_SESSION['area'] = intval($_REQUEST['area']);
        } else {
            $_REQUEST['area'] = $_SESSION['area'] = 1;
        }
    }

    /* Работа через SSL */
    public function ssl() {
        $options = array(
            'val' => SX::protocol(),
            'ssl' => SX::get('configs.ssl'),
            'uri' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        );
        if ($options['ssl'] == '0' && $options['val'] == 'https://') {
            $this->__object('Redir')->redirect('http://' . $options['uri']);
        }
        $https = $this->getHttps();
        if ($options['ssl'] == '1' && $options['val'] == 'https://' && !$https) {
            $this->__object('Redir')->redirect('http://' . $options['uri']);
        } elseif ($options['ssl'] == '1' && $options['val'] == 'http://' && $https) {
            $this->__object('Redir')->redirect('https://' . $options['uri']);
        }
    }

    /* Метод определения страниц доступных по https */
    public function getHttps() {
        $result = false;
        $https = SX::get('configs.https');
        if (!empty($https)) {
            $request = Arr::getRequest(array('p' => '*', 'action' => '*'));
            foreach ($https as $value) {
                list($p, $action) = explode(':', $value);
                if (
                        ('*' == $p && '*' == $action) ||
                        ('*' == $p && $request['action'] == $action) ||
                        ($request['p'] == $p && '*' == $action) ||
                        ($request['p'] == $p && $request['action'] == $action)
                ) {
                    $result = true;
                    break;
                }
            }
        } else {
            $result = true;
        }
        return $result;
    }

    /* Переключатель языков на сайте */
    public function selectLangs() {
        if (!empty($_SESSION['lang']) && !isset($_REQUEST['lang']) && (is_file(LANG_DIR . '/' . $_SESSION['lang'] . '/main.txt'))) {
            $Language = $_SESSION['lang'];
        } else {
            $Language = (strlen(Arr::getRequest('lang')) == 2 && (is_file(LANG_DIR . '/' . Arr::getRequest('lang') . '/main.txt'))) ? Arr::getRequest('lang') : SX::get('langs.1');
        }
        $_SESSION['lang'] = preg_replace('/[^a-z]/iu', '', $Language);
        if (!empty($_REQUEST['lredirect'])) {
            $this->__object('Redir')->seoRedirect(base64_decode($_REQUEST['lredirect']));
        }
    }

    /* Получаем список активных языков */
    public function aktiveLangs() {
        $array = array();
        $sql = $this->_db->query("SELECT SQL_CACHE Id, Sprachcode FROM " . PREFIX . "_sprachen WHERE Aktiv = 1 ORDER BY Id ASC");
        while ($row = $sql->fetch_assoc()) {
            $array[$row['Id']] = $row['Sprachcode'];
        }
        SX::set('langs', $array);
    }

    /* Получаем настройки текущего языка */
    public function langSettings() {
        $seo = array();
        $query = "SELECT * FROM " . PREFIX . "_sprachen WHERE Id = '" . Arr::getSession('Langcode', 1) . "' AND Aktiv = '1' ; ";
        $query .= "SELECT title, keywords, description, canonical FROM " . PREFIX . "_seotags WHERE " . $this->seoQuery() . " AND Aktiv = '1'";
        if ($this->_db->multi_query($query)) {
            if (($result = $this->_db->store_result())) {
                $row = $result->fetch_assoc();
                $result->close();
            }
            if (($result = $this->_db->store_next_result())) {
                $seo = $result->fetch_assoc();
                $result->close();
            }
        }
        SX::set('seo', $seo);
        return $row;
    }

    /* Метод формирует возможные варианты адреса страницы */
    protected function seoQuery() {
        $DB = DB::get();
        $val = 'index.php?' . Tool::cleanUrl(strtolower($_SERVER['QUERY_STRING']));
        $val2 = $_REQUEST['p'] != 'index' ? Tool::cleanUrl(strtolower($_SERVER['REQUEST_URI'])) : 'home';
        return " (page = '" . $DB->escape(ltrim($val, '/')) . "' OR page = '" . $DB->escape(ltrim($val2, '/')) . "') ";
    }

    /* Устанавливаем код языка в $_SESSION['Langcode'] */
    public function getLangcode() {
        $Langcode = array_flip(SX::get('langs'));
        if (empty($Langcode[$_SESSION['lang']])) {
            $row = $this->_db->cache_fetch_assoc("SELECT Sprachcode FROM " . PREFIX . "_sprachen WHERE Aktiv = '1' ORDER BY Id ASC LIMIT 1");
            $_SESSION['lang'] = $row['Sprachcode'];
        }
        $_SESSION['Langcode'] = $Langcode[$_SESSION['lang']];
    }

    /* Переключатель тем сайта */
    public function template() {
        $tpl = Arr::getPost('tpl_current');
        $_SESSION['tpl_current'] = $tpl_current = (!empty($tpl) && is_dir(SX_DIR . 'theme/' . $tpl)) ? Tool::cleanAllow($tpl) : Tool::cleanAllow(Arr::getSession('tpl_current'));
        $tpl = (!empty($tpl_current) && is_dir(SX_DIR . 'theme/' . $tpl_current)) ? $tpl_current : SX::get('section.Template');
        if (!is_dir(SX_DIR . '/theme/' . $tpl)) {
            SX::syslog('Папка содержащая файлы темы ' . $tpl . ' не найдена', '5', $_SESSION['benutzer_id']);
            SX::output('<li> Папка содержащая файлы Вашей темы не найдена...', true);
        }
        SX::set('options.theme', $tpl);
    }

    /* Вывод в шаблон переключатель языков на сайте */
    public function langChooser() {
        $langchooser = NULL;
        $languages = SX::get('langs');
        if (count($languages) >= 2) {
            $this->_view->assign('languages', $languages);
            $langchooser = $this->_view->fetch(THEME . '/langswitcher/switcher.tpl');
        }
        $this->_view->assign('langchooser', $langchooser);
    }

    /* Подключаем модуль магазина */
    public function shop() {
        $p = Arr::getRequest('p');
        if (!in_array($p, SX::get('configs.shop'))) {
            $Shop = $this->__object('Shop');
            $Shop->ShopWarenkorb();
            if (get_active('shop_newinshop') && $p != 'shop') {
                if (get_active('shop_newinshop_startpage') && ($p == 'index' || empty($p))) {
                    $Shop->NewShop();
                }
                if (get_active('shop_newinshop_navi')) {
                    $Shop->NewShopNavi();
                }
            }
        }
    }

    /* Метод вывода сообщения о неактивности модуля и далее редирект на главную страницу сайта */
    public function notActive() {
        $this->message('Global_error', 'Global_NotActive');
    }

    /* Метод подключения модулей */
    public function getModules($lang_settings) {
        $this->_view->assign('lang_settings', $lang_settings);                              // Передаем в шаблон список активных языков
        $this->__object('Login')->launch();                                                 // Выполняем авторизацию
        SX::checkBanned();                                                                  // Проверяем на бан если активно
        $this->_view->assign('maxattachment', SX::get('user_group.MaxAnlagen'));            // Передаем в шаблон переменную с допустимым количеством загрузок
        $this->_view->assign('loggedin', ($_SESSION['loggedin'] == 1 ? true : false));      // Передаем в шаблон переменную с параметром авторизован ли пользователь
        $this->_view->assign('ugroup', $_SESSION['user_group']);                            // Передаем в шаблон переменную с группой пользователя
        $this->_view->assign('quicknavi', $this->__object('Navigation')->quicknavi());      // Передаем в шаблон переменную с горизонтальным меню
        $this->_view->assign('robots', $this->noindex());
        if (get_active('shop')) {
            $this->shop();
        }
        if (get_active('langchooser')) {
            $this->langChooser();
        }
        if (get_active('poll')) {
            $this->__object('Poll')->show();
        }
        if (get_active('newsletter')) {
            $this->__object('Newsletter')->show();
        }
        if (get_active('newusers')) {
            $this->__object('User')->show();
        }
        if (get_active('partners')) {
            $this->__object('Partner')->show();
        }
        if (get_active('counter_display')) {
            $this->__object('Counter')->show();
        }
        if (get_active('whosonline')) {
            $this->_view->assign('WhoisOnline', $this->_view->fetch(THEME . '/user/useronline.tpl'));
        }
        if (get_active('search')) {
            $this->_view->assign('SearchForm', $this->_view->fetch(THEME . '/search/search_small.tpl'));
        }
        if (get_active('calendar') && !in_array(Arr::getRequest('p'), SX::get('configs.kalendar'))) {
            $this->__object('Calendar')->ajax();
        }
        if ($_SESSION['loggedin'] != 1) {
            $set = SX::get('system');
            if ($set['sape'] == '1' && !empty($set['code_sape'])) {
                $this->__object('MoneyLinks')->sape($set['code_sape']);
            }
            if ($set['linkfeed'] == '1' && !empty($set['code_linkfeed'])) {
                $this->__object('MoneyLinks')->linkfeed($set['code_linkfeed']);
            }
            if ($set['setlinks'] == '1' && !empty($set['code_setlinks'])) {
                $this->__object('MoneyLinks')->setlinks($set['code_setlinks']);
            }
            if ($set['mainlink'] == '1' && !empty($set['code_mainlink'])) {
                $this->__object('MoneyLinks')->mainlink($set['code_mainlink']);
            }
            if ($set['trustlink'] == '1' && !empty($set['code_trustlink'])) {
                $this->__object('MoneyLinks')->trustlink($set['code_trustlink']);
            }
        }
        register_shutdown_function(array('SX', 'sendMail'));
        if (SX::get('configs.cron') == '1') {
            register_shutdown_function(array('Cron', 'get'));
        }
    }

    protected function noindex() {
        $result = 'index,follow';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $array = SX::get('configs.noindex');
            if (!empty($array)) {
                $uri = $_SERVER['REQUEST_URI'];
                foreach ((array) $array as $value) {
                    if (stripos($uri, $value) !== false) {
                        $result = 'noindex,nofollow';
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /* Метод мониторига запросов поиска */
    public function monitor($text, $where = '') {
        if (!empty($text)) {
            $this->_db->insert_query('suche_log', array(
                'Suche'   => sanitize($text),
                'Ip'      => IP_USER,
                'Datum'   => time(),
                'Suchort' => $where,
                'UserId'  => $_SESSION['benutzer_id']
            ));
        }
    }

    /* Метод вывода информационного сообщения с сообщением об отсутствии прав доступа */
    public function noAccess() {
        $this->message('NoPerm', 'NoAccess', BASE_URL, 5, 403);
    }

    /* Метод вывода информационного сообщения с последующим редиректом */
    public function message($title, $msg, $meta = BASE_URL, $time = 5, $code = 200) {
        if ($code == 404 || $this->__object('Agent')->is_robot === false) {
            $this->__object('Response')->get($code);
            $meta = str_replace('&amp;', '&', $meta);
            if (SX::get('system.use_seo') == 1) {
                $meta = $this->__object('Rewrite')->get(str_replace('&', '&amp;', $meta));
            }
            $pagetitle = isset(SX::$lang[$title]) ? SX::$lang[$title] : $title;
            $this->_view->assign(array(
                'pagetitle'   => $pagetitle,
                'keywords'    => $pagetitle,
                'description' => isset(SX::$lang[$msg]) ? SX::$lang[$msg] : $msg,
                'timerefresh' => $time * 1000,
                'url'         => str_replace('__URL__', $meta, $this->_lang['Global_Redirection']),
                'meta'        => $meta,
                'code'        => $code
            ));
            $out = $this->_view->fetch(THEME . '/other/message.tpl');
            SX::output($out, true);
        } else {
            $this->__object('Redir')->redirect($meta);
        }
    }

    /* Конечные замены вывода */
    public function finish($text) {
        $text = Text::get()->clear($text);
        if (stripos($text, 'youtube') !== false && !in_array(Arr::getRequest('p'), array('newpost', 'addpost'))) {
            $text = preg_replace("!\[(?i)youtube:([\w-:\)#=\+\^ ]+)\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu", "<div class=\"infobox\" style=\"text-align:center\"><h3 style=\"margin-bottom:10px\">\\1</h3><object width=\"375\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\2\"></param><param name=\"wmode\" value=\"opaque\"><embed src=\"http://www.youtube.com/v/\\2\" type=\"application/x-shockwave-flash\" width=\"425\" height=\"350\" wmode=\"opaque\"></embed></object></div>", $text);
            $text = preg_replace("!\[(?i)youtube\]([\w-:&/\?\[\]=.@ ]+)\[(?i)/youtube\]!iu", "<div class=\"infobox\" style=\"text-align:center\"><object width=\"375\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"opaque\"><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" width=\"425\" height=\"350\" wmode=\"opaque\"></embed></object></div>", $text);
            $text = preg_replace("!\[(?i)youtube-small:([\w-:\)#=\+\^ ]+)\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu", "<div class=\"infobox\" style=\"padding:4px\"><div style=\"margin-bottom:5px\"><small><strong>\\1</strong></small></div><object width=\"190\" height=\"170\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\2\"></param><param name=\"wmode\" value=\"opaque\"><embed src=\"http://www.youtube.com/v/\\2\" type=\"application/x-shockwave-flash\" width=\"190\" height=\"170\" wmode=\"opaque\"></embed></object></div>", $text);
            $text = preg_replace("!\[(?i)youtube-small\]([\w-:&/\?\[\]=.@]+)\[(?i)/youtube\]!iu", "<div class=\"infobox\" style=\"padding:4px\"><object width=\"190\" height=\"170\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"opaque\"><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" width=\"190\" height=\"170\" wmode=\"opaque\"></embed></object></div>", $text);
        }
        return $text;
    }

    /* Удаляем удаления не нужной технической информации */
    public function cleanup($tpl) {
        return str_replace(array('<!--START_NO_REWRITE-->', '<!--END_NO_REWRITE-->'), '', $tpl);
    }

    /* Проверка папок компиляции шаблонов */
    public function control($area) {
        if (!is_dir(TEMP_DIR . '/compiled/' . $area . '/')) {
            Folder::protection(TEMP_DIR . '/compiled/' . $area . '/', 0777);
            Folder::protection(TEMP_DIR . '/compiled/' . $area . '/main/', 0777);
            Folder::protection(TEMP_DIR . '/compiled/' . $area . '/admin/', 0777);
        }
    }

    /* Метод загрузки данных вставки в шаблон */
    public function insert() {
        $value = $_SESSION['lang'] . 'insert';
        $array = $this->__object('Cache')->get($value);
        if ($array === false) {
            $array = array();
            $sql = $this->_db->query("SELECT Name, Text1 as Def, Text" . Arr::getSession('Langcode', 1) . " AS Text FROM " . PREFIX . "_collection WHERE Active = 1");
            while ($row = $sql->fetch_assoc()) {
                $array[$row['Name']] = !empty($row['Text']) ? $row['Text'] : $row['Def'];
            }
            $this->__object('Cache')->set($value, $array, 86400);
        }
        $this->_view->assign('insert', $array);
    }

}