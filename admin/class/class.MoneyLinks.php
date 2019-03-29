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

class MoneyLinks extends Magic {

    /* Система продажи ссылок Sape */
    public function sape($code) {
        SX::setDefine('_SAPE_USER', $code);
        $options['charset'] = CHARSET;
        $this->_view->assign('Sape_links', $this->aktive($this->__object('SAPE_client', $options)->return_links()));
    }

    /* Система продажи ссылок Linkfeed */
    public function linkfeed($code) {
        SX::setDefine('LINKFEED_USER', $code);
        $options['charset'] = CHARSET;
        $this->_view->assign('Linkfeed_links', $this->aktive($this->__object('LinkfeedClient', $options)->return_links()));
    }

    /* Система продажи ссылок Setlinks */
    public function setlinks($code) {
        SX::setDefine('SETLINKS_USER', $code);
        $this->_view->assign('setlinks_links', $this->aktive($this->__object('SLClient')->GetLinks(0, '<br />')));
    }

    /* Система продажи ссылок Mainlink */
    public function mainlink($code) {
        SX::setDefine('SECURE_CODE', $code);
        $this->_view->assign('mainlink_links', $this->aktive($this->__object('ML')->Get_Links()));
    }

    /* Система продажи ссылок Trustlink */
    public function trustlink($code) {
        SX::setDefine('TRUSTLINK_USER', $code);
        $options['charset'] = CHARSET;
        $this->_view->assign('trustlink_links', $this->aktive($this->__object('TrustlinkClient', $options)->build_links()));
    }

    /* Проверяем на пустоту параметр */
    protected function aktive($value) {
        static $load = false;
        if (!empty($value) && $load === false) {
            $load = true;
            $this->_view->assign('AktiveLink', 1);
        }
        return $value;
    }

}