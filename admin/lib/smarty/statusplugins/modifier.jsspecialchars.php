<?php
function smarty_modifier_jsspecialchars($text) {
    $text = stripslashes($text);
    $text = str_replace(array('\'', '&', '<', '>', '"', 'Ђ', '»', '«', '©', '®', '™', '„', '“'), array('&#039;', '&amp;', '&lt;', '&gt;', '&quot;', '&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;'), $text);
    return str_replace('&#039;', '\&#039;', $text);
}

?>
