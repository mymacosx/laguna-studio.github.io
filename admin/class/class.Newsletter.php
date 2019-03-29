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

class Newsletter extends Magic {

    public function show() {
        $this->_view->assign('nl_items', $this->section());
        $this->_view->assign('Newsletter', $this->_view->fetch(THEME . '/newsletter/newsletter_small.tpl'));
    }

    public function delete() {
        $error = false;
        if (Arr::nilGet('email') || Arr::nilGet('code') || !Tool::isMail(Arr::getGet('email')) || !Tool::isAllow(Arr::getGet('code'))) {
            $error = true;
            $error_msg[] = $this->_lang['Newsletter_e_unsubscribe'];
        }

        if (!$error) {
            $code = $this->_db->escape(Tool::cleanAllow(Arr::getGet('code')));
            $email = $this->_db->escape(Tool::cleanMail(Arr::getGet('email')));
            $check = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_newsletter_abos WHERE Aktiv='1' AND Email='" . $email . "' AND Code='" . $code . "' LIMIT 1");
            if (!is_object($check)) {
                $error = true;
                $error_msg[] = $this->_lang['Newsletter_e_unsubscribe'];
            } else {
                $this->_db->query("DELETE FROM " . PREFIX . "_newsletter_abos WHERE Email='" . $email . "' AND Code='" . $code . "'");
            }
        }

        if ($error) {
            $this->_view->assign('error', $error_msg);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Newsletter_unsubscribe'],
            'pagetitle' => $this->_lang['Newsletter_unsubscribe'],
            'content'   => $this->_view->fetch(THEME . '/newsletter/newsletter_unsubscribe.tpl'));
        $this->_view->finish($seo_array);
    }

    protected function section() {
        $nl = array();
        $sql = $this->_db->query("SELECT SQL_CACHE Name, Id, Info FROM " . PREFIX . "_newsletter WHERE Sektion='" . AREA . "' ORDER BY Name ASC");
        $c = 0;
        while ($row = $sql->fetch_object()) {
            $c++;
            $row->Count = $c;
            $nl[] = $row;
        }
        $sql->close();
        $this->_view->assign('Nl_Count', $c);
        return $nl;
    }

    public function activate() {
        $error = false;
        if (Arr::nilGet('email') || Arr::nilGet('code') || !Tool::isMail(Arr::getGet('email')) || !Tool::isAllow(Arr::getGet('code'))) {
            $error = true;
            $error_msg[] = $this->_lang['Newsletter_e_activatee'];
        }

        if (!$error) {
            $code = $this->_db->escape(Tool::cleanAllow(Arr::getGet('code')));
            $email = $this->_db->escape(Tool::cleanMail(Arr::getGet('email')));
            $check = $this->_db->cache_fetch_object("SELECT Id FROM " . PREFIX . "_newsletter_abos WHERE Aktiv!='1' AND Email='" . $email . "' AND Code='" . $code . "' LIMIT 1");
            if (!is_object($check)) {
                $error = true;
                $error_msg[] = $this->_lang['Newsletter_e_activatee'];
            } else {
                $this->_db->query("UPDATE " . PREFIX . "_newsletter_abos SET Aktiv='1' WHERE Email='" . $email . "' AND Code='" . $code . "'");
            }
        }

        if ($error) {
            $this->_view->assign('email_error', $error_msg);
        }

        $seo_array = array(
            'headernav' => $this->_lang['Newsletter'],
            'pagetitle' => $this->_lang['Newsletter'],
            'content'   => $this->_view->fetch(THEME . '/newsletter/newsletter_abo_ok.tpl'));
        $this->_view->finish($seo_array);
    }

    public function create() {
        if (Arr::getPost('action') == 'abonew') {
            $error = false;

            if (empty($_POST['nl_email'])) {
                $error = true;
                $error_msg[] = $this->_lang['Newsletter_e_nomail'];
            }

            if (!$error) {
                if (!Tool::isMail($_POST['nl_email'])) {
                    $error = true;
                    $error_msg[] = $this->_lang['Newsletter_e_email'];
                }
            }

            if (empty($_POST['nl_welche'])) {
                $error = true;
                $error_msg[] = $this->_lang['Newsletter_e_noabo'];
            }

            if (!$error) {
                $where = array();
                $secure = Tool::random(8);
                $nl_email = Tool::cleanMail(Arr::getPost('nl_email'));
                $f = isset($_POST['nl_format']) && $_POST['nl_format'] == 'html' ? 'html' : 'text';
                foreach ($_POST['nl_welche'] as $nlid) {
                    if (!empty($nlid)) {
                        $where[] = "Id = '" . intval($nlid) . "'";
                    }
                }
                $sql_nl = $this->_db->query("SELECT Id, Name FROM " . PREFIX . "_newsletter WHERE (" . implode(' OR ', $where) . ") AND Sektion = '" . AREA . "'");
                while ($row = $sql_nl->fetch_object()) {
                    $check = $this->_db->cache_fetch_object("SELECT Email, Aktiv FROM " . PREFIX . "_newsletter_abos WHERE Newsletter_Id='" . $row->Id . "' AND Email = '" . $this->_db->escape($nl_email) . "' LIMIT 1");
                    if (is_object($check)) {
                        if ($check->Aktiv == '0') {
                            $this->_db->query("UPDATE " . PREFIX . "_newsletter_abos SET
                                Datum = '" . time() . "',
                                Code = '" . $secure . "',
                                Format = '" . $f . "'
                            WHERE
                                Newsletter_Id='" . $row->Id . "'
                            AND
                                Email = '" . $this->_db->escape($nl_email) . "'");
                        } else {
                            $error = true;
                            $error_msg[] = str_replace('__NAME__', $row->Name, $this->_lang['Newsletter_e_aboexists']);
                        }
                    } else {
                        $insert_array = array(
                            'Newsletter_Id' => $row->Id,
                            'Datum'         => time(),
                            'Email'         => $nl_email,
                            'Format'        => $f,
                            'Sektion'       => AREA,
                            'Code'          => $secure,
                            'Aktiv'         => 0);
                        $this->_db->insert_query('newsletter_abos', $insert_array);
                    }
                }
                $sql_nl->close();
            }

            if ($error) {
                $this->_view->assign('email_error', $error_msg);
            } else {
                $this->_view->assign('Entry_Ok', 1);
                $mail_array = array(
                    '__WEBSEITE__' => BASE_URL . '/',
                    '__LINK__'     => BASE_URL . '/index.php?p=newsletter&action=activate&email=' . $nl_email . '&code=' . $secure);
                $Text = $this->_text->replace($this->_lang['Newsletter_text'], $mail_array);
                SX::setMail(array(
                    'globs'     => '1',
                    'to'        => $nl_email,
                    'to_name'   => '',
                    'text'      => $Text,
                    'subject'   => $this->_lang['Newsletter_subject'],
                    'fromemail' => SX::get('system.Mail_Absender'),
                    'from'      => SX::get('system.Mail_Name'),
                    'type'      => 'text',
                    'attach'    => '',
                    'html'      => '',
                    'prio'      => 1));
            }
        }

        $seo_array = array(
            'headernav' => $this->_lang['Newsletter'],
            'pagetitle' => $this->_lang['Newsletter'],
            'content'   => $this->_view->fetch(THEME . '/newsletter/newsletter_abo.tpl'));
        $this->_view->finish($seo_array);
    }

}
