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

class Guestbook extends Magic {

    public function get() {
        $comment = $this->__object('Comments');
        if (Arr::getPost('comment_action') == 'edit' && permission('edit_comments')) {
            $comment->edit('guestbook', $_POST['comment_id']);
        }
        if (permission('guestbook_add')) {
            $comment->add('guestbook', Arr::getPost('Redir'), 9999999);
        }
        $comment->get('guestbook', 9999999, 'index.php?p=guestbook', 1);
        $this->_view->assign('GetComments', $this->_view->fetch(THEME . '/guestbook/entries.tpl'));

        $seo_array = array(
            'headernav' => $this->_lang['Guestbook_t'],
            'pagetitle' => $this->_lang['Guestbook_t'] . Tool::numPage(),
            'content'   => $this->_view->fetch(THEME . '/guestbook/guestbook.tpl'));
        $this->_view->finish($seo_array);
    }

}
