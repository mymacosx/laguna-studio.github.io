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
        $langcode = Arr::getSession('admin_lang', 'ru');
        SX::setDefine('AREA', intval($_SESSION['a_area']));

        $this->compile_dir  = TEMP_DIR . '/compiled/' . AREA . '/admin';
        $this->cache_dir    = TEMP_DIR . '/compiled/' . AREA . '/admin';
        $this->config_dir   = LANG_DIR . '/' . $langcode;
        $this->template_dir = THEME;
        $this->compile_id   = $langcode . '_' . $theme;
        $this->plugins_dir  = array(SMARTY_DIR . 'plugins', SMARTY_DIR . 'statusplugins');
        $this->compile_check           = true;  // Проверять шаблон на изменения
        $this->compile_locking         = true;  // Блокировать доступ к файлам на время компиляции
        $this->force_compile           = false; // Режим усиленной компиляции
        $this->use_sub_dirs            = false; // Использовать подпапки для скомпилированных или кешированных файлов
        $this->caching                 = false; // Состояние кеширования
        $this->merge_compiled_includes = false; // Компилировать инклюды в одном файле с родителем
        $this->cache_lifetime          = 86400; // Время жизни кеша // 3600
        $this->force_cache             = false; // Режим усиленного создания кеша
        $this->cache_modified_check    = false; // Провераяем заголовки If-Modified-Since
        $this->direct_access_security  = true;  // Режим прямого доступа к скомпилированным файлам // if(!defined('SMARTY_DIR')) exit('no direct access allowed');

        $this->registerPlugin('modifier', 'translit', 'translit');
        $this->registerPlugin('modifier', 'sanitize', 'sanitize');
        $this->registerPlugin('modifier', 'base64encode', 'base64_encode');

        $this->registerPlugin('function', 'perm', 'perm');
        $this->registerPlugin('function', 'admin_active', 'admin_active');
        $this->registerPlugin('function', 'page_link', array(SX::object('Redir'), 'link'));
        if (SX::get('configs.tplcleanid') == '0') {
            $this->registerFilter('output', array($this, 'marker'));
        }
        $tpl_array = array(
            'charset'  => CHARSET,
            'settings' => SX::get('system'), // Передаем в шаблон глобальные настройки системы
            'configs'  => SX::get('configs'),
            'basepath' => BASE_PATH,
            'baseurl'  => BASE_URL,
            'browser'  => Tool::browser(),
            'langcode' => $langcode,
            'source'   => 'theme/' . $theme,
            'theme'    => $theme,
            'area'     => AREA,
            'csspath'  => 'theme/' . $theme . '/css',
            'jspath'   => '../js',
            'imgpath'  => 'theme/' . $theme . '/images',
            'incpath'  => SX_DIR . '/admin/theme/' . $theme,
            'time'     => time());
        $this->assign($tpl_array);
    }

    /* Метод добавления идентификатора в шаблон в режиме отладки */
    public function marker($source, $view) {
        if (isset($view->template_resource) && stripos($source, '<!DOCTYPE') === false) {
            $id = str_replace(SX_DIR, '', $view->template_resource);
            $source = PE . '<!-- Start - ' . $id . ' -->' . PE . $source . PE . '<!-- End - ' . $id . ' -->' . PE;
        }
        return $source;
    }

    /* Вывод в финальный тег */
    public function content($tpl, $tag = 'content') {
        return $this->assign($tag, $this->fetch(THEME . $tpl));
    }

}
