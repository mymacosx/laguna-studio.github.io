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

class Comments extends Magic {

    public function load($modul, $id, $url, $new = true) {
        if (Arr::getPost('comment_action') == 'edit' && permission('edit_comments')) {
            $this->edit($modul, Arr::getPost('comment_id'));
        }
        if (permission('comments') && $new === true) {
            $this->add($modul, Arr::getPost('Redir'), $id);
        } else {
            $this->_view->assign('noComment', 1);
        }
        $this->get($modul, $id, $url);
        $this->_view->assign('GetComments', $this->_view->fetch(THEME . '/comments/comments.tpl'));
    }

    public function change($id) {
        if (!permission('edit_comments')) {
            exit;
        }
        $this->_db->query("UPDATE " . PREFIX . "_kommentare SET Aktiv = '1' WHERE Id = '" . intval($id) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' активировал комментарий', '6', $_SESSION['benutzer_id']);
        $this->__object('Redir')->seoRedirect($this->__object('Redir')->referer(true) . '#comments');
    }

    public function get($bereich, $objekt_id, $navlink, $guestbook = '') {
        $ga = $guestbook == 1 ? 'guestbook' : 'comments';
        if (get_active($ga)) {
            $settings = SX::get('system');
            $db_active = permission('edit_comments') ? '' : "AND Aktiv = '1'";
            $limit = $settings['Kommentare_Seite'];
            $a = Tool::getLimit($limit);
            $gb_sql = $this->_db->query("SELECT SQL_CALC_FOUND_ROWS * FROM " . PREFIX . "_kommentare WHERE Bereich = '" . $this->_db->escape($bereich) . "' AND Objekt_Id = '" . intval($objekt_id) . "' {$db_active} ORDER BY Id DESC LIMIT $a, $limit");
            $num = $this->_db->found_rows();
            $seiten = ceil($num / $limit);
            $eintrag = array();
            while ($_comment = $gb_sql->fetch_assoc()) {
                $_comment['Eintrag_Raw'] = $_comment['Eintrag'];
                if ($settings['SysCode_Aktiv'] == 1) {
                    $_comment['Eintrag'] = $this->__object('Post')->bbcode($_comment['Eintrag'], '', 1);
                } else {
                    $_comment['Eintrag'] = sanitize($_comment['Eintrag']);
                    $_comment['Eintrag'] = nl2br($_comment['Eintrag']);
                }

                if ($settings['SysCode_Smilies'] == 1) {
                    $_comment['Eintrag'] = $this->__object('Post')->smilies($_comment['Eintrag']);
                }
                $_comment['Avatar'] = ($_comment['Autor_Id'] && $settings['Kommentare_Icon'] == 1) ? $this->__object('Avatar')->get($_comment['Autor_Id'], SX::get('system.Kommentare_IconBreite')) : '';
                $_comment['Eintrag'] = Tool::censored($_comment['Eintrag']);
                $_comment['Eintrag'] = $this->__object('Glossar')->get($_comment['Eintrag']);
                $_comment['Autor_Web'] = !empty($_comment['Autor_Web']) ? Tool::checkSheme($_comment['Autor_Web']) : '';
                $eintrag[] = $_comment;
            }

            if ($num > $limit) {
                $this->_view->assign('pages', $this->__object('Navigation')->pagenav($seiten, "<a class=\"page_navigation\" style=\"text-decoration:none\" href=\"" . $navlink . "&amp;page={s}&amp;area=" . AREA . "#comments\">{t}</a> "));
            }
            $gb_sql->close();

            $tpl_array = array(
                'listemos' => $this->__object('Post')->listsmilies(),
                'comments' => 1,
                'eintrag'  => $eintrag);
            $this->_view->assign($tpl_array);
        }
    }

    public function edit($bereich, $id) {
        if (!permission('edit_comments')) {
            exit;
        }
        if (!empty($_POST['E_Eintrag']) && !empty($_POST['E_Autor'])) {
            $array = array(
                'Eintrag'        => Tool::cleanTags($_POST['E_Eintrag'], array('codewidget')),
                'Autor'          => Tool::cleanTags($_POST['E_Autor'], array('codewidget')),
                'Autor_Web'      => Tool::cleanTags($_POST['E_Webseite'], array('codewidget')),
                'Autor_Email'    => Tool::cleanTags($_POST['E_Email'], array('codewidget')),
                'Autor_Herkunft' => Tool::cleanTags($_POST['E_Herkunft'], array('codewidget')),
            );
            $this->_db->update_query('kommentare', $array, "Id = '" . intval($id) . "'");
            SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' отредактировал комментарий (' . $bereich . ')', '6', $_SESSION['benutzer_id']);
        }
    }

    public function delete($id) {
        if (!permission('delete_comments')) {
            exit;
        }
        $this->_db->query("DELETE FROM " . PREFIX . "_kommentare WHERE Id = '" . intval($id) . "'");
        SX::syslog('Пользователь ' . $_SESSION['user_name'] . ' удалил комментарий', '6', $_SESSION['benutzer_id']);
        $this->__object('Redir')->seoRedirect($this->__object('Redir')->referer(true) . '#comments');
    }

    public function add($bereich, $page, $objekt_id) {
        if (Arr::getPost('Eintrag') == 1) {
            $error = array();
            if ($_SESSION['user_group'] == 2) {
                if (empty($_POST['Autor'])) {
                    $error[] = $this->_lang['Comment_NoAuthor'];
                }
                if (empty($_POST['Email'])) {
                    $error[] = $this->_lang['Comment_NoEmail'];
                }
            } else {
                $_POST['Autor'] = $_SESSION['user_name'];
                $_POST['Email'] = $_SESSION['login_email'];
            }
            if (empty($_POST['text'])) {
                $error[] = $this->_lang['Comment_NoComment'];
            }
            if ($this->__object('Captcha')->check($error, true)) {
                $insert_array = array(
                    'Objekt_Id'      => intval($objekt_id),
                    'Bereich'        => $bereich,
                    'Datum'          => time(),
                    'Titel'          => '',
                    'Eintrag'        => $this->_text->substr(Tool::cleanTags(Arr::getPost('text'), array('codewidget')), 0, SX::get('system.Kommentar_Laenge')),
                    'Autor'          => Tool::cleanTags(Arr::getPost('Autor'), array('codewidget')),
                    'Autor_Id'       => ($_SESSION['benutzer_id'] > 0 ? $_SESSION['benutzer_id'] : ''),
                    'Autor_Web'      => Tool::cleanTags(Arr::getPost('Webseite'), array('codewidget')),
                    'Autor_Herkunft' => Tool::cleanTags(Arr::getPost('Herkunft'), array('codewidget')),
                    'Autor_Email'    => Tool::cleanTags(Arr::getPost('Email'), array('codewidget')),
                    'Autor_Ip'       => IP_USER,
                    'Aktiv'          => $this->is());
                $this->_db->insert_query('kommentare', $insert_array);
                $this->__object('Core')->message('Comment_thankyou', 'Comment_thankyouText', $page);
            }
        }
        $this->__object('Captcha')->start(); // Инициализация каптчи
    }

    /* Метод проверки на модерацию комментариев */
    protected function is() {
        if ($_SESSION['user_group'] == 1) {
            return 1;
        }
        return SX::get('system.Kommentar_Moderiert') == 1 ? 0 : 1;
    }

}
