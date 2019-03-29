<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

class Highlight {

    public function get($text) {
        if (preg_match('#\[sx_code lang=(php|html|js|css|mysql|java|delphi)\](.*?)\[/sx_code\]#siu', $text)) {
            $jscode = '<script type="text/javascript" src="' . JS_PATH . '/chili/jquery.chili-2.2.js></script>
		       <script type="text/javascript" src="' . JS_PATH . '/chili/recipes.js></script>
		       <script type="text/javascript"> ChiliBook.automatic = true; ChiliBook.lineNumbers = true; </script>';
            $text = str_replace("</head>", "\n" . $jscode . "\n</head>", $text);

            $agent = Tool::browser();
            if ($agent == 'IE9' || $agent == 'IE8' || $agent == 'IE7' || $agent == 'IE6') {
                $replace = '<code class=\"\\1\" style=\"\"><pre>\\2</pre></code>';
            } else {
                $replace = '<code class=\"\\1\" style=\"\">\\2</code>';
            }
            $text = preg_replace('#\[sx_code lang=(.*?)\](.*?)\[/sx_code\]#siu', $replace, $text);
        }
        return $text;
    }

}