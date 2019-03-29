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

class SAPE_base {

    public $_version = '1.0.5';
    public $_verbose = false;
    public $_charset = '';
    public $_server_list = array('dispenser-01.sape.ru', 'dispenser-02.sape.ru');
    public $_cache_lifetime = 3600;
    public $_cache_reloadtime = 600;
    public $_error = '';
    public $_host = '';
    public $_request_uri = '';
    public $_multi_site = false;
    public $_fetch_remote_type = ''; // Способ подключения к удалённому серверу [file_get_contents|curl|socket]
    public $_socket_timeout = 6; // Сколько ждать ответа
    public $_force_show_code = false;
    public $_is_our_bot = false;
    public $_debug = false;
    public $_db_file = '';

    public function __construct($options = null) {
        $host = '';
        if (is_array($options)) {
            if (isset($options['host']))
                $host = $options['host'];
        } elseif (strlen($options)) {
            $host = $options;
            $options = array();
        } else {
            $options = array();
        }

        if (strlen($host)) {
            $this->_host = $host;
        } else {
            $this->_host = $_SERVER['HTTP_HOST'];
        }

        $this->_host = preg_replace('/^http:\/\//', '', $this->_host);
        $this->_host = preg_replace('/^www\./', '', $this->_host);

        if (isset($options['request_uri']) && strlen($options['request_uri'])) {
            $this->_request_uri = $options['request_uri'];
        } else {
            $this->_request_uri = $_SERVER['REQUEST_URI'];
        }

        if (isset($options['multi_site']) && $options['multi_site'] == true)
            $this->_multi_site = true;
        if (isset($options['verbose']) && $options['verbose'] == true)
            $this->_verbose = true;
        if (isset($options['charset']) && strlen($options['charset']))
            $this->_charset = $options['charset'];
        if (isset($options['fetch_remote_type']) && strlen($options['fetch_remote_type']))
            $this->_fetch_remote_type = $options['fetch_remote_type'];
        if (isset($options['socket_timeout']) && is_numeric($options['socket_timeout']) && $options['socket_timeout'] > 0)
            $this->_socket_timeout = $options['socket_timeout'];
        if (isset($options['force_show_code']) && $options['force_show_code'] == true)
            $this->_force_show_code = true;
        if (isset($options['debug']) && $options['debug'] == true)
            $this->_debug = true;
        if (!defined('_SAPE_USER'))
            return $this->raise_error('Не задана константа _SAPE_USER');
        if (isset($_COOKIE['sape_cookie']) && ($_COOKIE['sape_cookie'] == _SAPE_USER)) {
            $this->_is_our_bot = true;
            if (isset($_COOKIE['sape_debug']) && ($_COOKIE['sape_debug'] == 1))
                $this->_debug = true;
        } else {
            $this->_is_our_bot = false;
        }
    }

    public function fetch_remote_file($host, $path) {
        $user_agent = $this->_user_agent . ' ' . $this->_version;
        @ini_set('allow_url_fopen', 1);
        @ini_set('default_socket_timeout', $this->_socket_timeout);
        @ini_set('user_agent', $user_agent);
        if ($this->_fetch_remote_type == 'file_get_contents' || ($this->_fetch_remote_type == '' && function_exists('file_get_contents') && ini_get('allow_url_fopen') == 1)) {
            $this->_fetch_remote_type = 'file_get_contents';
            if ($data = @file_get_contents('http://' . $host . $path))
                return $data;
        } elseif ($this->_fetch_remote_type == 'curl' || ($this->_fetch_remote_type == '' && function_exists('curl_init'))) {
            $this->_fetch_remote_type = 'curl';
            if ($ch = @curl_init()) {
                @curl_setopt($ch, CURLOPT_URL, 'http://' . $host . $path);
                @curl_setopt($ch, CURLOPT_HEADER, false);
                @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_socket_timeout);
                @curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                if ($data = @curl_exec($ch))
                    return $data;
                @curl_close($ch);
            }
        } else {
            $this->_fetch_remote_type = 'socket';
            $buff = '';
            $fp = @fsockopen($host, 80, $errno, $errstr, $this->_socket_timeout);
            if ($fp) {
                @fputs($fp, "GET {$path} HTTP/1.0\r\nHost: {$host}\r\n");
                @fputs($fp, "User-Agent: {$user_agent}\r\n\r\n");
                while (!@feof($fp))
                    $buff .= @ fgets($fp, 128);
                @fclose($fp);
                $page = explode("\r\n\r\n", $buff);
                return $page[1];
            }
        }
        return $this->raise_error('Не могу подключиться к серверу: ' . $host . $path . ', type: ' . $this->_fetch_remote_type);
    }

    public function _read($filename) {
        $fp = @fopen($filename, 'rb');
        @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache();
            $length = @filesize($filename);
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                $mqr = get_magic_quotes_runtime();
                @set_magic_quotes_runtime(0);
            }
            if ($length) {
                $data = fread($fp, $length);
            } else {
                $data = '';
            }

            if (version_compare(PHP_VERSION, '5.3.0', '<'))
                @set_magic_quotes_runtime($mqr);
            @flock($fp, LOCK_UN);
            @fclose($fp);
            return $data;
        }
        return $this->raise_error('Не могу считать данные из файла: ' . $filename);
    }

    public function _write($filename, $data) {
        $fp = @fopen($filename, 'wb');
        if ($fp) {
            @flock($fp, LOCK_EX);
            $length = strlen($data);
            @fwrite($fp, $data, $length);
            @flock($fp, LOCK_UN);
            @fclose($fp);
            if (md5($this->_read($filename)) != md5($data))
                return $this->raise_error('Нарушена целостность данных при записи в файл: ' . $filename);
            return true;
        }
        return $this->raise_error('Не могу записать данные в файл: ' . $filename);
    }

    public function raise_error($e) {
        $this->_error = '<p style="color: red; font-weight: bold;">SAPE ERROR: ' . $e . '</p>';
        if ($this->_verbose == true)
            print $this->_error;
        SX::syslog('Ошибка в модуле SAPE! ' . $e, '3', $_SESSION['benutzer_id']);
        return false;
    }

    public function load_data() {
        $this->_db_file = $this->_get_db_file();
        if (!is_file($this->_db_file)) {
            if (@touch($this->_db_file)) {
                @chmod($this->_db_file, 0666);
            } else {
                return $this->raise_error('Нет файла ' . $this->_db_file . '. Создать не удалось. Выставите права 777 на папку.');
            }
        }

        if (!is_writable($this->_db_file))
            return $this->raise_error('Нет доступа на запись к файлу: ' . $this->_db_file . '! Выставите права 777 на папку.');
        @clearstatcache();

        if (!$this->_is_our_bot && (filemtime($this->_db_file) < (time() - $this->_cache_lifetime) || filesize($this->_db_file) == 0)) {
            @touch($this->_db_file, (time() - $this->_cache_lifetime + $this->_cache_reloadtime));
            $path = $this->_get_dispenser_path();
            if (strlen($this->_charset))
                $path .= '&charset=' . $this->_charset;
            foreach ($this->_server_list as $i => $server) {
                if ($data = $this->fetch_remote_file($server, $path)) {
                    if (substr($data, 0, 12) == 'FATAL ERROR:') {
                        $this->raise_error($data);
                    } else {
                        $hash = @unserialize($data);
                        if ($hash != false) {
                            $hash['__sape_charset__'] = $this->_charset;
                            $data_new = @serialize($hash);
                            if ($data_new)
                                $data = $data_new;
                            $this->_write($this->_db_file, $data);
                            break;
                        }
                    }
                }
            }
        }

        if (strlen(session_id())) {
            $session = session_name() . '=' . session_id();
            $this->_request_uri = str_replace(array('?' . $session, '&' . $session), '', $this->_request_uri);
        }

        if ($data = $this->_read($this->_db_file))
            $this->set_data(unserialize($data));
    }

}

class SAPE_client extends SAPE_base {

    public $_links_delimiter = '';
    public $_links = array();
    public $_links_page = array();
    public $_user_agent = 'SAPE_Client PHP';

    public function __construct($options = null) {
        parent::__construct($options);
        $this->load_data();
    }

    public function return_links($n = null, $offset = 0) {
        if (is_array($this->_links_page)) {
            $total_page_links = count($this->_links_page);
            if (!is_numeric($n) || $n > $total_page_links)
                $n = $total_page_links;
            $links = array();

            for ($i = 1; $i <= $n; $i++) {
                if ($offset > 0 && $i <= $offset) {
                    array_shift($this->_links_page);
                } else {
                    $links[] = array_shift($this->_links_page);
                }
            }

            $html = join($this->_links_delimiter, $links);
            if (strlen($this->_charset) > 0 && strlen($this->_sape_charset) > 0 && $this->_sape_charset != $this->_charset && function_exists('iconv')) {
                $new_html = @iconv($this->_sape_charset, $this->_charset, $html);
                if ($new_html) {
                    $html = $new_html;
                }
            }
            if ($this->_is_our_bot)
                $html = '<sape_noindex>' . $html . '</sape_noindex>';
            return $html;
        } else {
            return $this->_links_page;
        }
    }

    public function _get_db_file() {
        if ($this->_multi_site) {
            return 'temp/links/' . $this->_host . '.links.db';
        } else {
            return 'temp/links/links.db';
        }
    }

    public function _get_dispenser_path() {
        return '/code.php?user=' . _SAPE_USER . '&host=' . $this->_host;
    }

    public function set_data($data) {
        $this->_links = $data;
        if (isset($this->_links['__sape_delimiter__'])) {
            $this->_links_delimiter = $this->_links['__sape_delimiter__'];
        }
        if (isset($this->_links['__sape_charset__']))
            $this->_sape_charset = $this->_links['__sape_charset__'];
        if (@array_key_exists($this->_request_uri, $this->_links) && is_array($this->_links[$this->_request_uri])) {
            $this->_links_page = $this->_links[$this->_request_uri];
        } else {
            if (isset($this->_links['__sape_new_url__']) && strlen($this->_links['__sape_new_url__'])) {
                if ($this->_is_our_bot || $this->_force_show_code) {
                    $this->_links_page = $this->_links['__sape_new_url__'];
                }
            }
        }
    }

}

class SAPE_context extends SAPE_base {

    public $_words = array();
    public $_words_page = array();
    public $_user_agent = 'SAPE_Context PHP';
    public $_filter_tags = array('a', 'textarea', 'select', 'script', 'style', 'label', 'noscript', 'noindex', 'button');

    public function __construct($options = null) {
        parent::__construct($options);
        $this->load_data();
    }

    public function replace_in_text_segment($text) {
        $debug = '';
        if ($this->_debug)
            $debug .= "<!-- argument for replace_in_text_segment: \r\n" . base64_encode($text) . "\r\n -->";
        if (count($this->_words_page) > 0) {
            if ($this->_debug)
                $debug .= '<!-- sentences for replace: ';
            foreach ($this->_words_page as $n => $sentence) {
                $special_chars = array('&amp;' => '&', '&quot;' => '"', '&#039;' => '\'', '&lt;' => '<', '&gt;' => '>');
                $sentence = strip_tags($sentence);
                foreach ($special_chars as $from => $to)
                    str_replace($from, $to, $sentence);
                $sentence = htmlspecialchars($sentence);
                $sentence = preg_quote($sentence, '/');
                $replace_array = array();
                if (preg_match_all('/(&[#a-zA-Z0-9]{2,6};)/isU', $sentence, $out)) {
                    for ($i = 0; $i < count($out[1]); $i++) {
                        $unspec = $special_chars[$out[1][$i]];
                        $real = $out[1][$i];
                        $replace_array[$unspec] = $real;
                    }
                }
                foreach ($replace_array as $unspec => $real)
                    $sentence = str_replace($real, '((' . $real . ')|(' . $unspec . '))', $sentence);
                $source_sentences[$n] = str_replace(' ', '((\s)|(&nbsp;))+', $sentence);
                if ($this->_debug)
                    $debug .= $source_sentences[$n] . "\r\n\r\n";
            }

            if ($this->_debug)
                $debug .= '-->';
            $first_part = true;
            if (count($source_sentences) > 0) {
                $content = '';
                $open_tags = array();
                $close_tag = '';
                $part = strtok(' ' . $text, '<');
                while ($part !== false) {
                    if (preg_match('/(?si)^(\/?[a-z0-9]+)/', $part, $matches)) {
                        $tag_name = strtolower($matches[1]);
                        if (substr($tag_name, 0, 1) == '/') {
                            $close_tag = substr($tag_name, 1);
                            if ($this->_debug)
                                $debug .= '<!-- close_tag: ' . $close_tag . ' -->';
                        } else {
                            $close_tag = '';
                            if ($this->_debug)
                                $debug .= '<!-- open_tag: ' . $tag_name . ' -->';
                        }
                        $cnt_tags = count($open_tags);
                        if (($cnt_tags > 0) && ($open_tags[$cnt_tags - 1] == $close_tag)) {
                            array_pop($open_tags);
                            if ($this->_debug)
                                $debug .= '<!-- ' . $tag_name . ' - deleted from open_tags -->';
                            if ($cnt_tags - 1 == 0) {
                                if ($this->_debug)
                                    $debug .= '<!-- start replacement -->';
                            }
                        }

                        if (count($open_tags) == 0) {
                            if (!in_array($tag_name, $this->_filter_tags)) {
                                $split_parts = explode('>', $part, 2);
                                if (count($split_parts) == 2) {
                                    foreach ($source_sentences as $n => $sentence) {
                                        if (preg_match('/' . $sentence . '/u', $split_parts[1]) == 1) {
                                            $split_parts[1] = preg_replace('/' . $sentence . '/', str_replace('$', '\$', $this->_words_page[$n]), $split_parts[1], 1);
                                            if ($this->_debug)
                                                $debug .= '<!-- ' . $sentence . ' --- ' . $this->_words_page[$n] . ' replaced -->';
                                            unset($source_sentences[$n]);
                                            unset($this->_words_page[$n]);
                                        }
                                    }
                                    $part = $split_parts[0] . '>' . $split_parts[1];
                                    unset($split_parts);
                                }
                            } else {
                                $open_tags[] = $tag_name;
                                if ($this->_debug)
                                    $debug .= '<!-- ' . $tag_name . ' - added to open_tags, stop replacement -->';
                            }
                        }
                    } else {
                        foreach ($source_sentences as $n => $sentence) {
                            if (preg_match('/' . $sentence . '/', $part) == 1) {
                                $part = preg_replace('/' . $sentence . '/u', str_replace('$', '\$', $this->_words_page[$n]), $part, 1);
                                if ($this->_debug)
                                    $debug .= '<!-- ' . $sentence . ' --- ' . $this->_words_page[$n] . ' replaced -->';
                                unset($source_sentences[$n]);
                                unset($this->_words_page[$n]);
                            }
                        }
                    }

                    if ($this->_debug) {
                        $content .= $debug;
                        $debug = '';
                    }
                    if ($first_part) {
                        $content .= $part;
                        $first_part = false;
                    } else {
                        $content .= $debug . '<' . $part;
                    }
                    unset($part);
                    $part = strtok('<');
                }
                $text = ltrim($content);
                unset($content);
            }
        } else {
            if ($this->_debug)
                $debug .= '<!-- No word`s for page -->';
        }

        if ($this->_debug)
            $debug .= '<!-- END: work of replace_in_text_segment() -->';

        if ($this->_is_our_bot || $this->_force_show_code || $this->_debug) {
            $text = '<sape_index>' . $text . '</sape_index>';
            if (isset($this->_words['__sape_new_url__']) && strlen($this->_words['__sape_new_url__']))
                $text .= $this->_words['__sape_new_url__'];
        }

        if ($this->_debug) {
            if (count($this->_words_page) > 0) {
                $text .= '<!-- Not replaced: ' . "\r\n";
                foreach ($this->_words_page as $n => $value)
                    $text .= $value . "\r\n\r\n";
                $text .= '-->';
            }
            $text .= $debug;
        }
        return $text;
    }

    public function replace_in_page(&$buffer) {
        if (count($this->_words_page) > 0) {
            $split_content = preg_split('/(?smi)(<\/?sape_index>)/', $buffer, -1);
            $cnt_parts = count($split_content);
            if ($cnt_parts > 1) {
                if ($cnt_parts >= 3) {
                    for ($i = 1; $i < $cnt_parts; $i = $i + 2)
                        $split_content[$i] = $this->replace_in_text_segment($split_content[$i]);
                }
                $buffer = implode('', $split_content);
                if ($this->_debug)
                    $buffer .= '<!-- Split by Sape_index cnt_parts=' . $cnt_parts . '-->';
            } else {
                $split_content = preg_split('/(?smi)(<\/?body[^>]*>)/', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
                if (count($split_content) == 5) {
                    $split_content[0] = $split_content[0] . $split_content[1];
                    $split_content[1] = $this->replace_in_text_segment($split_content[2]);
                    $split_content[2] = $split_content[3] . $split_content[4];
                    unset($split_content[3]);
                    unset($split_content[4]);
                    $buffer = $split_content[0] . $split_content[1] . $split_content[2];
                    if ($this->_debug)
                        $buffer .= '<!-- Split by BODY -->';
                } else {
                    if ($this->_debug)
                        $buffer .= '<!-- Can`t split by BODY -->';
                }
            }
        } else {
            if (!$this->_is_our_bot && !$this->_force_show_code && !$this->_debug) {
                $buffer = preg_replace('/(?smi)(<\/?sape_index>)/', '', $buffer);
            } else {
                if (isset($this->_words['__sape_new_url__']) && strlen($this->_words['__sape_new_url__']))
                    $buffer .= $this->_words['__sape_new_url__'];
            }
            if ($this->_debug)
                $buffer .= '<!-- No word`s for page -->';
        }
        return $buffer;
    }

    public function _get_db_file() {
        if ($this->_multi_site) {
            return 'temp/links/' . $this->_host . '.words.db';
        } else {
            return 'temp/links/words.db';
        }
    }

    public function _get_dispenser_path() {
        return '/code_context.php?user=' . _SAPE_USER . '&host=' . $this->_host;
    }

    public function set_data($data) {
        $this->_words = $data;
        if (@array_key_exists($this->_request_uri, $this->_words) && is_array($this->_words[$this->_request_uri]))
            $this->_words_page = $this->_words[$this->_request_uri];
    }

}
