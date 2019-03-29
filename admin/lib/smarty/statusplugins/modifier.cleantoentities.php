<?php
function smarty_modifier_cleantoentities($text) {
    $text = str_replace(array('Ђ', '»', '«', '©', '®', '™', '„', '“'), array('&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;'), $text);
    return $text;
}

?>
