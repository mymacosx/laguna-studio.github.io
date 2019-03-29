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

class Binder extends Magic {

    protected $_content;
    protected $_csspath;
    protected $_elems   = array();
    protected $_media   = array();
    protected $_options = array();
    protected $_script_file = array();
    protected $_script_code = array();
    protected $_style_file  = array();
    protected $_style_code  = array();
    protected $_code_file   = array();
    protected $_code_code   = array();

    /* Метод конструктор класса */
    public function __construct() {
        $this->_options = SX::get('system');
        if (!empty($this->_options) && SX::get('configs.debug') != '1') {
            $this->_options['active'] = true;
            $this->setIgnore();
        } else {
            $this->_options['active'] = false;
        }
    }

    /* Метод создает метку для автозамены */
    public function result($array) {
        if (isset($array['type'], $array['format'], $array['position'])) {
            $key = $this->key($array['type'], $array['format'], $array['position']);
            $name = '_' . $array['type'] . '_' . $array['format'];
            if (!isset($this->{$name}[$key])) {
                $this->{$name}[$key] = array();
            }
            return $key;
        }
        return NULL;
    }

    /* Метод обработки произвольного кода */
    public function code($array) {
        $format = $this->format($array);
        if (!empty($format) && !empty($array[$format])) {
            $result = $this->checkPatch($format, $array[$format]);
            if (!$this->_options['active'] || empty($this->_options['min_page']) || !isset($array['position']) || $result === false) {
                return $array[$format];
            }
            $array += array('priority' => 100);
            $this->save('code', $format, $result, $array);
        }
        return NULL;
    }

    /* Метод обработки js */
    public function script($array) {
        $format = $this->format($array);
        if (!empty($format) && !empty($array[$format])) {
            $result = $this->checkPatch($format, $array[$format]);
            if (!$this->_options['active'] || empty($this->_options['comb_js']) || !isset($array['position']) || $result === false) {
                return $this->outscript($format, $array[$format]);
            }
            $array += array('priority' => 100);
            $this->save('script', $format, $result, $array);
        }
        return NULL;
    }

    /* Метод обработки css */
    public function style($array) {
        $format = $this->format($array);
        if (!empty($format) && !empty($array[$format])) {
            $result = $this->checkPatch($format, $array[$format]);
            if (!$this->_options['active'] || empty($this->_options['comb_css']) || !isset($array['position']) || $result === false) {
                $this->_media[$array[$format]] = isset($array['media']) ? $array['media'] : '';
                return $this->outstyle($format, $array[$format]);
            }
            if ($format == 'file') {
                $this->_media[$result] = isset($array['media']) ? $array['media'] : '';
            }
            $array += array('priority' => 100, 'media' => '');
            $this->save('style', $format, $result, $array);
        }
        return NULL;
    }

    /* Метод сохраннения данных */
    protected function save($type, $format, $result, $array) {
        $key = $this->key($type, $format, $array['position']);
        $name = '_' . $type . '_' . $format;
        $this->{$name}[$key][$array['priority']][] = $result;
    }

    /* Метод формирует ключ замены */
    protected function key($type, $format, $position) {
        $key = $type . '_' . $format . '_' . $position;
        $key = '@@@_REPLACE_' . strtoupper($key) . '_@@@';
        return $key;
    }

    /* Метод возвращает тип данных */
    protected function format($array) {
        $format = NULL;
        if (isset($array['file'])) {
            $format = 'file';
        } elseif (!empty($array['code'])) {
            $format = 'code';
        }
        return $format;
    }

    /* Метод вывода стилей */
    protected function outstyle($format, $value) {
        $result = NULL;
        if ($format == 'file') {
            $media = !empty($this->_media[$value]) ? ' media="' . $this->_media[$value] . '"' : '';
            $result = '<link type="text/css" rel="stylesheet" href="' . $value . '"' . $media . ' />' . PE;
        } elseif ($format == 'text') {
            $result = '<style type="text/css">' . PE;
            $result .= '/*<![CDATA[*/' . PE;
            $result .= $value . PE;
            $result .= '/*]]>*/' . PE;
            $result .= '</style>' . PE;
        }
        return $result;
    }

    /* Метод вывода js */
    protected function outscript($format, $value) {
        $result = NULL;
        if ($format == 'file') {
            $result = '<script type="text/javascript" src="' . $value . '"></script>' . PE;
        } elseif ($format == 'text') {
            $result = '<script type="text/javascript">' . PE;
            $result .= '//<![CDATA[' . PE;
            $result .= $value . PE;
            $result .= '//]]>' . PE;
            $result .= '</script>' . PE;
        }
        return $result;
    }

    /* Метод получения данных определенного типа */
    protected function data($type, $format) {
        $result = array();
        $name = '_' . $type . '_' . $format;
        if (!empty($this->$name)) {
            foreach ($this->$name as $key => $label) {
                krsort($label);
                $array = array();
                foreach ($label as $priority) {
                    foreach ($priority as $value) {
                        if (!empty($value)) {
                            $array[] = $value;
                        }
                    }
                }
                $result[$key] = array_unique($array);
            }
            $this->$name = array();
        }
        return $result;
    }

    /* Метод проверки допустимости кеширования файла */
    protected function checkPatch($format, $value) {
        if ($format == 'file') {
            if (empty($this->_options['ignore_list']) || !in_array(basename($value), (array) $this->_options['ignore_list'])) {
                if (strncasecmp($value, 'http:', 5) === 0) {
                    if (stripos($value, $_SERVER['HTTP_HOST']) === false) {
                        return false;
                    } else {
                        $value = str_replace(BASE_URL, '', $value);
                    }
                } else {
                    if (BASE_PATH != '/') {
                        $value = str_replace(BASE_PATH, '', $value);
                    }
                }
                return trim($value, '/\\');
            }
            return false;
        }
        return $value;
    }

    /* Метод выполнения задачи */
    public function execute($text) {
        $this->_content = $text;
        $this->getcss();
        $this->getJs();
        $this->getPage();
        return $this->_content;
    }

    /* Метод замены расширений в режиме реврайта */
    protected function useSeo($value, $type = '.php') {
        if ($this->_options['use_seo'] == 1) {
            $value = str_replace('.php', '.' . $type, $value);
        }
        return $value;
    }

    /* Метод сохраняет в массив список исключений js файлов */
    protected function setIgnore() {
        if (!empty($this->_options['ignore_list'])) {
            $this->_options['ignore_list'] = explode(',', $this->_options['ignore_list']);
            $this->_options['ignore_list'] = array_map('trim', $this->_options['ignore_list']);
        }
    }

    /* Метод выполнения операций с css */
    protected function getcss() {
        $array = $this->data('style', 'file');
        foreach ($array as $key => $value) {
            $replace = NULL;
            if (!empty($value)) {
                $name = $this->getName($value, 'css');
                $replace = $this->newCss($name, $value);
            }
            $this->_content = str_replace($key, $replace, $this->_content);
        }
        $array = $this->data('style', 'code');
        $this->replace($array, 'code', 'outstyle');
    }

    /* Метод контроля css */
    protected function newCss($name, $array) {
        $file = TEMP_DIR . '/cache/' . $name;
        if (!is_file($file)) {
            $text = $this->cssFiles($array);
            if (!empty($text)) {
                if ($this->_options['active']) {
                    if (!empty($this->_options['min_css'])) {
                        $text = $this->cssMinify($text);
                    }
                    if ($this->getParam('css')) {
                        $text = $this->setMods($this->_options['expires_css'], $this->_options['gzip_css'], 'css') . PE . $text;
                    }
                }
                $this->saveFile($file, trim($text));
            }
        }
        return $this->outstyle('file', BASE_URL . '/temp/cache/' . $this->useSeo($name, 'css'));
    }

    /* Метод сохранения файла */
    protected function saveFile($file, $text) {
        File::set($file, $text);
    }

    /* Метод минимизации css */
    protected function cssMinify($text) {
        static $object = false;
        if ($object === false) {
            include_once SX_DIR . '/lib/cssmin/CSSmin.php';
            $object = new CSSmin;
        }
        return $object->run($text);
    }

    /* Метод минимизации js */
    protected function jsMinify($text) {
        static $load = false;
        if ($load === false) {
            $load = true;
            include_once SX_DIR . '/lib/jsmin/jsmin.php';
        }
        return JSMin::minify($text);
    }

    /* Метод замены значений */
    protected function replace($array, $type = NULL, $method = NULL) {
        foreach ($array as $key => $value) {
            $value = implode(PE, $value);
            if (!empty($method)) {
                $value = $this->$method($type, $value);
            }
            $this->_content = str_replace($key, $value, $this->_content);
        }
    }

    /* Метод выполнения операций с js */
    protected function getJs() {
        $array = $this->data('script', 'file');
        foreach ($array as $key => $value) {
            $replace = NULL;
            if (!empty($value)) {
                $name = $this->getName($value, 'js');
                $replace = $this->newJs($name, $value);
            }
            $this->_content = str_replace($key, $replace, $this->_content);
        }
        $array = $this->data('script', 'code');
        $this->replace($array, 'code', 'outscript');
    }

    /* Метод контроля js */
    protected function newJs($name, $array) {
        $file = TEMP_DIR . '/cache/' . $name;
        if (!is_file($file)) {
            $text = $this->jsFiles($array);
            if (!empty($text)) {
                if ($this->_options['active']) {
                    if (!empty($this->_options['min_js'])) {
                        $text = $this->jsMinify($text);
                    }
                    if ($this->getParam('js')) {
                        $text = $this->setMods($this->_options['expires_js'], $this->_options['gzip_js'], 'javascript') . PE . $text;
                    }
                }
                $this->saveFile($file, trim($text));
            }
        }
        return $this->outscript('file', BASE_URL . '/temp/cache/' . $this->useSeo($name, 'js'));
    }

    /* Метод выполнения операций с шаблоном */
    protected function getPage() {
        $this->replace($this->data('code', 'file'));
        $this->replace($this->data('code', 'code'));
        if ($this->_options['active'] && !Arr::request('AJAX')) {
            if ($this->_options['min_page'] == 1) {
                $this->getMinify();
            }
            if ($this->_options['gzip_page'] == 1) {
                $text = $this->getCompress();
                if (!empty($text)) {
                    $this->gzipHeader();
                    $this->_content = $text;
                }
            }
        }
    }

    /* Метод получения статуса состояния параметров expires и gzip */
    protected function getParam($type) {
        static $cache = array();
        if (!isset($cache[$type])) {
            if (!empty($this->_options['expires_' . $type]) || !empty($this->_options['gzip_' . $type])) {
                $cache[$type] = true;
            } else {
                $cache[$type] = false;
            }
        }
        return $cache[$type];
    }

    /* Метод установки исключений минимизации текста */
    protected function ignoreMinify($value) {
        static $count = 1;
        $key = '@@@_REPLACE_IMUNE_' . $count++ . '_@@@';
        $this->_elems[$key] = $value[0];
        return $key;
    }

    /* Метод минимизации текста */
    protected function getMinify() {
        $search = '/<script[^>]*>.*?<\/script>|<pre[^>]*>.*?<\/pre>|<textarea[^>]*>.*?<\/textarea>/isu';
        $text = preg_replace_callback($search, array($this, 'ignoreMinify'), $this->_content);
        $text = Tool::cleanSpace($text);
        $this->_content = strtr($text, $this->_elems);
    }

    /* Метод возвращает имя нового файла */
    protected function getName($array, $ext) {
        $hash = array(
            $this->getTimes($array),
            $this->_options['expires_' . $ext],
            $this->_options['gzip_' . $ext],
            $ext
        );
        $hash = md5(implode('_', $hash));
        $ext = $this->extension($ext);
        return urlencode('sx_cms_' . $ext . '_' . $hash . '.' . $ext);
    }

    /* Метод чтения веременных меток файлов */
    protected function getTimes($array) {
        if (!empty($this->_options['cleanup'])) {
            $times = NULL;
            foreach ($array as $value) {
                if (($time = filemtime(SX_DIR . '/' . $value)) !== false) {
                    $times .= $time;
                }
            }
        } else {
            $times = 'no_control_auto';
        }
        return $times;
    }

    /* Метод gz сжатия данных */
    protected function getCompress() {
        $result = NULL;
        if (function_exists('gzcompress') && stripos(Arr::getServer('HTTP_ACCEPT_ENCODING'), 'gzip') !== false) {
            $size = strlen($this->_content);
            $crc = crc32($this->_content);
            $this->_content = gzcompress($this->_content, 2);
            $this->_content = substr($this->_content, 0, strlen($this->_content) - 4);
            $result .= "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $result .= $this->_content . pack('V', $crc) . pack('V', $size);
        }
        return $result;
    }

    /* Метод вывода заголовка */
    protected function gzipHeader() {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            if (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                header('Content-Encoding: gzip');
            } elseif (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
                header('Content-Encoding: x-gzip');
            }
        }
    }

    /* Метод создания данных для файлов вывода */
    protected function setMods($expires, $gzip, $type) {
        $result = '<?php
$hash = md5($_SERVER[\'SCRIPT_FILENAME\']);
header(\'Content-type: text/' . $type . '; charset: ' . CHARSET . '\');
header(\'Etag: "\' . $hash . \'"\');';
        if (!empty($expires)) {
            $result .= '
header(\'Cache-Control: must-revalidate\');
header(\'Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (365 * 24 * 3600)) . '\');
';
        }
        $result .= '
if (isset($_SERVER[\'HTTP_IF_NONE_MATCH\']) && stripslashes($_SERVER[\'HTTP_IF_NONE_MATCH\']) == \'"\' . $hash . \'"\') {
	if (php_sapi_name() == \'cgi\') {
		$type = \'Status:\';
	} else {
		$type = (isset($_SERVER[\'SERVER_PROTOCOL\']) && $_SERVER[\'SERVER_PROTOCOL\'] == \'HTTP/1.1\') ? \'HTTP/1.1\' : \'HTTP/1.0\';
	}
	header($type . \' 304 Not Modified\');
	header(\'Content-Length: 0\');
	exit;
}';
        if (!empty($gzip)) {
            $result .= '
ob_start(\'compress_output_option\');
function compress_output_option($contents) {
	if (!empty($_SERVER[\'HTTP_ACCEPT_ENCODING\'])) {
		$encoding = false;
		if (stripos($_SERVER[\'HTTP_ACCEPT_ENCODING\'], \'gzip\') !== false) {
			$encoding = \'gzip\';
		} elseif (stripos($_SERVER[\'HTTP_ACCEPT_ENCODING\'], \'deflate\') !== false) {
			$encoding = \'deflate\';
		}
		if ($encoding !== false && !empty($_SERVER[\'HTTP_USER_AGENT\'])) {
			$matches = array();
			if (stripos($_SERVER[\'HTTP_USER_AGENT\'], \'Opera\') === false && preg_match(\'/^Mozilla\/4\.0 \(compatible; MSIE ([\d]\.[\d])/iu\', $_SERVER[\'HTTP_USER_AGENT\'], $matches)) {
				$version = floatval($matches[1]);
				if ($version < 6 || ($version == 6 && stripos($_SERVER[\'HTTP_USER_AGENT\'], \'EV1\') === false)) {
				    $encoding = false;
				}
			}
		}
		if ($encoding !== false) {
			$contents = gzencode($contents, 9, ($encoding == \'gzip\' ? FORCE_GZIP : FORCE_DEFLATE));
			header(\'Content-Encoding: \' . $encoding);
			header(\'Content-Length: \' . strlen($contents));
		}
	}
	return $contents;
}
?>';
        }
        $result .= '';
        return $result;
    }

    /* Метод получения содержимого js файлов */
    protected function jsFiles($array) {
        $text = NULL;
        foreach ($array as $value) {
            if (($content = File::get(SX_DIR . '/' . $value)) !== false) {
                $text .= rtrim($content, ';') . ';' . PE;
            }
        }
        return $text;
    }

    /* Метод вставляет тип медиа в css файле */
    protected function setMedia($content, $value) {
        if (!empty($this->_media[$value])) {
            $content = '@media ' . $this->_media[$value] . ' {' . PE . $content . PE . '}';
        }
        return $content;
    }

    /* Метод получения содержимого css файлов */
    protected function cssFiles($array) {
        $text = NULL;
        foreach ($array as $value) {
            if (($content = File::get(SX_DIR . '/' . $value)) !== false) {
                $content = $this->setPatch($content, $value);
                $text .= $this->setMedia($content, $value) . PE;
            }
        }
        return $text;
    }

    /* Метод установки абсолютного пути к картинкам */
    protected function setPatch($load, $path) {
        $this->_csspath = $path;
        return preg_replace_callback('/url\((.*?)\)/siu', array($this, 'setUrl'), $load);
    }

    /* Метод изменения ссылки */
    protected function setUrl($match) {
        $url = trim($match[1], '\'"');
        if (strncasecmp($url, '/', 1) === 0 || strncasecmp($url, 'data:', 5) === 0 || strncasecmp($url, 'http:', 5) === 0) {
            return $match[0];
        }
        $path = trim(str_replace(basename($this->_csspath), '', $this->_csspath), '/\\');
        return 'url(\'' . BASE_PATH . $path . '/' . $url . '\')';
    }

    /* Метод установки расширения */
    protected function extension($ext) {
        return $this->getParam($ext) ? 'php' : $ext;
    }

}
