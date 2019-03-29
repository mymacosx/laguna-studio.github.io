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

class Flashtag extends Magic {

    /* Флеш-облако навигации */
    public function get() {
        $options = array();
        $options['width'] = '200';      // Ширина
        $options['height'] = '200';      // Высота
        $options['tcolor'] = '666666';   // Цвет текста
        $options['tcolor2'] = '333333';
        $options['hicolor'] = '000000';
        $options['bgcolor'] = 'FFFFFF';   // Цвет фона
        $options['speed'] = '200';      // Скорость вращения
        $options['trans'] = 'false';    // Прозрачный фон - true
        $options['distr'] = 'true';     // Равномерное распределение
        $words = $links = '';
        $i = 0;
        $sql = DB::get()->query("SELECT SQL_CACHE * FROM " . PREFIX . "_navi_flashtag WHERE Aktiv='1' LIMIT 50");
        while ($row = $sql->fetch_object()) {
            $i++;
            $words .= '<a href="' . $row->Dokument . '">' . $row->Title . '</a>';
            $links .= "<a style='" . $row->Size . "' href='" . $row->Dokument . "'>" . $row->Title . "</a>";
        }
        $sql->close();
        if ($i > 1) {
            $rand = mt_rand(0, 9999999);
            $soname = 'so' . $rand;
            $divname = 'sys' . $rand;
            $flashtag = '';
            $flashtag .= '<script type="text/javascript" src="' . JS_PATH . '/swfobject.js"></script>';
            $flashtag .= '<div id="' . $divname . '" align="center"><p style="display:none">' . urldecode($words) . '</p></div>';
            $flashtag .= "\r\n<script type=\"text/javascript\">\r\n//<![CDATA[\r\n";
            $flashtag .= 'var ' . $soname . ' = new SWFObject("' . BASE_URL . '/lib/tagcloud.swf?r=' . $rand . '","tagcloudflash","' . $options['width'] . '","' . $options['height'] . '","9","#' . $options['bgcolor'] . '");' . "\r\n";
            if ($options['trans'] == 'true') {
                $flashtag .= $soname . '.addParam("wmode", "transparent");';
            }
            $flashtag .= $soname . '.addParam("allowScriptAccess", "always");';
            $flashtag .= $soname . '.addVariable("tcolor", "0x' . $options['tcolor'] . '");';
            $flashtag .= $soname . '.addVariable("tcolor2", "0x' . $options['tcolor2'] . '");';
            $flashtag .= $soname . '.addVariable("hicolor", "0x' . $options['hicolor'] . '");';
            $flashtag .= $soname . '.addVariable("tspeed", "' . $options['speed'] . '");';
            $flashtag .= $soname . '.addVariable("distr", "' . $options['distr'] . '");';
            $flashtag .= $soname . '.addVariable("mode", "tags");';
            $flashtag .= $soname . '.addVariable("tagcloud", "<tags>' . preg_replace(array('/\?/su', '/(&amp;|&)/su'), array('%3F', '%26'), $links) . '</tags>");';
            $flashtag .= $soname . '.write("' . $divname . '");';
            $flashtag .= "\r\n//]]>\r\n</script>";
            return $flashtag;
        }
        return '';
    }

}
