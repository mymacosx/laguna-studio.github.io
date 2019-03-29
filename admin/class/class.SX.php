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

abstract class Magic {

    protected static $__storage_objects = array();

    protected function __object($key, $params = NULL) {
        if (!isset(self::$__storage_objects[$key])) {
            SX::object($key, $params);
        }
        return self::$__storage_objects[$key];
    }

    public function __get($key) {
        static $objects = array('_view' => false, '_db' => false, '_text' => false, '_lang' => false);
        if (isset($objects[$key])) {
            if ($objects[$key] === false) {
                switch ($key) {
                    case '_db' : $objects[$key] = DB::get(); break;
                    case '_view': $objects[$key] = View::get(); break;
                    case '_text': $objects[$key] = Text::get(); break;
                    case '_lang': $objects[$key] = SX::$lang; break;
                }
            }
            return $objects[$key];
        }
        return SX::get('classes.' . $key);
    }

    public function __set($key, $value) {
        SX::set('classes.' . $key, $value);
    }

    public function __isset($key) {
        return SX::has('classes.' . $key);
    }

    public function __unset($key) {
        SX::del('classes.' . $key);
    }

}

abstract class SX extends Magic {

    public static $lang = array();
    protected static $_mail = array();
    protected static $_param = array();

    /* Метод подготовки системы */
    public static function preload($type) {
        self::encoding();
        spl_autoload_register(array('SX', $type . 'Autoload'));   // Объявляем автозагрузчик классов
        self::unsetGlobals();                                     // Удаляем глобальные массивы
        $configs = self::getConfig('sys.config');                 // Подключаем файл с настройками системы
        self::set('configs', $configs);
        self::cleanArray();                                       // Выполняем фильтрацию глобальных массивов
        self::stripslashesArray();                                // Выполняем проход функцией stripslashes глобальных массивов
        self::checkDomain();                                      // Проверка на наличие запрещённых символов в $_SERVER['HTTP_HOST']
        self::systemDefines();                                    // Устанавливаем основные константы системы
        if ($configs['site']['aktiv'] == '0') {                   // Выключение сайта при необходимости
            self::object('Repair');
        }

        $database = self::getConfig('db.config');                 // Подключаем файл с настройками базы данных
        $database['load'] = false;
        self::set('database', $database);
        self::checkInstall($database);                            // Проверяем установлена ли система
        self::setDefine('PREFIX', $database['dbprefix']);         // Устанавливаем константу с префиксом базы данных
        self::load();                                             // Загружаем настройки системы
        self::setTimeZone();                                      // Устанавливаем временную зону по умолчанию
        require SX_DIR . '/lib/functions.php';                    // Подключаем функции
        self::getDebug();                                         // Логирование ошибок
        self::startSession($database['type_sess']);               // Инициализируем и стартуем сессию
        self::setDefaults();                                      // Устанавливаем обязательные параметры при отсутствии
    }

    /* Метод установки кодировки */
    protected static function encoding() {
        self::setDefine('CHARSET', 'UTF-8');
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding(CHARSET);
        }
        ini_set('default_charset', CHARSET);
        mb_regex_encoding(CHARSET);
        mb_http_output(CHARSET);
        mb_http_input(CHARSET);
        mb_substitute_character('none');
    }

    /* Метод установки основных констант */
    public static function systemDefines() {
        self::setDefine('VERSION', '1.06 UTF');                   // Версия системы
        self::setDefine('PE', PHP_EOL);                           // Устанавливаем константу c переносом строк
        self::setDefine('IP_USER', self::getIp());                // Устанавливаем константу с IP пользователя
        self::setDefine('BASE_PATH', self::basePatch());          // Устанавливаем константу корня, указывает установлен сайт в подпапку или нет
        self::setDefine('SHEME_URL', self::protocol());           // Устанавливаем константу схемы сайта
        self::setDefine('BASE_URL', self::baseUrl());             // Устанавливаем константу адреса сайта учитывая вложенность папок
        self::setDefine('UPLOADS_DIR', SX_DIR . '/uploads');      // Устанавливаем полный путь до папки uploads
        self::setDefine('LANG_DIR', SX_DIR . '/lang');            // Устанавливаем полный путь до папки lang
        self::setDefine('TEMP_DIR', SX_DIR . '/temp');            // Устанавливаем полный путь до папки temp
        self::setDefine('MODUL_DIR', SX_DIR . '/modules');        // Устанавливаем полный путь до папки с модулями
        self::setDefine('WIDGET_DIR', SX_DIR . '/widgets');       // Устанавливаем полный путь до папки с виджетами
        self::setDefine('JS_PATH', BASE_PATH . 'js');             // Устанавливаем полный путь до папки с js
        self::setDefine('HTTP', self::httpProtocol());            // Устанавливаем протокол сервера
    }

    /* Метод устанавливает умолчания для обязательных параметров */
    public static function setDefaults() {
        $_REQUEST['p'] = Arr::getRequest('p', 'index');
        $_REQUEST['t'] = Arr::getRequest('t', '-');
        $_SESSION['loggedin'] = Arr::getSession('loggedin', 0);
        $_SESSION['user_group'] = Arr::getSession('user_group', 2);
        $_SESSION['benutzer_id'] = Arr::getSession('benutzer_id', 0);
    }

    /* Метод устанавливает временную зону */
    public static function setTimeZone() {
        $timezone = self::get('system.timezone');
        date_default_timezone_set(!empty($timezone) ? $timezone : 'Europe/Moscow');
    }

    /* Методполучения протокола сервера */
    public static function httpProtocol() {
        return isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'HTTP/1.1' : 'HTTP/1.0';
    }

    /* Метод получает путь вложенности сайта в подпапки */
    public static function basePatch() {
        return str_replace('//', '/', str_replace('/index.php', '/', $_SERVER['PHP_SELF']));
    }

    /* Метод получает адрес сайта учитывая вложенность папок */
    public static function baseUrl() {
        $patch = str_replace(array('/admin/index.php', '/index.php'), '', $_SERVER['PHP_SELF']);
        return SHEME_URL . $_SERVER['HTTP_HOST'] . $patch;
    }

    /* Метод подключения конфиг файла */
    public static function getConfig($name) {
        $config = array();
        include SX_DIR . '/config/' . $name . '.php';
        return $config;
    }

    /* Метод создания объекта */
    public static function object($key, $params = NULL, $new = false) {
        if (!isset(self::$__storage_objects[$key]) || $new === true) {
            self::$__storage_objects[$key] = new $key($params);
        }
        return self::$__storage_objects[$key];
    }

    /* Метод загрузки языковых переменных */
    public static function loadLang($file) {
        $object = View::get();
        $object->configLoad($file);
        self::$lang = $object->getConfigVars();
        $object->assign('lang', self::$lang);
    }

    /* Метод выбора типа хранения сессий */
    public static function startSession($type = 'base') {
        if ($type == 'base') {
            if (version_compare(PHP_VERSION, '7.2.0', '<')) {
                ini_set('session.save_handler', 'user');
            }
            new Session;
        }
        session_set_cookie_params(0, BASE_PATH);
        session_name('SX_CMS');
        session_start();
    }

    /* Метод установки локали PHP */
    public static function setLocale($langcode, $locale = null) {
        $value = $langcode . '_' . strtoupper($langcode);
        $array = array(
            $value . '.UTF-8',
            $value . '.utf-8',
            $value . '.UTF8',
            $value . '.utf8',
            $value,
            $langcode,
        );
        if (!empty($locale)) {
            array_unshift($array, $locale . '.UTF-8', $value . '.utf-8', $locale . '.UTF8', $locale . '.utf8');
        }
        return setlocale(LC_ALL, $array);
    }

    /* Метод получения параметров */
    public static function get($key, $default = NULL) {
        list($a, $count) = self::parse($key);
        switch ($count) {
            case 1: return isset(self::$_param[$a[0]]) ? self::$_param[$a[0]] : $default;
            case 2: return isset(self::$_param[$a[0]][$a[1]]) ? self::$_param[$a[0]][$a[1]] : $default;
            case 3: return isset(self::$_param[$a[0]][$a[1]][$a[2]]) ? self::$_param[$a[0]][$a[1]][$a[2]] : $default;
            case 4: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]]) ? self::$_param[$a[0]][$a[1]][$a[2]][$a[3]] : $default;
            case 5: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]]) ? self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]] : $default;
            case 6: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]][$a[5]]) ? self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]][$a[5]] : $default;
        }
        return $default;
    }

    /* Метод сохранения параметров */
    public static function set($key, $value = NULL) {
        list($a, $count) = self::parse($key);
        switch ($count) {
            case 1: self::$_param[$a[0]] = $value; break;
            case 2: self::$_param[$a[0]][$a[1]] = $value; break;
            case 3: self::$_param[$a[0]][$a[1]][$a[2]] = $value; break;
            case 4: self::$_param[$a[0]][$a[1]][$a[2]][$a[3]] = $value; break;
            case 5: self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]] = $value; break;
            case 6: self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]][$a[5]] = $value; break;
        }
    }

    /* Метод проверки существования параметра */
    public static function has($key) {
        list($a, $count) = self::parse($key);
        switch ($count) {
            case 1: return isset(self::$_param[$a[0]]);
            case 2: return isset(self::$_param[$a[0]][$a[1]]);
            case 3: return isset(self::$_param[$a[0]][$a[1]][$a[2]]);
            case 4: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]]);
            case 5: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]]);
            case 6: return isset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]][$a[5]]);
        }
        return false;
    }

    /* Метод получения параметров */
    public static function del($key) {
        list($a, $count) = self::parse($key);
        switch ($count) {
            case 1: unset(self::$_param[$a[0]]); break;
            case 2: unset(self::$_param[$a[0]][$a[1]]); break;
            case 3: unset(self::$_param[$a[0]][$a[1]][$a[2]]); break;
            case 4: unset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]]); break;
            case 5: unset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]]); break;
            case 6: unset(self::$_param[$a[0]][$a[1]][$a[2]][$a[3]][$a[4]][$a[5]]); break;
        }
    }

    /* Метод кеширования параметров методов работы с параметрами */
    public static function parse($key) {
        static $cache = array();
        if (!isset($cache[$key])) {
            $array = explode('.', $key);
            $cache[$key] = array($array, count($array));
        }
        return $cache[$key];
    }

    /* Метод сохранения настроек в массив */
    public static function load($array = NULL) {
        $where = '';
        if (!empty($array)) {
            $lists = array();
            foreach ((array) $array as $value) {
                $lists[] = "'" . DB::get()->escape(trim($value)) . "'";
            }
            $where = " WHERE `Modul` IN(" . implode(',', $lists) . ")";
        }
        $sql = DB::get()->query("SELECT SQL_CACHE `Modul`, `Name`, `Value` FROM `" . PREFIX . "_settings`" . $where);
        while ($row = $sql->fetch_assoc()) {
            self::$_param[$row['Modul']][$row['Name']] = $row['Value'];
        }
        $sql->close();
    }

    /* Метод сохранения - обновления настроек */
    public static function save($table, $array = array()) {
        if (!empty($array)) {
            $DB = DB::get();
            foreach ($array as $name => $value) {
                $name = $DB->escape(trim($name));
                $table = $DB->escape(trim($table));
                $value = $DB->escape(trim($value));
                $id = strtolower($table . '_' . $name);
                $DB->query("INSERT INTO `" . PREFIX . "_settings` (
                        `Id`,
                        `Modul`,
                        `Name`,
                        `Value`
                ) VALUES (
                        '" . $id . "',
                        '" . $table . "',
                        '" . $name . "',
                        '" . $value . "'
                ) ON DUPLICATE KEY UPDATE
                        `Modul` = '" . $table . "',
                        `Name` = '" . $name . "',
                        `Value` = '" . $value . "'");
            }
        }
    }

    /* Метод автозагрузки классов */
    public static function userAutoload($class) {
        if (strncasecmp($class, 'smarty_', 7) !== 0) {
            include SX_DIR . '/class/class.' . $class . '.php';
        }
        return class_exists($class, false);
    }

    /* Метод автозагрузки классов админки */
    public static function adminAutoload($class) {
        if (strncasecmp($class, 'smarty_', 7) !== 0) {
            $patch = is_file(SX_DIR . '/admin/class/class.' . $class . '.php') ? 'admin/' : '';
            include SX_DIR . '/' . $patch . 'class/class.' . $class . '.php';
        }
        return class_exists($class, false);
    }

    /* Метод записи системных сообщений в базу */
    public static function syslog($action, $type = 1, $benutzer = 0) {
        self::fileLogs($action, $type);
        self::errorBrowser($action, $type);
        if (self::get('database.load') === true && self::get('system.Logging') == 1) {
            $insert_array = array(
                'Datum'    => time(),
                'Benutzer' => $benutzer,
                'Aktion'   => Text::get()->substr($action, 0, 10000),
                'Typ'      => $type,
                'Ip'       => IP_USER,
                'Agent'    => Arr::getServer('HTTP_USER_AGENT'));
            DB::get()->insert_query('log', $insert_array);
        }
    }

    /* Метод работы с логами */
    protected static function fileLogs($action, $type) {
        $array = array(
            '0' => 'admin',
            '1' => 'adminshop',
            '2' => 'payment',
            '3' => 'sys',
            '4' => 'mysql',
            '5' => 'erphp',
            '6' => 'seite');
        switch (self::get('configs.loger')) {
            case '0': return;
            case '1':
                $file = isset($array[$type]) ? $array[$type] : 'other';
                error_log($action . PE . str_repeat('-', 80) . PE, 3, SX_DIR . '/temp/logs/' . $file . '_errors.log');
                break;
            case '2':
                if ($type == '5') {
                    error_log($action, 0);
                }
                break;
            default:
                if (Tool::isMail(self::get('configs.loger'))) {
                    error_log($action . PE . str_repeat('-', 80) . PE, 1, self::get('configs.loger'));
                }
                break;
        }
    }

    /* Метод вывода сообщения на экран */
    public static function errorBrowser($action, $type) {
        if (self::get('configs.debug') == '1' && ($type == '4' || $type == '5')) {
            self::output('<div style="height:140px; padding:8px; width:800px; overflow:auto; background:#ffff00">
			  <pre style="font-size:13px">Системное сообщение:<br />' . $action . '</pre></div>');
        }
    }

    /* Метод перехвата фатальных ошибок */
    public static function fatalError() {
        $error = error_get_last();
        if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR))) {
            if (stripos($error['message'], 'Allowed memory size') !== false) {
                ini_set('memory_limit', (int) ini_get('memory_limit') + 32 . 'M');
            }
            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /* Метод обработки ошибок PHP пользовательского уровня */
    public static function errorHandler($errno, $errmsg, $filename, $linenum) {
        if (!in_array($errno, array(E_NOTICE, E_STRICT, E_WARNING, E_DEPRECATED))) {
            self::syslog('Ошибка PHP!' . PE . 'Ошибка №: ' . $errno . PE . 'Сообщение: ' . $errmsg . PE . 'Файл: ' . $filename . PE . 'Строка: ' . $linenum, 5);
        }
    }

    /* Метод записи в лог исключений php */
    public static function exceptionHandler($exception) {
        self::syslog('Иключение PHP!' . PE . 'Текст: ' . $exception, 5);
    }

    /* Метод включения отлова ошибок */
    public static function getDebug() {
        register_shutdown_function(array('SX', 'fatalError'));
        set_error_handler(array('SX', 'errorHandler'), E_ALL);
        set_exception_handler(array('SX', 'exceptionHandler'));
    }

    /* Метод удаления глобальных массивов */
    public static function unsetGlobals() {
        if (ini_get('register_globals')) {
            if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
                self::output('Попытка проведения несанкционированных действий! Запрос отвергнут...', true);
            }
            $global = array_keys($GLOBALS);
            $global = array_diff($global, array(
                '_COOKIE',
                '_ENV',
                '_GET',
                '_FILES',
                '_POST',
                '_REQUEST',
                '_SERVER',
                '_SESSION',
                'GLOBALS'));
            foreach ($global as $value) {
                unset($GLOBALS[$value]);
            }
        }
    }

    /* Метод выполняет фильтрацию глобальных массивов $_POST, $_GET, $_REQUEST, $_COOKIE, $_SERVER */
    public static function cleanArray() {
        if (!empty($_SERVER)) {
            array_walk_recursive($_SERVER, array(__CLASS__, 'sanitArray'));
        }
        if (!empty($_POST)) {
            array_walk_recursive($_POST, array(__CLASS__, 'sanitArray'));
        }
        if (!empty($_GET)) {
            array_walk_recursive($_GET, array(__CLASS__, 'sanitArray'));
        }
        if (!empty($_REQUEST)) {
            array_walk_recursive($_REQUEST, array(__CLASS__, 'sanitArray'));
        }
        if (!empty($_COOKIE)) {
            array_walk_recursive($_COOKIE, array(__CLASS__, 'sanitArray'));
        }
        if (!empty($_FILES)) {
            array_walk_recursive($_FILES, array(__CLASS__, 'sanitArray'));
        }
    }

    /* Метод прохода функцией stripslashes глобальных массивов $_POST, $_GET, $_REQUEST, $_COOKIE */
    public static function stripslashesArray() {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (!empty($_POST)) {
                array_walk_recursive($_POST, array('SX', 'stripslashes'));
            }
            if (!empty($_GET)) {
                array_walk_recursive($_GET, array('SX', 'stripslashes'));
            }
            if (!empty($_REQUEST)) {
                array_walk_recursive($_REQUEST, array('SX', 'stripslashes'));
            }
            if (!empty($_COOKIE)) {
                array_walk_recursive($_COOKIE, array('SX', 'stripslashes'));
            }
        }
    }

    /* Метод обертка для stripslashes */
    public static function stripslashes(&$value) {
        $value = stripslashes($value);
    }

    /* Метод очистки переменных */
    protected static function sanitArray(&$value, $key) {
        static $object = null;
        if ($object === null) {
            $object = Text::get();
        }
        $value = $object->clear($value);

        static $array = array(
            'area'            => array('int'),
            'gid'             => array('int'),
            'categ'           => array('int'),
            'langcode'        => array('int'),
            'galid'           => array('int'),
            'hid'             => array('int'),
            'limit'           => array('int'),
            'pp'              => array('int'),
            'uid'             => array('int'),
            'fid'             => array('int'),
            'pid'             => array('int'),
            'toid'            => array('int'),
            'cid'             => array('int'),
            't_id'            => array('int'),
            'period'          => array('int'),
            'forum_id'        => array('int'),
            'group'           => array('int'),
            'posticon'        => array('int'),
            'img_id'          => array('preg', '/[^\d]/u'),
            'p'               => array('preg', '/[^a-z]/iu'),
            't'               => array('preg', '/[^\da-z-_]/iu'),
            'id'              => array('preg', '/[^\da-z-_]/iu'),
            'sort'            => array('preg', '/[^\da-z-_]/iu'),
            'unit'            => array('preg', '/[^\da-z-_]/iu'),
            'listpn'          => array('preg', '/[^\da-z-_]/iu'),
            'action'          => array('preg', '/[^\da-z-_]/iu'),
            'do'              => array('preg', '/[^\da-z-_]/iu'),
            'sub'             => array('preg', '/[^\da-z-_]/iu'),
            'phpsessid'       => array('preg', '/[^\da-z-_]/iu'),
            'high'            => array('preg', '/[^\w-. ]/iu'),
            'http_referer'    => array('preg', '![^\w-,.:;\/&=?#%+ ]!iu'),
            'http_user_agent' => array('preg', '![^\w-,.:;\/&=?#%+ ]!iu'),
            'request_uri'     => array('preg', '![^\w-,.:;\/&=?#%+ ]!iu'),
            'query_string'    => array('preg', '![^\w-,.:;\/&=?#%+ ]!iu'),
            'page'            => array('empty', 1),
            'newsid'          => array('empty', 1),
            'artpage'         => array('empty', 1),
        );

        $key = strtolower($key);
        if (isset($array[$key])) {
            if ($array[$key][0] == 'int') {
                $value = intval($value);
            } elseif ($array[$key][0] == 'preg') {
                $value = preg_replace($array[$key][1], '', $value);
            } elseif ($array[$key][0] == 'empty') {
                $value = !empty($value) ? intval($value) : $array[$key][1];
            }
        }
    }

    /* Метод проверки установлена ли система */
    public static function checkInstall($config) {
        if (empty($config['dbhost']) || empty($config['dbname'])) {
            if (is_file(SX_DIR . '/setup/setup.php')) {
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', gmmktime(0, 4, 0, 11, 1, 1974)));
                self::object('Redir')->redirect('setup/setup.php');
            }
            self::output('<li> Сайт на профилактике, заходите позже!', true);
        }
    }

    /* Метод определения IP пользователя */
    public static function getIp() {
        $addr = $_SERVER['REMOTE_ADDR'];
        $addr = filter_var($addr, FILTER_VALIDATE_IP) !== false ? $addr : '0.0.0.0';
        $array = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED');
        foreach ($array as $value) {
            if (!empty($_SERVER[$value])) {
                $arr = explode(',', $_SERVER[$value]);
                $ip = trim(end($arr));
                if ($addr != $ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false) {
                    return $ip;
                }
            }
        }
        return $addr;
    }

    /* Метод проверки на наличие запрещённых символов в $_SERVER['HTTP_HOST'] */
    public static function checkDomain() {
        if (isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
            if (!preg_match('/^\[?(?:[\w-:\]_]+\.?)+$/iu', $_SERVER['HTTP_HOST'])) {
                self::object('Response')->get(400);
                exit;
            }
        } else {
            $_SERVER['HTTP_HOST'] = '';
        }
    }

    /* Метод возвращает протокол */
    public static function protocol() {
        static $cache = NULL;
        if (empty($cache)) {
            $cache = 'http://';
            $array = array(
                'HTTPS'                  => 'on',
                'SERVER_PORT'            => 443,
                'HTTP_SCHEME'            => 'https',
                'HTTP_X_HTTPS'           => 1,
                'HTTP_X_FORWARDED_PROTO' => 'https',
                'HTTP_X_FORWARDED_PORT'  => 443,
                'HTTP_X_FORWARDED_SSL'   => 'on',
            );
            foreach ($array as $key => $value) {
                if (isset($_SERVER[$key]) && $_SERVER[$key] === $value) {
                    $cache = 'https://';
                    break;
                }
            }
        }
        return $cache;
    }

    /* Метод вывода */
    public static function output($content, $exit = false, $expires = NULL, $modified = NULL) {
        if (!headers_sent()) {
            header('Date: ' . gmdate('D, d M Y H:i:s \G\M\T'));
            header('Server: Protected by SX CMS');
            header('X-Powered-By: SX CMS');
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', !empty($expires) ? $expires : gmmktime(0, 4, 0, 11, 1, 1974)));
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', !empty($modified) ? $modified : time() - mt_rand(1000, 10000)));
        }
        if ($exit === true) {
            $content = Tool::prefixPatch($content);
            $content = SX::object('Binder')->execute($content);
            echo($content);
            exit;
        } else {
            echo($content);
        }
    }

    /* Метод установки define */
    public static function setDefine($define, $value = '') {
        if (!defined($define)) {
            define($define, $value);
        }
    }

    /* Метод постановки письма в очередь */
    public static function setMail($array) {
        self::$_mail[] = $array;
    }

    /* Метод отправки писем из очереди */
    public static function sendMail() {
        $mails = self::$_mail;
        if (!empty($mails)) {
            $Mail = self::object('Mail');
            foreach ($mails as $x) {
                $Mail->send($x['globs'], $x['to'], $x['to_name'], $x['text'], $x['subject'], $x['fromemail'], $x['from'], $x['type'], $x['attach'], $x['html'], $x['prio']);
            }
        }
    }

    /* Метод проверки на бан */
    public static function checkBanned() {
        if (Arr::getSession('user_group') == 1) {
            $_SESSION['banned'] = 0;
        } else {
            $where = array();
            DB::get()->query("DELETE FROM `" . PREFIX . "_banned` WHERE `TimeEnd` <= '" . time() . "'");
            $array = Arr::getSession(array('user_name' => '', 'login_email' => '', 'benutzer_id' => 0));
            $array['cookie_ip'] = preg_replace('/[^\d.]/u', '', Arr::getCookie('welcome'));
            if (!empty($array['benutzer_id'])) {
                $where[] = "`User_id` = '" . $array['benutzer_id'] . "'";
            }
            if (!empty($array['user_name'])) {
                $where[] = "`Name` = '" . $array['user_name'] . "'";
            }
            if (Tool::isMail($array['login_email'])) {
                $domain = explode('@', $array['login_email']);
                $where[] = "`Email` = '" . $array['login_email'] . "'";
                $where[] = "`Email` = '*@" . $domain[1] . "'";
            }
            $ip = explode('.', IP_USER);
            $where[] = "`Ip` = '" . IP_USER . "'";
            $where[] = "`Ip` = '" . $ip[0] . "." . $ip[1] . "." . $ip[2] . ".*'";
            $where[] = "`Ip` = '" . $ip[0] . "." . $ip[1] . ".*.*'";
            $where[] = "`Ip` = '" . $ip[0] . "*.*.*'";
            if (!empty($array['cookie_ip'])) {
                $ip = explode('.', $array['cookie_ip']);
                $where[] = "`Ip` = '" . $array['cookie_ip'] . "'";
                $where[] = "`Ip` = '" . $ip[0] . "." . $ip[1] . "." . $ip[2] . ".*'";
                $where[] = "`Ip` = '" . $ip[0] . "." . $ip[1] . ".*.*'";
                $where[] = "`Ip` = '" . $ip[0] . "*.*.*'";
            }
            $sql = DB::get()->fetch_object("SELECT SQL_CACHE `Id` FROM `" . PREFIX . "_banned` WHERE " . implode(' OR ', $where) . " AND `Aktiv` = '1' LIMIT 1");
            $_SESSION['banned'] = is_object($sql) ? 1 : 0;
        }
    }

}