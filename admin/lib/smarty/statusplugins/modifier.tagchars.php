<?php
function smarty_modifier_tagchars($text) {
    $text = stripslashes($text);
    $text = str_replace(array('\'', '&', '<', '>', '"', 'Ђ', '»', '«', '©', '®', '™', '„', '“'), array('', '', '', '', '', 'euro', '&raquo;', '&laquo;', '', '', '', '', ''), $text);
    return mb_strtolower($text, Smarty::$_CHARSET);
}

?>
