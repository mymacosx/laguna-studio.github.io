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

class AdminPhpEdit extends Magic {

    /* Вывод доступных для редактирования конфиг файлов */
    public function get() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        $request = Arr::getRequest('file');
        if (Arr::getPost('save') == 1) {
            if ($request == 'robots.txt') {
                $this->saveRobots();
            } else {
                $this->savePhp();
            }
        }

        if (!empty($request)) {
            if ($request == 'robots.txt') {
                $this->showRobots();
            } else {
                $this->showPhp();
            }
        }

        $d = SX_DIR . '/config/';
        $handle = opendir($d);
        $folders = array();
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, array('.', '..', '.htaccess', 'index.php')) && is_file($d . $file)) {
                $f = new stdClass;
                $f->Name = $file;
                $folders[] = $f;
            }
        }
        closedir($handle);

        if (is_file(SX_DIR . '/robots.txt')) {
            $e->Name = 'robots.txt';
            $folders[] = $e;
        }

        $this->_view->assign('folders', $folders);
        $this->_view->content('/settings/showphp.tpl');
    }

    /* Записываем данные в конфиг файл */
    protected function savePhp() {
        $f = SX_DIR . '/config/' . Arr::getRequest('file');
        if (is_file($f)) {
            if (!is_writable($f)) {
                chmod($f, 0777);
            }
            if (is_writable($f)) {
                File::set($f, Arr::getPost('file_content'));
            }
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=phpedit');
        }
    }

    /* Открываем для редактирования конфиг файл */
    protected function showPhp() {
        $d = SX_DIR . '/config/' . Arr::getRequest('file');
        if (is_file($d)) {
            $this->_view->assign('file_edit', 1);
            $this->_view->assign('file_content', File::get($d));
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=phpedit');
        }
    }

    /* Записываем данные в robots.txt */
    protected function saveRobots() {
        $f = SX_DIR . '/robots.txt';
        if (is_file($f)) {
            if (!is_writable($f)) {
                chmod($f, 0777);
            }
            if (is_writable($f)) {
                $fc = str_replace("\r\n", "\n", Arr::getPost('file_content'));
                File::set($f, $fc);
            }
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=phpedit');
        }
    }

    /* Открываем для редактирования robots.txt */
    protected function showRobots() {
        $d = SX_DIR . '/robots.txt';
        if (is_file($d)) {
            $this->_view->assign('file_edit', 1);
            $this->_view->assign('file_content', File::get($d));
        } else {
            $this->__object('Redir')->redirect('index.php?do=settings&sub=phpedit');
        }
    }

}
