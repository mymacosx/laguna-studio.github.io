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

class Redir {

    public function __construct() {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', SX::basePatch());
        }
    }

    public function location($link, $code = 301) {
        SX::object('Response')->get($code);
        header('Location: ' . $link);
        exit;
    }

    protected function index($link = null) {
        if (empty($link) || $link == 'index.php') {
            $this->location(BASE_PATH);
        }
    }

    protected function normalize($link) {
        return str_replace('&amp;', '&', $link);
    }

    /* Метод редиректа с реврайтом */
    public function seoRedirect($link = null, $code = 301) {
        $domain = '';
        $this->index($link);
        $link = $this->normalize($link);
        if (SX::get('system.use_seo') == 1) {
            $domain = strpos($_SERVER['SCRIPT_NAME'], 'index.php') ? str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']) : '';
            $url = $_SERVER['HTTP_HOST'] . BASE_PATH;
            $link = $this->normalize(SX::object('Rewrite')->get(str_replace('&', '&amp;', $link)));
            $link = $domain . '/' . str_replace(array('http://' . $url, 'https://' . $url), '', $link);
        }
        $link = str_replace(array($domain . '/http://', $domain . '/https://'), array('http://', 'https://'), $link);
        $this->location($link, $code);
    }

    /* Метод редиректа без раврайта */
    public function redirect($link = null, $code = 301) {
        $this->index($link);
        $link = $this->normalize($link);
        if (stripos($link, '://') === false) {
            $link = BASE_PATH . $link;
        }
        $this->location($link, $code);
    }

    /* Метод формирует ссылку текущей страницы */
    public function link() {
        static $cache = NULL;
        if ($cache === NULL) {
            $uri = $_SERVER['PHP_SELF'];
            if (!empty($_GET)) {
                $params = array();
                foreach ($_GET as $key => $value) {
                    $params[] = urlencode($key) . '=' . urlencode($value);
                }
                $uri .= '?' . implode('&amp;', $params);
            }
            $cache = SX::protocol() . $_SERVER['HTTP_HOST'] . $uri;
        }
        return $cache;
    }

    public function referer($link = false) {
        static $cache = array();
        if (empty($cache)) {
            $value = Arr::getServer('HTTP_REFERER');
            if (stripos($value, BASE_URL) !== false) {
                $array['bool'] = true;
                $array['link'] = $value;
            } else {
                $array['bool'] = false;
                $array['link'] = BASE_URL;
            }
        }
        return $link === true ? $array['link'] : $array['bool'];
    }

}
