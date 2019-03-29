<?php
function smarty_modifier_specialchars($text) {
    $text = str_replace(array('©', '®', '\'', 'Ђ', '»', '«', '™', '„', '“'), array('&copy;', '&reg;', '&#039;', '&euro;', '&raquo;', '&laquo;', '&trade;', '&bdquo;', '&ldquo;'), $text);
    return $text;
}

?>
