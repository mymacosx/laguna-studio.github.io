<?php
function smarty_modifier_cleantext($text) {
    $text = stripslashes($text);
    $text = str_replace(array('"', ' & ', '<', '>', '»', '«', 'Ђ', '©', '®', '™'), array('&quot;', ' &amp; ', '&lt;', '&gt;', '&raquo;', '&laquo;', '&euro;', '&copy;', '&reg;', '&#8482;'), $text);
    return $text;
}

?>
