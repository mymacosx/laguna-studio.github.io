<?php
function smarty_modifier_numformat($string) {
    return number_format((double) $string, '2', ',', '.');
}

?>