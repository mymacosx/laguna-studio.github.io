<?php
function smarty_modifier_slice($string, $length = 80, $etc = '...') {
    if ($length == 0)
        return '';
    if (mb_strlen($string, Smarty::$_CHARSET) > $length) {
        $bit_len = floor($length / 2) - floor(mb_strlen($etc, Smarty::$_CHARSET) / 2);
        return mb_substr($string, 0, $bit_len, Smarty::$_CHARSET) . $etc . mb_substr($string, -($bit_len - 1), null, Smarty::$_CHARSET);
    } else {
        return $string;
    }
}

?>