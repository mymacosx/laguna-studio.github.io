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
function getAll($var, $val = '') {
    return preg_replace('/[^\w-' . $val . ']/iu', '', $var);
}

function sanitize($text) {
    static $arr = NULL;
    if ($arr === NULL) {
        $arr['search'] = array('\'', ' & ', '<', '>', '"', 'Ђ', '»', '«', '©', '®', '™', '„', '“');
        $arr['replace'] = array('&#039;', ' &amp; ', '&lt;', '&gt;', '&quot;', '&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;');
    }
    return str_replace($arr['search'], $arr['replace'], $text);
}

abstract class Tool {

    /* Метод проверки активности модулей apache */
    public static function apacheModul($module) {
        if (function_exists('apache_get_modules')) {
            static $modules = NULL;
            if (empty($modules)) {
                $modules = apache_get_modules();
            }
            return in_array($module, $modules) ? true : false;
        }
        return true;
    }

    /* Метод фильтрации на допустимый набор символов */
    public static function cleanAllow($value, $mask = NULL) {
        return trim(preg_replace('/[^\w-' . preg_quote($mask, '/') . ']/iu', '', $value));
    }

}

abstract class SX {

    public static $lang = array();
    protected static $_param = array();

    /* Метод получения параметров */
    public static function get($key, $default = NULL) {
        list($a, $count) = self::_parse($key);
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
        list($a, $count) = self::_parse($key);
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
        list($a, $count) = self::_parse($key);
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
        list($a, $count) = self::_parse($key);
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
    protected static function _parse($key) {
        static $cache = array();
        if (!isset($cache[$key])) {
            $array = explode('.', $key);
            $cache[$key] = array($array, count($array));
        }
        return $cache[$key];
    }

    /* Метод инициализации установки */
    public static function getInit() {
        date_default_timezone_set('Europe/Moscow');
        self::encoding();
        self::setLocale();
        self::versionPhp();
        self::getCheckCompiled();
        self::stripslashesArray();
        self::set('database', self::getConfig('db.config'));
        self::setDefine('PE', PHP_EOL);
        self::setDefine('BASE_PATH', self::basePatch());
        self::setDefine('THEME', SX_DIR . '/setup/theme');
        self::setDefine('SMARTY_DIR', SX_DIR . '/lib/smarty/');
        self::setDefine('SMARTY_RESOURCE_CHAR_SET', CHARSET);
        self::setDefine('SMARTY_RESOURCE_DATE_FORMAT', '%d.%m.%Y, %H:%M');
        spl_autoload_register(array('SX', 'setup_autoload'));
    }

    /* Метод установки кодировки */
    public static function encoding() {
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

    /* Метод получает путь вложенности сайта в подпапки */
    protected static function basePatch() {
        return str_replace('//', '/', str_replace('setup/setup.php', '/', $_SERVER['PHP_SELF']));
    }

    /* Метод установки локали PHP */
    public static function setLocale($langcode = 'ru') {
        header('Content-type: text/html; charset=' . CHARSET);
        $locale = $langcode . '_' . strtoupper($langcode);
        return setlocale(LC_ALL, array(
            $locale . '.UTF-8',
            $locale . '.utf-8',
            $locale . '.UTF8',
            $locale . '.utf8',
            $locale,
            $langcode,
        ));
    }

    /* Метод автозагрузки классов */
    public static function setup_autoload($class) {
        switch ($class) {
            case 'DB':
                include (SX_DIR . '/class/class.DB.php');
                break;
            case 'Smarty':
                include (SMARTY_DIR . 'Smarty.class.php');
                break;
            default:
                if (substr(strtolower($class), 0, 7) != 'smarty_') {
                    include (SX_DIR . '/setup/class/class.' . $class . '.php');
                }
                break;
        }
        return class_exists($class, false);
    }

    /* Метод подключения конфиг файла */
    public static function getConfig($name) {
        $config = array();
        include (SX_DIR . '/config/' . $name . '.php');
        return !empty($config) ? $config : '';
    }

    /* Метод установки define */
    public static function setDefine($define, $value = '') {
        if (!defined($define)) {
            define($define, $value);
        }
    }

    /* Метод определения браузера */
    public static function getAgent() {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'firefox')) {
            return 'firefox';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'safari')) {
            return 'safari';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'msie 8')) {
            return 'ie8';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'msie 7')) {
            return 'ie7';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'msie 6')) {
            return 'ie6';
        } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'opera')) {
            return 'opera';
        } else {
            return '';
        }
    }

    /* Метод определения установлена ли система */
    public static function getCheckInstall() {
        $config = self::getConfig('db.config');
        if (self::getConnect($config)) {
            self::setDefine('PREFIX', $config['dbprefix']);
            $check = DB::get()->fetch_object("SELECT Id FROM " . PREFIX . "_benutzer WHERE Id='1' LIMIT 1");
            if (is_object($check)) {
                View::get()->assign('title', self::$lang['NameSite']);
                View::get()->assign('content', View::get()->fetch(THEME . '/error.tpl'));
                View::get()->display(THEME . '/main.tpl');
                exit;
            }
        }
    }

    /* Метод пытается создать базу */
    public static function getCreateBase($config) {
        $link = mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass']);
        if (mysqli_connect_errno()) {
            mysqli_query($link, "CREATE DATABASE IF NOT EXISTS " . $config['dbname'] . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
            mysqli_close($link);
            return true;
        }
        return false;
    }

    /* Метод проверки подключения к базе */
    public static function getConnect($config) {
        if (!empty($config['dbhost']) && !empty($config['dbname'])) {
            if (mysqli_connect($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbport'])) {
                return true;
            }
        }
        return false;
    }

    /* Метод проверки доступности изменения места хранения сессий */
    public static function checkSession() {
        $result = false;
        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
            ini_set('session.save_handler', 'user');
            $result = ini_get('session.save_handler') == 'user' ? true : false;
        }
        return $result;
    }

    /* Метод проверки версии php */
    public static function checkVersion() {
        return version_compare(PHP_VERSION, '5.2.0', '<') ? true : false;
    }

    /* Метод определения установлена ли система */
    public static function versionPhp() {
        if (self::checkVersion()) {
            self::output('<pre><font color="#FF0000"><h2>Неудача!</h2></font> Система не может быть установлена, сервер использует PHP-версию ' . PHP_VERSION . '<br>Минимальная необходимая версия PHP для корректной работы системы <strong>5.2</strong></pre>', true);
        }
    }

    /* Метод проверки доступности папки кеша шаблонов на запись */
    public static function getCheckCompiled() {
        if (!is_writable(SX_DIR . '/temp/compiled/1/main')) {
            chmod(SX_DIR . '/temp/compiled/1/main', 0777);
        }
        if (!is_writable(SX_DIR . '/temp/compiled/1/main')) {
            self::output('<pre><font color="#FF0000"><h2>Неудача! / Ошибка!</h2></font>Каталог <strong>&quot;/temp/compiled/1/main/&quot;</strong> не имеет прав на запись!
		Пожалуйста измените права доступа на 777<br /></pre>', true);
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

    /* Метод вывода */
    public static function output($content, $exit = false) {
        echo $content;
        if ($exit === true) {
            if (!headers_sent()) {
                header('Date: ' . gmdate('D, d M Y H:i:s \G\M\T'));
                header('Server: Protected by SX CMS');
                header('X-Powered-By: SX CMS');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', gmmktime(0, 4, 0, 11, 1, 1974)));
            }
            exit;
        }
    }

    /* Метод заглушка */
    public static function syslog($action, $type = 1, $benutzer = 0) {

    }

}
