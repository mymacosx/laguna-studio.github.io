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

/* Метод преобразования формата, пример: вход = 1234.56 выход = 1234.56 */
function numf($num) {
    return number_format((double) $num, '2', '.', '');
}

/* Метод преобразования в латиницу */
function translit($text, $sep = '-') {
    static $object = null;
    if ($object === null) {
        $object = Text::get();
    }

    static $cache = array();
    if (!isset($cache[$text])) {
        $result = $object->translit($text, $sep);
        $cache[$text] = !empty($result) ? trim($result, $sep) : $sep;
    }
    return $cache[$text];
}

/* Метод замены символов для RSS */
function sanitizeRss($text) {
    static $array = array(
        'search'  => array('&bdquo;', '&ldquo;', '&ndash;', '&copy;', '&reg;', '&pound;', '&laquo;', '&raquo;', '&quot;', '&', '<o:p>', '</o:p>', '&nbsp;', '&trade;', '&#8482;', '&#8364;', '&euro;', '&hellip;', '&bull;'),
        'replace' => array('„', '“', '-', '©', '®', 'Ј', '«', '»', '"', '&amp;', '<p>', '</p>', ' ', '™', '™', 'Ђ', 'Ђ', '…', '•'),
    );
    $text = preg_replace('#\[sx_code lang=(.*?)\](.*?)\[\/sx_code\]#siu', '\\2', $text);
    return str_replace($array['search'], $array['replace'], $text);
}

/* Метод замены символов на сущности */
function sanitize($text) {
    static $cache = array();
    static $array = array(
        '\''  => '&#039;',
        ' & ' => ' &amp; ',
        '<'   => '&lt;',
        '>'   => '&gt;',
        '"'   => '&quot;',
        'Ђ'   => '&euro;',
        '»'   => '&raquo;',
        '«'   => '&laquo;',
        '©'   => '&copy;',
        '®'   => '&reg;',
        '™'   => '&trade;',
        '„'   => '&bdquo;',
        '“'   => '&ldquo;',
    );
    if (!isset($cache[$text])) {
        $cache[$text] = strtr($text, $array);
    }
    return $cache[$text];
}

/* Метод установки администраторских прав группы */
function perm($action) {
    static $arr = NULL;
    if ($arr === NULL) {
        $arr['p'] = Arr::getSession('perm_admin');
        $arr['a'] = $_SESSION['a_area'];
        $arr['a'] = is_numeric($arr['a']) ? $arr['a'] : 1;
    }
    if (isset($arr['p'][$action . $arr['a']]) && $arr['p'][$action . $arr['a']] == 1) {
        return true;
    }
    if (isset($arr['p']['all' . $arr['a']]) && $arr['p']['all' . $arr['a']] == 1) {
        return true;
    }
    if (Arr::getSession('user_group') == 1) {
        return true;
    }
    return false;
}

/* Метод установки пользовательских прав группы */
function permission($action) {
    static $arr = NULL;
    if ($arr === NULL) {
        $arr['p'] = Arr::getSession('perm');
        $arr['a'] = Arr::getSession('area');
        $arr['a'] = is_numeric($arr['a']) ? $arr['a'] : 1;
    }
    if (isset($arr['p'][$action . $arr['a']]) && $arr['p'][$action . $arr['a']] == 1) {
        return true;
    }
    if (isset($arr['p']['all' . $arr['a']]) && $arr['p']['all' . $arr['a']] == 1) {
        return true;
    }
    if (Arr::getSession('user_group') == 1) {
        return true;
    }
    return false;
}

/* Метод установки активности модулей в админ панели */
function admin_active($param) {
    static $active = NULL;
    if ($active === NULL) {
        $active = SX::get('admin_active');
        $active['Aktiv_Modul'] = SX::get('admin.Aktiv_Modul');
    }
    if ((isset($active[$param]) && $active[$param] == 1) || $active['Aktiv_Modul'] == 1) {
        return true;
    }
    return false;
}

/* Метод проверки активности модулей */
function get_active($param) {
    static $active = NULL;
    if ($active === NULL) {
        $active = SX::get('active');
    }
    if (isset($active[$param]) && $active[$param] == 1) {
        return true;
    }
    return false;
}
