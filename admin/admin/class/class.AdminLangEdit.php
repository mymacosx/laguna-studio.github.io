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

class AdminLangEdit extends Magic {

    public function get() {
        if (!perm('lang_edit')) {
            $this->__object('AdminCore')->noAccess();
        }
        $_REQUEST['type'] = !empty($_REQUEST['type']) ? $_REQUEST['type'] : 'dir';
        switch ($_REQUEST['type']) {
            case 'widget':
                $this->extDir(WIDGET_DIR, 'widgets');
                break;

            case 'modul':
                $this->extDir(MODUL_DIR, 'modules');
                break;

            case 'files':
                $this->files(SX_DIR, '/lang/' . Arr::getRequest('path'), 'files');
                break;

            case 'widgets':
                $this->files(WIDGET_DIR, '/' . Arr::getRequest('path') . '/lang/' . Arr::getRequest('subpath'), 'widgets');
                break;

            case 'modules':
                $this->files(MODUL_DIR, '/' . Arr::getRequest('path') . '/lang/' . Arr::getRequest('subpath'), 'modules');
                break;

            default:
            case 'dir':
                $this->dirs();
                break;
        }
    }

    protected function dirs() {
        $this->_view->assign('zone', 1);
        $this->_view->assign('modul', $this->loadDir(MODUL_DIR . '/'));
        $this->_view->assign('widget', $this->loadDir(WIDGET_DIR . '/'));
        $this->_view->assign('folders', $this->loadDir(LANG_DIR . '/'));
        $this->_view->content('/settings/lang_edit.tpl');
    }

    protected function files($home, $dir, $type) {
        $file = Arr::getRequest('file');
        if (Arr::getPost('save') == 1) {
            $this->saveFiles($home . $dir . '/' . $file);
        }
        if (!empty($file)) {
            $this->text($home . $dir . '/' . $file);
            $this->_view->assign('dir', $dir . '/' . $file);
        }
        $this->_view->assign('zone', 0);
        $this->_view->assign('type', $type);
        $this->_view->assign('allowed', $this->allowed($file));
        $this->_view->assign('folders', $this->loadFiles($home . $dir . '/'));
        $this->_view->content('/settings/lang_edit.tpl');
    }

    protected function extDir($dir, $type) {
        $this->_view->assign('zone', 2);
        $this->_view->assign('type', $type);
        $this->_view->assign('folders', $this->loadDir($dir . '/' . Arr::getRequest('path') . '/lang/'));
        $this->_view->content('/settings/lang_edit.tpl');
    }

    protected function text($d) {
        $d = str_replace('./', '---', $d);
        if (is_file($d)) {
            $this->_view->assign('file_edit', 1);
            $this->_view->assign('file_content', File::get($d));
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=lang_edit');
        }
    }

    protected function loadDir($d) {
        $folders = array();
        $handle = opendir($d);
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_dir($d . $file)) {
                $f = new stdClass;
                $f->Name = $file;
                $folders[] = $f;
            }
        }
        closedir($handle);
        return $folders;
    }

    protected function loadFiles($d) {
        $folders = array();
        $handle = opendir($d);
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_file($d . $file)) {
                if (Tool::extension($file) == 'txt') {
                    $f = new stdClass;
                    $f->Name = $file;
                    $folders[] = $f;
                }
            }
        }
        closedir($handle);
        return $folders;
    }

    protected function saveFiles($f) {
        if (is_file($f)) {
            if (!is_writable($f)) {
                chmod($f, 0777);
            }
            if (is_writable($f)) {
                File::set($f, Arr::getPost('file_content'));
                if (Arr::getRequest('sort') == 1) {
                    $this->sort($f);
                }
            }
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=lang_edit');
        }
    }

    protected function allowed($file) {
        $file = basename($file);
        $array = array('main.txt', 'admin.txt', 'rewrite.txt');
        return in_array($file, $array);
    }

    protected function sort($file) {
        if ($this->allowed($file)) {
            $array = File::arr($file);
            if (!empty($array)) {
                $type = 'none';
                $result = array();
                foreach ($array as $value) {
                    $value = explode('=', $value, 2);
                    if (!isset($value[1])) {
                        $type = 'sort';
                    }
                    if (isset($value[0], $value[1])) {
                        $result[$type][trim($value[0])] = trim($value[1]);
                    }
                }
                if (!empty($result['sort'])) {
                    ksort($result['sort']);
                } else {
                    ksort($result['none']);
                }
                $save = NULL;
                foreach ($result['none'] as $key => $value) {
                    $save .= $key . ' = ' . $value . PE;
                }
                if (!empty($result['sort'])) {
                    $save .= PE;
                }
                foreach ($result['sort'] as $key => $value) {
                    $save .= $key . ' = ' . $value . PE;
                }
                File::set($file, $save);
            }
        }
    }

}
