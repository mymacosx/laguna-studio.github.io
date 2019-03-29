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

class Bookmark extends Magic {

    /* Вывод содержимого личных закладок */
    public function get() {
        if (Arr::getSession('loggedin') == 1 && $_SESSION['user_group'] != 2) {
            $bookmarks = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_bookmarks WHERE user_id = '" . $_SESSION['benutzer_id'] . "' ORDER BY bookmark_time DESC");
            $this->_view->assign('bookmarks', $bookmarks);
        }
        return $this->_view->fetch(THEME . '/bookmark/bookmarks.tpl');
    }

    /* Добавление пункта в личные закладки */
    public function add($page, $docname) {
        if (!empty($docname)) {
            $insert_array = array(
                'user_id'       => $_SESSION['benutzer_id'],
                'document'      => base64_decode($page),
                'doc_name'      => Tool::cleanAllow($docname, ' '),
                'bookmark_time' => time());
            $this->_db->insert_query('bookmarks', $insert_array);
        }
        $this->__object('Redir')->seoRedirect(base64_decode($page));
    }

    /* Удаление пункта из личных закладок */
    public function delete() {
        if (Arr::getRequest('del_bookmark') >= 1) {
            foreach ($_REQUEST['del_bookmark'] as $id) {
                $this->_db->query("DELETE FROM " . PREFIX . "_bookmarks WHERE id = '" . intval($id) . "' AND user_id = '" . $_SESSION['benutzer_id'] . "'");
            }
        }
        $this->__object('Redir')->seoRedirect(base64_decode($_REQUEST['backurl']));
    }

}
