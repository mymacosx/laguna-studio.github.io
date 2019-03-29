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

abstract class Arr {

    /* Метод возвращает метод запроса */
    public static function request($type = NULL) {
        if (empty($type)) {
            $result = strtoupper(self::getServer('REQUEST_METHOD'));
        } else {
            $type = strtoupper($type);
            if ($type == 'AJAX') {
                $result = self::getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' ? true : false;
            } else {
                $result = strtoupper(self::getServer('REQUEST_METHOD')) == $type ? true : false;
            }
        }
        return $result;
    }

    /* Метод возвращает случайное значение из массива */
    public static function rand(array $array, $limit = 1) {
        return array_rand(array_flip($array), $limit);
    }

    /* Метод возвращает параметр массива возможно получение параметров массивом */
    public static function get($array, $key, $default = NULL) {
        return !is_array($key) ? self::_get($array, $key, $default) : self::_getArr($array, $key, $default);
    }

    /* Метод устанавливает параметр массива, возможна установка параметров массивом */
    public static function set($array, $key, $value = NULL) {
        return !is_array($key) ? self::_set($array, $key, $value) : self::_setArr($array, $key);
    }

    /* Метод проверяет на пустоту параметр массива, возможен массив параметров */
    public static function nil($array, $key) {
        return !is_array($key) ? self::_nil($array, $key) : self::_nilArr($array, $key);
    }

    /* Метод проверяет на существование параметр массива, возможен массив параметров */
    public static function has($array, $key) {
        return !is_array($key) ? self::_has($array, $key) : self::_hasArr($array, $key);
    }

    /* Метод удаляет параметр массива, возможен массив параметров */
    public static function del(&$array, $key) {
        foreach ((array) $key as $value) {
            unset($array[$value]);
        }
    }

    /* Метод возвращает параметр массива $_POST, возможно получение параметров массивом */
    public static function getPost($key, $default = NULL) {
        return !is_array($key) ? self::_get($_POST, $key, $default) : self::_getArr($_POST, $key, $default);
    }

    /* Метод устанавливает параметр массива $_POST, возможна установка параметров массивом */
    public static function setPost($key, $value = NULL) {
        return !is_array($key) ? self::_set($_POST, $key, $value) : self::_setArr($_POST, $key);
    }

    /* Метод проверяет на пустоту параметра массива $_POST, возможен массив параметров */
    public static function nilPost($key) {
        return !is_array($key) ? self::_nil($_POST, $key) : self::_nilArr($_POST, $key);
    }

    /* Метод проверяет на существование параметр массива $_POST, возможен массив параметров */
    public static function hasPost($key) {
        return !is_array($key) ? self::_has($_POST, $key) : self::_hasArr($_POST, $key);
    }

    /* Метод удаляет параметр массива $_POST, возможен массив параметров */
    public static function delPost($key) {
        foreach ((array) $key as $value) {
            unset($_POST[$value]);
        }
    }

    /* Метод возвращает параметр массива $_GET, возможно получение параметров массивом */
    public static function getGet($key, $default = NULL) {
        return !is_array($key) ? self::_get($_GET, $key, $default) : self::_getArr($_GET, $key, $default);
    }

    /* Метод устанавливает параметр массива $_GET, возможна установка параметров массивом */
    public static function setGet($key, $value = NULL) {
        return !is_array($key) ? self::_set($_GET, $key, $value) : self::_setArr($_GET, $key);
    }

    /* Метод проверяет на пустоту параметра массива $_GET, возможен массив параметров */
    public static function nilGet($key) {
        return !is_array($key) ? self::_nil($_GET, $key) : self::_nilArr($_GET, $key);
    }

    /* Метод проверяет на существование параметр массива $_GET, возможен массив параметров */
    public static function hasGet($key) {
        return !is_array($key) ? self::_has($_GET, $key) : self::_hasArr($_GET, $key);
    }

    /* Метод удаляет параметр массива $_GET, возможен массив параметров */
    public static function delGet($key) {
        foreach ((array) $key as $value) {
            unset($_GET[$value]);
        }
    }

    /* Метод возвращает параметр массива $_REQUEST, возможно получение параметров массивом */
    public static function getRequest($key, $default = NULL) {
        return !is_array($key) ? self::_get($_REQUEST, $key, $default) : self::_getArr($_REQUEST, $key, $default);
    }

    /* Метод устанавливает параметр массива $_REQUEST, возможна установка параметров массивом */
    public static function setRequest($key, $value = NULL) {
        return !is_array($key) ? self::_set($_REQUEST, $key, $value) : self::_setArr($_REQUEST, $key);
    }

    /* Метод проверяет на пустоту параметра массива $_REQUEST, возможен массив параметров */
    public static function nilRequest($key) {
        return !is_array($key) ? self::_nil($_REQUEST, $key) : self::_nilArr($_GET, $_REQUEST);
    }

    /* Метод проверяет на существование параметр массива $_REQUEST, возможен массив параметров */
    public static function hasRequest($key) {
        return !is_array($key) ? self::_has($_REQUEST, $key) : self::_hasArr($_REQUEST, $key);
    }

    /* Метод удаляет параметр массива $_REQUEST, возможен массив параметров */
    public static function delRequest($key) {
        foreach ((array) $key as $value) {
            unset($_REQUEST[$value]);
        }
    }

    /* Метод возвращает параметр массива $_SESSION, возможно получение параметров массивом */
    public static function getSession($key, $default = NULL) {
        return !is_array($key) ? self::_get($_SESSION, $key, $default) : self::_getArr($_SESSION, $key, $default);
    }

    /* Метод устанавливает параметр массива $_SESSION, возможна установка параметров массивом */
    public static function setSession($key, $value = NULL) {
        return !is_array($key) ? self::_set($_SESSION, $key, $value) : self::_setArr($_SESSION, $key);
    }

    /* Метод проверяет на пустоту параметра массива $_SESSION, возможен массив параметров */
    public static function nilSession($key) {
        return !is_array($key) ? self::_nil($_SESSION, $key) : self::_nilArr($_GET, $_SESSION);
    }

    /* Метод проверяет на существование параметр массива $_SESSION, возможен массив параметров */
    public static function hasSession($key) {
        return !is_array($key) ? self::_has($_SESSION, $key) : self::_hasArr($_SESSION, $key);
    }

    /* Метод удаляет параметр массива $_SESSION, возможен массив параметров */
    public static function delSession($key) {
        foreach ((array) $key as $value) {
            unset($_SESSION[$value]);
        }
    }

    /* Метод возвращает параметр массива $_SERVER, возможно получение параметров массивом */
    public static function getServer($key, $default = NULL) {
        return !is_array($key) ? self::_get($_SERVER, $key, $default) : self::_getArr($_SERVER, $key, $default);
    }

    /* Метод устанавливает параметр массива $_SERVER, возможна установка параметров массивом */
    public static function setServer($key, $value = NULL) {
        return !is_array($key) ? self::_set($_SERVER, $key, $value) : self::_setArr($_SERVER, $key);
    }

    /* Метод проверяет на пустоту параметра массива $_SERVER, возможен массив параметров */
    public static function nilServer($key) {
        return !is_array($key) ? self::_nil($_SERVER, $key) : self::_nilArr($_GET, $key);
    }

    /* Метод проверяет на существование параметр массива $_SERVER, возможен массив параметров */
    public static function hasServer($key) {
        return !is_array($key) ? self::_has($_SERVER, $key) : self::_hasArr($_SERVER, $key);
    }

    /* Метод удаляет параметр массива $_SERVER, возможен массив параметров */
    public static function delServer($key) {
        foreach ((array) $key as $value) {
            unset($_SERVER[$value]);
        }
    }

    /* Метод получения параметра массива $_COOKIE, возможно получение параметров массивом */
    public static function getCookie($key, $default = '') {
        return !is_array($key) ? self::_get($_COOKIE, $key, $default) : self::_getArr($_COOKIE, $key, $default);
    }

    /* Метод установки параметра массива $_COOKIE */
    public static function setCookie($key, $value, $time = 86400, $path = '', $domain = '', $secure = false, $httponly = false) {
        if (is_numeric($time)) {
            $time += time();
        } else {
            $time = strtotime($time);
            $time = is_numeric($time) ? $time : 86400;
        }
        if (empty($path)) {
            $path = BASE_PATH;
        }
        $send = setcookie($key, $value, $time, $path, $domain, $secure, $httponly);
        if ($send) {
            $_COOKIE[$key] = $value;
        }
        return $send;
    }

    /* Метод проверяет на пустоту параметра массива $_COOKIE, возможен массив параметров */
    public static function nilCookie($key) {
        return !is_array($key) ? self::_nil($_COOKIE, $key) : self::_nilArr($_COOKIE, $key);
    }

    /* Метод проверяет на существование параметр массива $_COOKIE, возможен массив параметров */
    public static function hasCookie($key) {
        return !is_array($key) ? self::_has($_COOKIE, $key) : self::_hasArr($_COOKIE, $key);
    }

    /* Метод удаления параметра массива $_COOKIE */
    public static function delCookie($key, $path = '', $domain = '', $secure = false, $httponly = false) {
        $time = (3600 * 24 * 365) + time();
        if (empty($path)) {
            $path = BASE_PATH;
        }
        $send = setcookie($key, NULL, $time, $path, $domain, $secure, $httponly);
        if ($send) {
            unset($_COOKIE[$key]);
        }
        return $send;
    }

    /* Метод возвращает значение массива по ключу */
    protected static function _get($array, $key, $default = NULL) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /* Метод возвращает массив значений из другого массива */
    protected static function _getArr($array, $keys, $default = NULL) {
        $result = array();
        foreach ((array) $keys as $key => $val) {
            if (is_string($key)) {
                $result[$key] = self::_get($array, $key, $val);
            } else {
                $result[$val] = self::_get($array, $val, $default);
            }
        }
        return $result;
    }

    /* Метод устанавливает значение массива по ключу */
    protected static function _set(&$array, $key, $value = NULL) {
        $array[$key] = $value;
    }

    /* Метод устанавливает массив значений из другого массива */
    protected static function _setArr(&$array, $keys) {
        foreach ($keys as $key => $val) {
            self::_set($array, $key, $val);
        }
    }

    /* Метод проверки на пустоту параметра в массиве */
    protected static function _nil($array, $key) {
        return empty($array[$key]);
    }

    /* Метод массовой проверки на пустоту параметров в массиве */
    protected static function _nilArr($array, $keys) {
        $result = false;
        foreach ((array) $keys as $value) {
            if (($result = self::_nil($array, $value)) !== true) {
                return false;
            }
        }
        return $result;
    }

    /* Метод проверки существования параметра в массиве */
    protected static function _has($array, $key) {
        return isset($array[$key]);
    }

    /* Метод массовой проверки существования параметров в массиве */
    protected static function _hasArr($array, $keys) {
        $result = false;
        foreach ((array) $keys as $value) {
            if (($result = self::_has($array, $value)) !== true) {
                return false;
            }
        }
        return $result;
    }

}
