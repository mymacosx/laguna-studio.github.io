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

abstract class Tool {

    /* Метод генерации случайного набора md5 */
    public static function uniqid($value = NULL) {
        return md5(uniqid(mt_rand(), true) . $value);
    }

    /* Метод приведения даты к единому стандарту */
    public static function formatDate($date) {
        $array = array();
        if (preg_match('/^(\d{1,2}+)[ -.,:;\/\|](\d{1,2}+)[ -.,:;\/\|](\d{2,4}+)$/u', trim($date), $array)) {
            $date = mktime(0, 0, 0, $array[2], $array[1], $array[3]);
            if ($date !== false) {
                return date('d.m.Y', $date);
            }
        }
        return '';
    }

    /* Метод получения расширения файла */
    public static function extension($file, $point = false) {
        $value = pathinfo($file, PATHINFO_EXTENSION);
        if ($point === true && !empty($value)) {
            $value = '.' . $value;
        }
        return strtolower($value);
    }

    /* Метод возвращает хеш пароля и осуществляет фильтрацию пароля по единому стандарту */
    public static function getPass($value, $md5 = true) {
        $value = preg_replace('/[^\w-]/iu', '', $value);
        return $md5 === true ? md5(md5($value)) : $value;
    }

    /* Метод фильтрации лишних пробелов */
    public static function cleanSpace($value) {
        return preg_replace('!\s+!u', ' ', $value);
    }

    /* Метод фильтрации числа */
    public static function cleanDigit($value) {
        return preg_replace('/[^\d]/u', '', $value);
    }

    /* Метод фильтрации на допустимый набор символов */
    public static function cleanString($value, $mask = NULL) {
        return preg_replace('/[^a-z0-9' . preg_quote($mask, '/') . ']/iu', '', $value);
    }

    /* Метод фильтрации на допустимый набор символов */
    public static function cleanAllow($value, $mask = NULL) {
        return trim(preg_replace('/[^\w-' . preg_quote($mask, '/') . ']/iu', '', $value));
    }

    /* Метод фильтрации электронного адреса */
    public static function cleanMail($value) {
        return strtolower(filter_var($value, FILTER_SANITIZE_EMAIL));
    }

    /* Метод фильтрации адреса страницы */
    public static function cleanUrl($value) {
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    /* Метод проверки числа */
    public static function isDigit($value) {
        return (bool) preg_match('/^[\d]+$/u', $value);
    }

    /* Метод проверки почтового адреса */
    public static function isAddress($value) {
        return (bool) preg_match('/^[\w-,.;№#\/ ]+$/iu', $value);
    }

    /* Метод проверки на допустимый набор символов */
    public static function isAllow($value) {
        return (bool) preg_match('/^[\w-,.;№#\/)&(+ ]+$/iu', $value);
    }

    /* Метод проверки электронного адреса */
    public static function isMail($value) {
        if (!empty($value)) {
            return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
        }
        return false;
    }

    /* Метод проверки адреса страницы */
    public static function isUrl($value) {
        if (!empty($value)) {
            return (bool) filter_var($value, FILTER_VALIDATE_URL);
        }
        return false;
    }

    /* Метод получения имени текущего пользователя */
    public static function fullName() {
        $result = '';
        if ($_SESSION['loggedin'] == 1) {
            if (!empty($_SESSION['benutzer_vorname'])) {
                $result .= $_SESSION['benutzer_vorname'] . ' ';
            }
            if (!empty($_SESSION['benutzer_nachname'])) {
                $result .= $_SESSION['benutzer_nachname'] . ' ';
            }
            return !empty($result) ? $result . '(' . $_SESSION['user_name'] . ')' : $_SESSION['user_name'];
        }
        return SX::$lang['Guest'];
    }

    /* Метод разворота текста */
    public static function repeat($text) {
        if (!empty($text)) {
            $text = strip_tags($text);
            $array = explode(SX::$lang['PageSep'], $text);
            $array = array_reverse($array);
            $text = implode(SX::$lang['PageSep'], $array);
        }
        return sanitize($text);
    }

    /* Метод удаления из контента тегов */
    public static function cleanTags($text, $array = NULL) {
        if (!empty($array)) {
            foreach ($array as $arr) {
                $arr = strtoupper(trim($arr));
                $out[] = $arr == 'NEU' ? '/\[--' . $arr . '--\]/iu' : '/\[' . $arr . ':(.*)\]/iu';
            }
            $text = preg_replace($out, '', $text);
        }
        return $text;
    }

    /* Метод реплейза текста на текст с подсветкой */
    public static function highlight($text, $pattern = '') {
        $high = Arr::getRequest('high');
        if ((!empty($high) && Text::get()->strlen($high) >= 3) || !empty($pattern)) {
            $w = !empty($pattern) ? preg_quote(trim($pattern)) : preg_quote(trim($high));
            $text = preg_replace("/($w)(?![^<]+>)/iu", '<span class="highlight">' . $w . '</span>', $text);
        }
        return $text;
    }

    /* Метод получения списка стран */
    public static function countries() {
        static $cache = array();
        if (empty($cache)) {
            $cache = DB::get()->fetch_assoc_all("SELECT SQL_CACHE * FROM " . PREFIX . "_laender WHERE Aktiv='1' ORDER BY Name ASC");
        }
        return $cache;
    }

    /* Метод получения названия группы пользователя */
    public static function userGroup($id) {
        static $cache = array();
        if (!isset($cache[$id])) {
            $row = DB::get()->cache_fetch_assoc("SELECT Name FROM " . PREFIX . "_benutzer_gruppen WHERE Id='" . intval($id) . "' LIMIT 1");
            $cache[$id] = isset($row['Name']) ? sanitize($row['Name']) : '';
        }
        return $cache[$id];
    }

    /* Метод получения логина пользователя с вариантом на входе массива */
    public static function userName($id) {
        static $cache = array();
        if (!isset($cache[$id])) {
            $row = DB::get()->cache_fetch_assoc("SELECT Benutzername FROM " . PREFIX . "_benutzer WHERE Id='" . intval($id) . "' LIMIT 1");
            $cache[$id] = isset($row['Benutzername']) ? sanitize($row['Benutzername']) : '';
        }
        return $cache[$id];
    }

    /* Метод вывода количества комментариев объекта */
    public static function countComments($id, $objekt) {
        if (Arr::getRequest('p') != 'index') {
            $DB = DB::get();
            $res = $DB->cache_fetch_assoc("SELECT COUNT(Id) AS CCount FROM " . PREFIX . "_kommentare WHERE Objekt_Id='" . $DB->escape($id) . "' AND Bereich='" . $DB->escape($objekt) . "' AND Aktiv = '1'");
            return isset($res['CCount']) ? $res['CCount'] : 0;
        }
        return '';
    }

    /* Метод вывода рейтинга объекта */
    public static function rating($id, $where) {
        if (Arr::getRequest('p') != 'index') {
            $DB = DB::get();
            $res = $DB->cache_fetch_assoc("SELECT SUM(Wertung) AS GesamtWertung, SUM(Gesamt) AS Abstimmungen FROM " . PREFIX . "_wertung WHERE Objekt_Id='" . $DB->escape($id) . "' AND Bereich='" . $DB->escape($where) . "'");
            if (isset($res['GesamtWertung'], $res['Abstimmungen'])) {
                return ceil(number_format(($res['GesamtWertung'] / $res['Abstimmungen']), '2', '.', ''));
            }
        }
        return '';
    }

    /* Метод формирования ссылки на изображение */
    public static function thumb($action, $image = NULL, $width = 100, $cache = true) {
        if (!empty($image)) {
            $file = md5($image . '_' . $width) . Tool::extension($image, true);
            if ($cache === true && is_file(TEMP_DIR . '/cache/' . $file)) {
                return BASE_URL . '/temp/cache/' . $file;
            }
        }
        return BASE_URL . '/lib/image.php?action=' . $action . '&amp;width=' . $width . '&amp;image=' . $image;
    }

    /* Метод реплейза в контенте тегов вставленных изображений [SCREEN:X] */
    public static function screens($array, $text, $width = 50) {
        $screens = unserialize($array);
        if ($screens) {
            foreach ($screens as $key => $val) {
                $val['text'] = str_replace("\r\n", "<br />", $val['text']);
                $val['text'] = preg_replace("#\[b\](.*?)\[/b\]#siu", "<span style=\"font-weight:bold\">\\1</span>", $val['text']);
                $val['text'] = preg_replace("#\[s\](.*?)\[/s\]#siu", "<span style=\"text-decoration:line-through\">\\1</span>", $val['text']);
                $val['text'] = preg_replace("#\[u\](.*?)\[/u\]#siu", "<span style=\"text-decoration:underline\">\\1</span>", $val['text']);
                $val['text'] = preg_replace("#\[i\](.*?)\[/i\]#siu", "<span style=\"font-style:italic\">\\1</span>", $val['text']);
                $content = View::get()->fetch(THEME . '/other/screenshot.tpl');
                $array = array(
                    '%%src%%'   => Tool::thumb('screenshots', $val['id'], $width),
                    '%%title%%' => $val['titel'],
                    '%%text%%'  => $val['text'],
                    '%%id%%'    => $val['id'],
                );
                $content = Text::get()->replace($content, $array);
                $text = str_replace('[SCREEN:' . $key . ']', $content, $text);
            }
        }
        return $text;
    }

    public static function censored($value) {
        if (!empty($value)) {
            $baw = SX::get('system.Spamwoerter');
            if (!empty($baw)) {
                $baw = str_replace(array("\r\n", "\n"), ',', trim($baw));
                $bwrp = SX::get('system.SpamRegEx');
                if (empty($bwrp)) {
                    $bwrp = '***';
                }
                $bwarray = explode(',', $baw);
                if ($baw) {
                    foreach ($bwarray as $key => $val) {
                        $value = preg_replace('#([^\w])' . $val . '#iu', '\\1' . $bwrp, $value);
                    }
                }
            }
        }
        return $value;
    }

    public static function checkSpam($value) {
        $spamwoerter = str_replace(array("\r\n", "\n"), ',', SX::get('system.Spamwoerter'));
        $spamwoerter = explode(',', $spamwoerter);
        if (empty($value)) {
            foreach ($_POST as $fieldvalue) {
                foreach ($spamwoerter as $stopwords) {
                    if (preg_match('/.*' . trim($stopwords) . '.*/iu', $fieldvalue)) {
                        return false;
                    }
                }
            }
        } else {
            foreach ($spamwoerter as $stopwords) {
                if (preg_match('/.*' . trim($stopwords) . '.*/iu', $value)) {
                    return false;
                }
            }
        }
        return true;
    }

    /* Метод генерации случайного набора символов */
    public static function random($length = 8, $type = 'аll', $chars = NULL) {
        static $array = array(
            'аll'   => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'alfa'  => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'lower' => 'abcdefghijklmnopqrstuvwxyz',
            'upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'digit' => '0123456789');
        $chars .= isset($array[$type]) ? $array[$type] : $array['аll'];
        $result = NULL;
        $count = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars{mt_rand(0, $count)};
        }
        return $result;
    }

    /* Метод вывода количества страниц */
    public static function getLim($limit = 15) {
        return !empty($_REQUEST['pp']) ? intval($_REQUEST['pp']) : intval($limit);
    }

    /* Метод формирующий номер страницы в <title> */
    public static function numPage($param = 'page') {
        $result = '';
        if (!empty($_REQUEST[$param]) && is_numeric($_REQUEST[$param]) && $_REQUEST[$param] > 1) {
            $result = ' (' . SX::$lang['PageNavi_Page'] . ' ' . $_REQUEST[$param] . ')';
        }
        return $result;
    }

    public static function prePage($max = 0) {
        if (!empty($_REQUEST['page']) && is_numeric($_REQUEST['page']) && $_REQUEST['page'] > 0) {
            $page = !empty($max) && $_REQUEST['page'] > $max ? $max : intval($_REQUEST['page']);
        } else {
            $page = 1;
        }
        $_REQUEST['page'] = $page;
        return $page;
    }

    public static function aktPage() {
        return (!empty($_REQUEST['page']) && is_numeric($_REQUEST['page']) && $_REQUEST['page'] > 0) ? intval($_REQUEST['page']) : 1;
    }

    public static function getLimit($limit, $max = 0) {
        return Tool::prePage($max) * $limit - $limit;
    }

    /* Метод получения определенных данных из таблицы текущего пользователя */
    public static function userSettings($val, $default = '') {
        static $array = array();
        if ($_SESSION['benutzer_id'] != 0) {
            if (empty($array)) {
                $array = DB::get()->fetch_assoc("SELECT * FROM " . PREFIX . "_benutzer WHERE Id = '" . $_SESSION['benutzer_id'] . "' AND Kennwort = '" . Arr::getSession('login_pass') . "' LIMIT 1");
            }
            return isset($array[$val]) ? $array[$val] : $default;
        }
        return $default;
    }

    /* Очищаем таблицу */
    public static function cleanTable($table) {
        if (!empty($table)) {
            DB::get()->query("TRUNCATE " . PREFIX . "_" . $table);
        }
    }

    /* Метод получения номера сообщения в топике */
    public static function countPost($post_id, $topic_id, $limit = 15) {
        $count = 0;
        $sql = DB::get()->cache_fetch_assoc_all("SELECT id FROM " . PREFIX . "_f_post WHERE topic_id = '" . DB::get()->escape($topic_id) . "' ORDER BY id ASC");
        foreach ($sql as $row) {
            $count++;
            if ($post_id == $row['id']) {
                break;
            }
        }
        $page = ceil($count / $limit);
        return $page;
    }

    /* Метод установки прав доступа к форуму */
    public static function accessForum($forum_id) {
        static $cache = array();
        if (!isset($cache[$forum_id])) {
            $all_permissions = array();
            $group_id = Arr::getSession('user_group');
            $DB = DB::get();
            $sql = $DB->cache_fetch_assoc_all("SELECT permissions, forum_id, group_id FROM " . PREFIX . "_f_permissions");
            foreach ($sql as $row) {
                if ($row['forum_id'] == $forum_id && $row['group_id'] == $group_id) {
                    $res = $row['permissions'];
                    break;
                }
            }
            $check_perms = $DB->cache_fetch_assoc_all("SELECT id, group_id FROM " . PREFIX . "_f_forum");
            foreach ($check_perms as $row_perms) {
                if ($row_perms['id'] == $forum_id) {
                    $in_array = explode(',', $row_perms['group_id']);
                    break;
                }
            }
            $permissions = !in_array($group_id, $in_array) ? array() : explode(',', $res);
            $all_permissions[0] = $all_permissions['FORUM_SEE'] = $permissions[0] == 1 ? 1 : 0;
            $all_permissions[1] = $all_permissions['FORUM_SEE_TOPIC'] = $permissions[1] == 1 ? 1 : 0;
            $all_permissions[2] = $all_permissions['FORUM_SEE_DELETE_MESSAGE'] = $permissions[2] == 1 ? 1 : 0;
            $all_permissions[3] = $all_permissions['FORUM_SEARCH_FORUM'] = $permissions[3] == 1 ? 1 : 0;
            $all_permissions[4] = $all_permissions['FORUM_DOWNLOAD_ATTACHMENT'] = $permissions[4] == 1 ? 1 : 0;
            $all_permissions[5] = $all_permissions['FORUM_CREATE_TOPIC'] = $permissions[5] == 1 ? 1 : 0;
            $all_permissions[6] = $all_permissions['FORUM_REPLY_OWN_TOPIC'] = $permissions[6] == 1 ? 1 : 0;
            $all_permissions[7] = $all_permissions['FORUM_REPLY_OTHER_TOPIC'] = $permissions[7] == 1 ? 1 : 0;
            $all_permissions[8] = $all_permissions['FORUM_UPLOAD_ATTACHMENT'] = $permissions[8] == 1 ? 1 : 0;
            $all_permissions[9] = $all_permissions['FORUM_RATE_TOPIC'] = $permissions[9] == 1 ? 1 : 0;
            $all_permissions[10] = $all_permissions['FORUM_EDIT_OWN_POST'] = $permissions[10] == 1 ? 1 : 0;
            $all_permissions[11] = $all_permissions['FORUM_DELETE_OWN_POST'] = $permissions[11] == 1 ? 1 : 0;
            $all_permissions[12] = $all_permissions['FORUM_MOVE_OWN_TOPIC'] = $permissions[12] == 1 ? 1 : 0;
            $all_permissions[13] = $all_permissions['FORUM_CLOSE_OPEN_OWN_TOPIC'] = $permissions[13] == 1 ? 1 : 0;
            $all_permissions[14] = $all_permissions['FORUM_DELETE_OWN_TOPIC'] = $permissions[14] == 1 ? 1 : 0;
            $all_permissions[15] = $all_permissions['FORUM_DELETE_OTHER_POST'] = $permissions[15] == 1 ? 1 : 0;
            $all_permissions[16] = $all_permissions['FORUM_EDIT_OTHER_POST'] = $permissions[16] == 1 ? 1 : 0;
            $all_permissions[17] = $all_permissions['FORUM_OPEN_TOPIC'] = $permissions[17] == 1 ? 1 : 0;
            $all_permissions[18] = $all_permissions['FORUM_CLOSE_TOPIC'] = $permissions[18] == 1 ? 1 : 0;
            $all_permissions[19] = $all_permissions['FORUM_CHANGE_TOPICTYPE'] = $permissions[19] == 1 ? 1 : 0;
            $all_permissions[20] = $all_permissions['FORUM_MOVE_TOPIC'] = $permissions[20] == 1 ? 1 : 0;
            $all_permissions[21] = $all_permissions['FORUM_DELETE_TOPIC'] = $permissions[21] == 1 ? 1 : 0;
            $cache[$forum_id] = $all_permissions;
        }
        return $cache[$forum_id];
    }

    /* Метод удаления из контента тега видео с ютуба */
    public static function cleanVideo($string) {
        $string = preg_replace('!\[(?i)youtube:([\w-:\)#=\+\^ ]+)\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu', '', $string);
        $string = preg_replace('!\[(?i)youtube\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu', '', $string);
        $string = preg_replace('!\[(?i)youtube-small:([\w-:\)#=\+\^ ]+)\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu', '', $string);
        $string = preg_replace('!\[(?i)youtube-small\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu', '', $string);
        return $string;
    }

    /* Метод обрезки текста с сохранением вложенности тегов html */
    public static function truncateHtml($text, $size = 80, $finisher = '...') {
        $object = Text::get();

        $len = $object->strlen($text);
        if ($len <= $size) {
            return $text;
        }
        $position = -1;
        $openTagList = array();
        $state = $quoteType = $closeFlag = $tagNameStartPos = $tagNameEndPos = $textLen = 0;
        while (($position + 1) < $len && $textLen < $size) {
            $position++;
            $char = $text{$position};
            switch ($state) {
                case 0:
                    if ($char == '<') {
                        $state = 1;
                        $tagNameStartPos = $position + 1;
                        continue;
                    }
                    $textLen++;
                    break;
                case 1:
                    if ($char == ' ' || $char == "\t") {
                        $tagNameLen = $position - $tagNameStartPos;
                        $state = 2;
                        continue;
                    }
                    if ($char == '/') {
                        if ($tagNameStartPos == $position) {
                            continue;
                        }
                        $tagNameLen = $position - $tagNameStartPos + 1;
                        $state = 4;
                        continue;
                    }
                    if ($char == '>') {
                        $tagNameLen = $position - $tagNameStartPos;
                        $tagName = $object->substr($text, $tagNameStartPos, $tagNameLen);
                        if ($tagName{0} == '/') {
                            if (count($openTagList) && $openTagList[count($openTagList) - 1] == $object->substr($tagName, 1)) {
                                array_pop($openTagList);
                            }
                        } else {
                            if ($object->substr($tagName, -1, 1) != '/') {
                                $openTagList[] = $tagName;
                            }
                        }
                        $state = 0;
                        continue;
                    }
                    if (!(($char >= 'A' && $char <= 'Z') || ($char >= 'a' && $char <= 'z'))) {
                        $state = 0;
                        continue;
                    }
                    break;
                case 2:
                    if ($char == '/') {
                        $state = 4;
                        continue;
                    }
                    if ($char == '>') {
                        $tagName = $object->substr($text, $tagNameStartPos, $tagNameLen);
                        if (count($openTagList) && $openTagList[count($openTagList) - 1] == $object->substr($tagName, 1)) {
                            if ($openTagList[count($openTagList)] == $object->substr($tagName, 1)) {
                                array_pop($openTagList);
                            }
                        } else {
                            if ($object->substr($tagName, -1, 1) != '/') {
                                $openTagList[] = $tagName;
                            }
                        }
                        $state = 0;
                        continue;
                    }
                    if ($char == '"' || $char == "'") {
                        $quoteType = $char == '"' ? 2 : 1;
                        $state = 3;
                        continue;
                    }
                    break;
                case 3:
                    if (($char == '"' && $quoteType == 2) || ($char == "'" && $quoteType == 1)) {
                        $state = 2;
                        continue;
                    }
                    break;
                case 4:
                    if ($char == ' ' || $char == "\t") {
                        continue;
                    }
                    if ($char == '>') {
                        $tagName = $object->substr($text, $tagNameStartPos, $tagNameLen);
                        if ($tagName{0} != '/') {
                            if (count($openTagList) && $openTagList[count($openTagList) - 1] == $object->substr($tagName, 1)) {
                                array_pop($openTagList);
                            }
                        } else {
                            if ($object->substr($tagName, -1, 1) != '/') {
                                $openTagList[] = $tagName;
                            }
                        }
                        $state = 0;
                        continue;
                    }
                    $state = 0;
                    break;
            }
        }
        $output = $object->substr($text, 0, $position + 1) . (($position + 1) != $len ? $finisher : '');
        while ($tag = array_pop($openTagList)) {
            $output .= '</' . $tag . '>';
        }
        return $output;
    }

    /* Метод формирует параметры случайной выборки */
    public static function randQuery($array = array()) {
        $num = count($array);
        if ($num > 0) {
            $rand = array();
            $type = array('ASC', 'DESC');
            shuffle($array);
            $rand_num = mt_rand(1, $num);
            $array_rand = array_rand($array, $rand_num);
            if (is_array($array_rand)) {
                foreach ($array_rand as $val) {
                    $rand[] = $array[$val] . ' ' . $type[array_rand($type)];
                }
            } else {
                $rand[] = $array[$array_rand] . ' ' . $type[array_rand($type)];
            }
            return implode(', ', $rand);
        }
        return '';
    }

    /* Устанавливаем браузер для шаблонов */
    public static function browser() {
        switch (SX::object('Agent')->browser) {
            case 'Firefox': return 'firefox';
            case 'Opera' : return 'opera';
            case 'Safari' : return 'safari';
            case 'Internet Explorer' :
                switch (SX::object('Agent')->version) {
                    case '10.0' : return 'ie10';
                    case '9.0' : return 'ie9';
                    case '8.0' : return 'ie8';
                    case '7.0' : return 'ie7';
                    case '6.0' : return 'ie6';
                }
            default: return '';
        }
    }

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

    /* Метод установки приветствия в зависимости от времени суток */
    public static function welcome() {
        $time = date('H');
        if ($time >= 0 && $time < 5) {
            $welcome = SX::$lang['GoodNight'];
        } elseif ($time >= 5 && $time < 10) {
            $welcome = SX::$lang['GoodMorning'];
        } elseif ($time >= 10 && $time < 18) {
            $welcome = SX::$lang['GoodDay'];
        } elseif ($time >= 18 && $time < 24) {
            $welcome = SX::$lang['GoodEvening'];
        } else {
            $welcome = SX::$lang['Welcome'];
        }
        return $welcome;
    }

    /* Метод проверяет на запрет адрес письма и домен адреса письма */
    public static function lockedMail($email) {
        if (Tool::isMail($email)) {
            $DB = DB::get();
            $domain = explode('@', $email);
            $where[] = "Email = '" . $DB->escape(strtolower($email)) . "'";
            $where[] = "Email = '*@" . $DB->escape($domain[1]) . "'";
            $array = $DB->fetch_assoc("SELECT Id FROM " . PREFIX . "_banned WHERE " . implode(' OR ', $where) . " AND Aktiv = '1' LIMIT 1");
            return isset($array['Id']) ? true : false;
        }
        return false;
    }

    public static function getPatch() {
        static $result = NULL;
        if ($result === NULL) {
            $result = str_replace(array('/admin/index.php', '/lib/cron.php', '/index.php', '/yarss.php', '//'), '/', $_SERVER['PHP_SELF']);
        }
        return $result;
    }

    public static function prefixPatch($text, $clean = false) {
        $patch = $clean === true ? '' : self::getPatch();
        return str_replace('%%ECRUOS_GMI_IBOOK%%', $patch, $text);
    }

    public static function patchPrefix($text) {
        $patch = self::getPatch();
        $array = array(
            'src="' . $patch    => 'src="%%ECRUOS_GMI_IBOOK%%',
            'src=\'' . $patch   => 'src=\'%%ECRUOS_GMI_IBOOK%%',
            $patch . 'uploads/' => '%%ECRUOS_GMI_IBOOK%%uploads/'
        );
        return strtr($text, $array);
    }

    public static function checkSheme($text, $prefix = 'http://') {
        if (strncasecmp($text, 'http://', 7) !== 0 && strncasecmp($text, 'https://', 8) !== 0) {
            $text = $prefix . $text;
        }
        return $text;
    }

}