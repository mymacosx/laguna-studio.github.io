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

class Agent {

    public $uri;
    public $agent;
    public $referer;
    public $is_robot = false;
    public $platform;
    public $browser;
    public $version;
    public $mobile;
    public $robot;
    public $search;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'] = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $this->agent = $_SERVER['HTTP_USER_AGENT'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $this->referer = $_SERVER['HTTP_REFERER'] = !empty($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
        if (!empty($this->referer)) {
            $this->search = $this->word();
        }
        if (!empty($this->agent)) {
            $this->platform();
            $this->browser();
            $this->mobile();
            $this->is_robot = $this->robot();
        } else {
            $this->is_robot = true;
        }
    }

    /* Метод возвращает массив операционных систем */
    protected function platformsArray() {
        return array(
            'windows nt 10.0' => 'Windows 10',
            'windows nt 6.4'  => 'Windows 10',
            'windows nt 6.2'  => 'Windows 8',
            'windows nt 6.3'  => 'Windows 8.1',
            'windows nt 6.1'  => 'Windows 7',
            'windows nt 6.0'  => 'Windows Vista',
            'windows nt 5.2'  => 'Windows 2003',
            'windows nt 5.0'  => 'Windows 2000',
            'windows nt 5.1'  => 'Windows XP',
            'windows nt 4.0'  => 'Windows NT',
            'winnt4.0'        => 'Windows NT',
            'winnt 4.0'       => 'Windows NT',
            'winnt'           => 'Windows NT',
            'windows 98'      => 'Windows 98',
            'win98'           => 'Windows 98',
            'windows 95'      => 'Windows 95',
            'win95'           => 'Windows 95',
            'windows'         => 'Unknown Windows OS',
            'os x'            => 'Mac OS X',
            'intel mac'       => 'Intel Mac',
            'ppc mac'         => 'PowerPC Mac',
            'powerpc'         => 'PowerPC',
            'ppc'             => 'PowerPC',
            'cygwin'          => 'Cygwin',
            'linux'           => 'Linux',
            'debian'          => 'Debian',
            'openvms'         => 'OpenVMS',
            'sunos'           => 'Sun Solaris',
            'amiga'           => 'Amiga',
            'beos'            => 'BeOS',
            'apachebench'     => 'ApacheBench',
            'freebsd'         => 'FreeBSD',
            'netbsd'          => 'NetBSD',
            'bsdi'            => 'BSDi',
            'openbsd'         => 'OpenBSD',
            'os/2'            => 'OS/2',
            'warp'            => 'OS/2',
            'aix'             => 'AIX',
            'irix'            => 'Irix',
            'osf'             => 'DEC OSF',
            'hp-ux'           => 'HP-UX',
            'hurd'            => 'GNU/Hurd',
            'unix'            => 'Unknown Unix OS');
    }

    /* Метод возвращает массив браузеров */
    protected function browsersArray() {
        return array(
            'opera'             => 'Opera',
            'msie'              => 'Internet Explorer',
            'internet explorer' => 'Internet Explorer',
            'shiira'            => 'Shiira',
            'firefox'           => 'Firefox',
            'chrome'            => 'Google Chrome',
            'chimera'           => 'Chimera',
            'phoenix'           => 'Phoenix',
            'firebird'          => 'Firebird',
            'camino'            => 'Camino',
            'netscape'          => 'Netscape',
            'omniweb'           => 'OmniWeb',
            'mozilla'           => 'Mozilla',
            'safari'            => 'Safari',
            'konqueror'         => 'Konqueror',
            'icab'              => 'iCab',
            'lynx'              => 'Lynx',
            'links'             => 'Links',
            'hotjava'           => 'HotJava',
            'amaya'             => 'Amaya',
            'ibrowse'           => 'IBrowse');
    }

    /* Метод возвращает массив мобильных браузеров
     * @todo собрать нормальный список мобил, текущий подустарел */
    protected function mobilesArray() {
        return array(
            'mobileexplorer' => 'Mobile Explorer',
            'openwave'       => 'Open Wave',
            'opera mini'     => 'Opera Mini',
            'operamini'      => 'Opera Mini',
            'elaine'         => 'Palm',
            'palmsource'     => 'Palm',
            'digital paths'  => 'Palm',
            'avantgo'        => 'Avantgo',
            'xiino'          => 'Xiino',
            'palmscape'      => 'Palmscape',
            'nokia'          => 'Nokia',
            'ericsson'       => 'Ericsson',
            'blackberry'     => 'BlackBerry',
            'motorola'       => 'Motorola',
            'android'        => 'Android',
            'ipad'           => 'iPad',
            'htc'            => 'HTC');
    }

    /* Метод возвращает массив поисковых роботов */
    protected function robotsArray() {
        return array(
            'googlebot'      => 'Googlebot',
            'msnbot'         => 'MSNBot',
            'slurp'          => 'Inktomi Slurp',
            'yahoo'          => 'Yahoo',
            'askjeeves'      => 'AskJeeves',
            'fastcrawler'    => 'FastCrawler',
            'infoseek'       => 'InfoSeek Robot',
            'lycos'          => 'Lycos',
            'aport'          => 'Aport robot',
            'google'         => 'Google',
            'rambler'        => 'Rambler',
            'abachobot'      => 'AbachoBOT',
            'accoona'        => 'Accoona',
            'acoirobot'      => 'AcoiRobot',
            'aspseek'        => 'ASPSeek',
            'croccrawler'    => 'CrocCrawler',
            'dumbot'         => 'Dumbot',
            'geonabot'       => 'GeonaBot',
            'gigabot'        => 'Gigabot',
            'msrbot'         => 'MSRBOT',
            'scooter'        => 'Altavista robot',
            'altavista'      => 'Altavista robot',
            'webalta'        => 'WebAlta',
            'idbot'          => 'ID-Search Bot',
            'estyle'         => 'eStyle Bot',
            'mail.ru'        => 'Mail.Ru Bot',
            'scrubby'        => 'Scrubby robot',
            'yandex'         => 'Yandex',
            'yadirectbot'    => 'Yandex Direct',
            'abachobot'      => 'Abacho Bot',
            'ia_archiver'    => 'IA.Archiver Bot',
            'baiduspider'    => 'Baidu.com',
            'obot'           => 'oBot',
            'teoma'          => 'Ask Bot',
            'binky'          => 'Binky Bot',
            'amaya'          => 'Аmaya Bot',
            'webgate'        => 'Webgate Bot',
            'w3c_validator'  => 'W3C Validator Bot',
            'libwww'         => 'libwww.nothing Bot',
            'twiceler'       => 'Twiceler Bot',
            'lexxebot'       => 'LexxeBot',
            'bingbot'        => 'BingBot',
            'ahrefs'         => 'AhrefsBot',
            'ezooms'         => 'Ezooms Bot',
            'majestic12'     => 'MJ12bot',
            'trendictionbot' => 'TrendictionBot',
            'archiver'       => 'Archiver',
            'parser'         => 'Parser',
            'spider'         => 'Spider',
            'crawl'          => 'Crawler',
            'bot'            => 'Bot',
        );
    }

    /* Метод возвращает массив ключей поиска */
    protected function wordsArray() {
        return array(
            'yandex.'        => 'text',
            'google.'        => 'q',
            'yahoo.'         => 'p',
            'live.'          => 'q',
            'msn.'           => 'q',
            'lycos.'         => 'query',
            'ask.'           => 'q',
            'altavista.'     => 'q',
            'club-internet.' => 'q',
            'pchome.'        => 'q',
            'netscape.'      => 'query',
            'aport.ru '      => 'r',
            'looksmart.'     => 'qt',
            'alltheweb.'     => 'q',
            'mamma.'         => 'query',
            'about.'         => 'terms',
            'gigablast.'     => 'q',
            'voila.'         => 'rdata',
            'virgilio.'      => 'qs',
            'baidu.'         => 'wd',
            'alice.'         => 'qs',
            'najdi.'         => 'q',
            'mama.'          => 'query',
            'bing.'          => 'q',
            'speedbar.ru'    => 'text',
            'seznam.'        => 'q',
            'search.'        => 'q',
            'netsprint.'     => 'q',
            'luna.tv'        => 'q',
            '03compu.'       => 'query',
            'szukacz.'       => 'q',
            'yam.'           => 'k',
            'a-counter'      => 'sub_data',
            'mail.ru'        => 'q',
            'qip.'           => 'query',
            'meta.ua'        => 'q',
            'i.ua '          => 'q',
            'cnn.'           => 'query',
            'bigmir.net'     => 'q',
            'livetool.'      => 'text',
            'tut.'           => 'query',
            'nigma.'         => 's',
            'speed2.ru'      => 'text',
            'webalta.'       => 'q',
            'aol.'           => array('query', 'encquery', 'q'),
            'rambler.ru'     => array('query', 'words'),
            'szukaj.'        => array('szukaj', 'qt'),
            'ukr.net'        => array('search_query', 'q'));
    }

    /* Вывод поисковой фразы при переходе с поисковика */
    protected function word() {
        $array = array();
        $host = parse_url($this->referer, PHP_URL_HOST);
        parse_str(parse_url($this->referer, PHP_URL_QUERY), $array);
        foreach ($this->wordsArray() as $key => $param) {
            foreach ((array) $param as $p) {
                if (isset($array[$p]) && stripos($host, $key) !== false) {
                    return urldecode($array[$p]);
                }
            }
        }
        return NULL;
    }

    protected function platform() {
        foreach ($this->platformsArray() as $key => $val) {
            if (preg_match('|' . preg_quote($key) . '|iu', $this->agent)) {
                $this->platform = $val;
                return true;
            }
        }
        return false;
    }

    protected function browser() {
        foreach ($this->browsersArray() as $key => $val) {
            $match = array();
            if (preg_match('|' . preg_quote($key) . '.*?([\d\.]+)|iu', $this->agent, $match)) {
                $this->version = $match[1];
                $this->browser = $val;
                return true;
            }
        }
        return false;
    }

    protected function robot() {
        foreach ($this->robotsArray() as $key => $val) {
            if (preg_match('|' . preg_quote($key) . '|iu', $this->agent)) {
                $this->robot = $val;
                return true;
            }
        }
        return false;
    }

    protected function mobile() {
        foreach ($this->mobilesArray() as $key => $val) {
            if (stripos($this->agent, $key) !== false) {
                $this->mobile = $val;
                return true;
            }
        }
        return false;
    }

}
