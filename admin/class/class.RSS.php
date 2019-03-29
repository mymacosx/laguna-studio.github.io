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

class RSS extends Magic {

    protected $FONew = false; // При true выводятся только первые сообщения топиков на форуме, при false - все сообщения
    protected $NewsCateg;
    protected $ArticlesCateg;
    protected $Lc;
    protected $rss;
    protected $url_host;
    protected $Seitenname;
    protected $separator = ' :: ';

    /* Начальная инициализация класса */
    public function __construct() {
        $this->Seitenname = SX::get('system.Seitenname');
        $this->rss = SX::get('rss');
        $this->Lc = Arr::getSession('Langcode', 1);
        $this->url_host = BASE_URL;
    }

    /* Удаляем теги из контента */
    protected function cleanTags($text) {
        return Tool::cleanTags($text, array('screen', 'contact', 'audio', 'video', 'neu'));
    }

    protected function replace($text) {
        $text = str_replace("\r\n", '<br />', $text);
        return str_replace(array('<br /><br /><br /><br /><br />', '<br /><br /><br /><br />', '<br /><br /><br />', '<br /><br />'), '<br />', $text);
    }

    protected function clean($text) {
        return preg_replace('#\[sx_code lang=(.*?)\](.*?)\[\/sx_code\]#siu', '\\2', $text);
    }

    public function show() {
        $data = '';
        $array = array();

        if (get_active('News') && permission('news_rss')) {
            $data = $this->loadNews($array, $this->rss['all'], $this->_lang['Newsarchive'] . $this->separator, $this->_lang['Newsarchive']);
        }
        if (get_active('articles') && permission('articles_rss')) {
            $data = $this->loadArticles($data, $this->rss['all'], $this->_lang['Gaming_articles'] . $this->separator, $this->_lang['Gaming_articles']);
        }
        if (get_active('forums')) {
            $data = $this->loadForum($data, $this->rss['all'], $this->_lang['Forums_Title'] . $this->separator, $this->_lang['Forums_Title'], $this->rss['all_typ']);
        }
        $this->load($this->Seitenname, $data, $this->rss['all_typ']);
    }

    public function news() {
        if (!permission('news_rss')) {
            $this->__object('Core')->noAccess();
        }
        $array = array();
        $data = $this->loadNews($array, $this->rss['news'], '', '');
        $this->load($this->Seitenname . $this->separator . $this->_lang['Newsarchive'], $data, $this->rss['news_typ']);
    }

    public function articles() {
        if (!permission('articles_rss')) {
            $this->__object('Core')->noAccess();
        }
        $array = array();
        $data = $this->loadArticles($array, $this->rss['articles'], '', '');
        $this->load($this->Seitenname . $this->separator . $this->_lang['Gaming_articles'], $data, $this->rss['articles_typ']);
    }

    public function forum() {
        $array = array();
        $data = $this->loadForum($array, $this->rss['forum'], '', '', $this->rss['forum_typ']);
        $this->load($this->Seitenname . $this->separator . $this->_lang['Forums_Title'], $data, '1');
    }

    protected function allCategNews() {
        $categs = $this->_db->fetch_object_all("SELECT Id, Name_{$this->Lc} AS Name FROM " . PREFIX . "_news_kategorie WHERE Sektion = '" . AREA . "'");
        return $categs;
    }

    protected function categNews($id) {
        $categs = $this->NewsCateg;
        foreach ($categs as $categ) {
            if ($id == $categ->Id) {
                return $categ->Name;
            }
        }
        return '';
    }

    protected function loadNews($array, $limit, $title = '', $cat = '') {
        if (empty($cat)) {
            $this->NewsCateg = $this->allCategNews();
        }
        $q = "SELECT Id, Kategorie, ZeitStart, Sektion, Titel{$this->Lc} AS Titel, Intro{$this->Lc} AS Intro, News{$this->Lc} AS News FROM " . PREFIX . "_news WHERE (ZeitEnde >= " . time() . " OR ZeitEnde = '0') AND (Sektion = '" . AREA . "' OR AlleSektionen = '1') AND Aktiv = 1 ORDER BY ZeitStart DESC, Zeit DESC LIMIT " . intval($limit);
        $sql = $this->_db->query($q);
        while ($row = $sql->fetch_object()) {
            $row->rrs_title = $this->clean($title . $row->Titel);
            $row->rrs_link = $this->url_host . '/index.php?p=news&amp;area=' . $row->Sektion . '&amp;newsid=' . $row->Id . '&amp;name=' . translit($row->Titel);
            $row->rrs_description = $this->clean($this->cleanTags($row->Intro . ' ' . $row->News));
            $row->rrs_content = $this->replace($row->rrs_description);
            $row->rrs_pubDate = date('r', $row->ZeitStart);
            $row->rrs_category = (empty($cat)) ? $this->clean($this->categNews($row->Kategorie)) : $cat;
            $array[] = $row;
        }
        $sql->close();
        return $array;
    }

    protected function allCategArticles() {
        $categs = $this->_db->fetch_object_all("SELECT Id, Name_{$this->Lc} AS Name FROM " . PREFIX . "_artikel_kategorie WHERE Sektion = '" . AREA . "'");
        return $categs;
    }

    protected function categArticles($id) {
        $categs = $this->ArticlesCateg;
        foreach ($categs as $categ) {
            if ($id == $categ->Id) {
                return $categ->Name;
            }
        }
        return '';
    }

    protected function loadArticles($array, $limit, $title = '', $cat = '') {
        if (empty($cat)) {
            $this->ArticlesCateg = $this->allCategArticles();
        }
        $q = "SELECT Id, Kategorie, ZeitStart, Sektion, Kennwort, Titel_{$this->Lc} AS Titel, Untertitel_{$this->Lc} AS Intro, Inhalt_{$this->Lc} AS Article FROM " . PREFIX . "_artikel WHERE (ZeitEnde >= " . time() . " OR ZeitEnde = '0') AND (Sektion = '" . AREA . "' OR AlleSektionen = '1') AND Aktiv = '1' ORDER BY ZeitStart DESC, Zeit DESC LIMIT " . intval($limit);
        $sql = $this->_db->query($q);
        while ($row = $sql->fetch_object()) {
            if (empty($row->Kennwort)) {
                $row->rrs_title = $this->clean($title . $row->Titel);
                $row->rrs_link = $this->url_host . '/index.php?p=articles&amp;area=' . $row->Sektion . '&amp;action=displayarticle&amp;id=' . $row->Id . '&amp;name=' . translit($row->Titel);
                $row->rrs_description = $this->clean($this->cleanTags($row->Intro . ' ' . $row->Article));
                $row->rrs_content = $this->replace($row->rrs_description);
                $row->rrs_pubDate = date('r', $row->ZeitStart);
                $row->rrs_category = (empty($cat)) ? $this->clean($this->categArticles($row->Kategorie)) : $cat;
                $array[] = $row;
            }
        }
        $sql->close();
        return $array;
    }

    protected function aktivForum() {
        $post = '';
        $start = 1;
        $sql = $this->_db->query("SELECT SQL_CACHE id, group_id FROM " . PREFIX . "_f_forum WHERE active = '1'");
        while ($row = $sql->fetch_object()) {
            if (in_array($_SESSION['user_group'], explode(',', $row->group_id))) {
                $post .= ( $start == 1) ? " f.id='$row->id'" : " OR f.id='$row->id'";
                $start++;
            }
        }
        $sql->close();
        return (!empty($post)) ? 'AND (' . $post . ')' : '';
    }

    protected function loadForum($array, $limit, $title = '', $cat = '', $content = '0') {
        $forum = $this->aktivForum();
        $group = $this->FONew === true ? 'GROUP BY p.topic_id' : '';
        if (!empty($forum)) {
            $sql = $this->_db->query("SELECT
                    p.id,
                    p.title,
                    p.topic_id,
                    p.datum,
                    p.message,
                    f.id AS forum_id,
                    t.title AS topic_title
                FROM
                    " . PREFIX . "_f_post AS p,
                    " . PREFIX . "_f_topic AS t,
                    " . PREFIX . "_f_forum AS f
                WHERE
                    t.id = p.topic_id
                AND
                    t.forum_id = f.id
                    " . $forum . "
                    " . $group . "
                ORDER BY datum DESC LIMIT " . intval($limit));
            $limit = Tool::userSettings('Forum_Beitraege_Limit', 15);
            while ($row = $sql->fetch_object()) {
                $perms = Tool::accessForum($row->forum_id);
                if ($perms['FORUM_SEE_TOPIC']) {
                    $numPages = Tool::countPost($row->id, $row->topic_id, $limit);
                    $row->rrs_title = (empty($row->title)) ? $this->clean($title . $row->topic_title) : $this->clean($title . $row->title);
                    $row->rrs_link = $this->url_host . '/index.php?p=showtopic&amp;toid=' . $row->topic_id . '&amp;pp=' . $limit . '&amp;page=' . $numPages . '#pid_' . $row->id;
                    if ($content == '1') {
                        $row->rrs_description = $this->clean($this->cleanTags($this->replace($this->__object('Post')->bbcode($row->message, '', 1))));
                    } else {
                        $row->rrs_description = $this->clean($this->cleanTags($this->__object('Post')->hidden($row->message)));
                    }
                    $row->rrs_content = $row->rrs_description;
                    $row->rrs_pubDate = date('r', strtotime($row->datum));
                    $row->rrs_category = (empty($cat)) ? $this->clean($row->topic_title) : $cat;
                    $array[] = $row;
                }
            }
            $sql->close();
        }
        return $array;
    }

    /* Выполняем перекодировку */
    protected function iconv($text, $old, $charset) {
        if (function_exists('iconv') && strtolower($old) != strtolower($charset)) {
            $iconv = iconv($old, $charset . '//IGNORE', $text);
        }
        return isset($iconv) && $iconv !== false ? $iconv : $text;
    }

    /* Вывод ленты */
    protected function load($title, $array, $content = '0') {
        $charset = sanitize(Arr::getRequest('charset', CHARSET));
        $rss = "<?xml version=\"1.0\" encoding=\"" . $charset . "\" ?>\n";
        $rss .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
        $rss .= "<channel>\n";
        $rss .= "<pubDate>" . date('r') . "</pubDate>\n";
        $rss .= "<lastBuildDate>" . date('r') . "</lastBuildDate>\n";
        $rss .= "<title>" . $title . "</title>\n";
        $rss .= "<link>" . $this->url_host . "</link>\n";
        $rss .= "<description>" . $this->_lang['meta_description_rss'] . $this->separator . $this->url_host . "</description>\n";
        $rss .= "<generator>" . $this->_lang['meta_generator_rss'] . "</generator>\n";
        $rss .= "<language>" . $this->_lang['LangShort'] . "</language>\n";
        foreach ($array as $ar) {
            $rss .= "<item>\n";
            $rss .= "<title>" . $ar->rrs_title . "</title>\n";
            $rss .= "<link>" . $ar->rrs_link . "</link>\n";
            $rss .= "<description><![CDATA[" . $this->_text->substr(strip_tags($ar->rrs_description), 0, 400) . "...]]></description>\n";
            if ($content == '1') {
                $rss .= "<content:encoded><![CDATA[" . $ar->rrs_content . "]]></content:encoded>\n";
            }
            $rss .= "<pubDate>" . $ar->rrs_pubDate . "</pubDate>\n";
            $rss .= "<guid>" . $ar->rrs_link . "</guid>\n";
            $rss .= "<comments>" . $ar->rrs_link . "</comments>\n";
            $rss .= "<category>" . $ar->rrs_category . "</category>\n";
            $rss .= "</item>\n";
        }
        $rss .= "</channel>\n";
        $rss .= "</rss>\n";
        if (SX::get('system.use_seo') == 1) {
            $rss = $this->__object('Rewrite')->get($rss);
        }
        $rss = $this->iconv($rss, CHARSET, $charset);
        header('Content-Type: text/xml; charset=' . $charset);
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
        header('Content-Length: ' . strlen($rss));
        SX::output($rss, true);
    }

}
