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

class AdminHelp extends Magic {

    protected $help;

    public function __construct() {
        $this->_view->configLoad(LANG_DIR . '/' . Arr::getSession('admin_lang') . '/help.txt');
        $this->load();
        $this->help = $this->_view->getConfigVars();
    }

    /* Загружаем файлы помощи модулей */
    protected function load() {
        $files = glob(MODUL_DIR . '/*/lang/' . $_SESSION['admin_lang'] . '/help.txt');
        foreach ($files as $file) {
            $this->_view->configLoad($file);
        }
    }

    /* Устанавливаем путь до изображений */
    protected function image($text) {
        $text = str_replace('{$imgpath}', 'theme/' . SX::get('options.theme') . '/images', $text);
        return $text;
    }

    /* Выводим всю справку */
    public function show() {
        $help = array();
        foreach ($this->help as $lang => $nid) {
            if (strpos($lang, 'do_') !== false && $lang != 'do_not_found') {
                $nid = $this->image($nid);
                $help[] = $nid;
            }
        }
        $this->_view->assign('all_help', 1);
        $this->_view->assign('help', $help);
        $this->_view->content('/other/help.tpl');
    }

    /* Выводим справку по модулю */
    public function get($page) {
        if (isset($this->help[$page])) {
            $help = $this->help[$page];
        } else {
            $help = explode('_', $page);
            $help = $help[0] . '_' . $help[1] . '_sub_default';
            $help = isset($this->help[$help]) ? $this->help[$help] : $this->help['do_not_found'];
        }
        $this->_view->assign('help', $this->image($help));
        $this->_view->content('/other/help.tpl');
    }

}
