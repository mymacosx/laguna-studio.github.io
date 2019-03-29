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

class AdminComment extends Magic {

    public function show($where = '', $object = '') {
        $this->save();

        $comments = array();
        if (!empty($_REQUEST['id'])) {
            list($table, $column, $modul, $navi, $link) = $this->options($where);
            $row = $this->_db->fetch_object("SELECT *
            FROM
                " . PREFIX . "_kommentare
            WHERE
                Bereich = '" . $this->_db->escape($where) . "'
            AND
                Objekt_Id = '" . intval($object) . "'
            AND
                Id = '" . intval(Arr::getRequest('id')) . "' LIMIT 1");

            if (is_object($row) && !empty($table)) {
                $column = $column . $this->__object('AdminCore')->getLangcode();
                $res = $this->_db->fetch_object("SELECT " . $column . " FROM " . PREFIX . "_" . $table . " WHERE Id = '" . $row->Objekt_Id . "' LIMIT 1");
            } else {

            }
            $row->navi_link = sprintf($link, $row->Objekt_Id);
            $row->navi_modul = $navi;
            $row->title_modul = SX::$lang[$modul];
            $comments[] = $row;
        } else {
            $limit = 10;
            $a = Tool::getLimit($limit);
            $showall = ($where == 'all') ? " Aktiv = '0' " : " Bereich='" . $this->_db->escape($where) . "' AND Objekt_Id='" . intval($object) . "'";
            $sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_kommentare WHERE {$showall} ORDER BY Id DESC LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            while ($row = $sql->fetch_object()) {
                list($table, $column, $modul, $navi, $link) = $this->options($row->Bereich);
                $row->navi_link = sprintf($link, $row->Objekt_Id);
                $row->navi_modul = $navi;
                $row->title_modul = SX::$lang[$modul];
                $comments[] = $row;
            }
            $sql->close();

            if ($num > $limit) {
                $this->_view->assign('Navi', $this->__object('AdminCore')->pagination($seiten, "<a class=\"page_navigation\" href=\"index.php?do=comments&amp;where={$where}&amp;object={$object}&amp;page={s}\">{t}</a> "));
            }
        }
        $this->_view->assign('comments', $comments);
        $this->_view->content('/comments/comments.tpl');
    }

    protected function options($key = 'other') {
        $array = array(
            'news'      => array('news', 'Titel', 'News', 'do=news', 'index.php?p=news&amp;newsid=%s'),
            'content'   => array('content', 'Titel', 'Content', 'do=content', 'index.php?p=content&amp;id=%s'),
            'galerie'   => array('galerie', 'Name_', 'Gallery', 'do=gallery', 'index.php?p=gallery&action=showimage&id=%s'),
            'poll'      => array('umfrage', 'Titel_', 'Polls', 'do=poll', 'index.php?p=poll&amp;action=archive'),
            'links'     => array('links', 'Name_', 'Links', 'do=links', 'index.php?p=links&amp;action=showdetails&amp;id=%s'),
            'downloads' => array('downloads', 'Name_', 'Downloads', 'do=downloads', 'index.php?p=downloads&amp;action=showdetails&amp;id=%s'),
            'products'  => array('produkte', 'Name', 'Products', 'do=products', 'index.php?p=products&amp;action=showproduct&amp;id=%s'),
            'cheats'    => array('cheats', 'Name_1', 'Gaming_cheats', 'do=cheats&amp;sub=show', 'index.php?p=cheats&amp;action=showcheat&amp;id=%s'),
            'articles'  => array('artikel', 'Titel_', 'Articles', 'do=articles&amp;sub=show', 'index.php?p=articles&amp;action=displayarticle&amp;id=%s'),
            'guestbook' => array('', '', 'Guestbook_t', 'do=comments&amp;where=guestbook&amp;object=9999999', 'index.php?p=guestbook'),
            'other'     => array('', '', ' --------- ', ''));
        return isset($array[$key]) ? $array[$key] : $array['other'];
    }

    public function last($limit = 5) {
        $comments = $this->_db->fetch_object_all("SELECT * FROM " . PREFIX . "_kommentare WHERE Aktiv='0' ORDER BY Id DESC LIMIT " . intval($limit));

        $this->_view->assign('comments', $comments);
        return $this->_view->fetch(THEME . '/comments/comments-new.tpl');
    }

    protected function save() {
        if (Arr::getPost('save') == 1) {
            foreach ($_POST['Cid_k'] as $cid => $pid) {
                if (!empty($_POST['Autor'][$cid])) {
                    $array = array(
                        'Autor'          => $_POST['Autor'][$cid],
                        'Eintrag'        => $_POST['Eintrag'][$cid],
                        'Aktiv'          => $_POST['Aktiv'][$cid],
                        'Autor_Web'      => $_POST['Autor_Web'][$cid],
                        'Autor_Email'    => $_POST['Autor_Email'][$cid],
                        'Autor_Herkunft' => $_POST['Autor_Herkunft'][$cid],
                    );
                    $this->_db->update_query('kommentare', $array, "Id = '" . intval($cid) . "'");
                    if (isset($_POST['del'][$cid]) && $_POST['del'][$cid] == 1) {
                        $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Id = '" . intval($cid) . "'");
                    }
                }
            }
            $this->__object('AdminCore')->script('save');
        }
    }

}
