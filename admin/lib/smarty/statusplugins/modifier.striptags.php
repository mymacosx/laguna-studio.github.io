<?php
function smarty_modifier_striptags($text, $allowed = '') {
    return strip_tags($text, $allowed);
}

?>