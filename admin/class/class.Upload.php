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

class Upload extends Magic {

    protected $_folder;
    protected $_reports = array();
    protected $_allowed = array();
    protected $_options = array(
        'logs'   => true,          // Сохранение результатов загрузки
        'rand'   => false,         // Генерация случайного имени
        'input'  => false,         // Имя поля формы загрузки
        'upload' => false,         // Папка загрузки файлов
        'resize' => false,         // Значение ресайза изображения
        'result' => 'bool',        // orig, list, data, bool, ajax
        'type'   => 'file',        // file, image, audio, video, flash
        'size'   => 16384,         // Максимальный размер файла в Кб
    );
    protected $_blocked = array(   // Заблокированные файлы и расширения
        'file' => array('php.ini'),
        'exts' => array('php', 'php3', 'php4', 'php5', 'php6', 'phps', 'phtml', 'pht', 'cgi', 'fcgi', 'asp', 'aspx', 'shtml', 'shtm', 'js', 'jsp', 'htm', 'html', 'wml', 'fpl', 'pl', 'py', 'rb', 'sh', 'xl', 'htaccess', 'htpasswd'),
    );
    protected $_extensions = array(// Разрешенные расширения, могут быть перегружены из вне
        'file'  => array('asf', 'avi', 'csv', 'doc', 'fla', 'mid', 'mov', 'mp4', 'mpc', 'mpg', 'pdf', 'ppt', 'pxd', 'ram', 'rar', 'rmi', 'rtf', 'swf', 'sxc', 'sxw', 'tar', 'tgz', 'tif', 'txt', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xml', 'zip'),
        'image' => array('image/pjpeg' => 'jpg', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/gif' => 'gif', 'image/x-png' => 'png', 'image/png' => 'png'),
        'flash' => array('swf', 'flv'),
        'audio' => array('mp3'),
        'video' => array('flv'),
    );

    /* Метод конструктор класса */
    public function __construct() {
        $this->_folder = SX_DIR;
    }

    /* Метод установки параметров */
    public function options($options, $value = NULL) {
        if (!empty($options)) {
            if (!is_array($options)) {
                $options = array($options => $value);
            }
            $this->_options = $options + $this->_options;
        }
    }

    /* Метод загрузки файлов */
    public function load($options = array()) {
        $this->options($options);                       // Загружаем параметры
        if ($this->needs()) {                           // Проверяем наличие обязательных параметров
            $files = $this->create();                   // Создаем массив для обработки
            if ($files !== false) {
                $this->allowed();                       // Загружаем допустимые расширения по типу
                foreach ($files as $file) {
                    if ($this->check($file) === true) { // Выполняем различные проверки
                        $this->select($file);           // Выполняем обработку файла
                    }
                }
            }
        }
        if ($this->_options['logs'] === true) {
            $this->logs();
        }
        return $this->result();
    }

    /* Метод установки и получения разрешенных расширений */
    public function extensions($type, $array = NULL) {
        if (!empty($array)) {
            foreach ((array) $array as $key => $value) {
                $array[$key] = trim($value, '.');
            }
            $this->_extensions[$type] = $array;
        }
        return $this->_extensions[$type];
    }

    /* Метод установки разрешенных расширений по типу */
    protected function allowed() {
        $type = $this->_options['type'];
        if ($type == 'file') {
            foreach ($this->_extensions as $array) {
                $this->_allowed = array_merge($array, $this->_allowed);
            }
        } elseif (isset($this->_extensions[$type])) {
            $this->_allowed = $this->_extensions[$type];
        }
    }

    /* Метод выбора метода обработки */
    protected function select($file) {
        if ($this->_options['type'] == 'image') {
            $this->image($file);
        } else {
            $this->file($file);
        }
    }

    /* Метод проверки установки необходимых параметров */
    protected function needs() {
        return $this->_options['input'] !== false && $this->_options['upload'] !== false;
    }

    /* Метод подготовки массива с данными */
    protected function create() {
        if (isset($_FILES[$this->_options['input']])) {
            $files = $_FILES[$this->_options['input']];
            $result = array();
            if (is_array($files['name'])) {
                for ($i = 0; $i < count($files['name']); $i++) {
                    if (!empty($files['name'][$i])) {
                        $result[] = array(
                            'name'     => $files['name'][$i],
                            'type'     => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error'    => $files['error'][$i],
                            'size'     => $files['size'][$i],
                            'ext'      => Tool::extension($files['name'][$i])
                        );
                    }
                }
            } else {
                if (!empty($files['name'])) {
                    $array = array(
                        'name' => $files['name'],
                        'ext'  => Tool::extension($files['name'])
                    );
                    $result[] = $array + $files;
                }
            }
            return $result;
        }
        return false;
    }

    /* Метод проверки соответствия правилам */
    protected function check($file) {
        $result = $this->error($file['error']);
        if (empty($result)) {
            if (empty($file['tmp_name']) || $file['tmp_name'] == 'none') {
                $result = $this->_lang['UploadFileError'];
            } elseif (($file['size'] / 1024) > $this->_options['size']) {
                $result = $this->_lang['UploadMaxSize'];
            } elseif (in_array($this->_text->lower($file['name']), $this->_blocked['file'])) {
                $result = $this->_lang['UploadFileNoAllowed'];
            } elseif (in_array($file['ext'], $this->_blocked['exts'])) {
                $result = $this->_lang['UploadExtNoAllowed'];
            } elseif (!in_array($file['ext'], $this->_allowed)) {
                $result = $this->_lang['UploadExtAllowed'];
            } elseif (!is_writable($this->_folder . $this->_options['upload'])) {
                $result = $this->_lang['UploadFolderNoWritable'];
            }
        }
        if (empty($result)) {
            return true;
        }
        return $this->report(false, $result, $file['name']);
    }

    /* Метод получения текста ошибки по коду */
    protected function error($error) {
        if ($error == 0) {
            $result = NULL;
        } else {
            $array = array(
                1       => 'UploadIniError',
                2       => 'UploadFormError',
                3       => 'UploadPartialError',
                4       => 'UploadFileError',
                6       => 'UploadFolderError',
                7       => 'UploadWritableError',
                8       => 'UploadExtensionError',
                'error' => 'UploadUnknownError'
            );
            $lang = isset($array[$error]) ? $array[$error] : $array['error'];
            $result = $this->_lang[$lang];
        }
        return $result;
    }

    /* Метод формирования нового имени */
    protected function name($file) {
        $name = $file['name'];
        $allow = false;
        do {
            if ($this->_options['rand'] === true) {
                $name = Tool::uniqid($name) . '.' . $file['ext'];
            } else {
                list($name) = explode('.', $name);
                if ($allow === true) {
                    $end = substr($name, -1);
                    $end = !is_numeric($end) ? $end . '-2' : $end + 1;
                    $name = substr($name, 0, -1) . $end;
                }
                $name = translit($name) . '.' . $file['ext'];
            }
            $allow = true;
        } while (is_file($this->_folder . $this->_options['upload'] . $name));
        return $name;
    }

    /* Метод работы с изображением */
    protected function image($file) {
        $object = SX::object('Image');
        if (($file['ext'] = $object->mime($file['tmp_name'], true)) !== false) {
            $name = $this->name($file);
            $new_file = $this->_folder . $this->_options['upload'] . $name;
            if ($object->open($file['tmp_name'])) {
                if ($this->_options['resize'] !== false && $this->_options['resize'] > 0) {
                    $object->resize($this->_options['resize'], 'width');
                }
                $result = $object->save($new_file);
                $object->close();
                if ($result) {
                    return $this->report(true, $this->_lang['UploadSucces'], $file['name'], $name);
                }
            }
        }
        return $this->report(false, $this->_lang['UploadFileError'], $file['name']);
    }

    /* Метод работы с файлами */
    protected function file($file) {
        if (in_array($file['ext'], $this->_extensions['image'])) {
            $file['ext'] = false;
            $info = getimagesize($file['tmp_name']);
            if ($info !== false && isset($this->_extensions['image'][$info['mime']])) {
                $file['ext'] = $this->_extensions['image'][$info['mime']];
            }
        }
        if ($file['ext'] !== false) {
            $name = $this->name($file);
            if (move_uploaded_file($file['tmp_name'], $this->_folder . $this->_options['upload'] . $name)) {
                return $this->report(true, $this->_lang['UploadSucces'], $file['name'], $name);
            }
        }
        return $this->report(false, $this->_lang['UploadFileError'], $file['name']);
    }

    /* Метод сохранения отчетов */
    protected function report($result, $text, $file, $load = NULL) {
        $this->_reports[] = array(
            'result' => $result,
            'text'   => trim($text, '.'),
            'file'   => $file,
            'load'   => $load,
        );
        return $result;
    }

    /* Метод логирования отчетов */
    protected function logs() {
        if (!empty($this->_reports)) {
            foreach ($this->_reports as $array) {
                if (empty($array['load'])) {
                    $array['load'] = $array['file'];
                }
                SX::syslog($array['text'] . ': ' . $this->_options['upload'] . $array['load'], '0', $_SESSION['benutzer_id']);
            }
        }
    }

    /* Метод вывода результата  */
    protected function result() {
        switch ($this->_options['result']) {
            case 'orig':
                $result = $this->_reports;
                break;
            case 'ajax':
                $result = $this->ajax();
                break;
            case 'list':
                $result = $this->data();
                break;
            case 'data':
                $result = $this->data();
                $result = implode(',', $result);
                break;
            default:
            case 'bool':
                $result = $this->data();
                $result = !empty($result);
                break;
        }
        return empty($result) ? false : $result;
    }

    /* Метод вывода результата в виде массива */
    protected function data() {
        $result = array();
        if (!empty($this->_reports)) {
            foreach ($this->_reports as $array) {
                if ($array['result'] === true) {
                    $result[] = $array['load'];
                }
            }
        }
        return $result;
    }

    /* Метод вывода сообщения для ajax загрузки */
    protected function ajax() {
        $filename = NULL;
        $result = '<strong style="color:red">' . $this->_lang['UploadFileError'] . '</strong>';
        if (isset($this->_reports[0])) {
            $array = $this->_reports[0];
            if ($array['result'] === true) {
                $filename = $array['load'];
                if ($this->_options['type'] == 'image') {
                    $result = '<img src=' . BASE_URL . $this->_options['upload'] . $array['load'] . ' />';
                } else {
                    $result = '<strong style="color:green">' . $array['text'] . ': ' . $array['load'] . '</strong>';
                }
            } else {
                $result = '<strong style="color:red">' . $array['text'] . '</strong>';
            }
        }
        $result = '{ result: \'' . $result . '\', filename: \'' . $filename . '\' }';
        SX::output($result, true);
    }

}
