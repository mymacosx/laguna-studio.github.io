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

class RPC extends Magic {

    public function get($name, $url, $lang) {
        if (!empty($name) && !empty($url) && $_SERVER['HTTP_HOST'] != 'localhost') {
            if (SX::get('system.use_seo') == 1) {
                $url = $this->rewrite($url, $lang);
            }
            $this->ping($name, $url);
        }
    }

    /* Делаем урл красивым */
    protected function rewrite($url, $lang) {
        $url = str_replace('&amp;', '&', $url);
        $url = str_replace('&', '&amp;', $url);
        $this->_view->configLoad(LANG_DIR . '/' . $lang . '/rewrite.txt');
        $vars = $this->_view->getConfigVars();

        $url = preg_replace('/index.php([?])p=showtopic&amp;print_post=([\d]*)&amp;toid=([\d]*)&amp;t=([\w-]*)/iu', $vars['postprint'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=content&amp;id=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $vars['content'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=showforum&amp;fid=([\d]*)&amp;t=([\w-]*)/iu', $vars['forum'] . '/\\2/\\3/', $url);
        $url = preg_replace('/index.php([?])p=roadmap&amp;area=([\d]*)/iu', $vars['roadmap'] . '/\\2/', $url);
        $url = preg_replace('/index.php([?])p=news&amp;area=([\d]*)&amp;newsid=([\d]*)&amp;name=([\w-]*)/iu', $vars['news'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=poll&amp;id=([\d]*)&amp;name=([\w-]*)&amp;area=([\d]*)/iu', $vars['poll'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=downloads&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['downloads'] . '/\\2/\\3/\\4/\\5/', $url);
        $url = preg_replace('/index.php([?])p=faq&amp;action=faq&amp;fid=([\d]*)&amp;area=([\d]*)&amp;name=([\w-]*)/iu', $vars['faq'] . '/' . $vars['show'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=articles&amp;area=([\d]*)&amp;action=displayarticle&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['articles'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=cheats&amp;action=showcheat&amp;area=([\d]*)&amp;plattform=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['cheats'] . '/\\2/\\3/\\4/\\5/', $url);
        $url = preg_replace('/index.php([?])p=links&amp;action=showdetails&amp;area=([\d]*)&amp;categ=([\d]*)&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['links'] . '/\\2/\\3/\\4/\\5/', $url);
        $url = preg_replace('/index.php([?])p=manufacturer&amp;area=([\d]*)&amp;action=showdetails&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['manufacturer'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=products&amp;area=([\d]*)&amp;action=showproduct&amp;id=([\d]*)&amp;name=([\w-]*)/iu', $vars['products'] . '/\\2/\\3/\\4/', $url);
        $url = preg_replace('/index.php([?])p=shop&amp;action=showproduct&amp;id=([\d]*)&amp;cid=([\d]*)&amp;pname=([\w-= ]*)/iu', $vars['shop'] . '/' . $vars['shop_product'] . '/\\2/\\3/\\4/', $url);
        return $url;
    }

    /* Выполняем пинги по списку */
    public function ping($name, $url) {
        $xml = $this->xml($name, $url);
        $sql = $this->_db->query("SELECT Dokument FROM " . PREFIX . "_ping WHERE Aktiv = '1'");
        while ($row = $sql->fetch_object()) {
            if (!empty($row->Dokument)) {
                if (!$this->send($row->Dokument, $xml, 10)) {
                    SX::syslog('Ошибка в модуле RPC! Пинг по адресу ' . $row->Dokument . ' не выполнен. Возможно сервис не доступен', '3', $_SESSION['benutzer_id']);
                }
            }
        }
        $sql->close();
    }

    /* Метод формирует xml код */
    protected function xml($name, $url) {
        $xml = '<?xml version="1.0"?>' . PE;
        $xml .= '<methodCall>' . PE;
        $xml .= '  <methodName>weblogUpdates.ping</methodName>' . PE;
        $xml .= '  <params>' . PE;
        $xml .= '    <param>' . PE;
        $xml .= '     <value>' . $name . '</value>' . PE;
        $xml .= '    </param>' . PE;
        $xml .= '    <param>' . PE;
        $xml .= '      <value>' . $url . '</value>' . PE;
        $xml .= '    </param>' . PE;
        $xml .= '  </params>' . PE;
        $xml .= '</methodCall>' . PE;
        return $xml;
    }

    /* Выполняем пинг */
    protected function send($target, $xml, $time = 30) {
        $target = parse_url($target);
        if (is_array($target)) {
            $target += array('host' => '', 'port' => 80, 'path' => '/', 'query' => '');
            if (!empty($target['host'])) {
                $fp = fsockopen($target['host'], $target['port'], $errno, $errstr, $time);
                if ($fp) {
                    $out = 'POST ' . $target['path'] . $target['query'] . ' ' . HTTP . PE;
                    $out .= 'User-Agent: SX CMS XML-RPC' . PE;
                    $out .= 'Host: ' . $target['host'] . PE;
                    $out .= 'Content-Type: text/xml' . PE;
                    $out .= 'Content-length: ' . strlen($xml) . PE . PE;
                    $out .= $xml;
                    fwrite($fp, $out);
                    fclose($fp);
                    return true;
                }
            }
        }
        return false;
    }

}
