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

class View extends Smarty {

    protected static $_instance;

    public static function get() {
        if (self::$_instance === NULL) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct() {
        parent::__construct();
        $this->compile_dir = SX_DIR . '/temp/compiled/1/main';
        $this->cache_dir = SX_DIR . '/temp/compiled/1/main';
        $this->config_dir = SX_DIR . '/setup/lang';
        $this->template_dir = SX_DIR . '/setup/theme';
        $this->plugins_dir = array(SMARTY_DIR . 'plugins', SMARTY_DIR . 'statusplugins');

        $this->debugging = false;
        $this->error_unassigned = false; // ќтображать сообщени€ о неприсвоенных переменных
        $this->registerPlugin('modifier', 'sanitize', 'sanitize');

        $this->configLoad(SX_DIR . '/setup/lang/lang.txt');
        SX::$lang = $this->getConfigVars();
        $this->assign('lang', SX::$lang);
        $this->assign('theme', THEME);
        $this->assign('browser', SX::getAgent());
        $this->assign('homedir', 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/setup/setup.php', '', $_SERVER['PHP_SELF']));
        $this->assign('setupdir', 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/setup.php', '', $_SERVER['PHP_SELF']));
        $this->assign('Version', '1.06 UTF');
    }

}
