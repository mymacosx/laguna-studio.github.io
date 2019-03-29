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

class Seo extends Magic {

    protected $words;
    protected $content;
    protected $paramseo;
    protected $seo;

    /* $paramseo['pagetitle']        тег <title> </title>
     * $paramseo['headernav']        хлебные крошки
     * $paramseo['tags_keywords']    вывод подготовленных тегов кейвордс
     * $paramseo['tags_description'] вывод подготовленных тегов дескрипшн
     * $paramseo['generate']         вывод текста для генерации
     * $paramseo['canonical']        вывод главного адреса с контентом */
    public function create($paramseo) {
        $pagetitle = '';
        $this->seo = SX::get('seo');
        $this->paramseo = $paramseo;
        $headernav = '<a href="index.php?area=' . $_SESSION['area'] . '">' . $this->_lang['Startpage'] . '</a>';
        if ($_REQUEST['p'] != 'index') {
            $headernav .= $this->_lang['PageSep'] . $this->paramseo['headernav'];
        }
        if (!empty($this->seo['title'])) {
            $pagetitle = $this->clean($this->seo['title']);
        } else {
            $pagetitle = Tool::cleanAllow($this->sanit($this->paramseo['pagetitle']), ' .,:?!\/\|()„-') . $this->_lang['PageSep'] . SX::get('system.Seitenname');
        }
        $tpl_array = array(
            'pagetitle'      => $pagetitle,
            'headernav'      => $headernav,
            'headernavarray' => explode($this->_lang['PageSep'], $headernav),
            'keywords'       => $this->keywords(150),
            'description'    => $this->description(200),
            'canonical'      => $this->canonical());
        $this->_view->assign($tpl_array);
    }

    /* Метод получение главной страницы при дублях */
    protected function canonical() {
        $result = NULL;
        if (!empty($this->seo['canonical'])) {
            $result = $this->seo['canonical'];
        } else {
            $uri = 'index.php?' . str_replace('&', '&amp;', $this->_text->lower($_SERVER['QUERY_STRING']));
            if (isset($this->paramseo['canonical']) && $this->paramseo['canonical'] != $uri) {
                $result = $this->paramseo['canonical'];
            }
        }
        return $result;
    }

    /* Метод получения текста тега keywords */
    protected function keywords($count = 150) {
        if (!empty($this->seo['keywords'])) {
            return $this->clean($this->seo['keywords']);
        } elseif (!empty($this->paramseo['tags_keywords'])) {
            $text = $this->_text->lower($this->sanit($this->paramseo['tags_keywords']));
        } elseif (!empty($this->paramseo['generate'])) {
            $text = $this->__object('Keywords')->create($this->sanit($this->paramseo['generate']));
            if (($strlen = $this->_text->strlen($text)) < $count) {
                $strlen = $count - $strlen;
                $text .= ', ' . ($this->baseContent() ? $this->baseWords($strlen) : $this->__object('Keywords')->create($this->content()));
            }
        } else {
            $text = $this->baseContent() ? $this->baseWords() : $this->__object('Keywords')->create($this->content());
        }
        $text = $this->_text->chars(Tool::cleanAllow($text, ' ,'), SX::get('system.CountKeywords'), '');
        return trim($text, ',');
    }

    /* Метод получения текста тега keywords */
    protected function description($count = 200) {
        if (!empty($this->seo['description'])) {
            return $this->clean($this->seo['description']);
        } elseif (!empty($this->paramseo['tags_description'])) {
            $text = $this->_text->lower($this->sanit($this->paramseo['tags_description']));
        } elseif (!empty($this->paramseo['generate'])) {
            $text = $this->setkey($this->sanit($this->paramseo['generate'], false));
            if (($strlen = $this->_text->strlen($text)) < $count) {
                $strlen = $count - $strlen;
                $text .= '. ' . $this->setkey(($this->baseContent() ? $this->baseWords($strlen) : $this->content()), $strlen);
            }
        } else {
            $text = $this->setkey($this->baseContent() ? $this->baseWords() : $this->content());
        }
        $text = $this->_text->chars(Tool::cleanAllow($text, ' .'), SX::get('system.CountDescription'), '');
        return trim($text);
    }

    /* Метод получения данных из базы */
    protected function baseContent() {
        static $aktiv = false;
        if ($aktiv === false) {
            $aktiv = true;
            $this->base(25);
        }
        return !empty($this->words) ? true : false;
    }

    protected function baseWords($num = 200) {
        $words = explode(',', $this->words);
        shuffle($words);
        $count = 0;
        $result = array();
        foreach ($words as $word) {
            $count += $this->_text->strlen($word);
            $result[] = trim($word);
            if ($count > $num) {
                break;
            }
        }
        return implode(', ', $result);
    }

    protected function content() {
        if (empty($this->content)) {
            $this->content = $this->sanit($this->paramseo['content']);
        }
        return $this->content;
    }

    /* Метод генерации description */
    protected function setkey($str, $num = 200) {
        if (!empty($str)) {
            $str = str_replace(array("\r\n", "\r", "\n", ',', '?', '!', ':', ';', '.'), PHP_EOL, $str);
            $result = $words = array();
            foreach (explode(PHP_EOL, $str) as $word) {
                if (isset($word{15}) && !is_numeric($word)) {
                    $words[] = $this->_text->ucfirst(trim(preg_replace(array('/[^\w- ]/iu', '/\s+/u'), ' ', $this->_text->lower($word))));
                }
            }
            $words = array_unique($words);
            shuffle($words);
            $count = 0;
            foreach ($words as $word) {
                if (!empty($word)) {
                    $count += $this->_text->strlen($word);
                    $result[] = $word;
                    if ($count > $num) {
                        break;
                    }
                }
            }
            return implode('. ', $result);
        }
        return '';
    }

    /* Метод получения чистого текста */
    protected function sanit($text, $param = true) {
        $text = strip_tags($text);
        $text = Tool::cleanTags($text, array('codewidget', 'screen', 'contact', 'audio', 'video', 'neu'));
        $search = array(
            '!\[code\].*?\[/code\]!siu',
            '!\[php\].*?\[/php\]!siu',
            '!\[reg\].*?\[/reg\]!siu',
            '!\[url[^\]]*?\].*?\[/url\]!siu',
            '!\[hide[^\]]*?\].*?\[/hide\]!siu',
            '!\[[^\]]*?\]!siu',
            '!<script[^>]*?>.*?</script>!siu',
            '!<[\/\!]*?[^<>]*?>!siu',
            '/&[#a-z0-9]{2,6};/iu'
        );
        $text = preg_replace($search, ' ', $text);
        return $param ? Tool::cleanSpace($text) : $text;
    }

    /* Метод вывода метатега description из базы */
    protected function base($val = '12') {
        if (get_active('seomod')) {
            $array = $this->__object('Cache')->get('full_description');
            if ($array === false) {
                $array = $this->query();
                $this->__object('Cache')->set('full_description', $array);
            }
            $array = $this->rand($val, $array);
        }
        $this->words = !empty($array) ? implode(', ', $array) : '';
    }

    /* Метод вывода метатега description из базы */
    protected function query() {
        $array = array();
        $sql = DB::get()->query("SELECT Text FROM " . PREFIX . "_description WHERE Aktiv = '1'");
        while ($row = $sql->fetch_object()) {
            $array[] = trim($row->Text);
        }
        $sql->close();
        return $array;
    }

    /* Метод вывода случайных значений из массива */
    protected function rand($val, $array) {
        $rand = array();
        if (!empty($array)) {
            $array_rand = array_rand($array, $val);
            if (is_array($array_rand)) {
                foreach ($array_rand as $val) {
                    $rand[] = $array[$val];
                }
            } else {
                $rand[] = $array[$array_rand];
            }
        }
        return $rand;
    }

    protected function clean($text) {
        return sanitize(strip_tags($text));
    }

}