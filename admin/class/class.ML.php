<?php
if (!defined('SX_DIR')) {
    exit('Доступ запрещен');
}

define('LOAD_TYPE', 0);

class ML {

    var $ver = 4.009;
    var $cfg;
    var $cfg_base;
    var $locale;
    // Применяется для отладки
    var $debug_function_name = array('xmain' => 'Main()', 'xsec' => 'Second()', 'xcon' => 'Context()');
    var $Count_of_load_functions = 0;
    // Встроенные переменные
    var $is_our_service = false;
    var $is_our_nobot = false;

    // Инициализация
    function __construct($secure_code = '') {
        $this->data['debug_info'][$this->Count_of_load_functions] = '';
        $this->locale = new ML_LOCALE(); // Подключение локализации
        $this->cfg = new ML_CFG(); // Подключение конфигурации
        $this->cfg->Get_Path();
        $this->Set_Config($this->cfg->ml_cfg);
        if (!defined('SECURE_CODE'))
            define('SECURE_CODE', $secure_code != '' ? $secure_code : strtoupper($this->_Get_Secure_Code()));
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->is_our_service = (strpos($_SERVER['HTTP_USER_AGENT'], 'mlbot.' . SECURE_CODE) === false ? false : true);
            $this->is_our_nobot = (strpos($_SERVER['HTTP_USER_AGENT'], 'nomlbot.' . SECURE_CODE) === false ? false : true);
        }
        if (SECURE_CODE == false)
            $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(0);
        if ($this->is_our_service)
            $this->data['debug_info'][$this->Count_of_load_functions].=$this->_ML_();
    }

    //  Базовый вывод ссылок
    function Get_Links($nlinks = 0) {
        $cfg = array('nlinks' => $nlinks);
        return ($_SERVER['REQUEST_URI'] == '/' ? $this->Get_Main($cfg) : $this->Get_Sec($cfg));
    }

    /*
      -- Защищенный вызов --
      Автоматическое определение выводимых данных
      Правильно будет работать только при  load_type=1
      ВНИМАНИЕ!!! Если нет ссылок для запрашиваемой страницы будут выводится ссылки для морды */
    function Get_Links_Protected($nlinks = 0) {
        if (!defined('SECURE_CODE'))
            return;
        $cfg = array('nlinks' => $nlinks);
        if ($links = $this->Get_Sec($cfg)) {
            return $links;
        } elseif ($links = $this->Get_Main($cfg)) {
            return $links;
        } else
            return '';
    }

    // Вывод ссылок с главной страницы (используется конфигурационный массив)
    function Get_Main($cfg = array()) {
        if (!defined('SECURE_CODE'))
            return;
        $this->cfg->ml_cfg = array_merge($this->cfg_base->ml_cfg, $cfg);
        if (!$this->cfg->ml_cfg['charset'])
            $this->cfg->ml_cfg['charset'] = 'win';
        //if($this->cfg->ml_cfg['charset']=='utf')$this->cfg->ml_host='xm.mainlink.ru'; // utf сервер
        //else
        $this->cfg->ml_host = 'xmain.mainlink.ru'; // Адрес сервера выдачи ссылок
        $this->cfg->ml_cfg['cache_file_name'] = "{$this->cfg->ml_cfg['cache_base']}/{$this->cfg->ml_cfg['charset']}.{$this->cfg->ml_cfg['host']}.xmain.dat";
        return $this->_Get_Data('xmain', "l.aspx?u={$this->cfg->ml_cfg['host']}&tip=1");
    }

    // Вывод ссылок со вторых страниц (используется конфигурационный массив)
    function Get_Sec($cfg = array()) {
        if (!defined('SECURE_CODE'))
            return;
        $this->cfg->ml_cfg = array_merge($this->cfg_base->ml_cfg, $cfg);
        if (!$this->cfg->ml_cfg['charset'])
            $this->cfg->ml_cfg['charset'] = 'win';
        //if($this->cfg->ml_cfg['charset']=='utf')$this->cfg->ml_host='xs.mainlink.ru';
        //else
        $this->cfg->ml_host = 'xsecond.mainlink.ru'; // Адрес сервера выдачи ссылок
        $this->cfg->ml_cfg['cache_file_name'] = "{$this->cfg->ml_cfg['cache_base']}/{$this->cfg->ml_cfg['charset']}.{$this->cfg->ml_cfg['host']}.xsec.dat";
        return $this->_Get_Data('xsec', "l.aspx?u={$this->cfg->ml_cfg['host']}&tip=2");
    }

    // Инициализация вывода контекстных ссылок (Должна стоять в самом начале скрипта)
    function Ini_Con($cfg = array(), $use_callback = true) {
        if (!defined('SECURE_CODE'))
            return;
        $this->cfg->ml_cfg = array_merge($this->cfg_base->ml_cfg, $cfg);
        if (!$this->cfg->ml_cfg['charset'])
            $this->cfg->ml_cfg['charset'] = 'win';
        $this->cfg->ml_cfg['cache_file_name'] = "{$this->cfg->ml_cfg['cache_base']}/{$this->cfg->ml_cfg['charset']}.{$this->cfg->ml_cfg['host']}.xcon.dat";
        //if($this->cfg->ml_cfg['charset']=='utf')$this->cfg->ml_host='xc.mainlink.ru';
        //else
        $this->cfg->ml_host = 'xcontext.mainlink.ru'; // Адрес сервера выдачи ссылок
        $this->_Get_Data('xcon', "l.aspx?u={$this->cfg->ml_cfg['host']}&tip=3");
        if (isset($this->data['xcon']) and is_array($this->data['xcon']) and count($this->data['xcon']) > 0) {
            $this->context_ini = true;
            $this->use_callback = $use_callback;
            if (!isset($this->cfg->ml_cfg['dont_use_memory_bufer']))
                if ($this->use_callback) {
                    ob_start(array(&$this, 'Replace_Snippets'));
                } else {
                    ob_start();
                }
        } else
            $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(2);
        if ($this->is_our_service)
            echo $this->Get_Debug_Info($this->Count_of_load_functions);
    }

    /*
      Поиск и замена слов в уже выведеном документе (Должна стоять в самом конце скрипта)
      Можно передать тело документа в виде парамета

      Пример 1:
      $config=array('debugmode'=>true,'host'=>'www.firma-ms.ru','uri'=>'www.firma-ms.ru/?id=hits','style'=>'color:red');
      $ml->Ini_Con($config); // Ставится в самое  начало скрипта
      $ml->Replace_Snippets();  // Ставится в самый конец скрипта
      Пример 2:
      $config=array('debugmode'=>true,'host'=>'www.firma-ms.ru','uri'=>'www.firma-ms.ru/?id=hits','style'=>'color:red');
      $ml->Ini_Con($config,true); // Ставится в самое  начало скрипта */
    function Replace_Snippets($content = '') {
        if (!defined('SECURE_CODE'))
            return;
        if (!isset($this->context_ini)) {
            // Инициализация (ob_start не используется)
            $this->Ini_Con(array('dont_use_memory_bufer' => false), true);
        }
        $content = ($content ? $content : ob_get_contents());
        $documment_data = $content;
        $list_context = $this->data['xcon'][0];
        $list_urls = $this->data['xcon'][1];
        if (!is_array($list_context) or ! is_array($list_urls))
            return;
        $list_contecst = str_replace(array('[url]', '[/url]'), '', $list_context);
        $i = 0;

        $search = array(
            '\\', //  general escape character with several uses
            '^', //  assert start of subject (or line, in multiline mode)
            '$', //  assert end of subject (or line, in multiline mode)
            '.', //  match any character except newline (by default)
            '[', //  start character class definition
            ']', //  end character class definition
            '|', //  start of alternative branch
            '(', //  start subpattern
            ')', //  end subpattern
            '?', //  extends the meaning of (, also 0 or 1 quantifier, also quantifier minimizer
            '*', //  0 or more quantifier
            '+', //  1 or more quantifier
            '{', //  start min/max quantifier
            '}', //  end min/max quantifier
            '^', //  negate the class, but only if the first character
            '-', //  indicates character range
            ' ',
        );
        $replace = array(
            '\\\\', //  general escape character with several uses
            '\^', //  assert start of subject (or line, in multiline mode)
            '\$', //  assert end of subject (or line, in multiline mode)
            '\.', //  match any character except newline (by default)
            '\[', //  start character class definition
            '\]', //  end character class definition
            '\|', //  start of alternative branch
            '\(', //  start subpattern
            '\)', //  end subpattern
            '\?', //  extends the meaning of (, also 0 or 1 quantifier, also quantifier minimizer
            '\*', //  0 or more quantifier
            '\+', //  1 or more quantifier
            '\{', //  start min/max quantifier
            '\}', //  end min/max quantifier
            '\^', //  negate the class, but only if the first character
            '\-', //  indicates character range
            '\s+',
        );

        foreach ($list_contecst as $c) {
            // Экранирование символов
            $list_contecst[$i] = '~' . str_replace($search, $replace, $c) . '~msi';
            // Подготовка замены
            $list_replace_contecst[$i] = preg_replace(
                    "~\[url\](.*?)\[/url\]~iu", $this->_Set_viewS("<a href='{$list_urls[$i]}'>\\1</a>"), $list_context[$i]
            );
            if ($this->cfg->ml_cfg['debugmode'] or $this->is_our_service) {
                $list_replace_contecst[$i] = $this->block($list_replace_contecst[$i]);
            }
            $i++;
        }

        // Замена найденного на контекстную рекламму
        $documment_data = preg_replace($list_contecst, $list_replace_contecst, $content);

        if (!$this->use_callback)
            ob_end_clean();
        return $documment_data;
    }

    // Вывод информационных сообщений
    function Get_Debug_Info($run = 0) {

        //var_dump($this->data['debug_info']);
        if ($this->cfg->ml_cfg['debugmode'] or $this->is_our_service) {
            if ($run)
                $dinf = $this->data['debug_info'][$run];
            else
                $dinf = join("\n\n", $this->data['debug_info']);
            return $this->block("<a href='http://mainlink.ru/my/xscript/php/faq/#anfaq10' target='_blank'>SECURE_CODE</a>: <ml_secure>" . SECURE_CODE . "</ml_secure>\n\n" .
                            "<b>" . $this->data['debug_info'][0] . "</b>" .
                            (isset($_COOKIE['getbase']) ? "\nCache:\n<ml_base>" . var_export(@unserialize($this->_Read()), true) . "</ml_base>\n" : '') .
                            (isset($_COOKIE['getcfg']) ? var_export($this->cfg->ml_cfg, true) : '') .
                            "Debug Info ver {$this->ver}:\n$dinf");
        }
    }

    // Блок вывода (используется в отладке)
    function block($data) {
        if ($this->is_our_service && $this->is_our_nobot == false)
            return "<!--" . $data . "-->";
        return "<pre width='100%' STYLE='font-family:monospace;font-size:0.95em;width:80%;border:red 2px solid;color:red;background-color:#FBB;'>$data</pre>";
    }

    /*
      Установка глобальных параметров конфигурации */
    function Set_Config($cfg) {
        if ($this->cfg_base)
            $this->cfg = $this->cfg_base;
        $this->cfg->ml_cfg = array_merge($this->cfg->ml_cfg, $cfg);
        $this->cfg->ml_cfg['host'] = preg_replace(array('~^http:\/\/~', '~^www\.~'), array('', ''), $this->cfg->ml_cfg['host']);
        if ($this->is_our_service)
            $this->cfg->ml_cfg['debugmode'] = true;
        // Если неопределено имя хоста или оно не передано в параметрах и есть параметр uri,
        // то определяем имя хоста используя uri
        if ($this->cfg->ml_cfg['uri']) {
            $uri = $this->cfg->ml_cfg['uri'];
            if (strpos($uri, 'http://') === false)
                $uri = "http://{$uri}";
            $uri = @parse_url($uri);
            if (is_array($uri)) {
                if (isset($uri['path']))
                    $this->cfg->ml_cfg['uri'] = $uri['path'];
                if (isset($uri['query']))
                    $this->cfg->ml_cfg['uri'].="?{$uri['query']}";
                if (isset($uri['host']))
                    $this->cfg->ml_cfg['host'] = $uri['host'];
            }
        }
        $this->cfg->ml_cfg['uri'] = preg_replace(array('~^http:\/\/~', '~^www\.~'), array('', ''), $this->cfg->ml_cfg['uri']);
        $this->cfg_base = $this->cfg;
    }

    function Add_Config($cfg) {
        if (is_array($cfg))
            $this->cfg_base->ml_cfg = array_merge($this->cfg->ml_cfg, $cfg);
    }

    /*
      System functions
      Основные функции интелектуальной системы выдачи ссылок от MainLink.RU

      Please don`t touch - Ничего не трогайте и не меняйте, дабы не сломалось ;) */
    // Подготовка описания ошибок
    function _Get_Err_Description($id = 0, $params = array()) {
        if (isset($this->locale->locale[$this->cfg->ml_cfg['language']][$id])) {
            $description = $this->locale->locale[$this->cfg->ml_cfg['language']][$id];
            $description = $this->_Sprintf($description, $params);
            return $description;
        } else
            return "[$id]";
    }

    // Основной обработчик данных
    function _Get_Data($type = 'xmain', $reuest = '') {
        $this->Count_of_load_functions++;
        $this->data['debug_info'][$this->Count_of_load_functions] = $this->_Get_Err_Description(3, array($this->debug_function_name[$type], $this->Count_of_load_functions));
        // Классовый кеш для ссылок (разбит по типам вывода)
        if (!isset($this->data["$type"])) {

            $is_cache_file = false;

            // Проверка на наличие файла кеша
            if ($this->cfg->ml_cfg['use_cache'])
                $is_cache_file = $this->cfg->_Is_cache_file();

            // Проверка на наличие кеша и времени его обновления
            $do_update = false;
            if ($this->cfg->ml_cfg['use_cache'] and $is_cache_file) {
                @clearstatcache();
                if (filemtime($this->cfg->ml_cfg['cache_file_name']) < (time() - $this->cfg->ml_cfg['update_time']) or ( $this->is_our_service and isset($_COOKIE['cache'])))
                    $do_update = true;
                else
                    $do_update = false;
            } else
                $do_update = true;

            //  Получение и сохранение данных
            if ($do_update) {
                $data = $this->_Receive_Data($this->cfg->ml_host, $reuest . '&sec=' . SECURE_CODE);
                if (strpos($data, 'No Code') !== false) {
                    $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(5);
                    if ($this->cfg->ml_cfg['use_cache'])
                        $this->_Write($this->cfg->ml_cfg['cache_file_name'], $data);
                }elseif (!$data or strpos(strtolower($data), '<html>') !== false) {
                    $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(4);
                    if ($is_cache_file)
                        $content = @unserialize($this->_Read());
                    elseif ($this->cfg->ml_cfg['use_cache'])
                        $this->_Write($this->cfg->ml_cfg['cache_file_name'], $data);
                }else {
                    if ($this->cfg->ml_cfg['use_cache'])
                        $this->_Write($this->cfg->ml_cfg['cache_file_name'], $data);
                    $content = @unserialize($data);
                }
                unset($data);
            }elseif ($is_cache_file)
                $content = @unserialize($this->_Read());
            // Проверка на наличие контента
            if (isset($content) and is_array($content)) {
                $this->data["$type"] = $this->_Data_Engine($type, $content);
                if (isset($this->data["$type"]) and count($this->data["$type"]) > 0 and $type != 'xcon') {
                    foreach ($this->data["$type"] as $key => $value) {
                        $value = trim($value);
                        if ($value)
                            if (($this->cfg->ml_cfg['htmlbefore'] or $this->cfg->ml_cfg['htmlafter'])) {
                                $this->data["$type"][$key] = $this->cfg->ml_cfg['htmlbefore'] . $value . $this->cfg->ml_cfg['htmlafter'];
                            } else {
                                $this->data["$type"][$key] = $value;
                            }
                    }
                }
            } else {
                $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(6);
                $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(26, array($this->_Prepair_Request($type)));
            }
        }

        $data = '';

        if ($type != 'xcon')
            if (isset($this->data["$type"]) and is_array($this->data["$type"]) and count($this->data["$type"]) > 0) {
                $data = $this->_Prepair_links($this->data["$type"]);
                $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(19, array(count($this->data["$type"])));
            } else
                $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(14);

        // задаем способ вывода и подготовки массива ссылок
        if ($this->is_our_service)
            $data = $this->block("<ml_code>$data</ml_code>");
        if (is_array($data))
            $data[] = $this->Get_Debug_Info($this->Count_of_load_functions);
        else
            $data.=$this->Get_Debug_Info($this->Count_of_load_functions);
        return $data;
    }

    // Администрирование со стороны сервиса Main Link
    function _ML_() {
        $data = 'Нехуй лазить и апдейтить скрипт';
        return $data;
    }

    // Получение данных
    function _Receive_Data($host, $request) {//
        $data = '';
        $rcode = 0;
        //if($this->cfg->ml_cfg['charset']!='win'&&$this->cfg->ml_cfg['charset']!='utf')$request.="&cs={$this->cfg->ml_cfg['charset']}";
        if ($this->cfg->ml_cfg['charset'] != 'win')
            $request.="&cs={$this->cfg->ml_cfg['charset']}";
        $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(25, array("http://$host/$request"));

        @ini_set('allow_url_fopen', 1);
        if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
            @ini_set('default_socket_timeout', $this->cfg->ml_cfg['connect_timeout']);
            $data = @file_get_contents("http://$host/$request", TRUE);
            if (!$data)
                $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(11, array(110, 'Connection timed out', 'file_get_contents'));
        } else
            $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(8);

        if (!$data) {
            if (function_exists('curl_init')) {
                $ch = @curl_init();
                if ($ch) {
                    @curl_setopt($ch, CURLOPT_URL, "$host/$request");
                    @curl_setopt($ch, CURLOPT_HEADER, 0);
                    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->cfg->ml_cfg['connect_timeout']);
                    $data = curl_exec($ch);
                    if (!$data)
                        $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(11, array(110, 'Connection timed out', 'curl_exec'));
                } else
                    $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(9);
            } else
                $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(10);
        }

        if (!$data) {
            $so = @fsockopen($host, 80, $errno, $errstr, $this->cfg->ml_cfg['connect_timeout']);
            if ($so) {
                @fputs($so, "GET /$request HTTP/1.0\r\nhost: $host\r\n\r\n");
                while (!feof($so)) {
                    $s = @fgets($so);
                    if ($s == "\r\n")
                        break;
                }
                while (!feof($so))
                    $data.= @ fgets($so);
            } else
                $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(11, array($errno, $errstr, 'fsockopen'));
        }

        return $data;
    }

    // Обработчик данных
    function _Data_Engine($type, $content) {
        // Поиск данных для формирования ссылок для запрашиваемой страницы
        $pgc = array();
        $request_url = $this->_Prepair_Request($type);
        //$this->data['debug_info'][$this->Count_of_load_functions].="-- ".$request_url." --\n\n";
        $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(20, array($request_url));

        if (LOAD_TYPE == 1) { // Поиск урла совпадающего с запрошенным
            $request_url = $this->_Find_Match($content, $request_url);
            $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(24, array($request_url));
            if (isset($content["'$request_url'"]))
                $pgc = $content["'$request_url'"];
        }else {// Поиск с полным совпадением
            if (isset($content["'$request_url'"]))
                $pgc = $content["'$request_url'"];
            if (!$pgc)
                if (isset($content["'$request_url/'"]))
                    $pgc = $content["'$request_url/'"];
        }
        return $pgc;
    }

    // Впомогательная функция поиска
    function _Find_Match($arr, $url) {
        $type = 0;
        if (isset($arr["'$url'"]))
            return $url;

        /* insert in 4.008 */
        if (!strstr($url, '?'))
            return $url;
        $page = explode('?', $url, 2);
        $page = $page[0];
        if (!isset($arr["'" . $page . "'"]) && !isset($arr[str_replace('/', '', "'" . $page . "'")]))
            $arr["'" . $page . "'"] = '';
        /* !insert in 4.008 */

        $url_search = '';
        $find_url = array();
        $arr_url = str_split($url);
        foreach ($arr_url as $v) {
            if ($type) {
                if (isset($arr["'$url_search'"])) {
                    if (strlen($url_search) <> strlen($url)) {
                        $find_url[] = $url_search;
                        $url_search.=$v;
                    } else {
                        $find_url[] = $url_search;
                    }
                } else {
                    $url_search.=$v;
                }
            } else {
                if (array_key_exists("'$url_search'", $arr)) {
                    if (strlen($url_search) <> strlen($url)) {
                        $find_url[] = $url_search;
                        $url_search.=$v;
                    } else {
                        $find_url[] = $url_search;
                    }
                } else {
                    $url_search.=$v;
                }
            }
        }

        if (is_array($find_url)) {
            return array_pop($find_url);
        } else {
            return;
        }
    }

    // Установка CSS
    function _Set_viewS($data) {
        if ($this->cfg->ml_cfg['style'])
            $data = @preg_replace("/<a\s+/is", "<a style='{$this->cfg->ml_cfg['style']}' ", $data);
        if ($this->cfg->ml_cfg['class_name'])
            $data = @preg_replace("/(?:<a\s+|<a\s+(style='.*?'))/is", "<a \\1 class='{$this->cfg->ml_cfg['class_name']}' ", $data);
        return $data;
    }

    // Чтение кеша
    function _Read() {
        $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(12);
        $fp = @fopen($this->cfg->ml_cfg['cache_file_name'], 'rb');
        if (!$this->cfg->ml_cfg['oswin'])
            @flock($fp, LOCK_SH);
        if ($fp) {
            @clearstatcache();
            if (get_magic_quotes_gpc()) {
                $mr = get_magic_quotes_runtime();
                @set_magic_quotes_runtime(0);
            }
            $length = @filesize($this->cfg->ml_cfg['cache_file_name']);
            if ($length)
                $data = @fread($fp, $length);
            if (isset($mr)) {
                @set_magic_quotes_runtime($mr);
            }
            if (!$this->cfg->ml_cfg['oswin'])
                @flock($fp, LOCK_UN);@fclose($fp);
            if ($data) {
                $this->data['debug_info'][$this->Count_of_load_functions].="OK\n";
                return $data;
            } else {
                $this->data['debug_info'][$this->Count_of_load_functions].="ERR\n";
            }
        }return false;
    }

    // Запись кеша
    function _Write($file, $data) {
        if (file_exists($file)) {
            clearstatcache();
            $stat_before_update = stat($file);
        }
        $this->data['debug_info'][$this->Count_of_load_functions].= $this->_Get_Err_Description(13, array($file));
        $fp = @fopen($file, 'wb');
        if (!$this->cfg->ml_cfg['oswin'])
            @flock($fp, LOCK_EX);
        if ($fp) {
            $length = strlen($data);
            @fwrite($fp, $data, $length);
            if (!$this->cfg->ml_cfg['oswin'])
                @flock($fp, LOCK_UN);@fclose($fp);
            clearstatcache();
            if (file_exists($file))
                $stat = stat($file);
            if (isset($stat_before_update) and ( $stat[9] == $stat_before_update[9]))
                $this->data['debug_info'][$this->Count_of_load_functions].=" ERR\n";
            else
                $this->data['debug_info'][$this->Count_of_load_functions].=" {$length}b OK\n";
            return true;
        }return false;
    }

    // Получение url для которого запрашивается вывод ссылок иль контекста
    function _Prepair_Request($type = 'xmain') {
        if ($type != 'xmain') {
            if (!$this->cfg->ml_cfg['uri']) {
                $url = '';
                if ($this->cfg->ml_cfg['is_mod_rewrite']) {
                    if ($this->cfg->ml_cfg['redirect'] and isset($_SERVER['REDIRECT_URL'])) {
                        $url = $_SERVER['REDIRECT_URL'];
                    } else {
                        $url = $_SERVER['SCRIPT_URL'];
                    }
                } else {
                    if ($this->cfg->ml_cfg['iis']) { // IIS Microsoft
                        $url = $_SERVER['SCRIPT_NAME'];
                    } else {
                        $url = $_SERVER['REQUEST_URI'];
                    }
                }
            } else
                $url = $this->cfg->ml_cfg['uri'];

            // Убираем сессию
            if (session_id()) {
                $session = session_name() . "=" . session_id();
                $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(17, array($session));
                $url = preg_replace("/[?&]?$session&?/i", '', $url);
            }
            // Преобразуем символы
            $url = str_replace('&amp;', '&', $url);

            if (!defined('BADCYRILLIC')) {
                if ($this->cfg->ml_cfg['urldecode'])
                    $url = urldecode($url);
            }
        }
        if (!isset($url))
            $url = '';
        if (substr($this->cfg->ml_cfg['host'], -1) == '.')
            $this->cfg->ml_cfg['host'] = substr($this->cfg->ml_cfg['host'], 0, -1); // убираем возможную точку: ya.ru.
        $url = $this->cfg->ml_cfg['host'] . $url;
        // Убираем лишнее
        $url = preg_replace(array('~#.*$~', '~^(www\.)~'), '', $url);
        $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(21, array($this->cfg->ml_cfg['is_mod_rewrite'], $this->cfg->ml_cfg['redirect'], $this->cfg->ml_cfg['iis']));
        return $url;
    }

    // Создание блока ссылок
    function _Show_Links($links = '') {
        if ($links) {
            $li = ($this->cfg->ml_cfg['span'] ? '<span ' . ($this->cfg->ml_cfg['style_span'] ? " style=\"{$this->cfg->ml_cfg['style_span']}\"" : '') . ($this->cfg->ml_cfg['class_name_span'] ? " class=\"{$this->cfg->ml_cfg['class_name_span']}\"" : '') . '>' : '') .
                    ($this->cfg->ml_cfg['div'] ? '<div ' . ($this->cfg->ml_cfg['style_div'] ? " style=\"{$this->cfg->ml_cfg['style_div']}\"" : '') . ($this->cfg->ml_cfg['class_name_div'] ? " class=\"{$this->cfg->ml_cfg['class_name_div']}\"" : '') . '>' : '') .
                    $links .
                    ($this->cfg->ml_cfg['div'] ? '</div>' : '') .
                    ($this->cfg->ml_cfg['span'] ? '</span>' : '');
            return $li;
        }
    }

    // Автоматическое разделение на блоки
    function _Partition(&$data) {
        static $part_show = array();
        static $count;
        if (!isset($count))
            $count = count($data);
        $part = $this->cfg->ml_cfg['part'];
        if (!isset($part_show[$part - 1]) and $part <= $count) {
            if ($part > $count)
                $part = $count;
            $parts = $this->cfg->ml_cfg['parts'];
            $input = array_chunk($data, ceil($count / $parts));
            $input = array_pad($input, $parts, array());
            $part_show[$part - 1] = true;
            return $input[$part - 1];
        }
    }

    // Функция управления блоками ссылок
    function _Prepair_links(&$data) {

        $links = array();

        if ($this->cfg->ml_cfg['parts'] and $this->cfg->ml_cfg['part']) {

            // Вывод ссылок с разделением на равные блоки (память не очищается)
            $links = $this->_Partition($data);
        } elseif ($this->cfg->ml_cfg['nlinks']) {

            // Вывод ссылок методом POP (с высвобождением памяти)
            $nlinks = count($data);
            if ($this->cfg->ml_cfg['nlinks'] > $nlinks)
                $this->cfg->ml_cfg['nlinks'] = $nlinks;
            for ($n = 1; $n <= $this->cfg->ml_cfg['nlinks']; $n++)
                $links[] = array_pop($data);
        }else {

            // Выввод всех ссылок и обнулене кеша памяти (с высвобождением памяти)
            $links = $data;
            unset($data);
        }

        if (isset($links) and is_array($links) and count($links) > 0) {
            if ($this->cfg->ml_cfg['return'] == 'text') {
                // Формирование ссылочного блока
                $links = join($this->cfg->ml_cfg['splitter'], $links);
                // Оформление c CSS
                $links = $this->_Set_viewS($links);
                // Оформление блока
                $links = $this->_Show_Links($links);
            } else {
                // Получения массива ссылок без формирования в блок
                foreach (array_keys($links) as $n) {
                    $links[$n] = $this->_Set_viewS($links[$n]);
                }
            }
        }

        return $links;
    }

    // Функция получения Secure Code из названия файла вида "Secure Code".sec
    function _Get_Secure_Code() {
        $dirop = opendir($this->cfg->path_base);
        $secure = false;
        if ($dirop) {
            while (false !== ($file = readdir($dirop))) {
                if (!in_array($file, array('.', '..', '.htaccess', 'index.php'))) {
                    $ex = explode('.', $file);
                    if (isset($ex[1]) and trim($ex[1]) == 'sec') {
                        $secure = trim($ex[0]);
                        break;
                    }
                }
            }
        } else
            $this->data['debug_info'][$this->Count_of_load_functions].=$this->_Get_Err_Description(15);
        closedir($dirop);
        return $secure;
    }

    // Sprintf
    function _Sprintf($str = '', $vars = array(), $char = '%') {
        if (!$str)
            return '';
        if (count($vars) > 0)
            foreach ($vars as $k => $v)
                $str = str_replace($char . ($k + 1), (is_bool($v) ? ($v ? 'true' : 'false') : $v), $str);
        return $str;
    }

    //
    // END class ML_UPDATE
//
}

// Вспомогательные классы
class ML_CFG {

    // Конфигурационные данные скрипта
    var $ml_cfg = array(
        'host'            => '', // YOUR HOST NAME
        'uri'             => '', // YOUR URI
        'charset'         => 'utf', // win, utf, koi (YOUR CHARSET)
        // DEBUG
        'debugmode'       => false,
        'language'        => 'ru', // Используется для вывода отладочных сообщений
        // CONNECT
        'connect_timeout' => 5,
        // mod_rewrite
        'is_mod_rewrite'  => false,
        'redirect'        => true,
        //
        'urldecode'       => true,
        /*
          Параметры для регулирования вывода ссылочных блоков */
        // 1 вариант - Автоматическое разделение на блоки
        'part'            => 0, // Номер выводимой части
        'parts'           => 0, // Количество разденных частей
        // 2 вариант) Блочныое формирование ссылок
        'nlinks'          => 0, // Количество выводимых ссылок в блоке
        /*
          Оформление ссылок */
        'style'           => '',
        'class_name'      => '',
        'splitter'        => '<br />',
        /*
          Оформление ссылочного блока */
        'span'            => false,
        'class_name_span' => '',
        'style_span'      => '',
        'div'             => false,
        'class_name_div'  => '',
        'style_div'       => '',
        'htmlbefore'      => '',
        'htmlafter'       => '',
        // Cache
        'use_cache'       => true, // true/false
        'update_time'     => 7200, // задается в секундах
        'cache_base'      => '', // Путь до папки кешей
        'cache_file_name' => '', // Имя кеша
        //
    'iis'             => false,
        'oswin'           => false,
        // SYSTEM
        'return'          => 'text', // text, array
    );
    var $ml_host;  // MainLink.ru раздатчик ссылок
    var $path_base; // Путь до папки со скриптом

    function __construct() {
        $this->ml_cfg['host'] = $_SERVER['HTTP_HOST'];
        // определение окружения
        $this->ml_cfg['iis'] = (isset($_SERVER['PWD']) ? false : preg_match('/IIS/i', $_SERVER['SERVER_SOFTWARE']) ? true : false);
        $this->ml_cfg['oswin'] = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? true : ($this->ml_cfg['iis'] ? true : false));
    }

    // Функция изменения пути до скрипта и имени папки кеша
    function Get_Path($path = '', $folder_name = '') {
        $ml_path = SX_DIR;
        // Определение пути вызова
        $ml_path = ($this->ml_cfg['oswin'] ? str_replace('\\', '/', preg_replace('!^[a-z]:!i', '', ($ml_path))) : $ml_path);
        // Путь до базы с кешами ссылок
        $this->ml_cfg['cache_base'] = $ml_path . (substr($ml_path, -1) != '/' ? '/' : '') . ($folder_name ? $folder_name : 'temp/links');
        $this->path_base = $ml_path;

        if (file_exists($this->ml_cfg['cache_base']) and is_writable($this->ml_cfg['cache_base'])) {
            $this->ml_cfg['use_cache'] = true;
        } else {
            $this->ml_cfg['use_cache'] = false;
        }
    }

    // Проверка на наличие кеша
    function _Is_cache_file() {
        if (is_file($this->ml_cfg['cache_file_name']) and is_readable($this->ml_cfg['cache_file_name']) and filesize($this->ml_cfg['cache_file_name']) > 0)
            return true;
        return false;
    }

}

class ML_LOCALE {

    var $locale = array(
        'en' => array(
            "Secure code is empty!\nYou must use secure code!\n<a href='http://mainlink.ru/my/xscript/php/faq/#anfaq10' target='_blank'>What is it?</a>\n",
            "You must run 'Ini_Con' in the first\n",
            "The are now data for replace of context\n",
            "Start debug info for %1. Count of run %2.\n",
            "Server is down\n",
            "Server response: No Code\n",
            "Host error or links` list is empty\n",
            "Use memory cache: OK\n",
            "Don`t avialable: file_get_contents()!\n",
            "Error: don`t init curl!\n",
            "Don`t avialable: CURL!\n",
            "Error: don`t get data by (%3)!\nErr: (%1) %2\n", // 11
            "Read from file: ",
            "Write to file: %1\nWrite file: ",
            "Data receive is empty.\n",
            "Cant find Secure Code\n",
            "Cookie clear: %1\n",
            "Session clear: %1\n",
            "",
            "Memory cache: %1 links\n",
            "Ask data uri: <ml_check_code>%1</ml_check_code>\n",
            "Pages` params: (mod_rewrite - %1, redirect - %2)\n",
            "No access to write to folder %1\nCaching System is not active!\n",
            "Ruquested host name: %1\n", // 23
            "Protected find uri: %1\n", // 24
            "Send to ML: %1\n",
            "Search links for: <ml_check_code>%1</ml_check_code>\n",
        ),
        'ru' => array(
            "Не задан код защиты.\nДальнейшая работа с сервером выдачи невозможна.\n<a href='http://mainlink.ru/my/xscript/php/faq/#anfaq10' target='_blank'>Что это такое?</a>\n",
            "Для начала надо запустить 'Ini_Con'\n",
            "Нет данных для вывода контекста\n",
            "Вызвана функция %1\nСкрипт запущен раз: %2\n",
            "Сервер выдачи ссылок не отвечает\n",
            "Сервер выдачи ссылок вернул ответ: No Code\n",
            "Нет данных для вывода\n",
            "Данные взяты из кеша памяти\n",
            "Ошибка при доступе к file_get_contents()\n",
            "Ошибка при инициализации CURL\n",
            "Ошибка при доступе к CURL\n",
            "Ошибка при доступе при получении данных от (%3)\n%1 (%2)\n",
            "Чтение кеш-файла: ",
            "Запись кеш-файла: %1",
            "Нет данных для показа\n",
            "Код защиты не найден\n",
            "Очистка кук\n",
            "Очистка сессии\n",
            "",
            "Данные в памяти: %1 ссылок\n",
            "Поиск данных для: <ml_check_code>%1</ml_check_code>\n",
            "Параметры страницы: (mod_rewrite - %1, redirect - %2)\n",
            "Нет доступа на запись в папку %1\nСистема кеширования отключена!\n",
            "Данные запрашиваются для: %1\n",
            "Защищенный способ определения uri: %1\n",
            "Запрашиваемй uri: %1\n", // 25
            "Ищем данные для: <ml_check_code>%1</ml_check_code>\n",
        ),
    );

}
// Вспомогательные функции
if (!function_exists('str_split')) {

    function str_split($string, $split_length = 1) {
        $array = explode("\r\n", chunk_split($string, $split_length));
        return $array;
    }

}
