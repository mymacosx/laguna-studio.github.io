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

class AdminThemes extends Magic {

    public function loadTpl() {
        $p = Arr::getRequest('path');
        $p = !empty($p) ? '/' . $p : '';
        $folders = array();
        $files = glob(SX_DIR . '/theme' . $p . '/*', GLOB_ONLYDIR);
        foreach ($files as $file) {
            $file = basename($file);
            if ($file != 'images' && $file != 'css') {
                $folders[] = $file;
            }
        }
        sort($folders);
        $this->_view->assign(array('title' => $this->_lang['Templates'], 'zone' => 1, 'folders' => $folders));
        $this->_view->content('/theme/edit_tpl.tpl');
    }

    public function showTpl() {
        $rpath = Arr::getRequest('path');
        $rfile = Arr::getRequest('file');
        if (Arr::getPost('save') == 1) {
            $f = SX_DIR . '/theme/' . $rpath . '/' . $rfile;
            if (!is_writable($f)) {
                chmod($f, 0777);
            }
            if (is_writable($f)) {
                $fc = Arr::getPost('file_content');
                if (!perm('templates_phpcode')) {
                    $fc = preg_replace('!{php}(.*){/php}!iu', '', $fc);
                    $fc = str_replace(array('{/php'), '', $fc);
                }
                File::set($f, $fc);
            }
        }

        $folders = array();
        $files = glob(SX_DIR . '/theme/' . $rpath . '/*.tpl');
        foreach ($files as $file) {
            $folders[] = basename($file);
        }

        if (!empty($rfile)) {
            $d = SX_DIR . '/theme/' . $rpath . '/' . $rfile;
            $d = str_replace('./', '---', $d);
            if (is_file($d)) {
                $content = File::get($d);
                $this->_view->assign(array('title' => $this->_lang['Templates'], 'file_edit' => 1, 'file_content' => $content));
            } else {
                $this->__object('Redir')->redirect('index.php?do=theme&amp;sub=show_all_tpl');
            }
        }
        sort($folders);
        $this->_view->assign(array('zone' => 0, 'topnav' => explode('/', $rpath), 'folders' => $folders));
        $this->_view->content('/theme/edit_tpl.tpl');
    }

    public function loadCss() {
        $p = Arr::getRequest('path');
        $_REQUEST['path'] = $p = (!empty($p)) ? $p . '/css' : '';
        $folders = array();
        $files = glob(SX_DIR . '/theme/' . $p . '/*', GLOB_ONLYDIR);
        foreach ($files as $file) {
            $folders[] = basename($file);
        }
        sort($folders);
        $this->_view->assign(array('title' => $this->_lang['ThemeStyle'], 'zone' => 1, 'folders' => $folders));
        $this->_view->content('/theme/edit_css.tpl');
    }

    public function showCss() {
        $rpath = Arr::getRequest('path');
        $rfile = Arr::getRequest('file');
        if (Arr::getPost('save') == 1) {
            $f = SX_DIR . '/theme/' . $rpath . '/' . $rfile;
            if (!is_writable($f)) {
                chmod($f, 0777);
            }
            if (is_writable($f)) {
                File::set($f, Arr::getPost('file_content'));
            }
        }

        $folders = array();
        $files = glob(SX_DIR . '/theme/' . $rpath . '/*.css');
        foreach ($files as $file) {
            $folders[] = basename($file);
        }

        if (!empty($rfile)) {
            $d = SX_DIR . '/theme/' . $rpath . '/' . $rfile;
            $d = str_replace('./', '---', $d);
            if (is_file($d)) {
                $content = File::get($d);
                $this->_view->assign(array('title' => $this->_lang['ThemeStyle'], 'file_edit' => 1, 'file_content' => $content));
            } else {
                $this->__object('Redir')->redirect('index.php?do=theme&amp;sub=show_all_css');
            }
        }
        sort($folders);
        $this->_view->assign(array('zone' => 0, 'folders' => $folders));
        $this->_view->content('/theme/edit_css.tpl');
    }

}
