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

class Rewrite extends Magic {

    protected $_count = 1;
    protected $_elems = array();

    /* Метод преобразования урлов в шаблонах */
    public function get($tpl) {
        $p = Arr::getRequest('p');
        $lang = $_SESSION['lang'];
        $load = SX::get('modules');
        $this->_view->configLoad(LANG_DIR . '/' . $lang . '/rewrite.txt');

        // Подключаем ленги реврайта внешних активных модулей
        foreach ($load as $modul) {
            if (is_file(MODUL_DIR . '/' . $modul . '/lang/' . $lang . '/rewrite.txt')) {
                $this->_view->configLoad(MODUL_DIR . '/' . $modul . '/lang/' . $lang . '/rewrite.txt');
            }
        }
        $arr = $this->_view->getConfigVars();
        if (SX::get('htaccess.auto') == 1) {
            $this->create();
        }
        $tpl = $this->normalize($tpl);
        $tpl = $this->start($tpl);

        $tpl = preg_replace('/&amp;lang=([a-z]*)/iu', '', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;currency=([\d]*)/iu', $arr['currency'] . '/\\2/', $tpl);

        // Подключаем файлы с реплейзами внешних активных модулей
        foreach ($load as $modul) {
            if (is_file(MODUL_DIR . '/' . $modul . '/main/rewrite.php')) {
                include MODUL_DIR . '/' . $modul . '/main/rewrite.php';
            }
        }

        if ($p == 'sitemap') {
            $tpl = $this->sitemap($tpl, $arr);
        }
        if (get_active('forums')) {
            $tpl = $this->forum($tpl, $arr, $p);
        }
        $tpl = $this->other($tpl, $arr, $p);
        if (get_active('pn')) {
            $tpl = $this->pn($tpl, $arr);
        }
        if (get_active('shop')) {
            $tpl = $this->shop($tpl, $arr);
        }
        if (get_active('gallery')) {
            $tpl = $this->gallery($tpl, $arr, $p);
        }
        if (get_active('poll')) {
            $tpl = $this->poll($tpl, $arr);
        }
        if (get_active('News')) {
            $tpl = $this->news($tpl, $arr);
        }
        if (get_active('content')) {
            $tpl = $this->content($tpl, $arr);
        }
        if (get_active('articles')) {
            $tpl = $this->articles($tpl, $arr);
        }
        if (get_active('links')) {
            $tpl = $this->links($tpl, $arr, $p);
        }
        if (get_active('downloads')) {
            $tpl = $this->downloads($tpl, $arr, $p);
        }
        if (get_active('cheats')) {
            $tpl = $this->cheats($tpl, $arr);
        }
        if (get_active('calendar')) {
            $tpl = $this->calendar($tpl, $arr);
        }
        if (get_active('products')) {
            $tpl = $this->products($tpl, $arr);
        }
        if (get_active('manufacturer')) {
            $tpl = $this->manufacturer($tpl, $arr);
        }
        if (get_active('faq')) {
            $tpl = $this->faq($tpl, $arr);
        }
        if (get_active('roadmap')) {
            $tpl = $this->roadmap($tpl, $arr, $p);
        }
        if (get_active('guestbook')) {
            $tpl = $this->guesbook($tpl, $arr);
        }
        if (get_active('newsletter')) {
            $tpl = $this->newsletter($tpl, $arr);
        }
        $tpl = $this->finish($tpl, $arr, $p);
        $tpl = $this->end($tpl);
        return $tpl;
    }

    protected function create() {
        $create = false;
        $seo = SX::get('system.Seo_Sprachen');
        $langs = SX::get('langs');
        if (!empty($seo)) {
            $sqllang = explode(';', $seo);
            foreach ($sqllang as $slang) {
                $svalue = explode(':', $slang);
                $sql_lang[] = $svalue[0];
                $sql_time[$svalue[0]] = $svalue[1];
            }
            if (count(array_diff($sql_lang, $langs)) != 1) {
                $create = true;
            }
        } else {
            $create = true;
        }

        $htempty = File::get(SX_DIR . '/.htaccess');
        if (empty($htempty)) {
            $create = true;
        }

        $sql_data = array();
        foreach ($langs as $lang) {
            $filetime = filemtime(LANG_DIR . '/' . $lang . '/rewrite.txt');
            $sql_data[] = $lang . ':' . $filetime;
            if (isset($sql_time[$lang])) {
                if ($sql_time[$lang] < $filetime) {
                    $create = true;
                }
            }
        }

        $filetime = filemtime(SX_DIR . '/class/class.Htaccess.php');
        $sql_data[] = 'func:' . $filetime;
        if (isset($sql_time['func'])) {
            if ($sql_time['func'] < $filetime) {
                $create = true;
            }
        }

        if ($create === true) {
            $this->__object('Htaccess')->get($sql_data);
        }
    }

    protected function normalize($tpl) {
        $_domain = stripos($_SERVER['SCRIPT_NAME'], 'index.php') ? str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']) : '';
        $_tags = array('URL=', 'background:url(', 'background-url:url(', 'src=', "src=\'", 'href=', 'onclick=', 'window.open(');
        $_link = array('index.php');

        $files = glob(SX_DIR . '/*', GLOB_ONLYDIR);
        foreach ($files as $file) {
            $file = basename($file);
            foreach ($_tags as $t) {
                $tpl = str_ireplace($t . '"' . $file . '/', $t . '"' . $_domain . '/' . $file . '/', $tpl);
                $tpl = str_ireplace($t . '\'' . $file . '/', $t . '\'' . $_domain . '/' . $file . '/', $tpl);
                $tpl = str_ireplace($t . $file . '/', $t . $_domain . '/' . $file . '/', $tpl);
            }
        }

        foreach ($_link as $l) {
            $tpl = str_ireplace('\'' . $l, '\'' . $_domain . '/' . $l, $tpl);
            $tpl = str_ireplace('"' . $l, '"' . $_domain . '/' . $l, $tpl);
        }
        return $tpl;
    }

    protected function start($tpl) {
        return preg_replace_callback('/<textarea[^>]+>.*?<\/textarea>|<!--START_NO_REWRITE-->.*?<!--END_NO_REWRITE-->/isu', array($this, 'noReplace'), $tpl);
    }

    protected function noReplace($source) {
        $key = '';
        if (!empty($source[0])) {
            $key = '___NO_REWRITE_' . $this->_count++ . '___';
            $this->_elems[$key] = $source[0];
        }
        return $key;
    }

    protected function end($tpl) {
        return strtr($tpl, $this->_elems);
    }

    protected function faq($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=faq&amp;action=faq&amp;fid=([\d]*)&amp;area=([\d]*)&amp;name=([\w-]*)/iu', $arr['faq'] . '/' . $arr['show'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=faq&amp;action=mail&amp;faq_id=([\d]*)&amp;area=([\d]*)/iu', $arr['faq'] . '/' . $arr['quest'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=faq&amp;action=display&amp;faq_id=([\d]*)&amp;area=([\d]*)&amp;name=([\w-]*)/iu', $arr['faq'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=faq&amp;area=([\d]*)/iu', $arr['faq'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function manufacturer($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=manufacturer&amp;area=([\d]*)&amp;page=([\d]*)&amp;sort=([\w-]*)&amp;q=([\w-+%]*)/iu', $arr['manufacturer'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=manufacturer&amp;area=([\d]*)&amp;page=([\d]*)&amp;sort=([\w-]*)/iu', $arr['manufacturer'] . '/' . $arr['page'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=manufacturer&amp;area=([\d]*)&amp;action=showdetails&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['manufacturer'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=manufacturer&amp;area=([\d]*)/iu', $arr['manufacturer'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function products($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=products&amp;area=([\d]*)&amp;page=([\d]*)&amp;sort=([\w-]*)&amp;q=([\w-+%]*)/iu', $arr['products'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=products&amp;area=([\d]*)&amp;page=([\d]*)&amp;sort=([\w-]*)/iu', $arr['products'] . '/' . $arr['page'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=products&amp;area=([\d]*)&amp;action=showproduct&amp;id=([\d]*)&amp;name=([\w-]*)&amp;artpage=([\d+}{]*)/iu', $arr['products'] . '/\\2/\\3/\\4/' . $arr['page'] . '/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=products&amp;area=([\d]*)&amp;action=showproduct&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['products'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=products&amp;area=([\d]*)/iu', $arr['products'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function calendar($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=calendar&amp;action=events&amp;show=([\w-]*)&amp;month=([\d]*)&amp;year=([\d]*)&amp;day=([\d]*)&amp;area=([\d]*)/iu', $arr['calendar'] . '/' . $arr['calendar_events'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;action=birthdays&amp;show=([\w-]*)&amp;month=([\d]*)&amp;year=([\d]*)&amp;day=([\d]*)&amp;area=([\d]*)/iu', $arr['calendar'] . '/' . $arr['birthdays'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;show=([\w-]*)&amp;action=week&amp;weekstart=([\d-]*)&amp;weekend=([\d-]*)&amp;area=([\d]*)/iu', $arr['calendar'] . '/' . $arr['calendar_week'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;area=([\d]*)&amp;action=displayyear&amp;show=([\w-]*)&amp;year=([\d]*)/iu', $arr['calendar'] . '/' . $arr['calendar_year'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;month=([\d]*)&amp;year=([\d]*)&amp;area=([\d]*)&amp;show=([\w-]*)/iu', $arr['calendar'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;month=([\d]*)&amp;year=([\d]*)&amp;area=([\d]*)/iu', $arr['calendar'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;area=([\d]*)&amp;action=myevents/iu', $arr['calendar'] . '/' . $arr['calendar_myevents'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;action=([\w-]*)&amp;show=([\w-]*)&amp;month=([\d]*)&amp;year=([\d]*)&amp;day=([\d]*)&amp;id=([\d]*)&amp;area=([\d]*)/iu', $arr['calendar'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;action=newevent&amp;day=([\d]*)&amp;month=([\d]*)&amp;year=([\d]*)&amp;area=([\d]*)&amp;show=([\w-]*)/iu', $arr['calendar'] . '/' . $arr['calendar_newevent'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;area=([\d]*)&amp;action=newevent&amp;month=([\d]*)&amp;year=([\d]*)&amp;area=([\d]*)&amp;show=([\w-]*)/iu', $arr['calendar'] . '/' . $arr['calendar_newevent'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=calendar&amp;area=([\d]*)/iu', $arr['calendar'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function cheats($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=cheats&amp;action=showcheat&amp;area=([\d]*)&amp;plattform=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['cheats'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=cheats&amp;area=([\d]*)&amp;plattform=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;sort=([\w-]*)/iu', $arr['cheats'] . '/\\2/\\3/\\4/' . $arr['page'] . '/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])?ql=([\w-+]*)&amp;action=search&amp;p=cheats&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['cheats'] . '/' . $arr['cheats_search'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=cheats&amp;area=([\d]*)&amp;plattform=([\d]*)&amp;name=([\w-]*)/iu', $arr['cheats'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=cheats&amp;area=([\d]*)&amp;action=search/iu', $arr['cheats'] . '/' . $arr['cheats_search'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=cheats&amp;area=([\d]*)/iu', $arr['cheats'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=cheats&amp;action=getfile&amp;id=([\d]*)/iu', $arr['cheats_getfile'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function articles($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;catid=([\d]*)&amp;page=([\d]*)&amp;q_news=([\w-+]*)&amp;limit=([\d]*)/iu', $arr['articles_archive'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;catid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)/iu', $arr['articles_archive'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;catid=([\d]*)&amp;name=([\w-+]*)/iu', $arr['articles_archive'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;action=displayarticle&amp;id=([\d]*)&amp;name=([\w-]*)&amp;artpage=([\d]*)/iu', $arr['articles'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;action=displayarticle&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['articles'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=reviews&amp;catid=([\d]*)&amp;name=([\w-]*)/iu', $arr['articles'] . '/' . $arr['articles_reviews'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=previews&amp;catid=([\d]*)&amp;name=([\w-]*)/iu', $arr['articles'] . '/' . $arr['articles_previews'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=specials&amp;catid=([\d]*)&amp;name=([\w-]*)/iu', $arr['articles'] . '/' . $arr['articles_specials'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=reviews/iu', $arr['articles'] . '/' . $arr['articles_reviews'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=previews/iu', $arr['articles'] . '/' . $arr['articles_previews'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;type=specials/iu', $arr['articles'] . '/' . $arr['articles_specials'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;mode=rss/iu', $arr['articles_rss'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)/iu', $arr['articles'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function links($tpl, $arr, $p) {
        if ($p == 'links') {
            $tpl = preg_replace('/index.php([?])p=links&amp;action=showdetails&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['links'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['links'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;sort=([\w-]*)/iu', $arr['links'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)/iu', $arr['links'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)/iu', $arr['links'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;action=links_sent/iu', $arr['links'] . '/' . $arr['links_sent'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;action=search/iu', $arr['links'] . '/' . $arr['links_search'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])?ql=([\w-+]*)&amp;action=search&amp;p=links&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['links'] . '/' . $arr['links_search'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)/iu', $arr['links'] . '/\\2/', $tpl);
        } else {
            $tpl = preg_replace('/index.php([?])p=links&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['links'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)/iu', $arr['links'] . '/\\2/', $tpl);
        }
        return $tpl;
    }

    protected function content($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=content&amp;id=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)#comments/iu', $arr['content'] . '/\\2/\\3/\\5/' . $arr['page'] . '/\\4/#comments', $tpl);
        $tpl = preg_replace('/index.php([?])p=content&amp;id=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)&amp;artpage=([\d]*)/iu', $arr['content'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=content&amp;id=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['content'] . '/\\2/\\3/\\4/', $tpl);
        return $tpl;
    }

    protected function news($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=news&amp;area=([\d]*)&amp;newsid=([\d]*)&amp;name=([\w-]*)&amp;artpage=([\d]*)/iu', $arr['news'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=news&amp;newsid=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['news'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=news&amp;area=([\d]*)&amp;newsid=([\d]*)&amp;name=([\w-]*)/iu', $arr['news'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=newsarchive&amp;area=([\d]*)&amp;catid=([\d]*)&amp;name=([\w-]*)/iu', $arr['newsarchive'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=newsarchive&amp;area=([\d]*)&amp;catid=([\d]*)/iu', $arr['newsarchive'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])area=([\d]*)&amp;p=newsarchive&amp;catid=([\d]*)&amp;t=1&amp;mode=rss/iu', $arr['newsrss'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=newsarchive&amp;area=([\d]*)&amp;mode=rss/iu', $arr['newsrss'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=newsarchive&amp;area=([\d]*)/iu', $arr['newsarchive'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function poll($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=poll&amp;id=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['poll'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=poll&amp;id=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['poll'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=poll&amp;action=archive&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['pollarchive'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=poll&amp;action=archive&amp;area=([\d]*)/iu', $arr['pollarchive'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=poll&amp;area=([\d]*)/iu', $arr['poll'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function gallery($tpl, $arr, $p) {
        if ($p == 'gallery') {
            $tpl = preg_replace('/index.php([?])ascdesc=([\w-]*)&amp;p=gallery&amp;action=showimage&amp;id=([\d]*)&amp;galid=([\d]*)&amp;categ=([\d]*)&amp;blanc=1&amp;first_id=([\d]*)/iu', $arr['gallery'] . '/' . $arr['gallerydiashow'] . '/\\2/\\3/\\6/\\5/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showimage&amp;id=([\d]*)&amp;galid=([\d]*)&amp;blanc=1&amp;first_id=([\d]*)&amp;ascdesc=([\w-]*)&amp;categ=([\d]*)/iu', $arr['gallery'] . '/' . $arr['gallerydiashow'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;id=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;ascdesc=([\w]*)&amp;pp=([\d]*)&amp;page=([\d]*)&amp;favorites=1&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimages'] . '/\\2/\\3/\\4/\\5/\\6/\\7/' . $arr['galleryfavs'] . '/\\8/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;id=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;ascdesc=([\w]*)&amp;pp=([\d]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimages'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showimage&amp;id=([\d]*)&amp;galid=([\d]*)&amp;ascdesc=([\w-]*)&amp;categ=([\d]*)&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimage'] . '/\\2/\\3/\\4/\\5/\\6/\\7/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showimage&amp;id=([\d]*)&amp;galid=([\d]*)&amp;ascdesc=([\w-]*)&amp;categ=([\d]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimage'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;id=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimages'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;q=([\w-+%]*)&amp;searchtype=([\w-]*)&amp;ascdesc=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;id=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;favorites=1&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimages'] . '/\\2/\\3/\\4/' . $arr['galleryfavs'] . '/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showincluded&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;q=([\w-+%]*)&amp;searchtype=([\w-]*)&amp;page=([\d]*)&amp;sort=([\w-]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showincluded&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/', $tpl);
        } else {
            $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showgallery&amp;id=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/' . $arr['galleryimages'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=gallery&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/', $tpl);
        }
        return $tpl;
    }

    protected function shop($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)&amp;s=([\d]*)&amp;avail=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/\\10/\\11/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;topseller=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/' . $arr['shop_topseller_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;offers=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/' . $arr['shop_offer_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;lowamount=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/' . $arr['shop_lowamount_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)&amp;topseller=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/' . $arr['shop_topseller_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)&amp;offers=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/' . $arr['shop_offer_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)&amp;lowamount=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/' . $arr['shop_lowamount_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)&amp;s=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/\\10/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/\\9/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;man=([\d]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/', $tpl);
        $tpl = preg_replace('/index.php([?])shop_q=([\w-=+% ]*)&amp;p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;pf=([\d.]*)&amp;pt=([\d.]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/' . $arr['search'] . '/\\2/\\3/\\4/\\5/\\6/\\7/\\8/', $tpl);
        $tpl = preg_replace('/index.php([?])exts=([\d]*)&amp;s=([\d]*)&amp;area=([\d]*)&amp;p=shop&amp;action=showproducts/iu', '\\2/\\3/\\4/' . $arr['shop'] . '/' . $arr['search'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])s=([\d]*)&amp;area=([\d]*)&amp;p=shop&amp;action=showproducts/iu', '\\2/\\3/' . $arr['shop'] . '/' . $arr['search'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_lowamount_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_offer_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_topseller_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;t=([\w-= ]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;cid=([\d]*)&amp;page=([\d]*)&amp;limit=([\d]*)&amp;t=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;area=([\d]*)&amp;action=showproduct&amp;id=([\d]*)&amp;cid=([\d]*)&amp;pname=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_product'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproduct&amp;id=([\d]*)&amp;cid=([\d]*)&amp;pname=([\w-= ]*)&amp;artpage=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_product'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproduct&amp;id=([\d]*)&amp;cid=([\d]*)&amp;pname=([\w-= ]*)&amp;blanc=1/iu', $arr['shop'] . '/' . $arr['shop_product'] . '/\\2/\\3/\\4/1/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproduct&amp;id=([\d]*)&amp;cid=([\d]*)&amp;pname=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_product'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;area=([\d]*)&amp;start=1&amp;name=([\w-= ]*)/iu', '\\3/\\2/' . $arr['shop'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;start=1/iu', $arr['shop'] . '/start/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=shippingcost/iu', $arr['shop'] . '/' . $arr['shippingcost'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showbasket/iu', $arr['shop'] . '/' . $arr['showbasket'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showsavedbaskets/iu', $arr['shop'] . '/' . $arr['showsavedbaskets'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=mylist&amp;subaction=load_list&amp;id=([\d]*)/iu', $arr['shop'] . '/' . $arr['mylist'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=mylist&amp;subaction=del_list&amp;id=([\d]*)/iu', $arr['shop'] . '/' . $arr['mylist'] . '/del/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=mylist/iu', $arr['shop'] . '/' . $arr['mylist'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=mydownloads&amp;sub=showfile&amp;Id=([\d]*)&amp;FileId=([\d]*)&amp;getId=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_download'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=mydownloads/iu', $arr['shop'] . '/' . $arr['mydownloads'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=myorders&amp;show=([\w-= ]*)&amp;page=([\d]*)/iu', $arr['shop'] . '/' . $arr['myorders'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=myorders&amp;show=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['myorders'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=myorders&amp;page=([\d]*)/iu', $arr['shop'] . '/' . $arr['myorders'] . '/-/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=myorders/iu', $arr['shop'] . '/' . $arr['myorders'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=prais&amp;page=([\d]*)/iu', $arr['shop'] . '/' . $arr['prais'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=prais/iu', $arr['shop'] . '/' . $arr['prais'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=shoporder&amp;step=2/iu', $arr['shop'] . '/' . $arr['shoporder'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=shoporder&amp;subaction=step([\d]*)/iu', $arr['shop'] . '/' . $arr['shoporder'] . '/' . $arr['step'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;s=1&amp;action=showproducts&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['search'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;s=1&amp;action=showproducts&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['search'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;s=1&amp;action=showproducts/iu', $arr['shop'] . '/' . $arr['search'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;cid=([\d]*)&amp;limit=([\d]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_new_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;cid=([\d]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_new_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;cid=([\d]*)&amp;limit=([\d]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_offer_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/' . $arr['shop_offer_products'] . '/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;cid=([\d]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_offer_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;cid=([\d]*)&amp;limit=([\d]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_lowamount_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;cid=([\d]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_lowamount_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/' . $arr['shop_lowamount_products'] . '/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;cid=([\d]*)&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_new_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_offer_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_lowamount_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;cid=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_new_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;offers=1&amp;cid=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_offer_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;lowamount=1&amp;cid=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_lowamount_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;cid=([\d]*)&amp;limit=([\d]*)&amp;list=([\w-]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_topseller_products'] . '/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;cid=([\d]*)&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_topseller_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;cid=([\d]*)&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_topseller_products'] . '/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;cid=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/\\3/' . $arr['shop_topseller_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;limit=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/' . $arr['shop_topseller_products'] . '/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/' . $arr['shop_topseller_products'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;page=([\d]*)&amp;topseller=1&amp;list=([\w-= ]*)/iu', $arr['shop'] . '/' . $arr['shop_products'] . '/\\2/' . $arr['shop_topseller_products'] . '/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;area=([\d]*)&amp;action=showseenproducts/iu', $arr['shop'] . '/' . $arr['showseenproducts'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=refusal/iu', $arr['shop'] . '/' . $arr['refusal'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=privacy/iu', $arr['shop'] . '/' . $arr['privacy'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=showproducts&amp;man=([\d]*)/iu', $arr['shop'] . '/' . $arr['shop_manufacturer'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;action=agb/iu', $arr['shop'] . '/' . $arr['shop_agb'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop&amp;area=([\d]*)/iu', $arr['shop'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=misc&amp;do=shippingcost/iu', $arr['misc'] . '/' . $arr['shippingcost'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=shop/iu', $arr['shop'] . '/', $tpl);
        return $tpl;
    }

    protected function forum($tpl, $arr, $p) {
        if (in_array($p, array('addpost', 'addtopic', 'showforums', 'showtopic', 'forum', 'forums', 'newpost', 'showforum', 'user', 'members', 'pn'))) {
            $tpl = preg_replace('/index.php([?])p=misc&amp;do=([\w-]*)&amp;uid=([\d]*)/iu', $arr['usercontact'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;print_post=([\d]*)&amp;toid=([\d]*)&amp;t=([\w-]*)/iu', $arr['postprint'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;fid=([\d]*)&amp;page=([\d]*)&amp;t=([\w-]*)/iu', $arr['topic'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;fid=([\d]*)&amp;t=([\w-]*)/iu', $arr['topic'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;pp=([\d]*)&amp;page=([\d]*)#pid_([\d]*)/iu', $arr['newforum'] . '/\\2/\\3/\\4/#\\5', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;pp=([\d]*)&amp;page=([\d]*)/iu', $arr['newforum'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)/iu', $arr['topic'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforums&amp;cid=([\d]*)&amp;t=([\w-]*)/iu', $arr['forums'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=show&amp;unit=h&amp;period=24/iu', $arr['last24'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=search_mask&amp;fid=([\d]*)/iu', $arr['search_mask'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=search_mask/iu', $arr['search_mask'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=posting&amp;id=([\d]*)&amp;page=([\d]*)&amp;pp=([\d]*)/iu', $arr['userposting'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=topicsempty&amp;page=([\d]*)&amp;pp=([\d]*)/iu', $arr['forumsemptytopics'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=topicsempty/iu', $arr['forumsemptytopics'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=posting&amp;id=([\d]*)/iu', $arr['userposting'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=subscription&amp;id=([\d]*)/iu', $arr['subscription'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=subscription/iu', $arr['subscriptions'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=print&amp;what=lastposts/iu', $arr['forumslastposts'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=markread&amp;what=forum&amp;ReadAll=1/iu', $arr['markread'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=markread&amp;what=forum&amp;id=([\d]*)/iu', $arr['markread'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=addsubscription&amp;t_id=([\d]*)/iu', $arr['addsubscription'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=related&amp;t_id=([\d]*)/iu', $arr['related'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=friendsend&amp;t_id=([\d]*)/iu', $arr['friendsend'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=unsubscription&amp;t_id=([\d]*)/iu', $arr['unsubscription'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=newpost&amp;action=edit&amp;pid=([\d]*)&amp;toid=([\d]*)/iu', $arr['editpost'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=newpost&amp;toid=([\d]*)&amp;pp=([\d]*)&amp;num_pages=([\d]*)/iu', $arr['newpost'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=newpost&amp;action=quote&amp;pid=([\d]*)&amp;toid=([\d]*)/iu', $arr['newquote'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=newpost&amp;toid=([\d]*)/iu', $arr['newpost'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=getfile&amp;id=([\d]*)&amp;f_id=([\d]*)&amp;t_id=([\d]*)/iu', $arr['getfile'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=getimage&amp;id=([\d]*)&amp;f_id=([\d]*)&amp;t_id=([\d]*)/iu', $arr['getimage'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforum&amp;fid=([\d]*)&amp;period=([\w- ]*)&amp;sortby=([\w- ]*)&amp;sort=([\w- ]*)&amp;pp=([\d]*)&amp;page=([\d]*)/iu', $arr['forumpage'] . '/\\2/\\3/\\4/\\5/\\6/\\7/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforum&amp;fid=([\d]*)&amp;sortby=([\w- ]*)&amp;sort=([\w- ]*)/iu', $arr['forumpage'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforum&amp;fid=([\d]*)&amp;t=([\w-]*)/iu', $arr['forum'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=help&amp;hid=([\d]*)&amp;sub=([\w-]*)/iu', $arr['forumshelp'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forum&amp;action=help/iu', $arr['forumshelp'] . '/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forums&amp;action=delpost&amp;pid=([\d]*)&amp;toid=([\d]*)/iu', $arr['userforum'] . '/' . $arr['delpost'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forums&amp;action=movepost&amp;pid=([\d]*)&amp;fid=([\d]*)/iu', $arr['userforum'] . '/' . $arr['movepost'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forums&amp;action=newtopic&amp;fid=([\d]*)/iu', $arr['userforum'] . '/' . $arr['newtopic'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forums&amp;action=complaint&amp;fid=([\d]*)&amp;pid=([\d]*)/iu', $arr['userforum'] . '/' . $arr['complaint'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=forums&amp;action=([\w-]*)&amp;pid=([\d]*)/iu', $arr['userforum'] . '/\\2/\\3/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforums/iu', $arr['forums'] . '/', $tpl);
        } else {
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;pp=([\d]*)&amp;page=([\d]*)#pid_([\d]*)/iu', $arr['newforum'] . '/\\2/\\3/\\4/#\\5', $tpl);
            $tpl = preg_replace('/index.php([?])p=showtopic&amp;toid=([\d]*)&amp;fid=([\d]*)&amp;t=([\w-]*)/iu', $arr['topic'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=showforums/iu', $arr['forums'] . '/', $tpl);
        }
        return $tpl;
    }

    protected function sitemap($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=links&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)/iu', $arr['links'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=gallery&amp;action=showincluded&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $arr['gallery'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=showforum&amp;fid=([\d]*)&amp;t=([\w-]*)/iu', $arr['forum'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=showforums&amp;cid=([\d]*)&amp;t=([\w-]*)/iu', $arr['forums'] . '/\\2/\\3/', $tpl);
        return $tpl;
    }

    protected function pn($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=pn&amp;action=message&amp;id=([\d]*)&amp;goto=inbox/iu', $arr['pn'] . '/' . $arr['inbox'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn&amp;action=message&amp;id=([\d]*)&amp;goto=outbox/iu', $arr['pn'] . '/' . $arr['outbox'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn&amp;goto=inbox/iu', $arr['pn'] . '/' . $arr['inbox'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn&amp;goto=outbox/iu', $arr['pn'] . '/' . $arr['outbox'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn&amp;action=new&amp;to=([\w-= ]*)/iu', $arr['pn'] . '/' . $arr['new'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn&amp;action=new/iu', $arr['pn'] . '/' . $arr['new'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pn/iu', $arr['pn'] . '/', $tpl);
        return $tpl;
    }

    protected function downloads($tpl, $arr, $p) {
        if ($p == 'downloads') {
            $tpl = preg_replace('/index.php([?])p=downloads&amp;action=showdetails&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)&amp;sort=([\w-]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)&amp;page=([\d]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)&amp;categ=([\d]*)&amp;name=([\w-]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)&amp;action=search/iu', $arr['downloads'] . '/' . $arr['downloads_search'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])?ql=([\w-+]*)&amp;action=search&amp;p=downloads&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['downloads'] . '/' . $arr['downloads_search'] . '/\\2/\\3/\\4/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)/iu', $arr['downloads'] . '/\\2/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;action=getfile&amp;id=([\d]*)/iu', $arr['downloads_getfile'] . '/\\2/', $tpl);
        } else {
            $tpl = preg_replace('/index.php([?])p=downloads&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $arr['downloads'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=downloads&amp;area=([\d]*)/iu', $arr['downloads'] . '/\\2/', $tpl);
        }
        return $tpl;
    }

    protected function roadmap($tpl, $arr, $p) {
        if ($p == 'roadmap' || $p == 'sitemap') {
            $tpl = preg_replace('/index.php([?])p=roadmap&amp;action=display&amp;rid=([\d]*)&amp;closed=([\d]*)&amp;area=([\d]*)&amp;name=([\w-]*)/iu', $arr['roadmap'] . '/' . $arr['roadmap_etap'] . '/\\2/\\3/\\4/\\5/', $tpl);
        }
        $tpl = preg_replace('/index.php([?])p=roadmap&amp;area=([\d]*)/iu', $arr['roadmap'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function guesbook($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=guestbook&amp;area=([\d]*)/iu', $arr['guestbook'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=guestbook&amp;page=([\d]*)&amp;area=([\d]*)/iu', $arr['guestbook'] . '/\\3/\\2/', $tpl);
        return $tpl;
    }

    protected function newsletter($tpl, $arr) {
        $tpl = preg_replace('/index.php([?])p=newsletter&amp;area=([\d]*)/iu', $arr['newsletter'] . '/\\2/', $tpl);
        return $tpl;
    }

    protected function other($tpl, $arr, $p) {
        $tpl = preg_replace('/index.php([?])p=members&amp;ud=([\w-]*)&amp;selby=([\w-]*)&amp;pp=([\d]*)&amp;page=([\d]*)/iu', $arr['users'] . '/\\2/\\3/\\4/\\5/', $tpl);
        $tpl = preg_replace('/index.php([?])p=members&amp;ud=([\w-]*)&amp;pp=([\d]*)&amp;page=([\d]*)/iu', $arr['users'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=members&amp;area=([\d]*)/iu', $arr['users'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=members/iu', $arr['users'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=userlogin&amp;action=ajaxlogin/iu', $arr['ajaxlogin'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=userlogin/iu', $arr['userlogin'] . '/', $tpl);
        if ($p == 'user') {
            $tpl = preg_replace('/index.php([?])p=user&amp;action=([\w-]*)&amp;do=([\w-]*)&amp;id=([\d]*)&amp;area=([\d]*)&amp;image=([\d]*)/iu', $arr['userprofile'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=user&amp;action=([\w-]*)&amp;do=([\w-]*)&amp;id=([\d]*)&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['userprofile'] . '/\\2/\\3/\\4/\\5/\\6/', $tpl);
            $tpl = preg_replace('/index.php([?])p=user&amp;action=([\w-]*)&amp;do=([\w-]*)&amp;id=([\d]*)&amp;area=([\d]*)/iu', $arr['userprofile'] . '/\\2/\\3/\\4/\\5/', $tpl);
            $tpl = preg_replace('/index.php([?])p=user&amp;id=([\d]*)&amp;area=([\d]*)&amp;friends=all#friends/iu', $arr['userprofile'] . '/\\2/\\3/all/#friends', $tpl);
        }
        $tpl = preg_replace('/index.php([?])p=user&amp;id=([\d]*)&amp;area=([\d]*)&amp;page=([\d]*)/iu', $arr['userprofile'] . '/\\2/\\3/\\4/', $tpl);
        $tpl = preg_replace('/index.php([?])p=user&amp;id=([\d]*)&amp;area=([\d]*)/iu', $arr['userprofile'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=user&amp;id=([\d]*)/iu', $arr['userprofile'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=useraction&amp;action=profile/iu', $arr['editprofile'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=useraction&amp;action=deleteaccount/iu', $arr['deleteaccount'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=useraction&amp;action=changepass/iu', $arr['changepass'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=forum&amp;action=ignorelist&amp;sub=([add|del]*)&amp;id=([\d]*)/iu', $arr['ignore'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=forum&amp;action=ignorelist/iu', $arr['ignorelist'] . '/', $tpl);
        return $tpl;
    }

    protected function finish($tpl, $arr, $p) {
        if ($p == 'search') {
            $tpl = preg_replace('/index.php([?])q=([\w-+%]+)&amp;where=([\w-]+)&amp;p=search/iu', '\\2/\\3/search/', $tpl);
        }
        if ($p == 'banned') {
            $tpl = preg_replace('/index.php([?])p=banned/iu', $arr['banned'] . '/', $tpl);
        }
        $tpl = preg_replace('/index.php([?])p=sitemap&amp;area=([\d]*)/iu', $arr['sitemap'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=sitemap&amp;action=full&amp;area=([\d]*)/iu', $arr['sitemap'] . '/' . $arr['full'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=imprint/iu', $arr['imprint'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=pwlost/iu', $arr['pwlost'] . '/', $tpl);
        $tpl = preg_replace('/index.php([?])p=rss&amp;area=([\d]*)&amp;action=([news|articles|forum]*)&amp;charset=([\w-]*)/iu', $arr['rss'] . '/\\2/\\4/\\3.xml', $tpl);
        $tpl = preg_replace('/index.php([?])p=rss&amp;area=([\d]*)&amp;action=([news|articles|forum]*)/iu', $arr['rss'] . '/\\2/\\3.xml', $tpl);
        $tpl = preg_replace('/index.php([?])p=rss&amp;area=([\d]*)&amp;charset=([\w-]*)/iu', '\\3/\\2/rss.xml', $tpl);
        $tpl = preg_replace('/index.php([?])p=rss&amp;area=([\d]*)/iu', '\\2/rss.xml', $tpl);
        $tpl = preg_replace('/index.php([?])p=index&amp;area=([\d]*)/iu', $arr['startindex'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])p=misc&amp;do=([\w-]*)&amp;id=([\d]*)/iu', $arr['misc'] . '/\\2/\\3/', $tpl);
        $tpl = preg_replace('/index.php([?])p=register&amp;area=([\d]*)/iu', $arr['register'] . '/\\2/', $tpl);
        $tpl = preg_replace('/index.php([?])area=([\d]*)/iu', $arr['startindex'] . '/\\2/', $tpl);
        return $tpl;
    }

}