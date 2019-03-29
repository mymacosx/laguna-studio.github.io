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

class Text {

    protected $charset = CHARSET;

    /* Работаем во всем приложении с одним экземпляром класса */
    public static function get() {
        static $object = NULL;
        if ($object === NULL) {
            $object = new self;
        }
        return $object;
    }

    /** Метод получения кодировки */
    public function charset() {
        return $this->charset;
    }

    /** Метод strtolower для UTF (Преобразует строку в нижний регистр) */
    public function lower($string) {
        return mb_strtolower($string, $this->charset);
    }

    /** Метод strtoupper для UTF (Преобразует строку в верхний регистр) */
    public function upper($string) {
        return mb_strtoupper($string, $this->charset);
    }

    /** Метод substr для UTF (Возвращает подстроку) */
    public function substr($string, $start, $length = null) {
        return mb_substr($string, $start, $length, $this->charset);
    }

    /** Метод substr_count для UTF (Возвращает число вхождений подстроки) */
    public function count($haystack, $needle) {
        return mb_substr_count($haystack, $needle, $this->charset);
    }

    /** Метод strrpos для UTF (Возвращает позицию последнего вхождения подстроки) */
    public function strrpos($haystack, $needle, $offset = 0) {
        return mb_strrpos($haystack, $needle, $offset, $this->charset);
    }

    /** Метод strripos для UTF (Возвращает позицию последнего вхождения подстроки без учета регистра) */
    public function strripos($haystack, $needle, $offset = 0) {
        return mb_strripos($haystack, $needle, $offset, $this->charset);
    }

    /** Метод strpos для UTF (Возвращает позицию первого вхождения подстроки) */
    public function strpos($haystack, $needle, $offset = 0) {
        return mb_strpos($haystack, $needle, $offset, $this->charset);
    }

    /** Метод stripos для UTF (Возвращает позицию первого вхождения подстроки без учета регистра) */
    public function stripos($haystack, $needle, $offset = 0) {
        return mb_stripos($haystack, $needle, $offset, $this->charset);
    }

    /** Метод strrchr для UTF (Находит последнее вхождение подстроки) */
    public function strrchr($haystack, $needle, $part = false) {
        return mb_strrchr($haystack, $needle, $part, $this->charset);
    }

    /** Метод strrichr для UTF (Находит последнее вхождение подстроки без учета регистра) */
    public function strrichr($haystack, $needle, $part = false) {
        return mb_strrichr($haystack, $needle, $part, $this->charset);
    }

    /** Метод strlen для UTF (Возвращает длину строки) */
    public function strlen($string) {
        return mb_strlen($string, $this->charset);
    }

    /** Метод strstr для UTF (Находит первое вхождение подстроки) */
    public function strstr($haystack, $needle, $before_needle = false) {
        return mb_strstr($haystack, $needle, $before_needle, $this->charset);
    }

    /** Метод stristr для UTF (Находит первое вхождение подстроки без учета регистра) */
    public function stristr($haystack, $needle, $before_needle = false) {
        return mb_stristr($haystack, $needle, $before_needle, $this->charset);
    }

    /** Метод split для UTF */
    public function split($pattern, $string, $limit = -1) {
        return mb_split($pattern, $string, $limit);
    }

    /** Метод ucwords для UTF (Преобразует в верхний регистр первый символ каждого слова в строке) */
    public function ucwords($string) {
        return mb_convert_case($string, MB_CASE_TITLE, $this->charset);
    }

    /** Метод ucfirst для UTF (Преобразует первый символ строки в верхний регистр) */
    public function ucfirst($string) {
        return $this->upper($this->substr($string, 0, 1)) . $this->substr($string, 1, $this->strlen($string));
    }

    /** Метод lcfirst для UTF (Преобразует первый символ строки в нижний регистр) */
    public function lcfirst($string) {
        return $this->lower($this->substr($string, 0, 1)) . $this->substr($string, 1, $this->strlen($string));
    }

    /** Метод wordwrap для UTF (Выполняет перенос строки на заданное количество символов) */
    public function wordwrap($string, $width = 76, $break = PHP_EOL, $cut = false) {
        return preg_replace('#([\S\s]{' . $width . '}' . ($cut ? '' : '\s') . ')#u', '\1' . $break, $string);
    }

    /** Метод strrev для UTF (Посимвольный разворот строки) */
    public function strrev($string) {
        preg_match_all('/./us', $string, $array);
        return implode(array_reverse($array[0]));
    }

    /** Метод проверки наличия значения в строке */
    public function search($string, $search) {
        return $this->stripos($string, $search) !== false;
    }

    /** Метод очистки текста от запрещенных символов */
    public function clear($string, $charset = null) {
        if (empty($charset)) {
            $charset = $this->charset;
        }
        return mb_convert_encoding($string, $charset, $charset);
    }

    /** Метод перекодировки текста */
    public function convert($string, $input = null, $output = null) {
        if (empty($input)) {
            $input = mb_detect_encoding($string);
        }
        if (empty($output)) {
            $output = $this->charset;
        }
        return mb_convert_encoding($string, $output, $input);
    }

    /** Метод определения наличия не ASCII символов в строке */
    public function detect($string) {
        return (bool) preg_match('/[^\x00-\x7F]/', $string);
    }

    /** Метод замены текста */
    public function replace($string, $array, $replace = '') {
        if (!is_array($array)) {
            $array = array($array => $replace);
        }
        return strtr($string, $array);
    }

    /** Метод удаления из текста */
    public function remove($string, $array) {
        return str_replace($array, '', $string);
    }

    /** Метод вывода указанного колличества слов */
    public function words($string, $limit = 10, $end = '...') {
        $words = explode(' ', $string, $limit + 1);
        if (count($words) > $limit) {
            array_pop($words);
        }
        return rtrim(implode(' ', $words)) . $end;
    }

    /** Метод вывода указанного колличества символов */
    public function chars($string, $limit = 70, $end = '...', $word = true) {
        if ($this->strlen($string) > $limit) {
            $string = $this->substr($string, 0, $limit);
            if ($word === true) {
                $array = explode(' ', $string);
                array_pop($array);
                $string = implode(' ', $array);
            }
            $string = rtrim($string) . $end;
        }
        return $string;
    }

    /** Метод обработки строки до указанной длины с заменой лишних символов в центре */
    public function slice($string, $limit = 70, $set = '...') {
        if ($this->strlen($string) > $limit) {
            $len = floor($limit / 2) - floor($this->strlen($set) / 2);
            $string = $this->substr($string, 0, $len) . $set . $this->substr($string, - ($len - 1));
        }
        return $string;
    }

    /** Метод добавления/удаления данных в начале строки */
    public function prefix($string, $search, $remove = false) {
        $count = $this->strlen($search);
        if ($this->substr($string, 0, $count) !== $search) {
            $string = $search . $string;
        }
        if ($remove === true) {
            $string = $this->substr($string, - ($this->strlen($string) - $count));
        }
        return $string;
    }

    /** Метод добавления/удаления данных в конце строки */
    public function suffix($string, $search, $remove = false) {
        $count = $this->strlen($search);
        if ($this->substr($string, - $count) !== $search) {
            $string = $string . $search;
        }
        if ($remove === true) {
            $string = $this->substr($string, 0, - $count);
        }
        return $string;
    }

    /** Метод получения окончания для множественного числа слова на основании числа */
    public function ending($number, $array, $join = false) {
        $num = $number % 100;
        if ($num >= 11 && $num <= 19) {
            $result = $array[2];
        } else {
            switch ($num % 10) {
                case 1:
                    $result = $array[0];
                    break;
                case 2:
                case 3:
                case 4:
                    $result = $array[1];
                    break;
                default:
                    $result = $array[2];
                    break;
            }
        }
        return $join === true ? $number . ' ' . $result : $result;
    }

    /** Метод транслитерации текста */
    public function translit($string, $separator = '-') {
        static $array = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e', //yo
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );
        $string = $this->lower($string);
        $string = preg_replace('/&([a-z0-9-#]{2,6});/u', $separator, $string);
        $string = strtr($string, $array);
        $string = preg_replace(array('/[^a-z0-9-]/u', '/' . $separator . '+/'), $separator, $string);
        return !empty($string) ? trim($string, $separator) : $separator;
    }

    /** Метод замены запрещенных слов */
    public function censor($string, $words, $replace = '***', $full = false) {
        if (!empty($string) && !empty($words)) {
            foreach ((array) $words as $key => $value) {
                $words[$key] = preg_quote($value, '/');
            }
            $regex = '(' . implode('|', $words) . ')';
            if ($full === true) {
                $regex = '(?<=\b|\s|^)' . $regex . '(?=\b|\s|$)';
            }
            $string = preg_replace('/' . $regex . '(?![^<]*?>)/iu', $replace, $string);
        }
        return $string;
    }

    /** Метод выделения текста */
    public function highlight($string, $words, $open = '<strong>', $close = '</strong>') {
        if (!empty($string) && !empty($words)) {
            foreach ((array) $words as $key => $value) {
                $words[$key] = preg_quote($value, '/');
            }
            $regex = '(' . implode('|', $words) . ')';
            $string = preg_replace('/' . $regex . '(?![^<]*?>)/iu', $open . '\1' . $close, $string);
        }
        return $string;
    }

    /** Метод удаления повторяющихся пробелов */
    public function whitespace($value) {
        return preg_replace('/\s+/', ' ', $value);
    }

    /** Метод получения массива строк */
    public function lines($string) {
        return preg_split('/\r\n|\n|\r/', $string);
    }

    /** Метод нормализации переводов строки */
    public function nl($string) {
        return str_replace(array("\r\n", "\n", "\r"), PHP_EOL, $string);
    }

    /** Метод преобразовывает символ перевода строки в HTML-код разрыва строки */
    public function nl2br($string) {
        return str_replace(array("\r\n", "\n", "\r"), '<br />', $string);
    }

    /** Метод преобразовывает HTML-код разрыва строки в символ перевода строки */
    public function br2nl($string) {
        return str_replace(array('<br>', '<br/>', '<br />'), PHP_EOL, $string);
    }

    /** Метод приведения к стандарту PascalCase */
    public function pascal($string, $separator = array('-', '_')) {
        $string = str_replace($separator, ' ', $string);
        $string = str_replace(' ', '', ucwords($string));
        return $string;
    }

    /** Метод приведения к стандарту camelCase */
    public function camel($string, $separator = array('-', '_')) {
        return lcfirst($this->pascal($string, $separator));
    }

    /** Метод приведения к стандарту snake_case */
    public function snake($string, $separator = '_') {
        if (!ctype_lower($string)) {
            $string = preg_replace('/(.)(?=[A-Z])/', '\1' . $separator, $string);
            $string = strtolower(str_replace(' ', '', $string));
        }
        return $string;
    }

    /** Метод преобразования символов в сущности */
    public function escape($value, $options = ENT_QUOTES, $double = true) {
        return htmlspecialchars($value, $options, $this->charset, $double);
    }

    /** Метод преобразования сущностей в символы */
    public function unescape($value, $options = ENT_QUOTES) {
        return htmlspecialchars_decode($value, $options);
    }

    /** Метод преобразования всех символов в сущности */
    public function entities($value, $options = ENT_QUOTES, $double = true) {
        return htmlentities($value, $options, $this->charset, $double);
    }

    /** Метод преобразования всех сущностей в символы */
    public function unentities($value, $options = ENT_QUOTES) {
        return html_entity_decode($value, $options, $this->charset);
    }

}