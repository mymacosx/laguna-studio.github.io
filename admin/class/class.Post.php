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

class Post extends Magic {

    protected $_nofollow = false;

    public function __construct() {
        $this->_nofollow = (bool) SX::get('forum.nofollow');
    }

    public function heightBox($text) {
        $maxlines = 15;
        $lines = max($this->_text->count($text, "\n"), $this->_text->count($text, '<br />')) + 2;
        if (empty($lines) || $lines <= 1) {
            return '40';
        } else {
            if ($lines > $maxlines && $maxlines > 0) {
                $lines = $maxlines;
            } elseif ($lines < 1) {
                $lines = 1;
            }
            return ($lines * 15);
        }
    }

    public function clean($text) {
        $text = preg_replace('#\[(\/?)(hide|mod|reg|quote|spoiler|php|code|email|url|highlight|youtube|u|i|b|s|img|face|size|color|left|center|right|list|justify)([^\]]*)\]#isu', '', $text);
        return $text;
    }

    public function hidden($text) {
        $text = preg_replace(array('#\[hide=.*?\].*?\[/hide\]#siu', '#\[reg\].*?\[/reg\]#siu'), ' ' . $this->_lang['reg'] . ' ', $text);
        return $this->clean($text);
    }

    public function replace($pattern, $replace, $text) {
        $data = $text;
        $text = preg_replace($pattern, $replace, $text);
        while ($data != $text) {
            $data = $text;
            $text = preg_replace($pattern, $replace, $text);
        }
        return $text;
    }

    public function bbcode($text, $location = '', $imgcode = '') {
        $erg = array();
        $c_mlength = '150';
        $pstring = time() . mt_rand(0, 10000000);
        $treffer = '#\[php\](.*?)\[\/php\]#siu';
        preg_match_all($treffer, $text, $erg);
        for ($i = 0, $count_erg = count($erg[1]); $i < $count_erg; $i++) {
            $text = str_replace($erg[1][$i], $pstring . $i . $pstring, $text);
        }
        $text = $this->correction($text);
        $text = sanitize($text);
        $lines = explode("\n", $text);
        for ($n = 0, $count_lines = count($lines); $n < $count_lines; $n++) {
            $words = explode(' ', $lines[$n]);
            $pstringount_w = count($words) - 1;
            if ($pstringount_w >= 0) {
                for ($i = 0; $i <= $pstringount_w; $i++) {
                    $max_length_word = $c_mlength;
                    $tword = trim($words[$i]);
                    $tword = preg_replace('#\[.*?\]#siu', '', $tword);
                    $displaybox = $this->_text->count($tword, 'http://') + $this->_text->count($tword, 'https://') + $this->_text->count($tword, 'www.') + $this->_text->count($tword, "ftp://");
                    if ($displaybox > 0) {
                        $max_length_word = $c_mlength;
                    }
                    if ($this->_text->strlen($tword) > $max_length_word) {
                        $words[$i] = chunk_split($words[$i], $max_length_word, "\n");
                        $length = $this->_text->strlen($words[$i]) - 5;
                        $words[$i] = $this->_text->substr($words[$i], 0, $length);
                    }
                }
                $lines[$n] = implode(' ', $words);
            } else {
                $lines[$n] = chunk_split($lines[$n], $c_mlength, "\n");
            }
        }
        $text = implode("\n", $lines);
        $text = nl2br($text);
        $text = $this->replaceCode($text);
        $text = $this->video($text);

        if (SX::get('system.SysCode_Bild') == 1) {
            if ($location == 'user_guestbook') {
                if ($imgcode == 1) {
                    $text = $this->replaceImage($text);
                }
            } else {
                $text = $this->replaceImage($text);
            }
        }

        if (SX::get('system.SysCode_Links') == 1) {
            $text = $this->replaceUrl($text);
        }

        $text = $this->code($text);
        $text = $this->quote($text);
        $text = preg_replace(array('#\[list\](.*?)\[\/list\]#siu', '#\[list=(.*?)\](.*?)\[\/list\]#siu', '#\[\*\](.*?)\\n#siu'), array('<ul>$1</ul>', '<ol type="$1">$2</ol>', '<li>$1</li>'), $text);
        $text = str_replace(array('Ђ', '»', '«', '©', '®', '™', '„', '“'), array('&euro;', '&raquo;', '&laquo;', '&copy;', '&reg;', '&trade;', '&bdquo;', '&ldquo;'), $text);

        for ($i = 0, $count_erg = count($erg[1]); $i < $count_erg; $i++) {
            $erg[1][$i] = '____PHPCODE_DEL____<?php ____PHPCODE_DEL_END____' . $erg[1][$i] . '____PHPCODE_DEL____?>____PHPCODE_DEL_END____';
            $highlight_string = highlight_string(trim($erg[1][$i]), true);
            $head_php = '<table width="100%" border="0" style="table-layout:fixed"><tr><td><div style="width:auto; overflow:auto"><div class="divcode_header">%%boxtitle%%</div><div class="divcode" style="width:auto; white-space:nowrap; height:' . $this->heightBox($erg[1][$i]) . 'px; overflow:auto"><code>';
            $foot_php = '</code></div></div></td></tr></table>';
            $displaybox = str_replace('%%boxtitle%%', $this->_lang['Forums_phpcode'], $head_php) . $highlight_string . $foot_php;
            $text = str_replace(array('[PHP]', '[/PHP]'), array('[php]', '[/php]'), $text);
            $text = str_replace('[php]' . $pstring . $i . $pstring . '[/php]', $displaybox, $text);
            $text = preg_replace('/____PHPCODE_DEL____(.*?)____PHPCODE_DEL_END____/siu', '<span style="display:none"></span>', $text);
        }
        $text = $this->spoiler($text);
        $text = $this->limited($text);
        $text = $this->hide($text);
        $text = $this->moderator($text);
        return $this->clean($text);
    }

    protected function code($text) {
        $code = '';
        preg_match_all('!\[code\](.*?)\[\/code\]!siu', $text, $code);
        for ($k = 0, $count_code = count($code[1]); $k < $count_code; $k++) {
            $text = str_replace($code[1][$k], $k, $text);
            $head_code = '<table width="100%" border="0" style="table-layout:fixed"><tr><td><div style="width:auto; overflow:auto"><div class="divcode_header">%%boxtitle%%</div><div class="divcode" style="width:auto; white-space:nowrap; height:' . $this->heightBox($code[1][$k]) . 'px; overflow:auto"><code>';
            $foot_code = '</code></div></div></td></tr></table>';
            $codebox = str_replace('%%boxtitle%%', $this->_lang['code'], $head_code) . $code[1][$k] . $foot_code;
            $text = str_replace('[code]' . $k . '[/code]', $codebox, $text);
        }
        return $text;
    }

    public function quote($text) {
        $head_quote = '<div style="width:auto; overflow:auto"><div class="divcode_header">%%boxtitle%%</div><div class="divcode" style="width:auto; white-space:normal; overflow:auto"><span style="font-style:italic;">';
        $foot_quote = '</span></div></div>';
        $text = $this->replace('#\[quote\](.*?)\[\/quote\]#si', str_replace('%%boxtitle%%', $this->_lang['quote'], $head_quote) . '$1' . $foot_quote, $text);
        return $text;
    }

    public function video($text) {
        $text = preg_replace("!\[(?i)youtube:([\w-;:\)#=\+\^ ]+)\]([\w-:/\?\[\]=.@]+)\[(?i)/youtube\]!iu", '<fieldset><legend>$1</legend><object width="375" height="350"><param name="movie" value="http://www.youtube.com/v/$2"></param><param name="wmode" value="opaque"><embed src="http://www.youtube.com/v/$2" type="application/x-shockwave-flash" width="425" height="350" wmode="opaque"></embed></object></fieldset>', $text);
        $text = preg_replace("!\[(?i)youtube\]([\w-:&/\?\[\]=.@]+)\[(?i)/youtube\]!iu", '<fieldset><legend>¬идео</legend><object width="375" height="350"><param name="movie" value="http://www.youtube.com/v/$1"></param><param name="wmode" value="opaque"><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" width="425" height="350" wmode="opaque"></embed></object></fieldset>', $text);
        return $text;
    }

    public function spoiler($text) {
        $head_spoiler = '<div class="spoiler"><a class="spoilerheader" onclick="toggleSpoiler(this);">%%boxtitle%%</a><div class="spoilertext"><div class="divcode" style="width:auto; white-space:normal; overflow:auto">';
        $foot_spoiler = '</div></div></div>';
        $replace_spoiler = str_replace('%%boxtitle%%', $this->_lang['spoiler'], $head_spoiler) . '$1' . $foot_spoiler;
        $text = $this->replace('/\[spoiler\](.*?)\[\/spoiler\]/si', $replace_spoiler, $text);
        return $text;
    }

    public function limited($text) {
        $head_reg = '<div style="width:auto; overflow:auto"><div class="divcode_header">%%boxtitle%%</div><div class="divcode" style="width:auto; white-space:normal; overflow:auto">';
        $foot_reg = '</div></div>';

        if ($_SESSION['loggedin'] != 0) {
            $replace_reg = str_replace('%%boxtitle%%', $this->_lang['reg'], $head_reg) . '$1' . $foot_reg;
        } else {
            $replace_reg = str_replace('%%boxtitle%%', $this->_lang['reg'], $head_reg) . $this->_lang['reg_text'] . $foot_reg;
        }
        $text = $this->replace('#\[reg\](.*?)\[\/reg\]#si', $replace_reg, $text);
        return $text;
    }

    public function hide($text) {
        $text = preg_replace('#\[hide\]|\[hide=0\]|\[hide=([^\d]*?)\]#siu', '[hide=1]', $text);
        $mes_num = Tool::userSettings('Beitraege', 0);
        $mask_hide = $replace_hide = array();
        $text_temp = $text;
        if (preg_match('#\[hide=([\d]*?)\](.*?)\[/hide\]#siu', $text)) {
            $text_temp = preg_replace('#(.*?)\[hide=([\d]*?)\](.*?)\[/hide\](.*?)#siu', '[hide=$2]$3[/hide]\n', $text_temp);
            $hides = array();
            $hides = explode('[hide=', $text_temp);
            foreach ($hides as $i => $hide) {
                $i = str_replace("\n", '', preg_replace('#\](.*?)\[\/hide\]#isu', '', $hide));
                $head_hide = '<div style="width:auto; overflow:auto"><div class="divcode_header">%%boxtitle%%</div><div class="divcode" style="width:auto; white-space:normal; overflow:auto">';
                if ($mes_num >= (int) $i) {
                    $foot_hide = '</div></div>';
                    $mask_hide[] = '#\[hide=' . intval($i) . '\](.*?)\[/hide\]#si';
                    $replace_hide[] = str_replace('%%boxtitle%%', $this->_lang['reg'], $head_hide) . '$1' . $foot_hide;
                } else {
                    $hide_text = $this->_lang['hide_text1'] . intval($i) . $this->_lang['hide_text2'] . intval($mes_num) . $this->_lang['hide_text3'];
                    $foot_hide = '</div></div>';
                    $mask_hide[] = '#\[hide=' . intval($i) . '\](.*?)\[/hide\]#si';
                    $replace_hide[] = str_replace('%%boxtitle%%', $this->_lang['reg'], $head_hide) . $hide_text . $foot_hide;
                }
            }
        }
        $text = $this->replace($mask_hide, $replace_hide, $text);
        return $text;
    }

    protected function moderator($text) {
        $head_mod = '<div style="width:auto; overflow:auto"><div class="mod_header">%%boxtitle%%</div><div class="modcode" style="width:auto; white-space:normal; overflow:auto">';
        $foot_mod = '</div></div>';
        $replace_mod = str_replace('%%boxtitle%%', $this->_lang['Mod_mes'], $head_mod) . '$1' . $foot_mod;
        $text = $this->replace('#\[mod\](.*?)\[\/mod\]#si', $replace_mod, $text);
        return $text;
    }

    public function codes($text) {
        return SX::get('system.SysCode_Aktiv') == 1 ? $this->bbcode($text) : nl2br(sanitize($text));
    }

    public function replaceCode($text) {
        $text = preg_replace(
                array(
                    '!\[color=(\#?[\da-fA-F]{6}|[a-z\ \-]{3,})\](.*?)\[/color\]+!iu',
                    '#\[size=([0-9+]{1,2}+)\](.*?)\[/size\]#siu',
                    '#\[face=()?(.*?)\](.*?)\[/face\]#siu',
                    '#\[font=()?(.*?)\](.*?)\[/font\]#siu',
                    '#\[highlight\](.*?)\[/highlight\]#siu',
                    '#\[left\](.*?)\[/left\]#siu',
                    '#\[right\](.*?)\[/right\]#siu',
                    '#\[center\](.*?)\[/center\]#siu',
                    '#\[justify\](.*?)\[/justify\]#siu',
                    '#\[b\](.*?)\[/b\]#siu',
                    '#\[s\](.*?)\[/s\]#siu',
                    '#\[u\](.*?)\[/u\]#siu',
                    '#\[i\](.*?)\[/i\]#siu',
                    '#\[(?i)email\]([\w-.]+@[\w-.]+)\[/(?i)email\]#iu',
                    '#\[email=()?(.*?)\](.*?)\[/email\]#siu'),
                array(
                    '<span style="color:$1">$2</span>',
                    '<span style="font-size:$1pt">$2</span>',
                    '<span style="font-family:$2">$3</span>',
                    '<span style="font-family:$2">$3</span>',
                    '<span class="forums_highlight">$1</span>',
                    '<div style="text-align:left">$1</div>',
                    '<div style="text-align:right">$1</div>',
                    '<div style="text-align:center">$1</div>',
                    '<div style="text-align:justify">$1</div>',
                    '<span style="font-weight:bold">$1</span>',
                    '<span style="text-decoration:line-through">$1</span>',
                    '<span style="text-decoration:underline">$1</span>',
                    '<span style="font-style:italic">$1</span>',
                    '<a href="mailto:$1">$1</a>',
                    '<a href="mailto:$2">$3</a>'), $text);
        return $text;
    }

    public function replaceUrl($text) {
        $value = $this->_nofollow === true ? ' rel="nofollow"' : '';
        return preg_replace(
                array(
                    '#\[(?i)url\](http://|ftp://|https://)(.*?)\[/(?i)url\]+#iu',
                    '#\[(?i)url\](.*?)\[/(?i)url\]+#iu',
                    '#\[url=(http://|ftp://|https://)?(.*?)\](.*?)\[/url\]#siu'),
                array(
                    '<a' . $value . ' href="$1$2" target="_blank">$1$2</a>',
                    '<a' . $value . ' href="http://$1" target="_blank">$1</a>',
                    '<a' . $value . ' href="http://$2" target="_blank">$3</a>'),
                $text);
    }

    public function replaceImage($text) {
        return preg_replace('#\[(?i)img\]([\w-%+:/\?\[\]=.@-]+)\[(?i)/img\]#iu', '<table width="100%" border="0" style="table-layout:fixed"><tr><td><div style="width:auto; overflow:auto"><img src="$1" border="0" alt="" /></div></td></tr></table>', $text);
    }

    public function correction($text) {
        return str_replace(array('[MAIL]', '[/MAIL]', '[EMAIL]', '[/EMAIL]', '[mail]', '[/mail]', '[PHP]', '[/PHP]'), array('[email]', '[/email]', '[email]', '[/email]', '[email]', '[/email]', '[php]', '[/php]'), $text);
    }

    public function parseUrl($text) {
        $arr = array(
            'urlsearch'    => array("/([^]_a-z0-9-=\"'\/])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/siu", "/^((https?|ftp):\/\/|www\.)([^ \r\n\(\)\*\^\$!`\"'\|\[\]\{\};<>]*)/siu"),
            'urlreplace'   => array('$1[URL]$2$4[/URL]', '[URL]$1$3[/URL]'),
            'emailsearch'  => array("/([\s])([\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*(\.[\w]{2,}))/siu", "/^([\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*(\.[\w]{2,}))/siu"),
            'emailreplace' => array('$1[EMAIL]$2[/EMAIL]', '[EMAIL]$0[/EMAIL]'));
        $text = preg_replace($arr['urlsearch'], $arr['urlreplace'], $text);
        if (strpos($text, '@')) {
            $text = preg_replace($arr['emailsearch'], $arr['emailreplace'], $text);
        }
        return $text;
    }

    public function smilies($text) {
        if (SX::get('system.SysCode_Smilies') == 1) {
            $theme = SX::get('options.theme');
            $sql = $this->_db->cache_fetch_assoc_all("SELECT code, path, title FROM " . PREFIX . "_smileys WHERE active='1' AND area='" . AREA . "'");
            foreach ($sql as $row) {
                $text = str_replace($row['code'], '<img src="theme/' . $theme . '/images/smilies/' . $row['path'] . '" border="0" alt="' . $row['title'] . '" title="' . $row['title'] . '" />', $text);
            }
        }
        return $text;
    }

    /* ¬ывод смайликов */
    public function listsmilies() {
        $smilie_id = 0;
        $theme = SX::get('options.theme');
        $smiliesw = '<table width="560" cellpadding="0" cellspacing="0" border="0"><tr>';
        $sql = $this->_db->query("SELECT SQL_CACHE code, path, title FROM " . PREFIX . "_smileys WHERE active = '1' AND area='" . AREA . "' ORDER BY posi ASC");
        while ($row = $sql->fetch_object()) {
            $smiliesw .= '<td width="10"><a class="menu_link" href="javascript:loadCode(\' ' . $row->code . ' \');" onclick="javascript:void(0);"><img hspace="1" vspace="1" src="theme/' . $theme . '/images/smilies/' . $row->path . '" border="0" alt="' . $row->title . '" title="' . $row->title . '" /></a></td>';
            $smiliesw .= '<td width="25%"><a class="menu_link" href="javascript:loadCode(\' ' . $row->code . ' \');" onclick="javascript:void(0);">' . $row->code . '</a></td>';
            $smilie_id++;
            if ($smilie_id == 4) {
                $smiliesw .= '</tr><tr>';
                $smilie_id = 0;
            }
        }
        $smiliesw .= '</tr></table>';
        $sql->close();
        $smiliesw_more = '<a id="smilies_click" href="javascript:void(0);" onclick="toggleSmiles(\'smilies_click\', \'smilies_content\');"><img class="format_buttons" src="' . BASE_PATH . 'theme/' . SX::get('options.theme') . '/images/comment/text_smilie.png" border="0" alt="" /></a>';
        $smiliesw_more .= '<div id="smilies_content" class="status" style="display:none">' . $smiliesw . '</div>';
        return $smiliesw_more;
    }

    public function font() {
        $array = array();
        $set = array(
            'Arial'     => 'Arial',
            'Times'     => 'Times',
            'Courier'   => 'Courier',
            'Impact'    => 'Impact',
            'Geneva'    => 'Geneva',
            'Optima'    => 'Optima',
            'Trebuchet' => 'Trebuchet'
        );
        foreach ($set as $font => $fontcode) {
            $array[] = array('font' => $font, 'fontname' => $fontcode);
        }
        return $array;
    }

    public function fontsize() {
        $array = array();
        $set = array('6', '8', '10', '12', '14', '16', '18', '20', '22', '24');
        foreach ($set as $size) {
            $array[] = array('size' => $size, 'css_size' => $size);
        }
        return $array;
    }

    public function color() {
        $array = array();
        $set = array(
            'blue'   => $this->_lang['color_blue'],
            'red'    => $this->_lang['color_red'],
            'purple' => $this->_lang['color_purple'],
            'orange' => $this->_lang['color_orange'],
            'yellow' => $this->_lang['color_yellow'],
            'gray'   => $this->_lang['color_grey'],
            'green'  => $this->_lang['color_green'],
            'indigo' => $this->_lang['color_indigo'],
            'black'  => $this->_lang['color_black'],
            'white'  => $this->_lang['color_white']);
        foreach ($set as $color => $fontcolor) {
            $array[] = array('color' => $color, 'fontcolor' => $fontcolor);
        }
        return $array;
    }

}