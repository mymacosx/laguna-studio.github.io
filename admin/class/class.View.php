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

SX::setDefine('SMARTY_DIR', SX_DIR . '/lib/smarty/');            // Устанавливаем путь к smarty
SX::setDefine('SMARTY_RESOURCE_CHAR_SET', CHARSET);              // Устанавливаем кодировку smarty
SX::setDefine('SMARTY_RESOURCE_DATE_FORMAT', '%d.%m.%Y, %H:%M'); // Устанавливаем формат вывода даты в smarty по умолчанию
include_once SMARTY_DIR . 'Smarty.class.php';

class View extends Smarty {

    protected $section;
    protected $default_tpl = 'main.tpl';

    /* Работаем во всем приложении с одним экземпляром класса */
    public static function get() {
        static $object = NULL;
        if ($object === NULL) {
            $object = new self;
        }
        return $object;
    }

    public function __construct() {
        parent::__construct();
        $theme = SX::get('options.theme');
        $config = SX::get('configs');
        $langcode = Arr::getSession('lang', 'ru');
        $this->section = SX::get('section');
        $css_theme = (!empty($this->section['CSS_Theme']) && is_dir(SX_DIR . '/theme/' . $theme . '/css/' . $this->section['CSS_Theme'])) ? $this->section['CSS_Theme'] : 'standard';
        SX::setDefine('AREA', intval($_SESSION['area']));

        $this->compile_dir = TEMP_DIR . '/compiled/' . AREA . '/main';
        $this->cache_dir = TEMP_DIR . '/compiled/' . AREA . '/main';
        $this->config_dir = LANG_DIR . '/' . $langcode;
        $this->template_dir = THEME;
        $this->compile_id = $langcode . '_' . $theme;
        $this->plugins_dir = array(SMARTY_DIR . 'plugins', SMARTY_DIR . 'statusplugins');
        $this->compile_check = true;            // Проверить шаблон на изменения
        $this->compile_locking = true;          // Блокировать доступ к файлам на время компиляции
        $this->force_compile = false;           // Режим усиленной компиляции
        $this->use_sub_dirs = false;            // Использовать подпапки для скомпилированных или кешированных файлов
        $this->caching = false;                 // Состояние кеширования
        $this->merge_compiled_includes = false; // Компилировать инклюды в одном файле с родителем
        $this->cache_lifetime = 86400;          // Время жизни кеша // 3600
        $this->force_cache = false;             // Режим усиленного создания кеша
        $this->cache_modified_check = false;    // Провераяем заголовки If-Modified-Since
        $this->direct_access_security = true;   // Режим прямого доступа к скомпилированным файлам // if(!defined('SMARTY_DIR')) exit('no direct access allowed');

        $binder = SX::object('Binder');
        $this->registerPlugin('function', 'result', array($binder, 'result'));
        $this->registerPlugin('function', 'script', array($binder, 'script'));
        $this->registerPlugin('function', 'style', array($binder, 'style'));
        $this->registerPlugin('function', 'code', array($binder, 'code'));

        $register = SX::object('Register');

        $this->registerPlugin('modifier', 'translit', 'translit');
        $this->registerPlugin('modifier', 'sanitize', 'sanitize');
        $this->registerPlugin('modifier', 'base64encode', 'base64_encode');
        $this->registerPlugin('modifier', 'html_truncate', array('Tool', 'truncateHtml'));
        $this->registerPlugin('modifier', 'autowords', array($register, 'autowords'));
        $this->registerPlugin('modifier', 'tooltip', array($register, 'tooltip'));

        $this->registerPlugin('function', 'get_active', 'get_active');
        $this->registerPlugin('function', 'permission', 'permission');
        $this->registerPlugin('function', 'perm', 'perm');
        $this->registerPlugin('function', 'widget', array($register, 'widget'));
        $this->registerPlugin('function', 'content', array($register, 'content'));
        $this->registerPlugin('function', 'contact', array($register, 'contact'));
        $this->registerPlugin('function', 'version', array($register, 'version'));
        $this->registerPlugin('function', 'page_link', array($register, 'page_link'));
        $this->registerPlugin('function', 'navigation', array($register, 'navigation'));
        $this->registerPlugin('function', 'phrases', array($register, 'phrases'));
        $this->registerPlugin('function', 'banner', array($register, 'banner'));
        $this->registerPlugin('function', 'flashtag', array($register, 'flashtag'));
        $this->registerPlugin('function', 'bookmarks', array($register, 'bookmarks'));
        $this->registerPlugin('function', 'forumstats', array($register, 'forumstats'));
        $this->registerPlugin('function', 'birthdays', array($register, 'birthdays'));
        $this->registerPlugin('function', 'newpn', array($register, 'newpn'));
        $this->registerPlugin('function', 'useronline', array($register, 'useronline'));
        $this->registerPlugin('function', 'onlinestatus', array($register, 'onlinestatus'));

        if ($config['tplcleanid'] == '0') {
            $this->registerFilter('output', array($this, 'marker'));
        }

        $tpl_array = array(
            'charset'        => CHARSET,
            'settings'       => SX::get('system'), // Передаем в шаблон переменную с глобальными настройками
            'basepath'       => BASE_PATH,
            'browser'        => Tool::browser(),
            'langcode'       => $langcode,
            'configs'        => $config,
            'theme'          => $theme,
            'area'           => AREA,
            'jspath'         => JS_PATH,
            'csspath'        => BASE_PATH . 'theme/' . $theme . '/css/' . $css_theme,
            'imgpath'        => BASE_PATH . 'theme/' . $theme . '/images',
            'imgpath_page'   => BASE_PATH . 'theme/' . $theme . '/images/page/',
            'imgpath_forums' => BASE_PATH . 'theme/' . $theme . '/images/forums/',
            'incpath'        => SX_DIR . '/theme/' . $theme,
            'baseurl'        => BASE_URL);
        $this->assign($tpl_array);
    }

    /* Метод вывода шаблона, в качестве шаблона используется строка с кодом вместо имени файла */
    public function text($text, $cache_id = NULL, $compile_id = NULL) {
        return $this->fetch('string: ' . $text, $cache_id, $compile_id, NULL, $this->debugging);
    }

    /* Метод добавления идентификатора в шаблон в режиме отладки */
    public function marker($source, $view) {
        if (isset($view->template_resource) && stripos($source, '<!DOCTYPE') === false) {
            $id = str_replace(SX_DIR, '', $view->template_resource);
            $source = PE . '<!-- Start - ' . $id . ' -->' . PE . $source . PE . '<!-- End - ' . $id . ' -->' . PE;
        }
        return $source;
    }

    /* Метод вывода */
    public function finish($param, $tag = 'content') {
        SX::object('Seo')->create($param);
        $this->assign($tag, $param['content']);
    }

    /* Оппределяем шаблон вывода */
    public function template($p = 'index') {
        if (defined('OUT_TPL')) {
            return (!is_file(THEME . '/page/' . OUT_TPL)) ? $this->default_tpl : OUT_TPL;
        } else {
            $p = trim(strtolower($p));
            $id = Arr::getRequest('id');
            if ($p == 'content' && !empty($id)) {
                $row = DB::get()->fetch_assoc("SELECT
                    Tpl_Extra
                FROM
                    " . PREFIX . "_content AS c,
                    " . PREFIX . "_content_kategorien AS k
                WHERE
                    k.Id = c.Kategorie
                AND
                    c.Id = '" . intval($id) . "'
                LIMIT 1");
                if (!empty($row['Tpl_Extra']) && is_file(THEME . '/page/' . $row['Tpl_Extra'])) {
                    return $row['Tpl_Extra'];
                }
            }

            $array = array(
                'forum'         => 'forums',
                'forums'        => 'forums',
                'newpost'       => 'forums',
                'showtopic'     => 'forums',
                'addtopic'      => 'forums',
                'showforum'     => 'forums',
                'showforums'    => 'forums',
                'addpost'       => 'forums',
                'user'          => 'forums',
                'banned'        => 'forums',
                'bookmark'      => 'useraction',
                'userlogin'     => 'useraction',
                'deleteaccount' => 'useraction',
                'rating'        => 'content',
                'comments'      => 'content',
                'contact'       => 'content',
                'notfound'      => 'content'
            );
            if (isset($array[$p])) {
                $p = $array[$p];
            }
            $p = 'Tpl_' . $p;
            $p = !empty($this->section[$p]) ? $this->section[$p] : $this->section['Tpl_index'];
            return $p == $this->default_tpl || !is_file(THEME . '/page/' . $p) ? $this->default_tpl : $p;
        }
    }

}