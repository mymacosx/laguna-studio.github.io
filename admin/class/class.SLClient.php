<?php
if (!defined('SX_DIR')) {
    exit('Доступ запрещен');
}

define('SETLINKS_CODE_VERSION', '4.0.0');

class SLConfig {

    var $aliases = Array(); // алиасы сайтов. без www, в нижнем регистре. пример: Array("sitealias.ru" => "mainsite.ru", "sitealias2.ru" => "mainsite.ru")
    var $password = SETLINKS_USER;  // Пароль
    var $encoding = 'UTF-8'; // Необходимая вам кодировка. (WINDOWS-1251, UTF-8, KOI8-R)
    var $server = 'show.setlinks.ru'; // сервер с которого берутся коды ссылок
    var $cachetimeout = 3600;  // Время обновления кэша в секундах
    var $errortimeout = 600;  // Период обновления кэша после ошибки в секундах
    var $cachedir = '';
    var $connecttype = '';  // тип соединения с сервером setlinks. (CURL - использовать библиотеку CURL, SOCKET - использовать сокеты, NONE - не соединяться с сервером, использовать данные кэша)
    // если $connecttype пусто, то тип соединения определяется автоматом
    var $sockettimeout = 5; // Ожидание кода, секунд
    var $indexfile = '^/index\\.(html|htm|php|phtml|asp)$'; // фильтр индексной страницы
    var $use_safe_method = false; // защита от проверки на продажность ссылок, читать тут http://forum.setlinks.ru/showthread.php?p=1506#post1495
    var $allow_url_params = ""; // параметры которые могут появлятся в урле через пробел "mod id username"
    var $show_comment = false; // если true, то выводить коментарии всем, а не только индексаторам
    var $show_errors = false; // выводить или нет ошибки
    // --- настройки для контекста ---
    // Список тегов в которых не будут проставляться контекстные ссылки
    var $context_bad_tags = array("a", "title", "head", "meta", "link", "h1", "h2", "thead", "xmp", "textarea", "select", "button", "script", "style", "label", "noscript", "noindex");
    var $context_show_comments = false; // если true, то выводить коментарии всем, а не только индексаторам
    var $path = '/articles/'; // путь с которого берутся статьи
    var $show_demo_links = false;

}

class SLClient {

    // внутренние переменные
    var $Config;
    var $links = false;
    var $context_links = false;
    var $forever_links = false;
    var $curlink = 0;
    var $servercachetime = 0;
    var $cachetime = 0;
    var $errortime = 0;
    var $delimiter = '';
    var $uri = false;
    var $host = '';
    var $_safe_params = Array();
    var $_servers = Array();
    var $_show_comment = false;
    var $_moder_message = '';

    function SLClient() {
        $this->Config = new SLConfig();
        $this->uri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $HTTP_SERVER_VARS['REQUEST_URI']);

        if (substr($this->Config->server, 0, 7) == 'http://') {
            $this->Config->server = substr($this->Config->server, 7);
        }

        if (strlen(session_id()) > 0) {
            $session = session_name() . "=" . session_id();
            $this->uri = str_replace(array('?' . $session, '&' . $session), '', $this->uri);
        }

        if (empty($this->uri) || (!empty($this->Config->indexfile) && preg_match("!" . $this->Config->indexfile . "!", $this->uri)))
            $this->uri = '/';

        $ok = false;
        if ($this->Config->connecttype == 'CURL' && !function_exists('curl_init'))
            $this->Error('CURL not found! Библиотека CURL не обнаружена!');
        elseif ($this->Config->connecttype == 'SOCKET' && !function_exists('fsockopen'))
            $this->Error('fsockopen not found! Внешние соединения не поддерживаются Вашим хостингом!');
        elseif ($this->Config->connecttype == 'NONE')
            ;
        elseif (!empty($this->Config->connecttype)) {
            $ok = true;
        }
        if (!$ok) {
            if (function_exists('curl_init'))
                $this->Config->connecttype = 'CURL';
            elseif (function_exists('fsockopen'))
                $this->Config->connecttype = 'SOCKET';
            else
                $this->Config->connecttype = 'NONE';
        }
        $this->host = $_SERVER['HTTP_HOST'];
        if (substr($this->host, 0, 4) == 'www.')
            $this->host = substr($this->host, 4);
        if (isset($this->Config->aliases[$this->host]))
            $this->host = $this->Config->aliases[$this->host];
        if (empty($this->Config->cachedir))
            $this->Config->cachedir = TEMP_DIR . '/links/';
        if (!is_dir($this->Config->cachedir))
            $this->Error("Can't open cache dir!");
        else if (!is_writable($this->Config->cachedir))
            $this->Error("Cache dir: Permission denied!");

        if (empty($this->cachefile))
            $this->cachefile = strtolower($this->host) . '.links';
        if ($this->Config->use_safe_method && trim($this->Config->allow_url_params) != '') {
            $prms = explode(" ", $this->Config->allow_url_params);
            foreach ($prms as $p)
                $this->_safe_params[] = sprintf("%u", crc32($p));
        }
    }

    function SaveLinksToCache($links, $info) {
        //     if(count($info)!=6) return false;
        unset($info[1]);
        $h = @fopen($this->Config->cachedir . $this->cachefile, 'w+');
        if ($h) {
            $info[6] = "0000000000";
            @fwrite($h, time() . "\t" . implode("\t", $info) . "\n");
            foreach ($links AS $val) {
                if (count($val) > 1)
                    @fwrite($h, implode("\t", $val) . "\n");
            }
            @fclose($h);
            return true;
        } else
            $this->Error('Can\'t open cache file!');
        return false;
    }

    function IsCached() {
        if (!is_file($this->Config->cachedir . $this->cachefile))
            return false;
        $h = @fopen($this->Config->cachedir . $this->cachefile, "r");
        if ($h) {
            $info = explode("\t", @fgets($h));
            if (empty($info) || count($info) != 7) {
                return false;
            }
            $this->cachetime = min(time() + 24 * 60 * 60, $info[0]);
            $this->servercachetime = $info[1];
            $this->delimiter = $info[2];
            $this->_safe_params = explode(' ', $info[4]);
            $this->_servers = explode(' ', $info[5]);
            $this->errortime = $info[6];
            @fclose($h);
        }

        if (($this->cachetime + $this->Config->cachetimeout > time()) || ($this->errortime + $this->Config->errortimeout > time()))
            return true;
        return false;
    }

    function GetLinks($countlinks = 0, $delimiter = false) {
        static $firstlink = true;

        if (!$this->IsCached()) {
            if (!$this->DownloadLinks()) {
                if (file_exists($this->Config->cachedir . $this->cachefile)) {
                    $h = fopen($this->Config->cachedir . $this->cachefile, "r+");
                    if ($h) {
                        $str = fgets($h);
                        if (strlen($str) > 25) {
                            fseek($h, strlen($str) - 11);
                            fwrite($h, time());
                        }
                        fclose($h);
                    }
                }
            }
        }


        $pageid = sprintf("%u", crc32($this->host . $this->uri));
        if ($this->links === false) {
            $h = @fopen($this->Config->cachedir . $this->cachefile, "r");
            if ($h) {
                $info = explode("\t", @fgets($h));
                $this->servercachetime = $info[0];
                $this->cachetime = $info[1];
                $this->delimiter = $info[2];
                $this->_safe_params = explode(' ', $info[4]);
                $this->_servers = explode(' ', $info[5]);
                $this->links = Array();

                while (!feof($h)) {
                    $links = explode("\t", @fgets($h));
                    $page_ids = explode(' ', $links[0]);
                    if ($page_ids[0] == -1 || $page_ids[0] == $pageid || ( isset($page_ids[1]) && $this->Config->use_safe_method && $page_ids[1] == $this->SafeUrlCrc32('http://' . $this->host . $this->uri))) {
                        unset($links[0]);
                        foreach ($links as $link) {
                            if (substr($link, 0, 1) == '1')
                                $this->context_links[] = substr($link, 1);
                            else if (substr($link, 0, 1) == '2')
                                $this->forever_links[] = substr($link, 1);
                            else if (substr($link, 0, 1) == '0')
                                $this->links[] = substr($link, 1);
                            else
                                $this->links[] = $link;
                        }
                    }
                }
                @fclose($h);
            }
            $user_ip = (isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR']);
            if ($this->Config->show_demo_links || in_array($user_ip, $this->_servers)) {
                if ($this->links === false || count($this->links) == 0)
                    $this->links = Array("This is <a href=''>DEMO</a> link!");
                if ($this->forever_links === false || count($this->forever_links) == 0)
                    $this->forever_links = Array("Title: <a href=''>link</a> demo.<br/>This is <a href=''>DEMO FOREVER</a> link.");
            }
        }

        //$this->ModerMessage("Page links: ".var_export($this->links,1)."\n".var_export($_SERVER,1));

        if ($countlinks == -1)
            return true;

        $returnlinks = Array();
        $cnt = count($this->links);
        if ($countlinks > 0)
            $cnt = min($cnt, $this->curlink + $countlinks);
        for (; $this->curlink < $cnt; $this->curlink++) {
            $returnlinks[] = $this->links[$this->curlink];
        }

        $user_ip = (isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR']);
        if ($this->Config->show_comment || (!empty($this->_servers) && in_array($user_ip, $this->_servers)) || empty($this->_servers)) {
            $this->_show_comment = true;
        } else {
            $this->_show_comment = false;
        }

        $retstring = (($firstlink && $this->_show_comment) ? '<!--' . substr($this->Config->password, 0, 5) . '-->' : '')
                . implode(($delimiter === false ? $this->delimiter : $delimiter), $returnlinks);
        $firstlink = false;

        return $retstring . $this->GetModerMessage();
    }

    function DownloadLinks() {
        $page = '';
        $path = "/?host=" . $this->host . "&k=" . $this->Config->encoding . "&p=" . $this->Config->password . "&time=" . time() . "&v=" . SETLINKS_CODE_VERSION . ($this->Config->use_safe_method ? "&safe" : "");
        if ($this->Config->connecttype == "CURL") {
            $curl = curl_init('http://' . $this->Config->server . $path);
            if ($curl) {
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->Config->sockettimeout);
                curl_setopt($curl, CURLOPT_TIMEOUT, $this->Config->sockettimeout);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $page = curl_exec($curl);
                if (curl_error($curl) OR curl_getinfo($curl, CURLINFO_HTTP_CODE) != '200') {
                    curl_close($curl);
                    return false;
                }
                curl_close($curl);
            } else {
                return false;
            }
        } else if ($this->Config->connecttype == "SOCKET") {
            $fp = @fsockopen($this->Config->server, 80);
            if (!$fp) {
                return false;
            } else {
                fputs($fp, "GET " . $path .
                        " HTTP/1.0\r\nHost: " . $this->Config->server . "\r\nConnection: Close\r\n\r\n");
                socket_set_timeout($fp, $this->Config->sockettimeout);
                $page = '';
                while (!feof($fp)) {
                    $page .= fread($fp, 2048);
                }
                $status = socket_get_status($fp);
                fclose($fp);
                if ($status['unread_bytes'] == 0 && $status['timed_out'] != 1) {
                    $page = substr($page, strpos($page, "\r\n\r\n") + 4);
                } else
                    return false;
            }
        } elseif ($this->Config->connecttype == "FGC" && function_exists('file_get_contents')) {
            $page = file_get_contents('http://' . str_replace('http://', '', $this->Config->server . $path));
        } else {
            $this->Error('CANNOT DOWNLOAD CODE!');
            return false;
        }
        $page = trim($page);
        if (strlen($page) < 20) {
            if ($page != 'NO SITE') {
                $this->Error($page);
            }
            return false;
        } else {
            $this->SaveLinks($page);
        }
        return true;
    }

    function SaveLinks($page) {
        $info = explode("\t", substr($page, 0, strpos($page, "\n")));
        if ($this->Config->password == $info[1]) {
            $this->servercachetime = $info[0];
            $this->cachetime = time();
            $this->delimiter = $info[2];
            $this->_safe_params = explode(" ", $info[4]);
            $this->_servers = explode(" ", $info[5]);
            $this->errortime = 0;
            if (isset($info[4]) && $info[4] != '')
                $this->_safe_params = explode(" ", $info[4]);
            else
                $this->_safe_params = Array();

            $page = explode("\n", substr($page, strpos($page, "\n") + 1));
            foreach ($page as $key => $val)
                $page[$key] = explode("\t", $val);

            if (!$this->SaveLinksToCache($page, $info))
                $this->Error('Can\'t write cache!');
            else
                return true;
        } else
            $this->Error('Incorrect password!');
        return false;
    }

    function SetCursorPosition($position) {
        $this->curlink = max(intval($position) - 1, 0);
    }

    function Error($error) {
        if ($this->Config->show_errors)
            print('<font color="red">Error: ' . $error . " </font><br>\n");
        SX::syslog('Ошибка в модуле Setlinks! ' . $error, '3', $_SESSION['benutzer_id']);
    }

    function SafeUrlCrc32($url) {
        $url = parse_url(trim($url));
        if (isset($url['query'])) {
            $params = $this->GetQueryParams($url['query']);
            if ($params !== false) {
                ksort($params, SORT_STRING);
                $params_string = Array();
                foreach ($params as $name => $value) {
                    if (in_array(sprintf("%u", crc32($name)), $this->_safe_params)) {
                        if ($value === false)
                            $params_string[] = $name;
                        else
                            $params_string[] = $name . '=' . $value;
                    }
                }
                $params_string = implode('&', $params_string);
            }
        }
        if (isset($url['host']))
            $url['host'] = preg_replace('/^(:?www\.)/i', '', strtolower($url['host']), 1);
        if (!isset($url['path']))
            $url['path'] = "/";
        if (isset($params_string) && $params_string != '')
            $url['query'] = '?' . $params_string;
        else
            $url['query'] = '';
        return sprintf("%u", crc32($url['host'] . $url['path'] . $url['query']));
    }

    function GetQueryParams($query) {
        if (is_null($query) || trim($query) == '')
            return false;
        $params = explode('&', $query);
        $out_params = Array();
        foreach ($params as $val) {
            $delimiter_position = strpos($val, '=');
            if ($delimiter_position === false && $val != '') {
                $out_params[$val] = false;
            } else if ($delimiter_position == 0) {
                // no name...
            } else {
                $name = substr($val, 0, $delimiter_position);
                $value = substr($val, $delimiter_position + 1);
                $out_params[strval($name)] = $value;
            }
        }
        return $out_params;
    }

    function Context(&$text) {
        $this->GetLinks(-1); // получаем ссылки

        $text_new = preg_replace("!<(" . implode("|", $this->Config->context_bad_tags) . ").*?>.*?</\\1>!i", "<!-- setlinks: bad tags replaced -->", $text);
        $goodtexts = explode("<!-- setlinks: bad tags replaced -->", $text_new);
        $replace_chars = array('&' => '(?:&|&amp;)', ' ' => '(?:\s|&nbsp;)+', '"' => '(?:"|&quot;)', '\'' => '(?:\'|&#039;)', '<' => '(?:<|&lt;)', '>' => '(?:>|&gt;)');

        $this->ModerMessage("Context links: " . count($this->context_links));

        if ($this->context_links !== false) {
            foreach ($this->context_links as $key => $link) {
                // приводим ссылку к регулярному виду
                preg_match("!^(.*?)(<a.*?>).*?</a>!i", ' ' . $link . ' ', $matches);
                $begin_text = str_replace(array_keys($replace_chars), $replace_chars, preg_quote(trim(substr($link, 0, strpos('<a', $link))), "!"));
                preg_match("!(<a href=\".*?\".*?>)(.*?)</a>(.*?)$!i", $link, $matches);
                $link_url = $matches[1];
                $link_text = str_replace(array_keys($replace_chars), $replace_chars, preg_quote(trim($matches[2]), "!"));
                $end_text = str_replace(array_keys($replace_chars), $replace_chars, preg_quote(trim($matches[3]), "!"));

                foreach ($goodtexts as $keytext => $goodtext) {
                    $goodtexts[$keytext] = preg_replace("!({$begin_text}(?:\s|&nbsp;)*)({$link_text})((?:\s|&nbsp;)*{$end_text})!i", "\\1$link_url\\2</a>\\3", $goodtexts[$keytext], 1);
                    if ($goodtexts[$keytext] != $goodtext) { // ставим ссылку
                        $text = str_replace($goodtext, $goodtexts[$keytext], $text);
                        unset($this->context_links[$key]);
                        break;
                    }
                }
            }
        }

        $user_ip = (isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR']);
        if ($this->Config->context_show_comments || in_array($user_ip, $this->_servers)) {
            if (preg_match("!<\!--sl_index-->.*?<\!--/sl_index-->!si", $text)) {
                $text = preg_replace("!<\!--sl_index-->!i", '<!--' . substr($this->Config->password, 2, 5) . '-->', $text);
                $text = preg_replace("!<\!--/sl_index-->!i", '<!--/' . substr($this->Config->password, 2, 5) . '-->', $text);
            } else {
                $text = '<!--' . substr($this->Config->password, 2, 5) . '-->' . $text . '<!--/' . substr($this->Config->password, 2, 5) . '-->';
            }
        }
        $text = preg_replace("!<\!--/?sl_index-->!i", '', $text);
        return $text;
    }

    function IsForeverLinks() {
        $this->GetLinks(-1); // получаем ссылки
        if ($this->forever_links === false)
            return false;
        else
            return count($this->forever_links) > 0;
    }

    function ForeverLinks() {
        $this->GetLinks(-1); // получаем ссылки
        $retstring = '';
        //$this->ModerMessage("Forever links: ".var_export($this->forever_links,1));
        if ($this->forever_links !== false) {
            foreach ($this->forever_links as $fl) {
                $fl = explode('<br/>', $fl);
                $retstring .= "<div><b>{$fl[0]}</b><br/><span>{$fl[1]}</span></div>";
            }
        }
        return '<!--' . substr($this->Config->password, 0, 5) . 'f--> ' . $retstring . $this->GetModerMessage();
    }

    function ModerMessage($text) {
        $this->_moder_message .= "[SetLinks: $text]\n";
    }

    function GetModerMessage() {
        $user_ip = (isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR']);
        if (!empty($this->_servers) && in_array($user_ip, $this->_servers)) {
            return '<!--' . $this->_moder_message . '-->';
        } else {
            return '';
        }
    }

// end of class
}

