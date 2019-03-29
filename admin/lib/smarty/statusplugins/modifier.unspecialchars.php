<?php
function smarty_modifier_unspecialchars($string) {
	return str_replace(array(' &amp; ', '&euro;', '&reg;', '&copy;'), array(' & ', 'Ђ', '®', '©'), $string);
}
?>
