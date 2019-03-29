<?php
function smarty_modifier_decimal3($string) {
    return number_format($string, '3', ',', '.');
}

?>