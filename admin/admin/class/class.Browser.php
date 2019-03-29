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

class Browser extends Magic {

    protected $_trash;
    protected $_upload;
    protected $_maxsize = 1280;
    protected $_blacklist = array('php', 'php3', 'php4', 'php5', 'php6', 'phps', 'phtml', 'pht', 'cgi', 'fcgi', 'asp', 'aspx', 'shtml', 'shtm', 'js', 'jsp', 'htm', 'html', 'wml', 'fpl', 'pl', 'py', 'rb', 'sh', 'xl', 'htaccess', 'htpasswd');
    protected $_allowed = array(
        'file'  => array('asf', 'avi', 'csv', 'doc', 'fla', 'mid', 'mov', 'mp4', 'mpc', 'mpg', 'pdf', 'ppt', 'pxd', 'ram', 'rar', 'rmi', 'rtf', 'swf', 'sxc', 'sxw', 'tar', 'tgz', 'tif', 'txt', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xml', 'zip'),
        'image' => array('jpeg', 'jpg', 'jpe', 'gif', 'png'),
        'audio' => array('mp3'),
        'video' => array('flv'),
        'flash' => array('swf', 'flv'),
    );

    /* Метод конструктор класса */
    public function __construct() {
        $folder = Arr::getRequest('target', 'media');
        if (Arr::getRequest('mode') == 'editor' || empty($folder) || !is_dir(UPLOADS_DIR . '/' . $folder)) {
            $folder = 'media';
        }

        $this->_trash = UPLOADS_DIR . '/trash';
        $this->_upload = UPLOADS_DIR . '/' . $folder;

        $typ = Arr::getRequest('typ');
        $this->_allowed['typ'] = isset($this->_allowed[$typ]) ? $this->_allowed[$typ] : array();
        $this->_allowed['file'] = array_merge($this->_allowed['file'], $this->_allowed['image'], $this->_allowed['audio'], $this->_allowed['video'], $this->_allowed['flash']);

        $array = array(
            'folder'  => $folder,
            'create'  => ($folder == 'media' ? 1 : 0),
            'allowed' => $this->_allowed['typ'],
            'funcnum' => Tool::cleanDigit(Arr::getRequest('CKEditorFuncNum')),
            'upload'  => Tool::getPatch() . 'uploads/' . $folder
        );
        $this->_view->assign($array);

        if (!empty($_REQUEST['dir'])) {
            $check = $_REQUEST['dir'];
            $check2 = explode('/', $check);
            if ($this->prefix('...', $check2[1]) || $this->prefix('../', $check) || $this->prefix('/../', $check) || $this->prefix('http://', $check) || $this->prefix('https://', $check)) {
                $_REQUEST['dir'] = '/';
            }
        }
    }

    /* Метод загрузки файлов */
    public function receive() {
        if (perm('mediapool_upload')) {
            $pfad = str_replace('.', '', Arr::getRequest('pfad'));
            for ($i = 0; $i < count($_FILES['upfile']['tmp_name']); $i++) {
                $name = $this->_text->lower(trim($_FILES['upfile']['name'][$i]));
                $name = str_replace(' ', '_', $name);
                $temp = $_FILES['upfile']['tmp_name'][$i];
                if (!empty($name)) {
                    $ext = Tool::extension($name);
                    if (!in_array($ext, $this->_blacklist) && in_array($ext, $this->_allowed['typ'])) {
                        $type = $_FILES['upfile']['type'][$i] == 'image/pjpeg';
                        if ($type == 'image/pjpeg' || $type == 'image/jpeg' || $type == 'image/x-png' || $type == 'image/png' || $type == 'image/gif') {
                            if (in_array($ext, $this->_allowed['image'])) {
                                $this->image($temp, $pfad, $name);
                            }
                        } else {
                            if (in_array($ext, $this->_allowed['file'])) {
                                $this->file($temp, $pfad, $name);
                            }
                        }
                    } else {
                        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' пытался закачать опасный файл: ' . $name, '0', $_SESSION['benutzer_id']);
                    }
                }
            }
        }
        $out = '<script type="text/javascript">' . PE;
        $out .= '<!--' . PE;
        $out .= 'window.opener.parent.frames[\'left\'].location.href = window.opener.parent.frames[\'left\'].location.href' . PE;
        $out .= 'window.close()' . PE;
        $out .= '//-->' . PE;
        $out .= '</script>' . PE;
        SX::output($out, true);
    }

    /* Метод вывода шаблона с загрузчиком */
    public function upload() {
        $tpl = !perm('mediapool_upload') ? '/other/no_perm.tpl' : '/browser/browser_upload.tpl';
        $out = $this->_view->fetch(THEME . $tpl);
        SX::output($out, true);
    }

    /* Метод создания превьюхи */
    public function thumb() {
        $image = Arr::getRequest('image');
        $object = SX::object('Image');
        $dir = $this->currdir();

        $file = $this->_upload . $dir . $image;
        if ($object->open($file)) {
            $width = Arr::getRequest('width', 80);
            if ($object->width() > $width) {
                $object->resize($width, 'width');
            }
            $object->output($file);
            $object->close();
        }
        exit;
    }

    /* Метод переименования файла */
    public function rename() {
        if (perm('mediapool_rename')) {
            $file = Arr::getRequest('file');
            $new = $this->extname(Arr::getRequest('newfile'), $file);
            $dir = $this->currdir();
            $new = $this->name($new, $this->_upload . $dir);
            if (rename($this->_upload . $dir . $file, $this->_upload . $dir . $new)) {
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' переименовал файл: ' . $file . ' в: ' . $new, '0', $_SESSION['benutzer_id']);
            }
        }
        $out = '<script type="text/javascript">' . PE;
        $out .= '<!--' . PE;
        $out .= "parent.frames['left'].location.href='index.php?do=browser&sub=left&target=" . Arr::getRequest('target') . "&typ=" . Arr::getRequest('typ') . "&dir=" . Arr::getRequest('dir') . "'" . PE;
        $out .= '//-->' . PE;
        $out .= '</script>' . PE;
        SX::output($out, true);
    }

    /* Метод копирования файла */
    public function copy() {
        if (perm('mediapool_copy')) {
            $file = Arr::getRequest('file');
            $new = $this->extname(Arr::getRequest('newfile'), $file);
            $dir = $this->currdir();
            $new = $this->name($new, $this->_upload . $dir);
            if (copy($this->_upload . $dir . $file, $this->_upload . $dir . $new)) {
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал файл: ' . $file, '0', $_SESSION['benutzer_id']);
            }
        }
        $out = '<script type="text/javascript">' . PE;
        $out .= '<!--' . PE;
        $out .= "parent.frames['left'].location.href='index.php?do=browser&sub=left&target=" . Arr::getRequest('target') . "&typ=" . Arr::getRequest('typ') . "&dir=" . Arr::getRequest('dir') . "'" . PE;
        $out .= '//-->' . PE;
        $out .= '</script>' . PE;
        SX::output($out, true);
    }

    /* Метод удаления файла */
    public function delfile() {
        if (perm('mediapool_del')) {
            $file = Arr::getRequest('file');
            $dir = $this->currdir();
            copy($this->_upload . $dir . $file, $this->_trash . $dir . $file);
            if (File::delete($this->_upload . $dir . $file)) {
                SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил файл: ' . $file, '0', $_SESSION['benutzer_id']);
            }
        }
        $out = '<script type="text/javascript">' . PE;
        $out .= '<!--' . PE;
        $out .= "parent.frames['left'].location.href='index.php?do=browser&sub=left&target=" . Arr::getRequest('target') . "&typ=" . Arr::getRequest('typ') . "&dir=" . Arr::getRequest('dir') . "'" . PE;
        $out .= '//-->' . PE;
        $out .= '</script>' . PE;
        SX::output($out, true);
    }

    /* Вывод правого фрейма */
    public function right() {
        $image = Arr::getRequest('image');
        if (!empty($image)) {
            $dir = $this->currdir();
            if (perm('mediapool_edit')) {
                $image = $this->edit($image, $dir);
            }
            $size = getimagesize($this->_upload . $dir . $image);
            $array = array(
                'size'   => ($size[0] > $size[1] ? $size[0] : $size[1]),
                'sizes'  => $size[0] . ' x ' . $size[1],
                'width'  => $size[0],
                'height' => $size[1],
                'image'  => $image,
                'thumb'  => '<img id="image_right" border="0" src="index.php?do=browser&amp;sub=thumb&amp;width=320&amp;noout=1&amp;target=' . Arr::getRequest('target') . '&amp;rand=' . time() . '&amp;dir=' . $dir . '&amp;image=' . $image . '">',
            );
            if (Arr::getPost('save') == 1) {
                $array['reload'] = "parent.document.dat.fn.value = '" . $image . "';" . PE;
                $array['reload'] .= "parent.frames['left'].location.href='index.php?do=browser&sub=left&target=" . Arr::getRequest('target') . "&typ=" . Arr::getRequest('typ') . "&dir=" . $dir . "'" . PE;
            }
            $this->_view->assign($array);
        }
        $out = $this->_view->fetch(THEME . '/browser/browser_right.tpl');
        SX::output($out, true);
    }

    /* Редактирование изображения */
    protected function edit($image, $dir) {
        if (Arr::getPost('save') == 1) {
            if (Arr::getPost('copy') == 1) {
                $new = $this->name($image, $this->_upload . $dir);
                if (copy($this->_upload . $dir . $image, $this->_upload . $dir . $new)) {
                    $image = $new;
                    SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' скопировал изображение: ' . $image, '0', $_SESSION['benutzer_id']);
                }
            }
            $file = $this->_upload . $dir . $image;
            $object = $this->__object('Image');
            if ($object->open($file)) {
                switch (Arr::getPost('edit_action')) {
                    case 'rotate':
                        $object->rotate(Arr::getPost('degrees', 90));
                        break;
                    case 'grayscale':
                        $object->grayscale();
                        break;
                    case 'brightness':
                        $object->brightness(Arr::getPost('percent'));
                        break;
                    case 'brightness2':
                        $object->brightness(Arr::getPost('percent2'));
                        break;
                    case 'contrast':
                        $object->contrast(Arr::getPost('percent'));
                        break;
                    case 'contrast2':
                        $object->contrast(Arr::getPost('percent2'));
                        break;
                    case 'emboss':
                        $object->emboss();
                        break;
                    case 'negate':
                        $object->negate();
                        break;
                    case 'border':
                        $object->border(Arr::getPost('color', '#FFF'), Arr::getPost('border_width', 5));
                        break;
                    case 'smooth':
                        $object->smooth(25);
                        break;
                    case 'flip':
                        $object->flip();
                        break;
                    case 'flop':
                        $object->flop();
                        break;
                    case 'meanremoval':
                        $object->meanremoval();
                        break;
                    case 'edgedetect':
                        $object->edgedetect();
                        break;
                    case 'sepia':
                        $object->sepia();
                        break;
                    case 'picture':
                        $object->picture();
                        break;
                    case 'blurgaussian':
                        $object->blur('gaussian');
                        break;
                    case 'blurselective':
                        $object->blur('selective');
                        break;
                    case 'corners':
                        $object->corners(Arr::getPost('pixel', 5));
                        break;
                    case 'resize':
                        $width = Arr::getPost('size', 150);
                        if ($width > $this->_maxsize) {
                            $width = $this->_maxsize;
                        }
                        $object->resize($width, Arr::getPost('type', 'auto'));
                        break;
                    case 'crop':
                        list($w, $h, $x, $y) = $this->calculate();
                        if ($w > 0 && $h > 0) {
                            $object->crop($w, $h, $x, $y);
                        }
                        break;
                }
                $object->save($file);
                $object->close();
            }
            usleep(300000);
        }
        return $image;
    }

    /* Метод расчета размеров */
    public function calculate() {
        $a = Arr::getPost(array('width', 'height', 'thumb-w', 'thumb-h', 'crop-w', 'crop-h', 'crop-x', 'crop-y'), 0);
        if ($a['width'] > $a['thumb-w']) {
            $w = $a['width'] / $a['thumb-w'];
            $a['crop-w'] *= $w;
            $a['crop-x'] *= $w;
        }
        if ($a['height'] > $a['thumb-h']) {
            $w = $a['height'] / $a['thumb-h'];
            $a['crop-h'] *= $w;
            $a['crop-y'] *= $w;
        }
        $a = array_map('round', $a);
        return array($a['crop-w'], $a['crop-h'], $a['crop-x'], $a['crop-y']);
    }

    /* Вывод левого фрейма */
    public function left() {
        $dir = $this->currdir();
        $this->newdir($dir);

        $contents = $this->contents($dir);
        $array = array(
            'dats'   => $this->files($contents['file'], $dir),
            'bfiles' => $this->dirs($contents['dir'], $dir),
            'dir'    => $dir
        );
        if (!($dir == '/')) {
            $array['dir'] = $dir;
            $array['dirup'] = 1;
        }

        $this->_view->assign($array);
        $out = $this->_view->fetch(THEME . '/browser/browser_left.tpl');

        SX::output($out, true);
    }

    /* Метод создает новую папку */
    public function newdir($dir) {
        if (perm('mediapool_folder')) {
            if (!empty($_REQUEST['newdir'])) {
                $_REQUEST['newdir'] = translit($_REQUEST['newdir']);
                if (Folder::create($this->_upload . $dir . $_REQUEST['newdir'])) {
                    chmod($this->_upload . $dir . $_REQUEST['newdir'], 0777);
                } else {
                    $out = '<script type="text/javascript">' . PE;
                    $out .= 'alert("' . SX::$lang['NewFolderInf_E'] . '");' . PE;
                    $out .= '</script>' . PE;
                    SX::output($out);
                }
            }
        }
    }

    /* Метод дефолтного вывода */
    public function load() {
        $out = '';
        if (Arr::getRequest('noout') != 1) {
            $out = $this->_view->fetch(THEME . '/browser/browser.tpl');
        }
        SX::output($out, true);
    }

    /* Метод получения имени папки */
    protected function currdir() {
        $dir = !empty($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
        $dir = strpos($dir, '//') !== false || substr($dir, 0, 4) == '/../' ? '' : $dir;

        if (substr($dir, strlen($dir) - 4) == '/../') {
            $zerlegen = explode('/', $dir);
            $myf = count($zerlegen) - 4;
            $myd = '';
            for ($i = 0; $i < $myf; $i++) {
                if (!empty($zerlegen[$i])) {
                    $myd .= '/' . $zerlegen[$i];
                }
            }
            $dir = substr($myd, strlen($myd) - 1) == '/' ? $myd : $myd . '/';
        }
        if (empty($dir)){
            $dir = '/';
        }
        return $dir;
    }

    /* Метод обработки папок */
    protected function dirs($dirs, $dir) {
        asort($dirs);
        $array = array();
        foreach ($dirs as $val) {
            $row = new stdClass;
            $row->open = Arr::getRequest('typ') . '&amp;dir=' . $dir . $val . '/&amp;sub=left';
            $row->val = $val;
            $array[] = $row;
        }
        return $array;
    }

    /* Метод обработки файлов */
    protected function files($files, $dir) {
        asort($files);
        $array = array();
        foreach ($files as $val) {
            $ext = Tool::extension($val);
            if (in_array($ext, $this->_allowed['typ'])) {
                $row = new stdClass;
                $row->val = $val;
                $row->ext = is_file(THEME . '/images/mediapool/' . $ext . '.gif') ? $ext : 'attach';
                $row->size = File::filesize(filesize($this->_upload . $dir . $val) / 1024);
                $row->date = date('d.m.Y, H:i', filemtime($this->_upload . $dir . $val));

                if (in_array($ext, $this->_allowed['image'])) {
                    $row->image = '<img border="0" src="index.php?do=browser&amp;sub=thumb&amp;width=80&amp;noout=1&amp;target=' . Arr::getRequest('target') . '&amp;rand=' . time() . '&amp;dir=' . $dir  . '&amp;image=' . $val . '">';
                }
                $array[] = $row;
            }
        }
        return $array;
    }

    /* Метод получения содержимого папки */
    protected function contents($dir) {
        $array = array('dir' => array(), 'file' => array());
        if (($handle = opendir($this->_upload . $dir))) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, array('.', '..', '.htaccess', 'index.php'))) {
                    if (is_dir($this->_upload . $dir . $file)) {
                        $array['dir'][] = $file;
                    } else {
                        $array['file'][] = $file;
                    }
                }
            }
            closedir($handle);
        }
        return $array;
    }

    /* Метод обработки изображения */
    protected function image($tmp, $dir, $name) {
        $object = SX::object('Image');
        if ($object->open($tmp)) {
            if (Arr::getRequest('resize') == 1) {
                $width = intval(Arr::getRequest('w'));
                if ($width < 10) {
                    $width = 10;
                }
                if ($width > $this->_maxsize) {
                    $width = $this->_maxsize;
                }
                $object->resize($width, 'width');
            }
            $name = $this->name($name, $this->_upload . $dir);
            $object->save($this->_upload . $dir . $name);
            $object->close();
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' закачал новое изображение: ' . $name, '0', $_SESSION['benutzer_id']);
        }
    }

    /* Метод обработки файла */
    protected function file($tmp, $dir, $name) {
        $name = $this->name($name, $this->_upload . $dir);
        if (move_uploaded_file($tmp, $this->_upload . $dir . $name)) {
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' закачал новый файл: ' . $name, '0', $_SESSION['benutzer_id']);
        }
    }

    /* Метод проверки вхождения */
    protected function prefix($str, $in) {
        return ($this->_text->substr($in, 0, $this->_text->strlen($str)) == $str);
    }

    /* Метод формирования нового имени */
    protected function name($file, $dir) {
        $ext = Tool::extension($file);
        $name = basename($file);
        $allow = false;
        do {
            list($name) = explode('.', $name);
            if ($allow === true) {
                $end = $this->_text->substr($name, -1);
                $end = !is_numeric($end) ? $end . '-2' : $end + 1;
                $name = $this->_text->substr($name, 0, -1) . $end;
            }
            $name = translit($name) . '.' . $ext;
            $allow = true;
        } while (is_file($dir . $name));
        return $name;
    }

    /* Метод сохранения расширения при переименовке и копировании */
    protected function extname($newfile, $file) {
        list($name) = explode('.', $newfile);
        return $name . '.' . Tool::extension($file);
    }

}
