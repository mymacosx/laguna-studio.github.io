<?php
function smarty_modifier_jsnum($num) {
    return number_format($num, 2, '.', '');
}

?>