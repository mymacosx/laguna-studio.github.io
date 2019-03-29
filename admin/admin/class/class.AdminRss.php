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

class AdminRss extends Magic {

    public function settings() {
        if (!perm('settings')) {
            $this->__object('AdminCore')->noAccess();
        }
        if (Arr::getPost('save') == 1) {
            $array = array(
                'all'             => $_POST['all'],
                'all_typ'         => $_POST['all_typ'],
                'news'            => $_POST['news'],
                'news_typ'        => $_POST['news_typ'],
                'articles'        => $_POST['articles'],
                'articles_typ'    => $_POST['articles_typ'],
                'forum'           => $_POST['forum'],
                'forum_typ'       => $_POST['forum_typ'],
            );
            SX::save('rss', $array);
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' обновил настройки RSS', '0', $this->UserId);

            $this->__object('AdminCore')->script('save');
            SX::load('rss');
        }
        $this->_view->assign('row', SX::get('rss'));
        $this->_view->assign('title', $this->_lang['SettingsModule'] . ' RSS');
        $this->_view->content('/rss/settings.tpl');
    }

}
